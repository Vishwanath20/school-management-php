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
 
<!-- Admission Procedure Section -->
<section class="py-5 bg-light">
    <div class="container">

        <!-- Heading -->
        <h2 class="text-center fw-bold text-dark mb-5">Admission Procedure</h2>

        <!-- Row 1 : Image + Intro -->
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <img src="<?php echo $baseUrl; ?>uploads/aboutus/admission-prodcedure.png" alt="Admission" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h4 class="fw-bold text-primary">Welcome to Our Admission Process</h4>
                <p class="text-muted mt-3">
                    We aim to make the admission process simple, transparent, and smooth for parents and students.
                    Our school welcomes students who are eager to learn, grow, and become responsible individuals.
                </p>
            </div>
        </div>

        <!-- Step-by-Step Process -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">01</div>
                        <h5 class="fw-bold text-dark">Collect Admission Form</h5>
                        <p class="text-muted mt-2">
                            Parents can collect the admission form from the school office or download it from the website.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">02</div>
                        <h5 class="fw-bold text-dark">Submit Required Documents</h5>
                        <p class="text-muted mt-2">
                            Submit necessary documents including birth certificate, address proof, and previous class report card.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">03</div>
                        <h5 class="fw-bold text-dark">Verification & Interaction</h5>
                        <p class="text-muted mt-2">
                            After document verification, students may be called for an interaction or entrance assessment.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">04</div>
                        <h5 class="fw-bold text-dark">Admission Confirmation</h5>
                        <p class="text-muted mt-2">
                            Selected students will receive a confirmation message or call from the school administration.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">05</div>
                        <h5 class="fw-bold text-dark">Fee Submission</h5>
                        <p class="text-muted mt-2">
                            Complete the fee payment at the school office to secure the admission seat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="display-5 text-primary fw-bold mb-3">06</div>
                        <h5 class="fw-bold text-dark">Enrollment & Orientation</h5>
                        <p class="text-muted mt-2">
                            Students receive their class schedule, books list, and orientation details after enrollment.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-5 p-4 bg-white shadow-sm rounded">
            <h4 class="fw-bold text-dark">Required Documents</h4>
            <ul class="text-muted mt-3">
                <li>Birth Certificate</li>
                <li>Aadhar Card (Student & Parents)</li>
                <li>Passport-size Photographs</li>
                <li>Previous School Report Card</li>
                <li>Transfer Certificate (if applicable)</li>
            </ul>
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