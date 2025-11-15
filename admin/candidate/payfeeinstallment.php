<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Validate and get user details
if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$user_id = $_GET['id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: list.php');
    exit;
}

// Fetch user's courses with installment details
// Update the SQL query for fetching user's courses
$sql = "SELECT uc.*, c.title as course_title, c.price as course_price,
        (SELECT COUNT(*) FROM installment_payments ip 
         JOIN orders o ON o.id = ip.order_id 
         WHERE o.course_id = uc.course_id AND o.user_id = uc.user_id AND ip.status = 1) as paid_installments,
        (SELECT SUM(ip.amount) FROM installment_payments ip 
         JOIN orders o ON o.id = ip.order_id 
         WHERE o.course_id = uc.course_id AND o.user_id = uc.user_id AND ip.status = 1) as total_paid
        FROM user_courses uc
        JOIN courses c ON c.id = uc.course_id
        WHERE uc.user_id = ? AND uc.status = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user_courses = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pay Fee Installment - <?php echo htmlspecialchars($user['name']); ?></h4>
                </div>
                <div class="card-body">
                    <?php foreach ($user_courses as $course): ?>
                        <?php
                        // Fetch remaining installments
                        $stmt = $pdo->prepare("SELECT * FROM course_installments 
                                             WHERE course_id = ? AND installment_number > ? AND status = 1 
                                             ORDER BY installment_number");
                        $stmt->execute([$course['course_id'], $course['paid_installments']]);
                        $remaining_installments = $stmt->fetchAll();
                        ?>
                        
                        <div class="course-section mb-4 p-3 border rounded text-dark">
                            <h5><?php echo htmlspecialchars($course['course_title']); ?></h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Total Course Fee: ₹<?php echo number_format($course['course_price'], 2); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p>Paid Amount: ₹<?php echo number_format($course['total_paid'], 2); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p>Remaining: ₹<?php echo number_format($course['course_price'] - $course['total_paid'], 2); ?></p>
                                </div>
                            </div>

                            <?php if (!empty($remaining_installments)): ?>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Installment No.</th>
                                                <th>Amount</th>
                                                <th>Due Days</th>
                                                <th>Due Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($remaining_installments as $installment): ?>
                                                <?php
                                                // Calculate due date based on first order
                                                $stmt = $pdo->prepare("SELECT created_at FROM orders 
                                                                     WHERE course_id = ? AND user_id = ? 
                                                                     ORDER BY created_at ASC LIMIT 1");
                                                $stmt->execute([$course['course_id'], $user_id]);
                                                $first_order = $stmt->fetch();
                                                $due_date = date('Y-m-d', strtotime($first_order['created_at'] . ' + ' . $installment['due_days'] . ' days'));
                                                ?>
                                                <tr>
                                                    <td><?php echo $installment['installment_number']; ?></td>
                                                    <td>₹<?php echo number_format($installment['amount'], 2); ?></td>
                                                    <td><?php echo $installment['due_days']; ?> days</td>
                                                    <td><?php echo date('d M Y', strtotime($due_date)); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm pay-installment"
                                                                data-course="<?php echo htmlspecialchars($course['course_title']); ?>"
                                                                data-amount="<?php echo $installment['amount']; ?>"
                                                                data-installment="<?php echo $installment['id']; ?>"
                                                                data-courseid="<?php echo $course['course_id']; ?>">
                                                            Pay Now
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mt-3">
                                    All installments have been paid for this course.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:100% !important;">
            <div class="modal-header">
                <h5 class="modal-title">Pay Installment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="course_id" id="course_id">
                    <input type="hidden" name="installment_id" id="installment_id">
                    <input type="hidden" name="amount" id="payment_amount">
                    
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" class="form-control" id="course_title" readonly>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" class="form-control" id="amount_display" readonly>
                    </div>
                    <div class="form-group">
                        <label>Payment Mode</label>
                        <select class="form-control" name="payment_mode" id="payment_mode" required>
                            <option value="">Select Payment Mode</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="razorpay">Razorpay</option>
                        </select>
                    </div>
                    <div class="form-group utr-field" style="display:none;">
                        <label>UTR/Reference Number</label>
                        <input type="text" class="form-control" name="utr_no">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code to Pay</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode"></div>
                <div class="mt-3">
                    <p>Amount: ₹<span id="qrAmount">0</span></p>
                    <p>UPI ID: <span id="upiId">8770031801@axl</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
$(document).ready(function() {
    $('.pay-installment').on('click', function() {
        var courseTitle = $(this).data('course');
        var amount = parseFloat($(this).data('amount')); // Convert string to number
        var installmentId = $(this).data('installment');
        var courseId = $(this).data('courseid');

        $('#course_title').val(courseTitle);
        $('#amount_display').val('₹' + amount.toFixed(2));
        $('#payment_amount').val(amount);
        $('#installment_id').val(installmentId);
        $('#course_id').val(courseId);

        $('#paymentModal').modal('show');
    });

    $('#payment_mode').on('change', function() {
        if ($(this).val() === 'upi') {
            $('.utr-field').show();
            $('.utr-field input').prop('required', true);
            generateQR();
        } else {
            $('.utr-field').hide();
            $('.utr-field input').prop('required', false);
            $('#qrModal').modal('hide');
        }
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: '../../api/installments/pay.php',  // Update the endpoint
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#paymentForm').find('button[type="submit"]').prop('disabled', false);
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    toastr.success('Payment recorded successfully!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to record payment!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#paymentForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});

function generateQR() {
    var amount = $('#payment_amount').val();
    var upiId = "8770031801@axl";
    var merchantName = "GEO IAS";
    
    var upiUrl = "upi://pay?pa=" + upiId + 
                 "&pn=" + encodeURIComponent(merchantName) + 
                 "&am=" + amount + 
                 "&cu=INR";

    $('#qrcode').empty();
    new QRCode(document.getElementById("qrcode"), {
        text: upiUrl,
        width: 256,
        height: 256
    });

    $('#qrAmount').text(amount);
    $('#qrModal').modal('show');
}
</script>