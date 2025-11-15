<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all exams
$stmt = $pdo->query("SELECT * FROM upcoming_exams ORDER BY display_order ASC, exam_date ASC");
$exams = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Upcoming Exams</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Exam</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Exam Date</th>
                                    <th>Countdown</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?php echo $exam['display_order']; ?></td>
                                    <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                    <td><?php echo date('d M Y h:i A', strtotime($exam['exam_date'])); ?></td>
                                    <td>
                                        <div class="countdown" data-date="<?php echo $exam['exam_date']; ?>">
                                            Loading...
                                        </div>
                                    </td>
                                   
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $exam['id']; ?>"
                                                   <?php echo $exam['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $exam['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-exam" 
                                                data-id="<?php echo $exam['id']; ?>">
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
    // Initialize DataTable
    $('.table').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 25
    });

    // Update countdowns
    function updateCountdowns() {
        $('.countdown').each(function() {
            var examDate = new Date($(this).data('date')).getTime();
            var now = new Date().getTime();
            var distance = examDate - now;

            if (distance < 0) {
                $(this).html('EXPIRED');
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $(this).html(
                days + "d " + 
                hours.toString().padStart(2, '0') + "h " + 
                minutes.toString().padStart(2, '0') + "m " + 
                seconds.toString().padStart(2, '0') + "s"
            );
        });
    }

    // Update countdown every second
    setInterval(updateCountdowns, 1000);
    updateCountdowns(); // Initial update

    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var examId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/upcomingexams/update-status.php',
            type: 'POST',
            data: { 
                id: examId,
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
    $('.delete-exam').on('click', function() {
        var examId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this exam?')) {
            $.ajax({
                url: '../../api/upcomingexams/delete.php',
                type: 'POST',
                data: { id: examId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Exam deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete exam!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
</script>

