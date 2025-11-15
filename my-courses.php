<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$stmt = $pdo->prepare("
    SELECT 
        c.*,b.name as batch_name, 
        o.id as order_id,
        o.amount,
        o.status as payment_status,
        o.payment_id,
        o.created_at as purchase_date,
        o.razorpay_order_id,
        COALESCE(uc.created_at, NULL) as access_granted_date,
        (SELECT COUNT(*) FROM installment_payments ip WHERE ip.order_id = o.id AND ip.status = 1) as paid_installments,
        (SELECT COUNT(*) FROM course_installments ci WHERE ci.course_id = c.id AND ci.status = 1) as total_installments,
        (SELECT SUM(amount) FROM installment_payments ip WHERE ip.order_id = o.id AND ip.status = 1) as total_paid_amount
    FROM orders o
    JOIN batches b ON b.id = o.batch_id 
    LEFT JOIN courses c ON b.course_id = c.id
    LEFT JOIN user_courses uc ON o.id = uc.order_id
   
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$courses = $stmt->fetchAll();
include 'include/header.php';
?>
<style>
.my-courses-section {
    background-color: #f8f9fa;
}

.card {
    transition: transform 0.2s;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}

.badge {
    padding: 8px 12px;
    font-weight: 500;
}



.btn-primary {
    background-color: #3399cc;
    border-color: #3399cc;
}

.btn-primary:hover {
    background-color: #2d87b4;
    border-color: #2d87b4;
}

@media (max-width: 768px) {
    .card-img-top {
        height: 180px;
    }
}
</style>
<section class="my-courses-section py-5">
    <div class="container">
        <h2 class="mb-4">My Courses</h2>
        
        <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            <p class="mb-0">You haven't purchased any courses yet. <a href="courses.php">Browse Courses</a></p>
        </div>
        <?php else: ?>
        
        <!-- Course Cards -->
        <div class="row g-4">
            <?php foreach ($courses as $course): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <img src="uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($course['batch_name']); ?></p>
                        
                        <!-- Payment Status Badge -->
                        <div class="mb-3">
                            <?php if ($course['total_installments'] > 0): ?>
                                <span class="badge bg-info">
                                    Installment: <?php echo $course['paid_installments']; ?>/<?php echo $course['total_installments']; ?>
                                </span>
                            <?php else: ?>
                                <?php
                                $statusClass = match($course['payment_status']) {
                                    'completed' => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($course['payment_status']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Purchase Details -->
                        <div class="small text-muted mb-3">
                            <p class="mb-1">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Purchased: <?php echo date('d M Y', strtotime($course['purchase_date'])); ?>
                            </p>
                            <?php if ($course['total_installments'] > 0): ?>
                                <p class="mb-1">
                                    <i class="fas fa-rupee-sign me-2"></i>
                                    Paid Amount: ₹<?php echo number_format($course['total_paid_amount']); ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-rupee-sign me-2"></i>
                                    Total Amount: ₹<?php echo number_format($course['price']); ?>
                                </p>
                            <?php else: ?>
                                <p class="mb-1">
                                    <i class="fas fa-rupee-sign me-2"></i>
                                    Amount: ₹<?php echo number_format($course['amount']); ?>
                                </p>
                            <?php endif; ?>
                            <p class="mb-1">
                                <i class="fas fa-receipt me-2"></i>
                                Order ID: #<?php echo $course['order_id']; ?>
                            </p>
                            <?php if ($course['payment_id']): ?>
                            <p class="mb-1">
                                <i class="fas fa-hashtag me-2"></i>
                                Payment ID: <?php echo $course['payment_id']; ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <?php if ($course['total_installments'] > 0): ?>
                                <?php if ($course['paid_installments'] < $course['total_installments']): ?>
                                    <a href="#" class="btn btn-warning">
                                        Pay Next Installment
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($course['payment_status'] === 'completed' || 
                                    ($course['total_installments'] > 0 && $course['paid_installments'] > 0)): ?>
                                <a href="course-resource.php?course=<?php echo $course['id']; ?>" class="btn btn-primary">
                                    Learning Resources
                                </a>
                                <a href="generate-receipt.php?order_id=<?php echo $course['order_id']; ?>" 
                                   class="btn btn-info" target="_blank">
                                    <i class="fas fa-file-invoice me-2"></i>Download Receipt
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>



<?php include('include/footer.php'); ?>

<!-- Add this before closing body tag -->
<script>
$(document).ready(function() {
    $('.verify-payment').on('click', function() {
        const button = $(this);
        const orderId = button.data('razorpay-order-id');
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Verifying...');

        // Call Razorpay API to check payment status
        $.ajax({
            url: 'api/payment/check-payment-status.php',
            type: 'POST',
            data: {
                order_id: orderId
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Verified!',
                        text: 'Your course access has been granted.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: data.message || 'Payment could not be verified'
                    });
                    button.prop('disabled', false).html('<i class="fas fa-sync me-2"></i>Verify Payment');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to check payment status'
                });
                button.prop('disabled', false).html('<i class="fas fa-sync me-2"></i>Verify Payment');
            }
        });
    });
});
</script>
