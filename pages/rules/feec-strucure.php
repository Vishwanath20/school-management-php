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
    <h2 class="text-center mb-4 fw-bold text-dark">Fee Structure</h2>

    <!-- Intro Box -->
    <div class="alert alert-info text-center shadow-sm">
        <strong>Note:</strong> The fee structure below applies to all classes for the academic session.
    </div>

    <!-- Fee Structure Table -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped align-middle shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Class</th>
                    <th>Admission Fee</th>
                    <th>Tuition Fee (Monthly)</th>
                    <th>Annual Charges</th>
                    <th>Total (Yearly)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nursery – UKG</td>
                    <td>₹5,000</td>
                    <td>₹1,500</td>
                    <td>₹3,000</td>
                    <td>₹26,000</td>
                </tr>
                <tr>
                    <td>Class 1 – 5</td>
                    <td>₹6,000</td>
                    <td>₹1,800</td>
                    <td>₹3,500</td>
                    <td>₹30,100</td>
                </tr>
                <tr>
                    <td>Class 6 – 8</td>
                    <td>₹7,000</td>
                    <td>₹2,000</td>
                    <td>₹4,000</td>
                    <td>₹35,000</td>
                </tr>
                <tr>
                    <td>Class 9 – 10</td>
                    <td>₹8,000</td>
                    <td>₹2,400</td>
                    <td>₹4,500</td>
                    <td>₹41,300</td>
                </tr>
                <tr>
                    <td>Class 11 – 12</td>
                    <td>₹10,000</td>
                    <td>₹3,000</td>
                    <td>₹5,000</td>
                    <td>₹51,000</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Instructions -->
    <div class="mt-5">
        <h4 class="fw-bold">Payment Instructions</h4>
        <ul>
            <li>Fees can be paid quarterly or annually.</li>
            <li>Late fee of ₹50/day will apply after the due date.</li>
            <li>All payments should be made at the school office or via online banking.</li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-4">
        <a href="#" class="btn btn-primary btn-lg shadow">Download Fee Structure PDF</a>
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