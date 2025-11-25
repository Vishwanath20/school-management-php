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



<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4 text-dark">Achievements </h2>
        <div class="row g-4">

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">Our school provides quality education from Nursery to Class 8 with a strong focus on holistic development.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">We offer a safe, nurturing, and inspiring environment where students enjoy learning.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">Our curriculum balances academics, co-curricular activities, and life-skills for overall growth.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">Experienced teachers use modern teaching methods to build a strong academic foundation.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">We encourage creativity, participation, and exploration through engaging classroom activities.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">The school promotes values, discipline, teamwork, and respect in everyday learning.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">We ensure a secure campus with well-maintained classrooms and student-friendly facilities.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="mb-0">Students are prepared for future challenges through essential skills and value-based education.</p>
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