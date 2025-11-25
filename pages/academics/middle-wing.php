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
<section class="py-5">
    <div class="container">

        <!-- Heading -->
        <h2 class="text-center mb-5 fw-bold text-dark">Middle School Wing</h2>

        <!-- Row 1 : Image + Description -->
        <div class="row align-items-center mb-5">
            <!-- Left Column : Image -->
            <div class="col-md-6">
                <img src="<?php echo $baseUrl; ?>uploads/aboutus/child-school.png" alt=" School Wing" class="img-fluid rounded shadow">
            </div>

            <!-- Right Column : Content -->
            <div class="col-md-6">
                <h4 class="fw-semibold text-dark">Building Strong Academic Foundations</h4>
                <p class="text-muted mt-3">
                    Our Middle School Wing focuses on empowering students with strong academic excellence 
                    and life skills. We provide a supportive environment that encourages analytical thinking, 
                    creativity, discipline, and leadership qualities. With qualified teachers, smart classrooms, 
                    and well-structured curriculum, we help students excel in their studies and overall development.
                </p>
            </div>
        </div>

        <!-- Row 2 : Course List -->
        <div class="row">
            <h4 class="fw-semibold text-dark mb-4">Courses & Subjects We Offer</h4>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Mathematics</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Science (Physics, Chemistry, Biology)</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>English</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Social Science</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Computer Education</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Environmental Studies</strong>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded shadow-sm text-center">
                    <strong>Languages (Hindi, Sanskrit)</strong>
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