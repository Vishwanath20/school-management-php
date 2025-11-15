<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all orders with user and course details
$stmt = $pdo->query("
    SELECT 
        o.*,
        u.name as user_name,
        u.email as user_email,
        b.name as batch_name,
        c.title as course_title,
        c.type as course_type,
        c.price as course_price,
        uc.status as access_status,
        (SELECT COUNT(*) FROM installment_payments ip WHERE ip.order_id = o.id AND ip.status = 1) as paid_installments,
        (SELECT COUNT(*) FROM course_installments ci WHERE ci.course_id = o.course_id AND ci.status = 1) as total_installments,
        (SELECT SUM(amount) FROM installment_payments ip WHERE ip.order_id = o.id AND ip.status = 1) as total_paid_amount
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN batches b ON o.batch_id = b.id
    JOIN courses c ON b.course_id = c.id
    LEFT JOIN user_courses uc ON o.user_id = uc.user_id AND o.course_id = uc.course_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Orders Management</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Payment ID</th>
                                    <th>Payment Status</th>
                                    <th>Course Access</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn=1;
                                 foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['user_name']); ?><br>
                                        <small><?php echo htmlspecialchars($order['user_email']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($order['course_title']); ?><br>
                                        <small><?php echo htmlspecialchars($order['batch_name']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($order['total_installments'] > 0): ?>
                                        ₹<?php echo htmlspecialchars($order['total_paid_amount']); ?> /
                                        ₹<?php echo htmlspecialchars($order['course_price']); ?>
                                        <br>
                                        <small class="text-muted">
                                            Installments:
                                            <?php echo $order['paid_installments']; ?>/<?php echo $order['total_installments']; ?>
                                        </small>
                                        <?php else: ?>
                                        ₹<?php echo htmlspecialchars($order['amount']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['upi_utr_ref']==""?$order['payment_id']:"UPI"."-". $order['upi_utr_ref']); ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?php echo $order['status'] == 'completed' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">

                                            <input type="checkbox" class="form-check-input access-toggle"
                                                data-user-id="<?php echo $order['user_id']; ?>"
                                                data-course-id="<?php echo $order['course_id']; ?>"
                                                <?php echo $order['access_status']==1 ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-order"
                                            data-id="<?php echo $order['id']; ?>"
                                            data-order="<?php echo htmlspecialchars(json_encode($order)); ?>">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <a href="../candidate/payfeeinstallment.php?id=<?php echo $order['user_id']; ?>"
                                            class="btn btn-primary btn-sm">
                                            Pay Fee
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm print-receipt"
                                            data-order="<?php echo htmlspecialchars(json_encode($order)); ?>">
                                            <i class="mdi mdi-printer"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $sn=$sn+1; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="width:100%;">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div id="orderDetails"></div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>
<div id="receiptTemplate" style="display:none;">
    <div class="receipt-content">
        <div style="text-align:center;margin-bottom:20px;">
            <h3>GEO IAS ACADEMY</h3>
            <p>Payment Receipt</p>
        </div>
        <table style="width:100%;margin-bottom:20px;">
            <tr>
                <td><strong>Receipt No:</strong></td>
                <td id="receipt-order-id"></td>
                <td style="text-align:right;"><strong>Date:</strong></td>
                <td style="text-align:right;" id="receipt-date"></td>
            </tr>
        </table>
        <table style="width:100%;margin-bottom:20px;">
            <tr>
                <td><strong>Student Name:</strong></td>
                <td id="receipt-name"></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td id="receipt-email"></td>
            </tr>
        </table>
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
            <tr style="border-bottom:1px solid #000;">
                <th style="text-align:left;padding:8px;">Course</th>
                <th style="text-align:right;padding:8px;">Amount</th>
            </tr>
            <tr>
                <td style="padding:8px;" id="receipt-course"></td>
                <td style="text-align:right;padding:8px;" id="receipt-amount"></td>
            </tr>
            <tr style="border-top:1px solid #000;">
                <td style="text-align:right;padding:8px;"><strong>Total Amount:</strong></td>
                <td style="text-align:right;padding:8px;" id="receipt-total"></td>
            </tr>
        </table>
        <div style="margin-bottom:20px;">
            <p><strong>Payment ID:</strong> <span id="receipt-payment-id"></span></p>
            <p><strong>Payment Status:</strong> <span id="receipt-status"></span></p>
        </div>
        <div style="text-align:center;margin-top:40px;">
            <p>Thank you for choosing GEO IAS ACADEMY!</p>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.table').DataTable({
        "order": [
            [0, "asc"]
        ],
        "pageLength": 25
    });
    // Handle course access toggle
    $('.access-toggle').on('change', function() {
        var userId = $(this).data('user-id');
        var courseId = $(this).data('course-id');
        var status = $(this).prop('checked') ? 1 : 0;

        $.ajax({
            url: '../../api/orders/update-access.php',
            type: 'POST',
            data: {
                user_id: userId,
                course_id: courseId,
                status: status
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.status === 'success') {
                    toastr.success('Course access updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update course access!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle view order details
    $('.view-order').on('click', function() {
        var order = $(this).data('order');
        var html = `
            <p><strong>Order ID:</strong> #${order.id}</p>
            <p><strong>User:</strong> ${order.user_name} (${order.user_email})</p>
            <p><strong>Course:</strong> ${order.course_title} (${order.course_type})</p>`;

        if (order.total_installments > 0) {
            html += `
                <p><strong>Payment Type:</strong> Installment</p>
                <p><strong>Installments Paid:</strong> ${order.paid_installments}/${order.total_installments}</p>
                <p><strong>Amount Paid:</strong> ₹${order.total_paid_amount}</p>
                <p><strong>Total Course Price:</strong> ₹${order.course_price}</p>`;
        } else {
            html += `<p><strong>Amount:</strong> ₹${order.amount}</p>`;
        }

        html += `
            <p><strong>Payment ID:</strong> ${order.upi_utr_ref==''?order.payment_id:"UPI-"+order.upi_utr_ref}</p>
            <p><strong>Payment Status:</strong> ${order.status}</p>
            <p><strong>Order Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
        `;
        $('#orderDetails').html(html);
        $('#orderDetailsModal').modal('show');
    });
});


$('.print-receipt').on('click', function() {
    var order = $(this).data('order');

    // Populate receipt template
    $('#receipt-order-id').text(order.id);
    $('#receipt-date').text(new Date(order.created_at).toLocaleDateString());
    $('#receipt-name').text(order.user_name);
    $('#receipt-email').text(order.user_email);
    $('#receipt-course').text(order.course_title);

    if (order.total_installments > 0) {
        $('#receipt-amount').html(
            `₹${order.total_paid_amount}<br><small>(Installment ${order.paid_installments}/${order.total_installments})</small>`
            );
        $('#receipt-total').text(`₹${order.total_paid_amount}`);
    } else {
        $('#receipt-amount').text('₹' + order.amount);
        $('#receipt-total').text('₹' + order.amount);
    }

    $('#receipt-payment-id').text(order.upi_utr_ref == '' ? order.payment_id : "UPI-" + order.upi_utr_ref);
    $('#receipt-status').text(order.status);

    // Create a new window for printing
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Payment Receipt</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        body { font-family: Arial, sans-serif; padding: 20px; }
        .receipt-content { max-width: 800px; margin: 0 auto; }
        @media print {
            @page { margin: 0.5cm; }
        }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write($('#receiptTemplate').html());
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Wait for content to load then print
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 250);
});
</script>