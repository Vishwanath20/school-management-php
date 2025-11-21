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
<section id="contact" class="contact-section py-5 stats-section">
    <div class="container">
        <div class="row">

        <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-offset="300"
                data-aos-easing="ease-in-sine">
</div>
           
            <div class="col-lg-6" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
                data-aos-duration="500">
                <div class="contact-form-container">
                    <h2 class="section-title mb-4">Search TC</h2>
                    <form id="contactForm" class="contact-form">
                        <input name="source" type="text" value="<?php echo $leads ; ?>" style="display:none;" />
                       
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Search by keyword*" required>
                        </div>
                        <div class="mb-3">
                            <select name="course_interest" class="form-select" required>
                                <option value="">Select Branch</option>
                                <option value="DCA">Pre-school</option>
                                <option value="PGDCA">primary school</option>
                                <option value="PRE NURSING TEST">Middle school</option>
                                <option value="PRE AGRICULTURE TEST">High school</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                      
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                            <label class="form-check-label" for="agreeCheck">
                                I agree to receive information regarding my submitted enquiry
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </form>
                    <!-- Alert Messages -->
                    <div id="successAlert" class="alert alert-success mt-3" style="display: none;">
                        Thank you for contacting us! We will get back to you soon.
                    </div>
                    <div id="errorAlert" class="alert alert-danger mt-3" style="display: none;">
                        Oops! Something went wrong. Please try again.
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