<?php
require_once 'config/database.php';
$alt="“computer course in sarangarh”, “DCA course in sarangarh”, “PGDCA course sarangarh”, “pre nursing test coaching in sarangarh”, “pre agriculture test coaching in sarangarh”";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
// Add this where you want to output the SEO meta tags
$current_url = $_SERVER['REQUEST_URI'];
$seo = $pdo->prepare("SELECT * FROM seo_settings WHERE page_url = ?");
$seo->execute([$current_url]);
$seo_data = $seo->fetch();

if ($seo_data) {
    echo "<title>" . htmlspecialchars($seo_data['meta_title']) . "</title>\n";
    echo "<meta name='description' content='" . htmlspecialchars($seo_data['meta_description']) . "'>\n";
    echo "<meta name='keywords' content='" . htmlspecialchars($seo_data['meta_keywords']) . "'>\n";
    echo "<meta property='og:url' content='https://margdarshaninstitute.com/'>\n";
    if ($seo_data['og_title']) {
        echo "<meta property='og:title' content='" . htmlspecialchars($seo_data['og_title']) . "'>\n";
    }
    if ($seo_data['og_description']) {
        echo "<meta property='og:description' content='" . htmlspecialchars($seo_data['og_description']) . "'>\n";
    }
    if ($seo_data['og_image']) {
        echo "<meta property='og:image' content='https://margdarshaninstitute.com/uploads/seo/" . htmlspecialchars($seo_data['og_image']) . "'>\n";
    }
    if ($seo_data['canonical_url']) {
        echo "<link rel='canonical' href='" . htmlspecialchars($seo_data['canonical_url']) . "'>\n";
    }
    if ($seo_data['robots_tag']) {
        echo "<meta name='robots' content='" . htmlspecialchars($seo_data['robots_tag']) . "'>\n";
    }
    if ($seo_data['schema_markup']) {
        echo "<script type='application/ld+json'>" . $seo_data['schema_markup'] . "</script>\n";
    }
}
?>
    <!-- Google Fonts - Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php getSeoData(); ?>style.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" rel="stylesheet">
        <!-- AOS CSS -->
   <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
   <style>
    .btn-check:checked+.btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check)+.btn:active{
        background-color: #18c1e4 !important;
    }
    </style>
</head>
<body>
    <!-- Top Notification Bar -->
    <div class="notification-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-start">
                    <span><i class="fas fa-phone-alt me-2"></i>+91 XXXXXXXXX</span>
                    <span class="ms-3"><i class="fas fa-envelope me-2"></i>eps@gmail.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <!-- <a href="" class="text-white me-3"><i class="fab fa-twitter"></i></a> -->
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php getSeoData(); ?>index.php">
                <img src="<?php getSeoData(); ?>uploads/logo/eps-logo.jpeg" alt="<?php echo htmlspecialchars($alt); ?>" class="logo" >
                    <!-- <h2 class="m-0 text-primary">Margdarshan<span class="text-secondary">Coaching</span></h2> -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                    <a class="nav-link active" href="#">Home</a>
                        <!-- <a class="nav-link active" href="/index.php">Home</a> -->
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#faculty">Faculty</a>
                        <!-- <a class="nav-link active" href="#">Faculty</a> -->
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="freeresources.php">Free Resources</a>
                    </li> -->
                    <li class="nav-item">
                    <!-- <a class="nav-link" href="#">Gallery</a> -->
                        <a class="nav-link" href="photo-galery.php">Gallery</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#blog">Blog</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="contact-us.php">Contact US</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-us.php">About Us</a>
                    </li>
                </ul>
                <!-- <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown ms-3">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>My Account
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="my-courses.php"><i class="fas fa-graduation-cap me-2"></i>My Courses</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary ms-3">Login & Register</a>
                <?php endif; ?> -->
            </div>
        </div>
    </nav>