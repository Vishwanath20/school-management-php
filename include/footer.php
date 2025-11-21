<?php
require_once __DIR__ . '/../config/database.php';
?>
<!-- Footer -->
    <footer  data-aos="fade-up"
    data-aos-anchor-placement="top-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-5">
                    <!-- <h2 class="text-white mb-4">GEO<span class="text-accent">IAS</span></h2> -->
                    <img src="<?php echo $baseUrl; ?>uploads/logo/eps-logo.jpeg" alt="<?php echo $alt; ?>" class="logo" style="height:120px;
    background: #fff;
    border-radius: 10px;
}" >
                    <!-- <p>India's trusted Computer & coaching institute for examination preparation with a legacy of excellence and proven results.</p> -->
                    <div class="social-links mt-4">
                        <a href="#"><i class="fab fa-facebook-f" ></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links list-unstyled">
                        <!-- <li><a href="<?php getSeoData(); ?>index.php"><i class="fas fa-angle-right me-2"></i> Home</a></li>
                        <li><a href="<?php getSeoData(); ?>about-us.php"><i class="fas fa-angle-right me-2"></i> About Us</a></li>
                        <li><a href="<?php getSeoData(); ?>courses.php"><i class="fas fa-angle-right me-2"></i> Courses</a></li> -->
                        <li><a href="index.php"><i class="fas fa-angle-right me-2"></i> Home</a></li>
                        <li><a href="contact-us.php"><i class="fas fa-angle-right me-2"></i> Contact</a></li>
                         <li><a href="about-us.php"><i class="fas fa-angle-right me-2"></i> About</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="footer-title">Our Courses</h5>
                    <ul class="footer-links list-unstyled">
                        <!-- <?php
                        // Fetch active categories
                        $stmt = $pdo->prepare("SELECT * FROM course_categories WHERE status = 1 ORDER BY title");
                        $stmt->execute();
                        $categories = $stmt->fetchAll();
                        
                        foreach ($categories as $category):
                        ?>
                            <li>
                                <a href="<?php getSeoData(); ?>courses/<?php echo $category['slug']; ?>">
                                    <i class="fas fa-angle-right me-2"></i>
                                    <?php echo htmlspecialchars($category['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?> -->
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="footer-title">Newsletter</h5>
                    <p>Subscribe to our newsletter to receive updates on new batches, study materials, and exam notifications.</p>
                    <div class="newsletter-form mt-4">
                    <form id="newsletterForm">
                        <input type="email" class="form-control" name="email" placeholder="Your Email Address">
                        <button type="submit" class="btn">Subscribe</button>
</form>
<div id="newsletterSuccess" class="alert alert-success mt-2" style="display: none;">
                            Thank you for subscribing!
                        </div>
                        <div id="newsletterError" class="alert alert-danger mt-2" style="display: none;">
                            Something went wrong!
                        </div>
                    </div>
                    <div class="mt-4">
                        <h5 class="footer-title">Download Our App</h5>
                        <div class="d-flex mt-3">
                            <a href="#" class="me-2"><img src="<?php getSeoData(); ?>images/icons8-google-play-color/icons8-google-play-48.png" alt="Google Play"></a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <div class="container">
                 <p class="mb-0">© 2025 Evergreen public school. All Rights Reserved.
                    <!-- <a href="https://smartgensoftech.in/" target="_blank">Designed & Developed by SmartGen Softech</a>
                </p> -->
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></a>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/918319513300" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="<?php echo $baseUrl; ?>script.js"></script>