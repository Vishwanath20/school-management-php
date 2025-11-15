<?php
require_once 'config/database.php';
include 'include/header.php';

// Fetch all categories
// Step 1: Fetch all course categories
$stmt = $pdo->query("SELECT * FROM course_categories WHERE status = 1 ORDER BY title");
$categories = $stmt->fetchAll();

// Step 2: Handle category filter using slug
$category_slug = isset($_GET['category_slug']) ? $_GET['category_slug'] : '';
$category_filter = 'all';

if ($category_slug) {
    // Slug se category ID nikaalein
    $stmt = $pdo->prepare("SELECT id FROM course_categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $cat = $stmt->fetch();
    if ($cat) {
        $category_filter = $cat['id'];
    }
}

$where_clause = $category_filter !== 'all' ? "AND c.category_id = ?" : "";

// Step 3: Main query from batches → courses → categories
$sql = "SELECT  b.id as batch_id, b.name as batch_name, b.start_date as batch_start_date, b.end_date as batch_end_date, c.*, cc.title AS category_name 
        FROM batches b
        LEFT JOIN courses c ON b.course_id = c.id
        LEFT JOIN course_categories cc ON c.category_id = cc.id
        WHERE b.status = 1 AND c.status = 1 {$where_clause}
        ORDER BY b.created_at DESC";

$stmt = $category_filter !== 'all'
    ? $pdo->prepare($sql)
    : $pdo->query($sql);

if ($category_filter !== 'all') {
    $stmt->execute([$category_filter]);
} 

$courses = $stmt->fetchAll();

?>
<style>
.filter-section {
    border-bottom: 1px solid #eee;
}

.category-filter {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
}

.category-filter .btn {
    border-radius: 20px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.category-filter .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
</style>
<!-- Category Filter Section -->
<section class="filter-section py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="category-filter text-center">
                    <a href="<?php getSeoData(); ?>courses/all"
                        class="btn <?php echo $category_filter === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?> m-1">
                        All Courses
                    </a>
                    <?php foreach ($categories as $category): ?>
                    <a href="<?php getSeoData(); ?>courses/<?php echo htmlspecialchars($category['slug']); ?>"
                        class="btn <?php echo $category_filter == $category['id'] ? 'btn-primary' : 'btn-outline-primary'; ?> m-1">
                        <?php echo htmlspecialchars($category['title']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courses Section -->
<section id="courses" class="courses-section py-5" data-aos="fade-left" data-aos-delay="300">
    <div class="container">
        <h2 class="section-title text-center mb-5">Explore Our Courses</h2>
        <?php if (empty($courses)): ?>
        <div class="alert alert-info text-center">
            No courses found in this category.
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
            <div class="col-md-4">
                <div class="course-card">
                    <div class="course-header">

                        <img src="<?php getSeoData(); ?>uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>"
                            alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid">
                    </div>
                    <div class="course-body">
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?> <span
                                class="course-tag"><?php echo htmlspecialchars($course['batch_name']); ?></span></h3>
                        <div class="course-details">
                            <div style="display: flex;justify-content: space-between;">
                                <p><i class="fas fa-calendar-alt me-2 calendericon"></i>
                                    Starts on: <?php echo date('M d, Y', strtotime($course['batch_start_date'])); ?></p>
                                <p><i class="fas fa-clock me-2 clockicon"></i>
                                    Ends on: <?php echo date('M d, Y', strtotime($course['batch_end_date'])); ?></p>
                            </div>
                            <?php if($course['is_price_display'] == 1): ?>
                            <div class="course-price">
                                <span class="price">₹<?php echo number_format($course['price']); ?></span>
                                <?php if ($course['original_price'] > $course['price']): ?>
                                <span
                                    class="original-price">₹<?php echo number_format($course['original_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="course-actions mt-3"
                                style="<?php echo $course['is_price_display'] == 1 ? '' : 'margin-top: 67px !important;' ?>">
                                <a href="<?php getSeoData(); ?>course-details.php?id=<?php echo $course['batch_id']; ?>"
                                    class="btn btn-outline-primary me-2">Know More</a>
                                <a href="<?php getSeoData(); ?>course-details.php?id=<?php echo $course['batch_id']; ?>"
                                    class="btn btn-primary">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
    </div>
</section>


<?php
include('include/footer.php');
?>
<?php endif; ?>