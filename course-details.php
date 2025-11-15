<?php
require_once 'config/database.php';
$batchId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Fetch course details
$stmt = $pdo->prepare("
    SELECT c.*, b.name as batch_name, b.id as batch_id,b.start_date as b_start_date, b.end_date as b_end_date
    FROM batches b
    INNER JOIN courses c ON b.course_id = c.id
    WHERE b.id = ? AND b.status = 1 AND c.status = 1
");
$stmt->execute([$batchId]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit();
}

include 'include/header.php';
?>
<style>
.course-hero {
    padding: 100px 0;
    margin-bottom: 30px;
}

.course-meta {
    font-size: 1.1rem;
    opacity: 0.9;
}

.course-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #4a5568;
}

.course-sidebar {
    border-radius: 15px;
    position: sticky;
    top: 20px;
}

.price-box {
    padding: 20px 0;
}

.original-price {
    text-decoration: line-through;
    color: #c2185b;
    font-size: 15px;
    display: block;

}

.current-price {
    color:black;
    font-size: 2.5rem;
    margin: 0;
}

.course-features li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    color:rgb(47, 50, 52);
}

.course-features li:last-child {
    border-bottom: none;
}

.enroll-btn {
    padding: 15px 30px;
    font-size: 1.1rem;
}
.video-container {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.video-container:hover {
    transform: translateY(-5px);
}

.card {
    border: none;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.text-danger {
    color: #dc3545;
}

.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
/* .elementor-widget-container{
    header:auto !important;
} */
@media (max-width: 768px) {
    .course-hero {
        padding: 50px 0;
    }
    
    .course-sidebar {
        margin-top: 30px;
        position: static;
    }
}
</style>


<!-- Course Details Hero Section -->
<section class="course-hero py-5" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>') center/cover">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($course['batch_name']); ?></span>
                <h1 class="text-white mb-3"><?php echo htmlspecialchars($course['title']); ?></h1>
                <div class="course-meta text-white">
                    <span class="me-4"><i class="far fa-calendar-alt me-2"></i>Starts: <?php echo date('d M Y', strtotime($course['b_start_date'])); ?></span>
                    <span><i class="far fa-calendar-check me-2"></i>Ends: <?php echo date('d M Y', strtotime($course['b_end_date'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Details Content -->
<section class="course-details py-5">
    <div class="container">
        <div class="row">
            <!-- Course Content -->
            <!-- After the course description card and before the sidebar -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-4 text-dark">Course Details</h3>
                        <div class="course-description">
                            <?php echo $course['description'] ?>
                        </div>
                    </div>
                </div>

                <!-- Curriculum PDF Download Card -->
                <?php if ($course['curriculum_pdf']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-4 text-dark">Course Curriculum</h3>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger fa-3x me-3"></i>
                            <div>
                                <h5 class="mb-2 text-dark">Download Course Curriculum</h5>
                                <a href="uploads/courses/curriculum/<?php echo htmlspecialchars($course['curriculum_pdf']); ?>" 
                                   class="btn btn-primary" target="_blank">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Video Lectures Section -->
                <?php if (!empty($course['video_links'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-4 text-dark">Video Details</h3>
                        <div class="row">
                            <?php 
                            $videoLinks = json_decode($course['video_links'], true);
                            foreach ($videoLinks as $video): 
                                // Extract video ID from YouTube URL
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video, $matches);
                                if (!empty($matches[1])):
                                    $videoId = $matches[1];
                            ?>
                            <div class="col-md-6 mb-4">
                                <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 10px;">
                                    <iframe 
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                                        src="https://www.youtube.com/embed/<?php echo $videoId; ?>"
                                        title="YouTube video"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Course Sidebar -->
            <div class="col-lg-4">
                <div class="card course-sidebar shadow-sm" data-aos="fade-left">
                    <div class="card-body">
                        <img src="uploads/courses/<?php echo htmlspecialchars($course['thumbnail']);?>" alt="<?php echo htmlspecialchars($course['title']);?>" class="img-fluid mb-3">
                        <?php if($course['is_price_display'] == 1): ?>
                        <div class="price-box text-center mb-4">
                            <h2 class="current-price">₹<?php echo number_format($course['price']); ?><span style="font-size:13px;">Only</span></h2>
                            <?php if ($course['original_price'] > $course['price']): ?>
                                <small class="original-price">₹<?php echo number_format($course['original_price']); ?></small>
                            <?php endif; ?>
                        </div>
                         <?php endif; ?>
                          <?php
                                    $whatsapp_number = '918319513300'; // Replace with actual WhatsApp number
                                    $whatsapp_message = urlencode("Hello hello sir/ma'am, I am interested in the course: " . htmlspecialchars($course['title']) . ". Please provide the more info.");
                                    $whatsapp_link = "https://wa.me/{$whatsapp_number}?text={$whatsapp_message}";
                                ?>
                        <div class="d-grid gap-2">
                            <?php if($course['is_price_display'] == 1): ?>
                               <a class="btn btn-success btn-lg enroll-btn" href="<?php echo $whatsapp_link; ?>" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>JOIN US
                                </a>
                            <?php else: ?>
                               
                                <a class="btn btn-success btn-lg enroll-btn" href="<?php echo $whatsapp_link; ?>" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>JOIN US
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="course-features mt-4">
                            <h5 class="mb-3 text-dark">Course Features</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock me-2" style="color:#ff6d00;"></i>Duration: <?php 
                                    $start = new DateTime($course['b_start_date']);
                                    $end = new DateTime($course['b_end_date']);
                                    $duration = $start->diff($end);
                                    echo $duration->format('%m months %d days');
                                ?></li>
                                <li><i class="fas fa-graduation-cap me-2" style="color:#5e35b1;"></i>Type: <?php echo htmlspecialchars($course['type']); ?></li>
                                <li><i class="fas fa-calendar-alt me-2" style="color:#7a68f4;"></i>Batch Starts: <?php echo date('d M Y', strtotime($course['b_start_date'])); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include('include/footer.php'); ?>
