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
                        <p class="card-text">
                            To prepare dynamic and caring citizens of tomorrow, to meet the challenges of a global society, while retaining traditional values.
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-eye fa-2x text-primary me-3"></i>
                            <h3 class="mb-0 text-dark">Goal</h3>
                        </div>
                        <p class="card-text">
                            Our goal is to provide emotional stability so that children can learn confidently and encourage them to explore, experiment, and express themselves freely.
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-eye fa-2x text-primary me-3"></i>
                            <h3 class="mb-0 text-dark">Values</h3>
                        </div>
                        <p class="card-text">
                            We instill the feeling of "Service before Self" in our children. </br>
                            We encourage excellence in all aspects of schooling with honesty in thoughts, words, and actions.</br>
                            We promote honesty, integrity, and openness in all we do and foster an environment of collaboration.</br>
                        </p>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                          <img src="<?php echo $baseUrl; ?>uploads/aboutus/vision.png" alt="Evergreen principle image with office" class="img-fluid rounded-3 shadow">
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