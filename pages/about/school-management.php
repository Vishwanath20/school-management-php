<?php 
//require_once 'config/database.php';

require_once '../../config/database.php';

// Fetch institute details
$stmt = $pdo->prepare("SELECT * FROM contact_details WHERE id = 9");
$stmt->execute();
$institute = $stmt->fetch();

//include 'include/header.php';
include  '../../include/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">€
        <div class="row align-items-center">
            <div class="col-lg-6">
               <h1>Welcome to Evergreen public shcool</h1>

<p>
At <strong>Evergreen public shcool</strong>, we are committed to guiding students towards a brighter future through <strong>quality education</strong>, <strong>expert mentorship</strong>, and <strong>practical learning</strong>. Recognized as a <strong>trusted coaching center in Sarangarh</strong>, our goal is to empower students with the knowledge and skills they need to succeed in academics, <strong>competitive exams</strong>, and their professional careers.
</p>

<!-- <p>
We offer a wide range of certified courses including <strong>computer training</strong>, <strong>pre-nursing coaching</strong>, and <strong>pre-agriculture entrance preparation</strong>. Backed by an experienced faculty, advanced computer labs, and modern digital classrooms, we provide the best learning environment in the region.
</p> -->

<!-- <p>
With a strong focus on <strong>personalized attention</strong>, <strong>career counseling</strong>, and <strong>job placement support</strong>, Margdarshan Coaching Institute ensures every student receives the right direction — the true “Margdarshan” — to build a successful future.
</p> -->
<!-- 
<p>
📍 Located in the heart of Sarangarh, we are your local destination for career growth and academic excellence.  
📚 <strong>Join us today</strong> and take your first step towards a rewarding future!
</p> -->

            </div>
            <div class="col-lg-6">
                <!-- <img src="uploads/aboutus/about-margdarshan.jpg" alt="<?php echo htmlspecialchars($alt); ?>" class="img-fluid rounded-3 shadow"> -->
                 <img src="uploads/aboutus/office.jfif" alt="Evergreen principle image with office" class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Vision Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-eye fa-2x text-primary me-3"></i>
                            <h3 class="mb-0 text-dark">Our Vision</h3>
                        </div>
                        <p class="card-text">To become a leading educational institute that empowers students with knowledge, skills, and confidence, helping them achieve academic excellence and build successful careers in a competitive world.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-bullseye fa-2x text-primary me-3"></i>
                            <h3 class="mb-0 text-dark">Our Mission</h3>
                        </div>
                        <p class="card-text">✅ To provide quality education through experienced faculty and modern teaching methods.<br>
✅ To nurture talent by offering career-oriented courses with practical learning.<br>
✅ To create a supportive environment that encourages continuous growth and learning.<br>
✅ To guide students in achieving their goals through mentorship, educational tours, and job placement assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-4 text-dark">Visit Our Institute</h2>
                <!-- <div class="mb-4">
                    <p><i class="fas fa-map-marker-alt text-primary me-2"></i> <?php echo nl2br(htmlspecialchars($institute['address'])); ?></p>
                    <p><i class="fas fa-phone text-primary me-2"></i> <?php echo htmlspecialchars($institute['phone']); ?></p>
                    <p><i class="fas fa-envelope text-primary me-2"></i> <?php echo htmlspecialchars($institute['email']); ?></p>
                    <p><i class="fas fa-clock text-primary me-2"></i> <?php echo htmlspecialchars($institute['working_hours']); ?></p>
                </div> -->
                <div class="mb-4">
                    <p><i class="fas fa-map-marker-alt text-primary me-2"></i> Evergreen public school, Andola, Jashpur, Sarangarh</p>
                    <p><i class="fas fa-phone text-primary me-2"></i> XXXXXXXXX</p>
                    <p><i class="fas fa-envelope text-primary me-2"></i> info@epscg.in</p>
                    <p><i class="fas fa-clock text-primary me-2"></i> Monday - Saturday: 9:00 AM - 3:00 PM</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="map-container rounded-3 overflow-hidden">
                   <iframe src=" <?php echo $institute['map_embed']; ?>" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-primary text-white py-5" style="background:url('uploads/aboutus/call-to-action.jpg') no-repeat center center; background-size: cover;">
    <div class="container text-center" style="height:300px">
        <a href="courses.php" class="btn btn-light btn-lg">Explore Our Courses</a>
    </div>
</section>

<style>
.hero-section {
    background: linear-gradient(45deg, #2d87b4, #3399cc);
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.map-container {
    height: 400px;
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: 0;
}

.fas {
    transition: transform 0.3s ease;
}

.text-center:hover .fas {
    transform: scale(1.1);
}
</style>

<?php 
   
    include  '../../include/footer.php'; 
?>