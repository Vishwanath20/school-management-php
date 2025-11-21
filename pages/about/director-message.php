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
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <!-- <img src="uploads/aboutus/about-margdarshan.jpg" alt="<?php echo htmlspecialchars($alt); ?>" class="img-fluid rounded-3 shadow"> -->
                 <img src="<?php echo $baseUrl; ?>uploads/aboutus/director.png" alt="school director " class="img-fluid rounded-3 shadow">
            </div>

            <div class="col-lg-6">
               <h1>Director's Message</h1>

<p>
Dear Parents,</br>
Discover a nurturing haven for your little ones at our kindergarten school. With a play-based curriculum, experienced teachers, and safe surroundings. we foster a love for learning and social development. Join our vibrant community where every child's potential is nurtured with care and creativity. Enroll now for an enriching journey!
</p>

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