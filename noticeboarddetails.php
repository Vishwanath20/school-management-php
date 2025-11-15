<?php
require_once 'config/database.php';

// Fetch notice details
$noticeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ? AND status = 1");
$stmt->execute([$noticeId]);
$notice = $stmt->fetch();

if (!$notice) {
    header("Location: index.php");
    exit();
}

include 'include/header.php';
?>

<!-- Notice Details Section -->
<section class="notice-details-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card notice-card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Notice Header -->
                        <div class="notice-header mb-4">
                            <?php if ($notice['badge']): ?>
                                <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($notice['badge']); ?></span>
                            <?php endif; ?>
                            <h1 class="notice-title"><?php echo htmlspecialchars($notice['title']); ?></h1>
                            <div class="notice-meta">
                                <span class="date">
                                    <i class="far fa-calendar-alt me-2"></i>
                                    <?php echo date('F d, Y', strtotime($notice['date'])); ?>
                                </span>
                                <?php if ($notice['sections']): ?>
                                    <span class="sections ms-3">
                                        <i class="fas fa-tags me-2"></i>
                                        <?php echo htmlspecialchars($notice['sections']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Notice Content -->
                        <div class="notice-content mb-4">
                            <?php echo nl2br(htmlspecialchars($notice['description'])); ?>
                        </div>

                        <!-- Notice Actions -->
                        <?php if ($notice['link']): ?>
                            <div class="notice-actions">
                                <a href="<?php echo htmlspecialchars($notice['link']); ?>" 
                                   class="btn btn-primary" 
                                   target="_blank">
                                    <?php echo htmlspecialchars($notice['link_text'] ?: 'Learn More'); ?>
                                    <i class="fas fa-external-link-alt ms-2"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="javascript:history.back()" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Notices
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.notice-card {
    border-radius: 15px;
    border: none;
}

.notice-title {
    font-size: 2rem;
    color: #2c3e50;
    line-height: 1.3;
}

.notice-meta {
    color: #6c757d;
    font-size: 0.95rem;
    margin-top: 1rem;
}

.notice-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #4a5568;
}

.badge {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.notice-actions {
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .notice-title {
        font-size: 1.5rem;
    }
    
    .notice-content {
        font-size: 1rem;
    }
}
</style>

<?php include('include/footer.php'); ?>