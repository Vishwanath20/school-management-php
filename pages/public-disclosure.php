<?php 
//require_once 'config/database.php';

require_once '../config/database.php';

// Fetch institute details
$stmt = $pdo->prepare("SELECT * FROM contact_details WHERE id = 9");
$stmt->execute();
$institute = $stmt->fetch();

//include 'include/header.php';
include  '../include/header.php';
?>

<!-- Hero Section -->
<section class="py-5">
    <div class="container">

        <!-- Page Title -->
        <h2 class="text-center mb-5 fw-bold text-dark">Mandatory Public Disclosure</h2>

        <!-- General Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">General Information</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>NAME OF THE SCHOOL</th><td>Demo Public School </td></tr>
                        <tr><th>AFFILIATION NO</th><td>333015XXX4</td></tr>
                        <tr><th>SCHOOL CODE</th><td>15XX077</td></tr>
                        <tr><th>COMPLETE ADDRESS</th><td>Demo Public School, Raipur Road, Village Sankara,  (C.G.), Pin 49XXX3773</td></tr>
                        <tr><th>PRINCIPAL NAME</th><td>Dr demo Kumar Sharma</td></tr>
                        <tr><th>PRINCIPAL QUALIFICATION</th><td>''</td></tr>
                        <tr><th>SCHOOL EMAIL</th><td>principal.Demo@gmail.com</td></tr>
                        <tr><th>CONTACT DETAILS</th><td>XXXXXXXX, XXXXXXX</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Documents & Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Documents & Information</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>S. No.</th>
                            <th>Information</th>
                            <th>Uploaded Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>1</td><td>Land Certificate</td><td><a href="#">LINK</a></td></tr>
                        <tr><td>2</td><td>Fire Safety</td><td><a href="#">LINK</a></td></tr>
                        <tr><td>3</td><td>Water Health</td><td><a href="#">LINK</a></td></tr>
                        <tr><td>4</td><td>RTE</td><td><a href="#">LINK</a></td></tr>
                        <tr><td>5</td><td>Affiliation</td><td><a href="#">LINK</a></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Results and Academics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Results and Academics</h4>
            </div>
            <div class="card-body">
                <p>No Data Available</p>
            </div>
        </div>

        <!-- Staff & Teaching -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Staff and Teaching</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Principal</th><td>Dr Demo Kumar Sharma</td></tr>
                        <tr><th>Vice Principal</th><td>-----</td></tr>
                        <tr><th>NUMBER OF TEACHERS</th><td>48</td></tr>
                        <tr><th>PGT</th><td>08</td></tr>
                        <tr><th>TGT</th><td>10</td></tr>
                        <tr><th>PRT</th><td>20</td></tr>
                        <tr><th>NTT</th><td>10</td></tr>
                        <tr><th>Teacher / Section Ratio</th><td>3/1</td></tr>
                        <tr><th>Special Educator</th><td>08</td></tr>
                        <tr><th>Counsellor & Wellness Teacher</th><td>01</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Result Class X -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Result – Class X</h4>
            </div>
            <div class="card-body">
                <p>No Data Available</p>
            </div>
        </div>

        <!-- Result Class XII -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Result – Class XII</h4>
            </div>
            <div class="card-body">
                <p>No Data Available</p>
            </div>
        </div>

        <!-- School Infrastructure -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">School Infrastructure</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>TOTAL CAMPUS AREA</th><td>14 Acres</td></tr>
                        <tr><th>NUMBER & SIZE OF CLASSROOMS</th><td>500 sq. mtr.</td></tr>
                        <tr><th>LABORATORIES (Including Computer Labs)</th><td>800 sq. mtr.</td></tr>
                        <tr><th>Internet Facility</th><td>Yes</td></tr>
                        <tr><th>Girls Toilets</th><td>40</td></tr>
                        <tr><th>Boys Toilets</th><td>41</td></tr>
                    </tbody>
                </table>
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
   
    include  '../include/footer.php'; 
?>