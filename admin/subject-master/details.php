<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all subjects
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY name ASC");
$subjects = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Subjects</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Subject
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['id']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $subject['id']; ?>"
                                                   <?php echo $subject['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($subject['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $subject['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-subject" 
                                                data-id="<?php echo $subject['id']; ?>">
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
        var subjectId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/subject-master/update_status.php',
            type: 'POST',
            data: { 
                id: subjectId,
                status: status
            },
            success: function(response) {
                response=JSON.parse(response);
                if (response.success) {
                    toastr.success('Subject status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update subject status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle delete
    $('.delete-subject').on('click', function() {
        var subjectId = $(this).data('id');
        if (confirm('Are you sure you want to delete this subject?')) {
            $.ajax({
                url: '../../api/subject-master/delete_subject.php',
                type: 'POST',
                data: { id: subjectId },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Subject has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete subject!');
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
        "order": [[1, "asc"]],
        "pageLength": 25
    });
});
</script>