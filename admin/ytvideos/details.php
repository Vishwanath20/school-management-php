<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all videos
$stmt = $pdo->query("SELECT * FROM yt_videos ORDER BY display_order ASC, created_at DESC");
$videos = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Featured Videos</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Video</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($videos as $video): ?>
                                <tr>
                                    <td><?php echo $video['display_order']; ?></td>
                                    <td>
                                        <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($video['youtube_id']); ?>/mqdefault.jpg" 
                                             alt="video thumbnail" style="width: 120px;height:120px; cursor: pointer;"
                                             onclick="playVideo('<?php echo htmlspecialchars($video['youtube_id']); ?>')"/>
                                    </td>
                                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                                    <td><?php echo htmlspecialchars($video['subtitle']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $video['id']; ?>"
                                                   <?php echo $video['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($video['youtube_url']); ?>" 
                                           target="_blank" class="btn btn-success btn-sm">
                                            <i class="mdi mdi-youtube"></i>
                                        </a>
                                        <a href="manage.php?id=<?php echo $video['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-video" 
                                                data-id="<?php echo $video['id']; ?>">
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

<!-- Video Player Modal -->
<div id="videoModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" onclick="stopVideo()">x</button>
            </div>
            <div class="modal-body">
              
                    <iframe id="videoPlayer" src="" allowfullscreen style="height:300px;width:600px;"></iframe>
               
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 25
    });

    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var videoId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/ytvideos/update-status.php',
            type: 'POST',
            data: { 
                id: videoId,
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

    // Handle delete
    $('.delete-video').on('click', function() {
        var videoId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this video?')) {
            $.ajax({
                url: '../../api/ytvideos/delete.php',
                type: 'POST',
                data: { id: videoId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Video deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete video!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});

function playVideo(videoId) {
    $('#videoPlayer').attr('src', 'https://www.youtube.com/embed/' + videoId + '?autoplay=1');
    $('#videoModal').modal('show');
}

function stopVideo() {
    $('#videoPlayer').attr('src', '');
    $('#videoModal').modal('hide');
}
</script>

<style>
.modal-content {
    border: none;
}
.modal-header {
    border: none;
    padding: 0;
    position: absolute;
    right: 0;
    z-index: 1;
}
.btn-close {
    background-color: red;
    opacity: 1;
    padding: 0.5rem;
    margin: 0;
}
.modal-body {
    padding: 0;
}
</style>

