<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch user details
$user = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}

if (!$user) {
    header('Location: list.php');
    exit;
}

// Fetch all courses
$stmt = $pdo->query("SELECT id, title,price FROM courses WHERE status = 1");
$courses = $stmt->fetchAll();
// After fetching courses, add this:
$installments = [];
if (isset($_GET['course_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM course_installments WHERE course_id = ? AND status = 1 ORDER BY installment_number");
    $stmt->execute([$_GET['course_id']]);
    $installments = $stmt->fetchAll();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Assign Course to <?php echo htmlspecialchars($user['name']); ?></h4>
                    <div>
                        <a href="list.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>Orders List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="orderForm" class="forms-sample">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_id">Select Course</label>
                                    <select class="form-control" id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>" data-price="<?php echo $course['price']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
    <div class="form-group">
        <label for="batch_id">Batch</label>
        <select class="form-control" name="batch_id" id="batch_id" required>
            <option value="">Select Batch</option>
            <?php foreach ($batches as $batch): ?>
                <option value="<?php echo $batch['id']; ?>" 
                    <?php echo ($content && $content['batch_id'] == $batch['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($batch['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="installment_id">Select Installment</label>
                                    <select class="form-control" id="installment_id" name="installment_id">
                                        <option value="">Full Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="amount" name="amount" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="generateQR()">
                                                <i class="mdi mdi-qrcode"></i> Generate QR
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select class="form-control" id="payment_mode" name="payment_mode" required>
                                        <option value="">Select Payment Mode</option>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                        <option value="razorpay">Razorpay</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="utr_no">UTR/Reference Number</label>
                                    <input type="text" class="form-control" id="utr_no" name="utr_no">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Create Order</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='list.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this at the bottom of the page, before footer include -->
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
                    <p>UPI ID: <span id="upiId">your-upi-id@bank</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<!-- Add QR Code library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
$(document).ready(function() {
    // Add this new event handler
    // Modify the course change handler
    $('#course_id').on('change', function() {
        var courseId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price');
        
        // Reset amount and installment dropdown
        $('#amount').val(price || '');
        $('#installment_id').empty().append('<option value="">Full Payment</option>');
        
        if (courseId) {
            // Fetch installments for selected course
            $.ajax({
                url: '../../api/courses/get-installments.php',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.success && response.installments.length > 0) {
                        response.installments.forEach(function(inst) {
                            $('#installment_id').append(
                                `<option value="${inst.id}" data-amount="${inst.amount}">
                                    Installment ${inst.installment_number} - ₹${inst.amount}
                                </option>`
                            );
                        });
                    }
                }
            });
        }
    });

    // Add installment change handler
    $('#installment_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var amount = selectedOption.data('amount');
        if (amount) {
            $('#amount').val(amount);
        } else {
            // If full payment selected, show full course price
            var coursePrice = $('#course_id').find('option:selected').data('price');
            $('#amount').val(coursePrice || '');
        }
    });
    // Show/Hide UTR field based on payment mode
    $('#payment_mode').on('change', function() {
        if ($(this).val() === 'upi') {
            $('#utr_no').parent().show();
            $('#utr_no').prop('required', true);
        } else {
            $('#utr_no').parent().hide();
            $('#utr_no').prop('required', false);
        }
    });

    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            url: '../../api/orders/create.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#orderForm').find('button[type="submit"]').prop('disabled', false);
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.success) {
                    toastr.success('Order created successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to create order!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#orderForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });

    $('#course_id').on('change', function() {
        var courseId = $(this).val();
        var batchSelect = $('#batch_id');
        
        // Clear batch dropdown
        batchSelect.html('<option value="">Select Batch</option>');
        
        if (courseId) {
            // Fetch batches for selected course
            $.ajax({
                url: '../../api/batches/get_batches_by_course.php',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            batchSelect.append(
                                $('<option></option>')
                                    .val(batch.id)
                                    .text(batch.name)
                            );
                        });
                    }
                },
                error: function() {
                    toastr.error('Failed to fetch batches!');
                }
            });
        }
    });  
});

// Add this function to your existing JavaScript
function generateQR() {
    var amount = $('#amount').val();
    if (!amount) {
        toastr.error('Please enter amount first');
        return;
    }

    // Replace with your actual UPI ID
    var upiId = "8770031801@axl";
    var merchantName = "GEO IAS";
    
    // Generate UPI URL
    var upiUrl = "upi://pay?pa=" + upiId + 
                 "&pn=" + encodeURIComponent(merchantName) + 
                 "&am=" + amount + 
                 "&cu=INR";

    // Clear previous QR code
    $('#qrcode').empty();
    
    // Generate new QR code
    new QRCode(document.getElementById("qrcode"), {
        text: upiUrl,
        width: 256,
        height: 256
    });

    // Update amount in modal
    $('#qrAmount').text(amount);
    
    // Show modal
    $('#qrModal').modal('show');
}
</script>