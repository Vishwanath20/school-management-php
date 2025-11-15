<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY date DESC, created_at DESC");
$notices = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Notice Board List</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Notice</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Badge</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notices as $notice): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($notice['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($notice['title']); ?></td>
                                   
                                    <td>
                                        <?php if ($notice['badge']): ?>
                                            <span class="badge badge-<?php 
                                                echo $notice['badge'] == 'New' ? 'success' : 
                                                    ($notice['badge'] == 'Important' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo htmlspecialchars($notice['badge']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                      <?php  echo $notice['sections'] == '0' ? 'Notice Board' : 'Announcement'; ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $notice['id']; ?>"
                                                   <?php echo $notice['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $notice['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-notice" 
                                                data-id="<?php echo $notice['id']; ?>">
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
        var noticeId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/noticeboard/update-status.php',
            type: 'POST',
            data: { 
                id: noticeId,
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
    $('.delete-notice').on('click', function() {
        var noticeId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this notice?')) {
            $.ajax({
                url: '../../api/noticeboard/delete.php',
                type: 'POST',
                data: { id: noticeId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Notice deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete notice!');
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
        "order": [[0, "desc"]],
        "pageLength": 25
    });
});
</script>

