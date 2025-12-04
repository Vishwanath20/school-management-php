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
                        <img src="<?php echo $baseUrl; ?>uploads/aboutus/mission.png"
                            alt="Evergreen principle image with office" class="hero-section-image">
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
                            To nurture the tender minds to discover, develop, and draw out the hidden talents and magic
                            lying inside them..</br>
                            To provide a stimulating and safe learning environment with innovative and responsible
                            teaching across the curriculum.</br>
                            To maximize individual potential through our core values, ensuring that all students are
                            empowered to meet the challenges of education, work, and life in a rapidly changing global
                            environment.</br>
                            To develop a blend of traditional and modern outlook among the students with advancements in
                            technology.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../include/footer.php'; ?>
