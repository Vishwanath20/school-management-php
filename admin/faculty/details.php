<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all faculty
$stmt = $pdo->query("SELECT * FROM faculty ORDER BY display_order ASC, created_at DESC");
$faculties = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Our Expert Faculty</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Faculty</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>Experience</th>
                                    <th>Social Links</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faculties as $faculty): ?>
                                <tr>
                                    <td><?php echo $faculty['display_order']; ?></td>
                                    <td>
                                        <img src="../../uploads/faculty/<?php echo htmlspecialchars($faculty['photo']); ?>" 
                                             alt="faculty" style="cursor: pointer; height:50px;width:50px;"
                                              onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($faculty['title']); ?> 
                                        <?php echo htmlspecialchars($faculty['name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($faculty['specialization']); ?></td>
                                    <td><?php echo htmlspecialchars($faculty['experience']); ?></td>
                                    <td>
                                        <?php if ($faculty['linkedin']): ?>
                                            <a href="<?php echo htmlspecialchars($faculty['linkedin']); ?>" 
                                               target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="mdi mdi-linkedin"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($faculty['twitter']): ?>
                                            <a href="<?php echo htmlspecialchars($faculty['twitter']); ?>" 
                                               target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="mdi mdi-twitter"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $faculty['id']; ?>"
                                                   <?php echo $faculty['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $faculty['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-faculty" 
                                                data-id="<?php echo $faculty['id']; ?>">
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
                <img src="" id="popupImage" style="max-width: 100%;" alt="Faculty Photo">
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
        var facultyId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/faculty/update-status.php',
            type: 'POST',
            data: { 
                id: facultyId,
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
    $('.delete-faculty').on('click', function() {
        var facultyId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this faculty?')) {
            $.ajax({
                url: '../../api/faculty/delete.php',
                type: 'POST',
                data: { id: facultyId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Faculty deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete faculty!');
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

