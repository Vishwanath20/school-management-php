<?php 
require_once('../../config/database.php');
include('../include/header.php'); // Added missing semicolon here

$stmt = $pdo->query(
    "SELECT o.*, c.title as course_title, c.thumbnail, u.name as user_name, u.profile_image
    FROM orders o
    JOIN courses c ON o.course_id = c.id
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5"
);
$orders = $stmt->fetchAll();

$loginStmt = $pdo->query("
    SELECT h.*, a.name, a.profile_pic, a.type
    FROM admin_login_history h
    JOIN admin_users a ON h.admin_id = a.id
    ORDER BY h.login_time DESC
    LIMIT 5
");
$loginHistory = $loginStmt->fetchAll();




// Course Type Distribution
$courseTypeStats = $pdo->query("
    SELECT type, COUNT(*) as count 
    FROM courses 
    GROUP BY type
")->fetchAll(PDO::FETCH_KEY_PAIR);

$categoryCount = $pdo->query("SELECT COUNT(*) FROM course_categories WHERE status = 1")->fetchColumn();
// Enquiry Statistics
$enquiryStats = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM contact_enquiries 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Popular Courses (by enrollment)
$popularCourses = $pdo->query("
    SELECT c.title, COUNT(o.id) as enrollment_count, SUM(o.amount) as revenue
    FROM courses c
    LEFT JOIN orders o ON c.id = o.course_id AND o.status = 'completed'
    GROUP BY c.id
    ORDER BY enrollment_count DESC
    LIMIT 5
")->fetchAll();

// Attendance Overview (for today)
// $todayAttendance = $pdo->query("
//     SELECT status, COUNT(*) as count
//     FROM attendance
//     WHERE DATE(date) = CURDATE()
//     GROUP BY status
// ")->fetchAll(PDO::FETCH_KEY_PAIR);

// Resource Usage
$resourceStats = $pdo->query("
    SELECT m.title, COUNT(fr.id) as download_count
    FROM free_resource_master m
    LEFT JOIN free_resources fr ON m.id = fr.master_id
    GROUP BY m.id
    ORDER BY download_count DESC
    LIMIT 5
")->fetchAll();
?>


<style>
  .align-self-start h3,h2{
    color:#232396;
  }
  .card-body h5{
    color:#232396;
  }
  </style>
  <div class="content-wrapper">
      
  <?php
// Add this after your existing queries
// Total Students
$studentCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Total Courses
$courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

// Total Revenue
$totalRevenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE status = 'completed'")->fetchColumn();

// Today's Revenue
$todayRevenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE()")->fetchColumn();

// Monthly Revenue
$monthlyRevenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURDATE())")->fetchColumn();

// Active Students (enrolled in at least one course)
$activeStudents = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders WHERE status = 'completed'")->fetchColumn();

// Calculate growth percentages
$lastMonthRevenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)")->fetchColumn();
$revenueGrowth = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100) : 0;
?>

<!-- Replace the existing cards section with this -->
<div class="row">

    <div class="col-xl-2 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0"><?php echo number_format($categoryCount); ?></h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-warning">
                            <span class="mdi mdi-folder-multiple icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Course Categories</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0"><?php echo number_format($courseCount); ?></h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-info">
                            <span class="mdi mdi-book-open-variant icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Total Courses</h6>
            </div>
        </div>
    </div>
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="text-dark font-weight-normal">Enquiry Overview</h6>
                <?php foreach($enquiryStats as $status => $count): ?>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-muted"><?php echo ucfirst($status); ?></span>
                        <span class="badge badge-<?php echo $status == 'new' ? 'warning' : 
                            ($status == 'contacted' ? 'info' : 
                            ($status == 'converted' ? 'success' : 'danger')); ?>">
                            <?php echo $count; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<!-- Add this before your existing tables -->
            <div class="row">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Recent Login Activity</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th> Profile</th>
                            <th>Captured Pic</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Login Time</th>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if(!empty($loginHistory)): ?>
                            <?php foreach($loginHistory as $login): ?>
                              <tr>
                              <td>
                                  <img src="../../uploads/profile_pics/<?php echo $login['profile_pic'] ?: 'default.png'; ?>" 
                                       class="rounded-circle" width="40" height="40" alt="profile" />
                                </td>
                                <td>
    <img src="../../uploads/login_images/<?php echo $login['login_image'] ?: 'default.png'; ?>" 
         class="rounded-circle login-image-preview" width="40" height="40" alt="profile" 
         style="cursor: pointer;" 
         onclick="showImage('../../uploads/login_images/<?php echo $login['login_image'] ?: 'default.png'; ?>')" />
</td>

                                <td>
                                  <span class="pl-2"><?php echo htmlspecialchars($login['name']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($login['type']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($login['login_time'])); ?></td>
                                <td><?php echo htmlspecialchars($login['ip_address']); ?></td>
                                <td>
                                  <?php 
                                    $ua = $login['user_agent'];
                                    if(strpos($ua, 'Chrome') !== false) echo 'Chrome';
                                    elseif(strpos($ua, 'Firefox') !== false) echo 'Firefox';
                                    elseif(strpos($ua, 'Safari') !== false) echo 'Safari';
                                    elseif(strpos($ua, 'Edge') !== false) echo 'Edge';
                                    else echo 'Other';
                                  ?>
                                </td>
                                <td>
                                  <div class="badge badge-outline-<?php echo $login['status'] == 'success' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($login['status']); ?>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="7" class="text-center">No recent login activity found</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Login Capture</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Login Capture">
            </div>
        </div>
    </div>
</div>

              <!-- Custom js for this page -->
 
    <!-- End custom js for this page -->
<?php 
include('../include/footer.php')
?>
<script>
function showImage(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    $('#imageModal').modal('show');
}
</script>

<style>
.login-image-preview:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
}
</style>