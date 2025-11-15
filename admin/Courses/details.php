<?php 
require_once('../../config/database.php');
include('../include/header.php');
// Fetch all courses
$stmt = $pdo->query("
    SELECT c.*, b.name as batch_name,b.start_date as b_start_date,b.end_date as b_end_date 
    FROM courses c 
    LEFT JOIN batches b ON c.id = b.course_id 
    WHERE c.status = 1 
    ORDER BY c.created_at DESC
");
$courses = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Courses List</h4>
                    <div>
                
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Course</a>
                           
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Batche</th>
                                    <th>Selling Price</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <img src="../../uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>" 
                                             alt="course" style="width: 100px;border-radius: 0px;cursor: pointer;"
                                             class="course-image" onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($course['title']); ?>
                                        <br>
                                        <small class="text-muted">
                                            Original Price: ₹<?php echo number_format($course['original_price'], 2); ?>
                                        </small>
                                    </td>
                                    <td> 
                                        <?php echo htmlspecialchars($course['batch_name']); ?>
                                       
                                    </td>
                                    <td>₹<?php echo number_format($course['price'], 2); ?></td>
                                    <td>
                                        <?php 
                                        echo date('d M Y', strtotime($course['b_start_date'])) . ' to<br>' . 
                                             date('d M Y', strtotime($course['b_end_date'])); 
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $course['id']; ?>"
                                                   <?php echo $course['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $course['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-course" 
                                                data-id="<?php echo $course['id']; ?>">
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

<!-- Image Popup Modal -->
<div id="imagePopup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" onclick="hideImagePopup()">x</button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="popupImage" style="max-width: 100%;" alt="Course Image">
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 25
    });

    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var courseId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/courses/update-status.php',
            type: 'POST',
            data: { 
                id: courseId,
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
    $('.delete-course').on('click', function() {
        var courseId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this course?')) {
            $.ajax({
                url: '../../api/courses/delete.php',
                type: 'POST',
                data: { id: courseId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Course deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete course!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});

function showImagePopup(imageSrc) {
    $('#popupImage').attr('src', imageSrc);
    $('#imagePopup').modal('show');
}
function hideImagePopup() {
    $('#imagePopup').modal('hide');
}
</script>

<style>
.modal-content {
    background-color: transparent;
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
#popupImage {
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}
</style>

