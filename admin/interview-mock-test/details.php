<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all interview mock tests with course and batch info
$query = "
    SELECT 
        imt.*, 
        c.title as course_name, 
        b.name as batch_name 
    FROM interview_mock_tests imt
    JOIN courses c ON imt.course_id = c.id 
    JOIN batches b ON imt.batch_id = b.id 
    ORDER BY imt.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$mock_tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">AI Interview Mock Tests</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Create Mock Test</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Date</th>
                                    <th>Duration (min)</th>
                                    <th>AI Prompt</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mock_tests as $test): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($test['title']); ?></td>
                                    <td><?php echo htmlspecialchars($test['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($test['batch_name']); ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($test['test_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($test['duration']); ?></td>
                                    <td><?php echo htmlspecialchars(strlen($test['ai_prompt']) > 50 ? substr($test['ai_prompt'], 0, 50) . '...' : $test['ai_prompt']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $test['id']; ?>"
                                                   <?php echo $test['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($test['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $test['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-mock-test" 
                                                data-id="<?php echo $test['id']; ?>">
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
        var testId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/interview-mock-tests/update_status.php', // New API endpoint
            type: 'POST',
            data: { 
                id: testId,
                status: status
            },
            success: function(response) {
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
    $('.delete-mock-test').on('click', function() {
        var testId = $(this).data('id');

        if (confirm("Are you sure you want to delete this mock test? This action cannot be undone!")) {
            $.ajax({
                url: '../../api/interview-mock-tests/delete.php', // New API endpoint
                type: 'POST',
                data: { id: testId },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Mock Test has been deleted.', 'Deleted!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete mock test!', 'Error!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!', 'Error!');
                }
            });
        }
    });

    // Initialize DataTable
    $('.table').DataTable({
        "order": [[7, "desc"]], // Order by Created Date column
        "pageLength": 25
    });
});
</script>
