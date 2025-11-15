<?php 
require_once('../../config/database.php');
include('../include/header.php');

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM contact_details WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $contact = $stmt->fetch();
} else {
    $contact = null;
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php echo $contact ? 'Edit Contact Details' : 'Add Contact Details'; ?></h4>
                </div>
                <div class="card-body">
                    <form id="contactForm" class="forms-sample">
                        <?php if ($contact): ?>
                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Office Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"
                                        required><?php echo $contact ? htmlspecialchars($contact['address']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="<?php echo $contact ? htmlspecialchars($contact['phone']) : ''; ?>"
                                        placeholder="+91 XXXXXXXXXX" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="Active"
                                            <?php echo ($contact && $contact['status'] == "Inactive") ? 'selected' : ''; ?>>
                                            Active</option>
                                        <option value="Inactive"
                                            <?php echo ($contact && $contact['status'] == "Inactive") ? 'selected' : ''; ?>>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo $contact ? htmlspecialchars($contact['email']) : ''; ?>"
                                        placeholder="example@domain.com" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="working_hours">Working Hours</label>
                                    <input type="text" class="form-control" id="working_hours" name="working_hours"
                                        value="<?php echo $contact ? htmlspecialchars($contact['working_hours']) : ''; ?>"
                                        placeholder="Monday - Saturday: 9:00 AM - 7:00 PM" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="map_embed">Google Map Embed URL</label>
                                    <input type="url" class="form-control" id="map_embed" name="map_embed"
                                        value="<?php echo $contact ? htmlspecialchars($contact['map_embed']) : ''; ?>"
                                        placeholder="https://www.google.com/maps/embed?..." required>
                                    <small class="form-text text-muted">Get embed URL from Google Maps share
                                        option</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');
        var actionUrl = '../../api/contactdetails/' + (isEdit ? 'update.php' : 'update.php');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#contactForm').find('button[type="submit"]').prop('disabled', false);
                console.log(response)
                if (response.status=="success") {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' category!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#contactForm').find('button[type="submit"]').prop('disabled', false);
                
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