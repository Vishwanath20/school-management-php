<?php
include 'include/header.php';
?>

<section id="contact" class="contact-section py-5 stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-offset="300"
                data-aos-easing="ease-in-sine">
                <h2 class="section-title mb-4">Contact Details</h2>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <div>
                            <h4>Office</h4>
                            <p>Andola, Jashpur, Sarangarh</p>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['address']); ?></p> -->
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>XXXXXX</p>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['phone']); ?></p> -->
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <div>
                            <h4>Email</h4>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['email']); ?></p> -->
                            <p>info@eps.in</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <div>
                            <h4>Working Hours</h4>
                             <p>9:00 am to 3:00 pm</p>
                            <!-- <p><?php echo htmlspecialchars($contactDetails['working_hours']); ?></p> -->
                        </div>
                    </div>
                </div>
                <div>
                    <iframe src="<?php echo htmlspecialchars($contactDetails['map_embed']); ?>" width="100%"
                        height="150" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
                data-aos-duration="500">
                <div class="contact-form-container">
                    <h2 class="section-title mb-4">Get in Touch</h2>
                    <form id="contactForm" class="contact-form">
                        <input name="source" type="text" value="<?php echo $leads ; ?>" style="display:none;" />
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Your Name*" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Your Email*" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Your Phone*" required>
                        </div>
                        <div class="mb-3">
                            <select name="course_interest" class="form-select" required>
                                <option value="">Select Course Interest</option>
                                <option value="DCA">DCA</option>
                                <option value="PGDCA">PGDCA</option>
                                <option value="PRE NURSING TEST">PRE NURSING TEST</option>
                                <option value="PRE AGRICULTURE TEST">PRE AGRICULTURE TEST</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4"
                                placeholder="Your Message"></textarea>
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

<?php
include('include/footer.php');
?>