<?php 
//require_once 'config/database.php';

require_once '../../config/database.php';
include '../../include/fun.php';

// Fetch institute details
$stmt = $pdo->prepare("SELECT * FROM contact_details WHERE id = 9");
$stmt->execute();
$institute = $stmt->fetch();

//include 'include/header.php';
include  '../../include/header.php';

$stmt = $pdo->query("SELECT * FROM faculty WHERE status = 1 ORDER BY display_order ASC");
$faculty = $stmt->fetchAll();

?>


<!-- Faculty Section -->
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


<?php 
   
    include  '../../include/footer.php'; 
?>