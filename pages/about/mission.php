<?php 
// require_once 'config/database.php';
require_once __DIR__ . '/../../config/database.php';

// Fetch institute details
$stmt = $pdo->prepare("SELECT * FROM contact_details WHERE id = 9");
$stmt->execute();
$institute = $stmt->fetch();

//include 'include/header.php';
require_once __DIR__ . '/../../include/header.php';
?>


<!--  Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                          <img src="<?php echo $baseUrl; ?>uploads/aboutus/mission.png" alt="Evergreen principle image with office" class="img-fluid rounded-3 shadow">
                        <!-- <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-eye fa-2x text-primary me-3"></i>
                            <h3 class="mb-0 text-dark">Our Vision</h3>
                        </div>
                        <p class="card-text">To become a leading educational institute that empowers students with knowledge, skills, and confidence, helping them achieve academic excellence and build successful careers in a competitive world.</p> -->
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
                        <p class="card-text">
                            icon
Through learning, inspire all to make a difference.</br>
To nurture the tender minds to discover, develop, and draw out the hidden talents and magic lying inside them..</br>
To provide a stimulating and safe learning environment with innovative and responsible teaching across the curriculum.</br>
To maximize individual potential through our core values, ensuring that all students are empowered to meet the challenges of education, work, and life in a rapidly changing global environment.</br>
To develop a blend of traditional and modern outlook among the students with advancements in technology.
                        </p>
                    </div>
                </div>
            </div>
        </div>
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


 <?php include_once __DIR__ . '/../../include/footer.php'; ?>