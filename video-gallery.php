<?php
require_once 'config/database.php';
include 'include/header.php';
$stmt = $pdo->query("SELECT * FROM yt_videos WHERE status = 1 ORDER BY display_order ASC ");
$ytVideos = $stmt->fetchAll();

?>

   <!-- YouTube Videos Section -->
   <section class="youtube-section py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-5">Featured Videos</h2>
        <div class="row">
            <?php foreach ($ytVideos as $index => $video): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                    <div class="video-card">
                        <div class="video-wrapper">
                            <iframe width="100%" height="215" 
                                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['youtube_id']); ?>" 
                                title="<?php echo htmlspecialchars($video['title']); ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="video-content">
                            <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                            <p><?php echo htmlspecialchars($video['subtitle']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="https://www.youtube.com/@geoiasupsc" class="btn btn-primary" target="_blank">
                <i class="fab fa-youtube me-2"></i>Subscribe to Our Channel
            </a>
        </div>
    </div>
</section>
<?php
include('include/footer.php');
?>
   