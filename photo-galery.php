<?php
require_once 'config/database.php';

// Fetch all active categories
$stmt = $pdo->prepare("SELECT * FROM gallery_categories WHERE status = 1");
$stmt->execute();
$categories = $stmt->fetchAll();

include 'include/header.php';
?>
<style>
    .gallery-section {
        background-color: #f8f9fa;
    }

    .category-section {
        margin-bottom: 40px;
    }

    .category-section h2 {
        font-size: 1.8rem;
        font-weight: bold;
        /* border-bottom: 3px solid #007bff; */
        display: inline-block;
        margin-bottom: 15px;
    }

    .gallery-card {
        transition: transform 0.3s ease;
    }

    .gallery-card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .gallery-img {
        border-radius: 8px;
        height: 200px;
        object-fit: cover;
    }
</style>

<section class="gallery-section py-5">
    <div class="container">
        <!-- Gallery Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 text-dark">Photo Gallery</h1>
            <p class="lead">Explore our memorable moments</p>
        </div>

        <?php foreach ($categories as $category): ?>
            <!-- Category Section -->
            <div class="category-section mb-5">
                <h2 class="text-primary mb-3"><?php echo htmlspecialchars($category['title']); ?></h2>
                <div class="row g-4">
                    <?php 
                    // Fetch photos for each category
                    $stmt = $pdo->prepare("SELECT * FROM gallery_photos WHERE category_id = ? AND status = 1");
                    $stmt->execute([$category['id']]);
                    $photos = $stmt->fetchAll();
                    
                    if (count($photos) > 0):
                        foreach ($photos as $photo):
                    ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card gallery-card h-100">
                                <a href="uploads/gallery/<?php echo htmlspecialchars($photo['image_path']); ?>" 
                                   data-fancybox="gallery"
                                   data-caption="">
                                    <img src="uploads/gallery/<?php echo htmlspecialchars($photo['image_path']); ?>" 
                                         class="card-img-top gallery-img" 
                                         alt="<?php echo htmlspecialchars($alt); ?>">
                                </a>
                            </div>
                        </div>
                    <?php 
                        endforeach;
                    else: ?>
                        <div class="col-12">
                            <p class="text-muted">No photos available for this category.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<!-- Add Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<!-- Add Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
$(document).ready(function() {
    // Initialize Fancybox
    Fancybox.bind("[data-fancybox]", {
        // Custom options
    });

    // Filter functionality
    $('.filter-btn').click(function() {
        const category = $(this).data('category');
        
        // Update active button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filter items
        if (category === 'all') {
            $('.gallery-item').fadeIn();
        } else {
            $('.gallery-item').hide();
            $('.gallery-item[data-category="' + category + '"]').fadeIn();
        }
    });
});
</script>

<style>
.gallery-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
}

.gallery-img {
    height: 200px;
    object-fit: cover;
}

.filter-btn {
    border-radius: 25px;
    padding: 8px 20px;
}

.filter-btn.active {
    background-color: var(--bs-primary);
    color: white;
}

/* Fancybox Customization */
.fancybox__caption {
    text-align: center;
    font-size: 1.1rem;
}
</style>

<?php include('include/footer.php'); ?>