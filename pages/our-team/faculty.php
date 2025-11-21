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
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-dark">Meet Our Faculty</h2>

        <div class="row g-4">

            <!-- Faculty Card 1 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-1.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Mr. Rahul Sharma</h5>
                        <p class="text-primary mb-1 fw-semibold">Science Teacher</p>
                        <p class="card-text text-muted">
                            An experienced educator with 10+ years in teaching Physics & Chemistry, 
                            inspiring students with practical learning approaches.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Faculty Card 2 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-2.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Mrs. Priya Verma</h5>
                        <p class="text-primary mb-1 fw-semibold">Mathematics Teacher</p>
                        <p class="card-text text-muted">
                            Passionate about making mathematics fun and easy, helping students 
                            build strong analytical and problem-solving skills.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Faculty Card 3 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-3.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Ms. Anjali Gupta</h5>
                        <p class="text-primary mb-1 fw-semibold">English Teacher</p>
                        <p class="card-text text-muted">
                            Skilled in modern teaching techniques, she focuses on communication, 
                            grammar, and personality development of students.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

            <!-- Faculty Card 1 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-4.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Mr. Rahul Sharma</h5>
                        <p class="text-primary mb-1 fw-semibold">Science Teacher</p>
                        <p class="card-text text-muted">
                            An experienced educator with 10+ years in teaching Physics & Chemistry, 
                            inspiring students with practical learning approaches.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Faculty Card 2 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-4.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Mrs. Priya Verma</h5>
                        <p class="text-primary mb-1 fw-semibold">Mathematics Teacher</p>
                        <p class="card-text text-muted">
                            Passionate about making mathematics fun and easy, helping students 
                            build strong analytical and problem-solving skills.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Faculty Card 3 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?php echo $baseUrl; ?>uploads/aboutus/staff-1.png" class="card-img-top" alt="Faculty Image">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark">Ms. Anjali Gupta</h5>
                        <p class="text-primary mb-1 fw-semibold">English Teacher</p>
                        <p class="card-text text-muted">
                            Skilled in modern teaching techniques, she focuses on communication, 
                            grammar, and personality development of students.
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

<?php 
   
    include  '../../include/footer.php'; 
?>