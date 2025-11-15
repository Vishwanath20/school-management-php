<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all users
$stmt = $pdo->query("SELECT * FROM admin_users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Users List</h4>
                    <div>
                        <a href="manage.php" class="btn btn-info btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Users</a>
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
                                    <th>Gender</th>
                                    <th>City</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php if ($user['profile_pic']): ?>
                                            <img src="../../uploads/profile_pics/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                                 alt="profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"/>
                                        <?php else: ?>
                                            <img src="../../assets/images/faces/face1.jpg" 
                                                 alt="profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"/>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($user['city']); ?></td>
                                    <td><?php echo htmlspecialchars($user['type']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-user" 
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



<?php 
include('../include/footer.php');
?>
<script>
$(document).ready(function() {
    // Handle delete button click
    $('.delete-user').on('click', function() {
        var userId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: '../../api/users/delete.php',
                type: 'POST',
                data: { id: userId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('User deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete user!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
</script>