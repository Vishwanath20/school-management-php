<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch user data if editing
$user = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $user ? 'Edit User' : 'Add User'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="userForm" class="forms-sample">
                        <?php if ($user): ?>
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $user ? htmlspecialchars($user['name']) : ''; ?>" 
                                           placeholder="Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" 
                                           placeholder="Email" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">Password <?php echo $user ? '(Leave blank to keep current)' : ''; ?></label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Password" <?php echo !$user ? 'required' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="Male" <?php echo ($user && $user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($user && $user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Type</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="admin" <?php echo ($user && $user['type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="teacher" <?php echo ($user && $user['type'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                                        <option value="staff" <?php echo ($user && $user['type'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Profile Pic</label>
                             
                                    <input type="file" name="profile_pic" id="profile_pic" class="file-upload-default" accept="image/*">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <?php if ($user && $user['profile_pic']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/profile_pics/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                                 alt="Current profile" style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo $user ? htmlspecialchars($user['city']) : ''; ?>" 
                                           placeholder="Location">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="about">About</label>
                                    <textarea class="form-control" id="about" name="about" rows="4"><?php echo $user ? htmlspecialchars($user['about']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $user ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='details.php'">Cancel</button>
                    </form>
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
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/users/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#userForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'User ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' user!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#userForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    toastr.error(errorResponse.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });
    $(document).ready(function() {
    // File upload handling
    $('.file-upload-browse').on('click', function() {
        var file = $(this).parents().find('.file-upload-default');
        file.trigger('click');
    });

    $('.file-upload-default').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents('.form-group').find('.file-upload-info').val(fileName);
    });
});
    // File upload handling remains the same
});
</script>