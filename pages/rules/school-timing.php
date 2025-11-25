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
<section class="container py-5">

    <!-- Heading -->
    <h2 class="text-center fw-bold mb-4">School Timings</h2>

    <!-- Intro -->
    <p class="text-center text-muted mb-5">
        Our school follows a structured and student-friendly schedule designed to balance academics,
        co-curricular activities, and overall wellbeing.
    </p>

    <!-- Timings Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">Regular School Timings</h4>

                    <table class="table table-bordered mb-4">
                        <tbody>
                            <tr>
                                <th width="40%">Nursery – UKG</th>
                                <td>08:30 AM – 12:30 PM</td>
                            </tr>
                            <tr>
                                <th>Class 1 – 5</th>
                                <td>08:00 AM – 02:00 PM</td>
                            </tr>
                            <tr>
                                <th>Class 6 – 8</th>
                                <td>08:00 AM – 02:30 PM</td>
                            </tr>
                            <tr>
                                <th>Class 9 – 12</th>
                                <td>08:00 AM – 03:00 PM</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 class="fw-bold mt-4">Office Timings</h4>

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="40%">Monday – Saturday</th>
                                <td>09:00 AM – 04:00 PM</td>
                            </tr>
                            <tr>
                                <th>Sunday</th>
                                <td>Closed</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 class="fw-bold mt-4">Library & Activity Timings</h4>

                    <ul class="mb-0">



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