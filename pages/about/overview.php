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
                            Excillent public school is a co-educational school run and managed by "Shri Chaitanya Mahaprabhu Shikshan Sansthan, Raipur," affiliated with CBSE New Delhi. It boasts state-of-the-art infrastructure. The school is situated away from the hustle and bustle of the city, amidst nature on a lush green campus spanning 10.92 acres, providing a conducive atmosphere for learning. It offers a balanced mix of academics, co-curricular and extracurricular activities, sports, and games to prepare the global citizens of tomorrow. We provide the best facilities to ensure students' overall development, equipping them with the skills and values necessary to meet the challenges of the 21st century. Each day will be filled with excitement and learning for children through a routine of activities designed to build specific skills, ensuring that every day is unique.
                        </p>    
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