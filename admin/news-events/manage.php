<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch news/event data if editing
$newsEvent = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM news_events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $newsEvent = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
<h4 class="card-title"><?php echo $newsEvent ? 'Edit News/Event' : 'Add News/Event'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="newsEventForm" class="forms-sample" enctype="multipart/form-data">
                        <?php if ($newsEvent): ?>
                            <input type="hidden" name="id" value="<?php echo $newsEvent['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_name">Event Name</label>
                                    <input type="text" class="form-control" id="event_name" name="event_name" 
                                           value="<?php echo $newsEvent ? htmlspecialchars($newsEvent['event_name']) : ''; ?>" 
                                           placeholder="Enter event name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_date">Event Date</label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" 
                                           value="<?php echo $newsEvent ? htmlspecialchars($newsEvent['event_date']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="media_type">Media Type</label>
                                    <select class="form-control" id="media_type" name="media_type">
                                        <option value="none" <?php echo (!$newsEvent || (empty($newsEvent['photo']) && empty($newsEvent['youtube_url']))) ? 'selected' : ''; ?>>None</option>
                                        <option value="photo" <?php echo ($newsEvent && !empty($newsEvent['photo'])) ? 'selected' : ''; ?>>Photo</option>
                                        <option value="youtube" <?php echo ($newsEvent && !empty($newsEvent['youtube_url'])) ? 'selected' : ''; ?>>YouTube Video</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="photo_upload_field" style="display: <?php echo ($newsEvent && !empty($newsEvent['photo'])) ? 'block' : 'none'; ?>;">
                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                    <?php if ($newsEvent && $newsEvent['photo']): ?>
                                        <img src="../../uploads/news-events/<?php echo htmlspecialchars($newsEvent['photo']); ?>" alt="Event Photo" style="width: 100px; height: auto; margin-top: 10px;">
                                        <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($newsEvent['photo']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6" id="youtube_url_field" style="display: <?php echo ($newsEvent && !empty($newsEvent['youtube_url'])) ? 'block' : 'none'; ?>;">
                                <div class="form-group">
                                    <label for="youtube_url">YouTube Video URL</label>
                                    <input type="text" class="form-control" id="youtube_url" name="youtube_url" 
                                           value="<?php echo $newsEvent ? htmlspecialchars($newsEvent['youtube_url']) : ''; ?>" 
                                           placeholder="Enter YouTube embed URL">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($newsEvent && $newsEvent['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($newsEvent && $newsEvent['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $newsEvent ? 'Update' : 'Submit'; ?></button>
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
    $('#newsEventForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/news-events/' + (isEdit ? 'update_news_event.php' : 'add_news_event.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#newsEventForm').find('button[type="submit"]').prop('disabled', false);
                if (response.success) {
                    toastr.success('News/Event ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' news/event!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#newsEventForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });

    // Media type selection logic
    $('#media_type').on('change', function() {
        var selectedType = $(this).val();
        if (selectedType === 'photo') {
            $('#photo_upload_field').show();
            $('#youtube_url_field').hide();
            $('#photo').prop('required', true);
            $('#youtube_url').prop('required', false);
        } else if (selectedType === 'youtube') {
            $('#photo_upload_field').hide();
            $('#youtube_url_field').show();
            $('#photo').prop('required', false);
            $('#youtube_url').prop('required', true);
        } else {
            $('#photo_upload_field').hide();
            $('#youtube_url_field').hide();
            $('#photo').prop('required', false);
            $('#youtube_url').prop('required', false);
        }
    }).trigger('change'); // Trigger on page load to set initial state
});
</script>
