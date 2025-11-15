<?php
require_once 'config/database.php';
include 'include/header.php';
include 'include/fun.php';
$leads = isset($_GET['source']) ? $_GET['source'] : 'web';
// Fetch active slider items
$stmt = $pdo->query("SELECT * FROM hero_sliders WHERE status = 1 ORDER BY display_order ASC");
$sliders = $stmt->fetchAll();


$stmt = $pdo->query("SELECT * FROM free_resource_master WHERE status = 1 ORDER BY display_order ASC");
$freeResources = $stmt->fetchAll();

// Fetch active notices
$stmt = $pdo->query("SELECT * FROM notices WHERE status = 1 ORDER BY date DESC");
$notices = $stmt->fetchAll();

// Separate notices based on sections
$noticeBoard = array_filter($notices, function($notice) {
    return $notice['sections'] == 0;
});

$announcements = array_filter($notices, function($notice) {
    return $notice['sections'] == 1;
});

// $stmt = $pdo->query("SELECT * FROM courses WHERE status = 1 ORDER BY created_at DESC");
// $courses = $stmt->fetchAll();

// $stmt = $pdo->query("
//     SELECT c.*, b.name as batch_name ,b.id as batchid,b.start_date as b_start_date,b.end_date as b_end_date
//     FROM courses c 
//     LEFT JOIN batches b ON c.id = b.course_id 
//     WHERE c.status = 1 AND b.status = 1
//     ORDER BY c.created_at DESC
// ");
// $courses = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM why_choose_us WHERE status = 1 ORDER BY display_order ASC");
$whyChooseUs = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM toppers_reviews WHERE status = 1 ORDER BY display_order ASC");
$toppers = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM faculty WHERE status = 1 ORDER BY display_order ASC");
$faculty = $stmt->fetchAll();


$stmt = $pdo->query("SELECT * FROM topic_wise_syllabus_master WHERE status = 1 ORDER BY display_order ASC");
$syllabusCategories = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM topic_wise_syllabus_sub WHERE status = 1 ORDER BY display_order ASC");
$syllabusSubs = [];
foreach ($stmt->fetchAll() as $sub) {
    $syllabusSubs[$sub['master_id']][] = $sub;
}

$stmt = $pdo->query("SELECT * FROM topic_wise_syllabus_topics WHERE status = 1 ORDER BY display_order ASC");
$syllabusTopics = [];
foreach ($stmt->fetchAll() as $topic) {
    $syllabusTopics[$topic['sub_id']][] = $topic;
}

$stmt = $pdo->query("SELECT * FROM yt_videos WHERE status = 1 ORDER BY display_order ASC LIMIT 3");
$ytVideos = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM upcoming_exams WHERE status = 1 AND exam_date > NOW() ORDER BY exam_date ASC");
$upcomingExams = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM contact_details WHERE status='Active'");
 $contactDetails = $stmt->fetch();

 // Fetch active news and events
$stmt = $pdo->query("SELECT * FROM news_events WHERE status = 1 ORDER BY event_date DESC");
$newsEvents = $stmt->fetchAll();

?>

<style>
.owl-theme .owl-nav {
    margin-top: -5px !important;
}
</style>
<!-- Hero Section -->
<section class="hero-slider p-0" data-aos="fade-up">
    <div class="owl-carousel owl-theme">
        <?php foreach ($sliders as $slider): ?>
        <div class="hero-slide d-flex align-items-center"
            style="background-image: url('uploads/sliders/<?php echo htmlspecialchars($slider['image']); ?>');">
            <?php if ($slider['title'] || $slider['description']): ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
                            <?php if ($slider['title']): ?>
                            <h1 class="hero-title"><?php echo htmlspecialchars($slider['title']); ?></h1>
                            <?php endif; ?>

                            <?php if ($slider['description']): ?>
                            <p class="hero-text"><?php echo htmlspecialchars($slider['description']); ?></p>
                            <?php endif; ?>

                            <?php if ($slider['button_text'] && $slider['button_link']): ?>
                            <a href="<?php echo htmlspecialchars($slider['button_link']); ?>"
                                class="btn btn-primary btn-lg">
                                <?php echo htmlspecialchars($slider['button_text']); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<!-- Free Resources Section -->


<!-- Notice Board Section -->
<section class="notice-board-section py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <div class="row">
            <div class="col-md-6 Announcementsec">
                <h2 class="section-title text-center mb-3">About Us</h2>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="notice-container Announcement-container" style="padding: 0px;">
                            <div class="row">
                                <div class="col-md-12" style="padding:0px !important;">
                                    <img src="uploads/aboutus/eps-school.jfif" height="250px" class="img-fluid"
                                        alt="<?php echo htmlspecialchars($alt); ?>">
                                </div>
                                <div class="col-md-12" style="padding: 0px 30px 30px 30px;">
                                    <h1 class="mt-3 mb-2 about-heading" style="color:#3b2b98;font-size: 18px;">Welcome
                                        to Evergreen public shcool – Best english medium school</h1>
                                    <p style="text-align: justify;" class="about-desc">Margdarshan Institute Sarangarh,
                                        offering the best computer classes, pre-nursing and pre-agriculture coaching
                                        <a href="#">Read More..</a> </p>
                                         <!-- <a href="about-us.php">Read More..</a> </p> -->
                                    <!-- <h2 style="color:#fff;">Sarangarh Best Computer Institute</h2>       
                                    <h2 style="color:#fff;">Computer Institutes in Sarangarh</h2>
                                    <h2 style="color:#fff;">Government Recognized Computer Training Center</h2>
                                    <h2 style="color:#fff;">Computer Education Training Institute in Sarangarh</h2>
                                    <h2 style="color:#fff;">Best Computer Institute in Sarangarh</h2>
                                    <h2 style="color:#fff;">Best Computer Training Institute in Sarangarh</h2>
                                    <h2 style="color:#fff;">Best Computer Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Top Computers Classes Near Me in Sarangarh</h2>
                                    <h2 style="color:#fff;">Best Computer Course Institute in Sarangarh Chhattisgarh</h2>
                                    <h2 style="color:#fff;">DCA Computer Course Institute in Sarangarh Chhattisgarh</h2>
                                    <h2 style="color:#fff;">PGDCA Computer Course Institute in Sarangarh Chhattisgarh</h2>
                                    <h2 style="color:#fff;">Tally Computer Course Institute in Sarangarh Chhattisgarh</h2>
                                    <h2 style="color:#fff;">Popular Computer Training Institutes in Sarangarh, Raigarh Chhattisgarh</h2>
                                    <h2 style="color:#fff;">Pre Nursing Coaching for ANM/GNM in Sarangarh</h2>
                                     <h2 style="color:#fff;">Pre Nursing Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Best Pre Nursing Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Pre Nursing test Coaching center in Sarangarh</h2>
                                    <h2 style="color:#fff;">Margdarshan institute Pre Nursing Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Agriculture Entrance Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Agriculture exam Coaching in Sarangarh</h2>
                                    <h2 style="color:#fff;">Agriculture Entrance exam Coaching in Sarangarh</h2> -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Notice Board -->
            <div class="col-md-6 noticeboard-section">
                <h2 class="section-title text-center mb-3">Notice Board</h2>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="notice-container">
                            <div class="notice-scroll">
                                <?php foreach ($noticeBoard as $notice): 
                                    $date = new DateTime($notice['date']);
                                ?>
                                <div class="notice-item">
                                    <div class="notice-date">
                                        <span class="date"><?php echo $date->format('d'); ?></span>
                                        <span class="month"><?php echo $date->format('M'); ?></span>
                                    </div>
                                    <div class="notice-content">
                                        <h4><?php echo htmlspecialchars($notice['title']); ?>
                                            <?php if ($notice['badge']): ?>
                                            <span
                                                class="badge bg-danger ms-2"><?php echo htmlspecialchars($notice['badge']); ?></span>
                                            <?php endif; ?>
                                        </h4>
                                        <p><?php echo htmlspecialchars(truncateText($notice['description'], 50)); ?></p>
                                        <a href="<?php echo htmlspecialchars($notice['link']) ? htmlspecialchars($notice['link']) : 'noticeboarddetails.php?id=' . htmlspecialchars($notice['id']); ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <?php echo htmlspecialchars($notice['link_text']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements -->


        </div>
    </div>
</section>

<!-- Courses Section -->
<!-- <section id="courses" class="courses-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Explore Our Courses</h2>
        <div class="owl-carousel courses-carousel">
            <?php foreach ($courses as $course): ?>
            <div class="item">
                <div class="course-card">
                    <div class="course-header">

                        <img src="uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>"
                            alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid">
                    </div>
                    <div class="course-body">
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?> <span
                                class="course-tag"><?php echo htmlspecialchars($course['batch_name']); ?></span></h3>
                        <div class="course-details">
                            <div style="display: flex;justify-content: space-between;">
                                <p><i class="fas fa-calendar-alt me-2 calendericon"></i>
                                    Starts on: <?php echo date('M d, Y', strtotime($course['b_start_date'])); ?></p>
                                <p><i class="fas fa-clock me-2 clockicon"></i>
                                    Ends on: <?php echo date('M d, Y', strtotime($course['b_end_date'])); ?></p>
                            </div>
                            <?php if($course['is_price_display'] == 1): ?>

                            <div class="course-price">
                                <span class="price">₹<?php echo number_format($course['price']); ?></span>
                                <?php if ($course['original_price'] > $course['price']): ?>
                                <span class="original-price"
                                    style="color:red !important;">₹<?php echo number_format($course['original_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="course-actions mt-3"
                                style="<?php echo $course['is_price_display'] == 1 ? '' : 'margin-top: 67px !important;' ?>">
                                <a href="course-details.php?id=<?php echo $course['batchid']; ?>"
                                    class="btn btn-outline-primary me-2">Know More</a>
                                <a href="course-details.php?id=<?php echo $course['batchid']; ?>"
                                    class="btn btn-primary">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section> -->

<!-- Why Choose Us Section -->
<section class="why-choose-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5" style="color: #fff;">Why Choose EverGreen Public School?</h2>
        <div class="row">
            <?php foreach ($whyChooseUs as $feature): ?>
            <div class="col-lg-4 mb-4" data-aos="flip-right">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="uploads/features/<?php echo htmlspecialchars($feature['icon']); ?>"
                            alt="<?php echo htmlspecialchars($alt); ?>" class="img-fluid">
                    </div>
                    <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                    <p class="feature-text"><?php echo htmlspecialchars($feature['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonials-section py-5" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-1">Topper's Reviews</h2>
        <div class="owl-carousel testimonial-carousel">
            <?php foreach ($toppers as $topper): ?>
            <div class="testimonial-item">
                <div class="testimonial-content">
                    <div class="testimonial-img">
                        <img src="uploads/toppers/<?php echo htmlspecialchars($topper['photo']); ?>"
                            alt="<?php echo htmlspecialchars($alt); ?>" class="img-fluid rounded-circle">
                    </div>
                    <h4 class="testimonial-name"><?php echo htmlspecialchars($topper['name']); ?></h4>
                    <p class="testimonial-rank">
                        RANK <?php echo htmlspecialchars($topper['exam_rank']); ?>,
                        <?php echo htmlspecialchars($topper['exam']); ?>
                        <?php echo htmlspecialchars($topper['year']); ?>
                    </p>
                    <p class="testimonial-text">"<?php echo htmlspecialchars($topper['review']); ?>"</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- News and Events Section -->
<section id="news-events" class="news-events-section py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">News and Events</h2>
        <div class="owl-carousel news-events-carousel">
            <?php foreach ($newsEvents as $newsEvent): ?>
            <div class="item">
                <div class="news-event-card">
                    <div class="news-event-media">
                        <?php if (!empty($newsEvent['photo'])): ?>
                            <img src="uploads/news-events/<?php echo htmlspecialchars($newsEvent['photo']); ?>" alt="<?php echo htmlspecialchars($newsEvent['event_name']); ?>" class="img-fluid">
                        <?php elseif (!empty($newsEvent['youtube_url'])): ?>
                            <iframe width="100%" height="200" src="<?php echo htmlspecialchars($newsEvent['youtube_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php else: ?>
                            <img src="images/placeholder.svg" alt="No Media" class="img-fluid">
                        <?php endif; ?>
                    </div>
                    <div class="news-event-body">
                        <h3 class="news-event-title text-primary"><?php echo htmlspecialchars($newsEvent['event_name']); ?></h3>
                        <p class="news-event-date"><i class="fas fa-calendar-alt me-2"></i><?php echo date('d M Y', strtotime($newsEvent['event_date'])); ?></p>
                        
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- Faculty Section -->
<section id="faculty" class="faculty-section py-5" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-5">Our Expert Faculty</h2>
        <div class="owl-carousel faculty-carousel">
            <?php foreach ($faculty as $member): ?>
            <div class="item">
                <div class="faculty-card">
                    <div class="faculty-img">
                        <img src="uploads/faculty/<?php echo htmlspecialchars($member['photo']); ?>"
                            alt="<?php echo htmlspecialchars($alt); ?>" class="img-fluid">
                    </div>
                    <div class="faculty-info">
                        <h4 class="faculty-name">
                            <?php echo htmlspecialchars($member['title'] . ' ' . $member['name']); ?></h4>
                        <p class="faculty-subject"><?php echo htmlspecialchars($member['specialization']); ?></p>
                        <p class="faculty-exp"><?php echo htmlspecialchars($member['experience']); ?>+ years experience
                        </p>
                        <div class="faculty-social">
                            <?php if ($member['linkedin']): ?>
                            <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($member['twitter']): ?>
                            <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<!-- <section class="cta-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0" data-aos="fade-right" data-aos-offset="300"
                data-aos-easing="ease-in-sine">
                <h2 class="cta-title">Aiming for UPSC?</h2>
                <p class="cta-text">Begin Your Preparation Today!</p>
                <p class="cta-description">Join Margdarshan and get access to expert faculty, comprehensive study material,
                    and personalized mentorship.</p>
            </div>
            <div class="col-lg-4 text-lg-end" data-aos="fade-left" data-aos-anchor="#example-anchor"
                data-aos-offset="500" data-aos-duration="500">
                <a href="#" class="btn btn-light btn-lg me-3">Download Our App</a>
                <a href="#" class="btn btn-primary btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section> -->

<!-- App Download Section -->
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
                            title="<?php echo htmlspecialchars($video['title']); ?>" frameborder="0"
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
            <a href="video-gallery.php" class="btn btn-primary" target="_blank">
                <i class="fab fa-youtube me-2"></i>More..
            </a>
            <a href="#" class="btn btn-primary" target="_blank">
                <i class="fab fa-youtube me-2"></i>Subscribe to Our Channel
            </a>
        </div>
    </div>
</section>
<!-- Exam Counter Section -->
<section class="exam-counter-section py-5" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-5 text-white">Upcoming Exams</h2>
        <div class="row">
            <?php foreach ($upcomingExams as $index => $exam): ?>
            <div class="col-lg-6 mb-4">
                <div class="countdown-container">
                    <div class="countdown-box">
                        <h3 class="countdown-title"><?php echo htmlspecialchars($exam['title']); ?></h3>
                        <div class="countdown-timer" id="exam-counter-<?php echo $exam['id']; ?>">
                            <div class="countdown-item">
                                <div class="countdown-number" id="days-<?php echo $exam['id']; ?>">00</div>
                                <div class="countdown-label">Days</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-number" id="hours-<?php echo $exam['id']; ?>">00</div>
                                <div class="countdown-label">Hours</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-number" id="minutes-<?php echo $exam['id']; ?>">00</div>
                                <div class="countdown-label">Minutes</div>
                            </div>
                            <div class="countdown-item">
                                <div class="countdown-number" id="seconds-<?php echo $exam['id']; ?>">00</div>
                                <div class="countdown-label">Seconds</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- Contact Form Section -->
<section id="contact" class="contact-section py-5 stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-offset="300"
                data-aos-easing="ease-in-sine">
                <h2 class="section-title mb-4">Contact Details</h2>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <div>
                            <h4>Office</h4>
                            <p>Andola, Jashpur, Sarangarh</p>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['address']); ?></p> -->
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>XXXXXX</p>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['phone']); ?></p> -->
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <div>
                            <h4>Email</h4>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['email']); ?></p> -->
                            <p>info@eps.in</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <div>
                            <h4>Working Hours</h4>
                            <p><?php echo htmlspecialchars($contactDetails['working_hours']); ?></p>
                        </div>
                    </div>
                </div>
                <div>
                    <iframe src="<?php echo htmlspecialchars($contactDetails['map_embed']); ?>" width="100%"
                        height="150" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
                data-aos-duration="500">
                <div class="contact-form-container">
                    <h2 class="section-title mb-4">Get in Touch</h2>
                    <form id="contactForm" class="contact-form">
                        <input name="source" type="text" value="<?php echo $leads ; ?>" style="display:none;" />
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Your Name*" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Your Email*" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Your Phone*" required>
                        </div>
                        <div class="mb-3">
                            <select name="course_interest" class="form-select" required>
                                <option value="">Select Course Interest</option>
                                <option value="DCA">DCA</option>
                                <option value="PGDCA">PGDCA</option>
                                <option value="PRE NURSING TEST">PRE NURSING TEST</option>
                                <option value="PRE AGRICULTURE TEST">PRE AGRICULTURE TEST</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4"
                                placeholder="Your Message"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                            <label class="form-check-label" for="agreeCheck">
                                I agree to receive information regarding my submitted enquiry
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </form>
                    <!-- Alert Messages -->
                    <div id="successAlert" class="alert alert-success mt-3" style="display: none;">
                        Thank you for contacting us! We will get back to you soon.
                    </div>
                    <div id="errorAlert" class="alert alert-danger mt-3" style="display: none;">
                        Oops! Something went wrong. Please try again.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- <section id="contact" class="cta-section" style="padding:20px 0px 20px 0px">

    <div class="container">
        <div class="row" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
            data-aos-duration="500">
            <div class="col-lg-12">
                <div>
                    <img class="img-fluid" src="images/contact-banner.jpeg" alt="" srcset="">
                </div>
            </div>

        </div>
    </div>
</section> -->
<?php
include('include/footer.php');
?>
<script>
$(document).ready(function() {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 400 // Increase this value so animation triggers later
    });


    // // Hero Slider
    $('.hero-slider .owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        margin: 0,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        animateOut: 'fadeOut',
        navText: false,
        responsive: {
            0: {
                nav: false
            },
            768: {
                nav: true
            }
        }
    });


    // // Testimonial Carousel
    $('.testimonial-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });


    $('.courses-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: false,
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            576: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });
    // Add this with other carousel initializations
    $('.faculty-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            576: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
    // // Counter Animation
    function counterAnimation() {
        $('.stat-number').each(function() {
            var $this = $(this),
                countTo = $this.attr('data-count');

            $({
                countNum: $this.text()
            }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
    }
    // Trigger counter animation when stats section is in viewport
    var statsSection = $('.stats-section');
    var statsSectionTop = statsSection.offset().top;
    var windowHeight = $(window).height();
    var hasAnimated = false;

    $(window).scroll(function() {
        var scrollTop = $(this).scrollTop();

        if (!hasAnimated && scrollTop > (statsSectionTop - windowHeight + 200)) {
            counterAnimation();
            hasAnimated = true;
        }
    });

    // Smooth scrolling for anchor links
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location
            .hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
                return false;
            }
        }
    });

    // Back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });

    $('.back-to-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 1000);
        return false;
    });
    // Exam Counter
    function updateCounter(targetDate, daysId, hoursId, minutesId, secondsId) {
        const now = new Date().getTime();
        const distance = targetDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById(daysId).innerHTML = days.toString().padStart(2, '0');
        document.getElementById(hoursId).innerHTML = hours.toString().padStart(2, '0');
        document.getElementById(minutesId).innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById(secondsId).innerHTML = seconds.toString().padStart(2, '0');
    }

    <?php foreach ($upcomingExams as $exam): ?>
    const examDate<?php echo $exam['id']; ?> = new Date('<?php echo $exam['exam_date']; ?>').getTime();
    if (examDate<?php echo $exam['id']; ?> > new Date().getTime()) {
        setInterval(() => {
            updateCounter(
                examDate<?php echo $exam['id']; ?>,
                'days-<?php echo $exam['id']; ?>',
                'hours-<?php echo $exam['id']; ?>',
                'minutes-<?php echo $exam['id']; ?>',
                'seconds-<?php echo $exam['id']; ?>'
            );
        }, 1000);
    }
    <?php endforeach; ?>
});

    // News and Events Carousel
    $('.news-events-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        navText: ["", ""],
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            576: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });
document.querySelectorAll('.syllabus-tabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function() {
        const filePath = this.closest('.nav-item').getAttribute('data-id');
        console.log(filePath)
        const downloadBtn = document.getElementById('syllabusDownloadBtn');
        if (downloadBtn && filePath) {
            downloadBtn.href = filePath;
        }

    });
});
</script>