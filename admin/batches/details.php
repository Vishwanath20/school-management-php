<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all batches with course and faculty details
$stmt = $pdo->query("
    SELECT b.*, 
           c.title as course_title,
           f.name as faculty_name
    FROM batches b
    LEFT JOIN courses c ON b.course_id = c.id
    LEFT JOIN faculty f ON b.faculty_id = f.id
    ORDER BY b.created_at DESC
");
$batches = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Batches</h4>
                    <div>
                        <!-- <a href="../batches-content/details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>batches-content
                        </a> -->
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Batch
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
                                    <th>Course</th>
                                    <th>Faculty</th>
                                    <th>Duration</th>
                                    <th>Timing</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($batches as $batch): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($batch['id']); ?></td>
                                    <td><?php echo htmlspecialchars($batch['name']); ?></td>
                                    <td><?php echo htmlspecialchars($batch['course_title']); ?></td>
                                    <td><?php echo htmlspecialchars($batch['faculty_name']); ?></td>
                                    <td>
                                        <?php 
                                        echo date('d M Y', strtotime($batch['start_date'])) . ' to <br>' . 
                                             date('d M Y', strtotime($batch['end_date'])); 
                                        ?>
                                    </td>
                                    <td><?php echo date('h:i A', strtotime($batch['timing'])); ?></td>
                                    <td><?php echo htmlspecialchars($batch['capacity']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle"
                                                data-id="<?php echo $batch['id']; ?>"
                                                <?php echo $batch['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $batch['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-batch"
                                            data-id="<?php echo $batch['id']; ?>">
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
        var batchId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;

        $.ajax({
            url: '../../api/batches/update_status.php',
            type: 'POST',
            data: {
                id: batchId,
                status: status
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.success) {
                    toastr.success('Batch status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update batch status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle delete
    $('.delete-batch').on('click', function() {
        var batchId = $(this).data('id');
        if (confirm('Are you sure you want to delete this batch?')) {
            $.ajax({
                url: '../../api/batches/delete_batch.php',
                type: 'POST',
                data: {
                    id: batchId
                },
                success: function(response) {
                    response=JSON.parse(response);
                    if (response.success) {
                        toastr.success('Batch has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete batch!');
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
        "order": [
            [0, "desc"]
        ],
        "pageLength": 25
    });
});
</script>