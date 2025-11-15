<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Registered Candidates List</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Candidate</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Status</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <img src="../../uploads/profiles/<?php echo $user['profile_image'] ? htmlspecialchars($user['profile_image']) : 'default.png'; ?>" 
                                             alt="profile" style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer;"
                                             class="profile-image" onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($user['city']); ?></td>
                                    <td><?php echo htmlspecialchars($user['state']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $user['id']; ?>"
                                                   <?php echo $user['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-details" 
                                                data-id="<?php echo $user['id']; ?>"
                                                data-dob="<?php echo htmlspecialchars($user['dob']); ?>"
                                                data-address="<?php echo htmlspecialchars($user['address']); ?>"
                                                data-pincode="<?php echo htmlspecialchars($user['pincode']); ?>">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <a href="manage.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="../orders/manage.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-success btn-sm">
                                           Assign course
                                        </a>
                                        <a href="payfeeinstallment.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                           Pay Fee
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-user" 
                                                data-id="<?php echo $user['id']; ?>">
                                            <i class="mdi mdi-delete"></i>
                                        </button>

                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog" >
        <div class="modal-content" style="width:100%;">
            <div class="modal-header">
                <h5 class="modal-title">Candidate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p><strong>Date of Birth:</strong> <span id="userDob"></span></p>
            <p><strong>Pincode:</strong> <span id="userPincode"></span></p>
            <p><strong>Address: </strong> <span id="userAddress"></span></p>
        </div>
        <div class="col-md-12 mt-3">
            <h6>Purchased Courses</h6>
            <div id="userCourses" class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
  <!-- Update the modal to include courses section -->
  
                                        
                                       
<?php include('../include/footer.php'); ?>

<script>
$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var userId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/candidate/update-status.php',
            type: 'POST',
            data: { 
                id: userId,
                status: status
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.status === 'success') {
                    toastr.success('Status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle view details
   // Update the view details handler
   $('.view-details').on('click', function() {
                                            var userId = $(this).data('id');
                                            var dob = $(this).data('dob');
                                            var address = $(this).data('address');
                                            var pincode = $(this).data('pincode');
                                        
                                            $('#userDob').text(dob);
                                            $('#userPincode').text(pincode);
                                            $('#userAddress').text(address);
                                        
                                            // Fetch user's courses
                                            $.ajax({
                                                url: '../../api/candidate/get-courses.php',
                                                type: 'GET',
                                                data: { user_id: userId },
                                                success: function(response) {
                                                    if (typeof response === 'string') {
                                                        response = JSON.parse(response);
                                                    }
                                                    
                                                    var coursesHtml = '';
                                                    if (response.courses && response.courses.length > 0) {
                                                        response.courses.forEach(function(course) {
                                                            coursesHtml += `
                                                                <tr>
                                                                    <td>${course.title}</td>
                                                                    <td>${course.type}</td>
                                                                    <td>₹${course.price}</td>
                                                                    <td>${course.purchase_date}</td>
                                                                </tr>
                                                            `;
                                                        });
                                                    } else {
                                                        coursesHtml = '<tr><td colspan="4" class="text-center">No courses purchased yet</td></tr>';
                                                    }
                                                    $('#userCourses tbody').html(coursesHtml);
                                                },
                                                error: function() {
                                                    $('#userCourses tbody').html('<tr><td colspan="4" class="text-center">Failed to load courses</td></tr>');
                                                }
                                            });
                                        
                                            $('#userDetailsModal').modal('show');
                                        });

    // Handle delete
    $('.delete-user').on('click', function() {
        var userId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this candidate?')) {
            $.ajax({
                url: '../../api/candidate/delete.php',
                type: 'POST',
                data: { id: userId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Candidate deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete candidate!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});

// Existing image popup functions remain unchanged
</script>

