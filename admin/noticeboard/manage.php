<?php
require_once('../../config/database.php'); 
include('../include/header.php');


// Fetch notice data if editing
$notice = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $notice = $stmt->fetch();
}
?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    p{
        color:black;
    }
    li{
        color:black;
    }
    h1,h2,h3,h4,h5,h6{
        color:black;
    }
</style>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $notice ? 'Edit Notice' : 'Add Notice'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="noticeForm" class="forms-sample">
                        <?php if ($notice): ?>
                            <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $notice ? htmlspecialchars($notice['title']) : ''; ?>" 
                                           placeholder="Enter notice title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Date<span style="color:red;">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" 
                                           value="<?php echo $notice ? date('Y-m-d', strtotime($notice['date'])) : date('Y-m-d'); ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description<span style="color:red;">*</span></label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" required><?php echo $notice ? htmlspecialchars($notice['description']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link">Link (Optional)</label>
                                    <input type="url" class="form-control" id="link" name="link" 
                                           value="<?php echo $notice ? htmlspecialchars($notice['link']) : ''; ?>" 
                                           placeholder="Enter related link">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_text">Button Text<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="link_text" name="link_text" 
                                           value="<?php echo $notice ? htmlspecialchars($notice['link_text']) : 'Read More'; ?>" 
                                           placeholder="Enter link text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Badge</label>
                                    <select class="form-control" name="badge" id="badge">
                                        <option value="">None</option>
                                        <option value="New" <?php echo ($notice && $notice['badge'] == 'New') ? 'selected' : ''; ?>>New</option>
                                        <option value="Important" <?php echo ($notice && $notice['badge'] == 'Important') ? 'selected' : ''; ?>>Important</option>
                                        <option value="Urgent" <?php echo ($notice && $notice['badge'] == 'Urgent') ? 'selected' : ''; ?>>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Section</label>
                                    <select class="form-control" name="sections" id="sections">
                                        <option value="">select</option>
                                        <option value="0" <?php echo ($notice && $notice['sections'] == '0') ? 'selected' : ''; ?>>Notice Board</option>
                                        <option value="1" <?php echo ($notice && $notice['sections'] == '1') ? 'selected' : ''; ?>>Announcement</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $notice ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<!-- Include Summernote JS -->

<script>
    
$(document).ready(function() {

    $('#noticeForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/noticeboard/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#noticeForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Notice ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' notice!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#noticeForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    toastr.error(errorResponse.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });
});
</script>

