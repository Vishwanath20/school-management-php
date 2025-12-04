<?php
require_once('../../config/database.php');

if(!isset($_SESSION['admin_logged_in'])){
    header("location:../index.php");
}

$baseUrl = "http://localhost/school-website-evergreen/";

?>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Margdarshan Coaching</title>

    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="../assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
    .btn-sm{
        padding: 10px !important;
        font-size: 20px !important;
    }
    </style>
  </head>
  <body>
    
  <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="sidebar-brand brand-logo" href="../dashboard/dashboard.php" style="color:#fff;">Evergreen Public School</a>
          <a class="sidebar-brand brand-logo-mini" href="../dashboard/dashboard.php" style="color:#fff;">EPS.</a>
        </div>
        <ul class="nav">
  <li class="nav-item profile">
    <div class="profile-desc">
      <div class="profile-pic">
        <div class="count-indicator">
          <img class="img-xs rounded-circle" src="<?php echo $baseUrl; ?>uploads/logo/eps-logo.jpeg" alt="">
          <span class="count bg-success"></span>
        </div>
        <div class="profile-name">
          <h5 class="mb-0 font-weight-normal">Admin</h5>
        </div>
      </div>
    </div>
  </li>
  <li class="nav-item nav-category">
    <span class="nav-link">Navigation</span>
  </li>
  <li class="nav-item menu-items active">
    <a class="nav-link" href="../dashboard/dashboard.php">
      <span class="menu-icon">
        <i class="mdi mdi-speedometer"></i>
      </span>
      <span class="menu-title">Dashboard</span>
    </a>
  </li>

  <!-- Masters Management - start -->
  <!-- <li class="nav-item menu-items">
    <a class="nav-link" data-toggle="collapse" href="#masters" aria-expanded="false" aria-controls="masters">
      <span class="menu-icon">
        <i class="mdi mdi-database"></i>
      </span>
      <span class="menu-title">Masters</span>
      <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="masters">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item"> <a class="nav-link" href="../course-category/details.php"> Manage Course Category</a></li>
        <li class="nav-item"> <a class="nav-link" href="../subject-master/details.php"> Subject Master</a></li>
        <li class="nav-item"> <a class="nav-link" href="../batches/details.php"> Manage Batches</a></li>
         <li class="nav-item"> <a class="nav-link" href="../photogalerycategory/details.php" title="Photo Galery Category">P.G.C.</a></li>
      </ul>
    </div>
  </li> -->
    <!-- Masters Management - end -->
  
  <li class="nav-item menu-items">
    <a class="nav-link" data-toggle="collapse" href="#websitemanage" aria-expanded="false" aria-controls="websitemanage">
      <span class="menu-icon">
        <i class="mdi mdi-web"></i>
      </span>
      <span class="menu-title">CMS</span>
      <i class="menu-arrow"></i>
    </a>
    <div class="collapse hide" id="websitemanage">
      <ul class="nav flex-column sub-menu">
        <!-- <li class="nav-item"> <a class="nav-link" href="../heroslider/details.php"> Manage Banners</a></li>
        <li class="nav-item"> <a class="nav-link" href="../noticeboard/details.php"> Manage Notice Board</a></li>
        <li class="nav-item"> <a class="nav-link" href="../Courses/details.php"> Manage Courses</a></li>
        <li class="nav-item"> <a class="nav-link" href="../whychooseus/details.php"> Why Choose</a></li>
        <li class="nav-item"> <a class="nav-link" href="../toppersreviews/details.php"> Toppers Reviews</a></li>
        <li class="nav-item"> <a class="nav-link" href="../faculty/details.php"> Manage Faculty</a></li>
        <li class="nav-item"> <a class="nav-link" href="../ytvideos/details.php"> Manage YT Videos</a></li>
        <li class="nav-item"> <a class="nav-link" href="../upcomingexams/details.php"> Manage Upcoming Exams</a></li> -->
        <li class="nav-item"> <a class="nav-link" href="../contactdetails/details.php"> Manage Contact Details</a></li>
        <li class="nav-item"> <a class="nav-link" href="../photogalery/details.php"> Manage Photo Gallery</a></li>
      </ul>
    </div>
  </li>

  <!-- Leads Management - start -->
  <!-- <li class="nav-item menu-items">
    <a class="nav-link" data-toggle="collapse" href="#manageleads" aria-expanded="false" aria-controls="manageleads">
      <span class="menu-icon">
        <i class="mdi mdi-lead-pencil"></i>
      </span>
      <span class="menu-title">Leads Management</span>
      <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="manageleads">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item"> <a class="nav-link" href="../leadsmanagement/details.php">Manage Leads</a></li>
      </ul>
    </div>
  </li> -->
   <!-- Leads Management - end -->

  <!-- Staff-mamangemtn start-->
    <!-- <li class="nav-item menu-items">
    <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
      <span class="menu-icon">
        <i class="mdi mdi-account-group"></i>
      </span>
      <span class="menu-title">Staff Management</span>
      <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="ui-basic">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item"> <a class="nav-link" href="../users/details.php">Staff Details</a></li>
        <li class="nav-item"> <a class="nav-link" href="../users/attendance.php"> Attendance</a></li>
        <li class="nav-item"> <a class="nav-link" href="../users/salary.php"> Salary</a></li>
      </ul>
    </div>
  </li> -->
    <!-- Staff-mamangemtn end-->

      <!-- SEO Tool - start -->
<!-- <li class="nav-item menu-items">
    <a class="nav-link" data-toggle="collapse" href="#webseo" aria-expanded="false" aria-controls="webseo">
      <span class="menu-icon">
        <i class="mdi mdi-magnify"></i>
      </span>
      <span class="menu-title">SEO Tool</span>
      <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="webseo">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item"> <a class="nav-link" href="../seo/webseo.php"><i class="mdi mdi-search-web"></i> Web SEO</a></li>
      </ul>
    </div>
  </li> -->
    <!-- SEO Tool - end -->

</ul>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar p-0 fixed-top d-flex flex-row">
          <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="#" style="padding-left:5px;color:#fff;">M.D</a>
          </div>
          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="mdi mdi-menu"></span>
            </button>
           
            <ul class="navbar-nav navbar-nav-right">
      
              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="<?php echo $baseUrl; ?>uploads/logo/eps-logo.jpeg" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name text-white"><?php echo $_SESSION['name']; ?></p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0 text-white">Profile</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1 text-white">Settings</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item" href="../logout.php">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1 text-white">Log out</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                 
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-format-line-spacing"></span>
            </button>
          </div>
        </nav>
        <!-- partial -->
        <div class="main-panel">