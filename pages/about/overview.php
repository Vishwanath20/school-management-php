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
                            <h3 class="mb-0 text-dark">Overview</h3>
                        </div>
                        <p class="card-text">
                            Excillent public school is a co-educational school run and managed by "Shri Chaitanya Mahaprabhu Shikshan Sansthan, Raipur," affiliated with CBSE New Delhi. It boasts state-of-the-art infrastructure. The school is situated away from the hustle and bustle of the city, amidst nature on a lush green campus spanning 10.92 acres, providing a conducive atmosphere for learning. It offers a balanced mix of academics, co-curricular and extracurricular activities, sports, and games to prepare the global citizens of tomorrow. We provide the best facilities to ensure students' overall development, equipping them with the skills and values necessary to meet the challenges of the 21st century. Each day will be filled with excitement and learning for children through a routine of activities designed to build specific skills, ensuring that every day is unique.
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php include  '../../include/footer.php';  ?>
