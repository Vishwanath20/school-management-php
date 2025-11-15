<?php
require_once('../../config/database.php'); 
include('../include/header.php');


// Fetch data if editing
$video = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM yt_videos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $video = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $video ? 'Edit Video' : 'Add Video'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="videoForm" class="forms-sample">
                        <?php if ($video): ?>
                            <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $video ? htmlspecialchars($video['title']) : ''; ?>" 
                                           placeholder="Enter video title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subtitle">Subtitle</label>
                                    <input type="text" class="form-control" id="subtitle" name="subtitle" 
                                           value="<?php echo $video ? htmlspecialchars($video['subtitle']) : ''; ?>" 
                                           placeholder="Enter video subtitle" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="youtube_url">YouTube Video URL</label>
                                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                           value="<?php echo $video ? htmlspecialchars($video['youtube_url']) : ''; ?>" 
                                           placeholder="Enter YouTube video URL" required>
                                    <small class="form-text text-muted">Example: https://www.youtube.com/watch?v=XXXXXXXXXXX</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $video ? $video['display_order'] : '0'; ?>" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $video ? 'Update' : 'Submit'; ?></button>
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
    // Form submission
    $('#videoForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        // Extract video ID from YouTube URL
        var youtubeUrl = formData.get('youtube_url');
        var videoId = extractYoutubeId(youtubeUrl);
        
        if (!videoId) {
            toastr.error('Invalid YouTube URL');
            Spinner.hide();
            $(this).find('button[type="submit"]').prop('disabled', false);
            return;
        }

        formData.append('youtube_id', videoId);

        $.ajax({
            url: '../../api/ytvideos/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#videoForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Video ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' video!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#videoForm').find('button[type="submit"]').prop('disabled', false);
                
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

function extractYoutubeId(url) {
    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    var match = url.match(regExp);
    return (match && match[2].length == 11) ? match[2] : false;
}
</script>

