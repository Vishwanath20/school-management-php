<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch user data if editing
$user = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $user ? 'Edit Candidate' : 'Add Candidate'; ?></h4>
                    <div>
                        <a href="list.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="candidateForm" class="forms-sample" enctype="multipart/form-data">
                        <?php if ($user): ?>
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $user ? htmlspecialchars($user['name']) : ''; ?>" 
                                           placeholder="Enter name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" 
                                           placeholder="Enter email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?php echo $user ? htmlspecialchars($user['phone']) : ''; ?>" 
                                           placeholder="Enter phone number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?php echo !$user ? 'required' : ''; ?> 
                                           placeholder="<?php echo $user ? 'Leave blank to keep current password' : 'Enter password'; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" 
                                           value="<?php echo $user ? $user['dob'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" 
                                              placeholder="Enter address" required><?php echo $user ? htmlspecialchars($user['address']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?php echo $user ? htmlspecialchars($user['city']) : ''; ?>" 
                                           placeholder="Enter city" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="<?php echo $user ? htmlspecialchars($user['state']) : ''; ?>" 
                                           placeholder="Enter state" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" 
                                           value="<?php echo $user ? htmlspecialchars($user['pincode']) : ''; ?>" 
                                           placeholder="Enter pincode" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_image">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" 
                                           accept="image/*">
                                    <?php if ($user && $user['profile_image']): ?>
                                        <img src="../../uploads/profiles/<?php echo $user['profile_image']; ?>" 
                                             alt="Profile" class="mt-2" style="max-width: 100px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($user && $user['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($user && $user['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
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
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    $('#candidateForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/candidate/' + (isEdit ? 'update.php' : 'add.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#candidateForm').find('button[type="submit"]').prop('disabled', false);
                // response = JSON.parse(response);
                if (response.success) {
                    toastr.success('Candidate ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' candidate!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#candidateForm').find('button[type="submit"]').prop('disabled', false);
                try {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });
});
</script>