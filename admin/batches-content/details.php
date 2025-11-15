<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all content with batch and subject details
$stmt = $pdo->query("
    SELECT bc.*, 
           b.name as batch_name,
           s.name as subject_name,
           c.title as course_name
    FROM batch_content bc
    LEFT JOIN batches b ON bc.batch_id = b.id
    LEFT JOIN subjects s ON bc.subject_id = s.id
    LEFT JOIN courses c ON bc.course_id = c.id
    ORDER BY bc.lecture_date DESC
");
$contents = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Batch Content</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Content
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Batch</th>
                                    <th>Subject</th>
                                    <th>Title</th>
                                    <th>Lecture Date</th>
                                    <th>YouTube URL</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contents as $content): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($content['id']); ?></td>
                                    <td><?php echo htmlspecialchars($content['batch_name']); ?><br><br><span class="text-success"><?php echo htmlspecialchars($content['course_name']); ?></span></td>
                                    <td><?php echo htmlspecialchars($content['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($content['title']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($content['lecture_date'])); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($content['youtube_url']); ?>" 
                                           target="_blank" class="btn btn-link">
                                            <i class="mdi mdi-youtube"></i> Watch
                                        </a>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $content['id']; ?>"
                                                   <?php echo $content['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $content['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-content" 
                                                data-id="<?php echo $content['id']; ?>">
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

<?php include('../include/footer.php'); ?>

<script>
$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var contentId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/batches-content/update_status.php',
            type: 'POST',
            data: { 
                id: contentId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Content status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update content status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle delete
    $('.delete-content').on('click', function() {
        var contentId = $(this).data('id');
        if (confirm('Are you sure you want to delete this content?')) {
            $.ajax({
                url: '../../api/batches-content/delete_content.php',
                type: 'POST',
                data: { id: contentId },
                success: function(response) {
                    response= JSON.parse(response);
                    if (response.success) {
                        toastr.success('Content has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete content!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });

    // Initialize DataTable
    $('.table').DataTable({
        "order": [[4, "desc"]],
        "pageLength": 25
    });
});
</script>