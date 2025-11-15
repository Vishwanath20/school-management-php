<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all sliders
$stmt = $pdo->query("SELECT * FROM hero_sliders ORDER BY created_at DESC");
$sliders = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Banner List</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Banner</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Display Order</th>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sliders as $slider): ?>
                                <tr>
                                    // Add this CSS in the table row where the image is displayed
                                    <td>
                                        <img src="../../uploads/sliders/<?php echo htmlspecialchars($slider['image']); ?>" 
                                             alt="slider" style="width: 100px;border-radius: 0px;cursor: pointer;"
                                             class="slider-image" onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td><?php echo htmlspecialchars($slider['display_order']); ?></td>
                                    <td><?php echo htmlspecialchars($slider['title']); ?></td>
                                    <td><?php echo htmlspecialchars($slider['subtitle']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $slider['id']; ?>"
                                                   <?php echo $slider['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($slider['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $slider['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-slider" 
                                                data-id="<?php echo $slider['id']; ?>">
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
        var sliderId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/heroslider/update-status.php',
            type: 'POST',
            data: { 
                id: sliderId,
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
    $('.delete-slider').on('click', function() {
        var sliderId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this slider?')) {
            $.ajax({
                url: '../../api/heroslider/delete.php',
                type: 'POST',
                data: { id: sliderId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Slider deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete slider!');
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


<div id="imagePopup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" onclick="hideImagePopup()">X</button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="popupImage" style="max-width: 100%;" alt="Slider Image">
            </div>
        </div>
    </div>
</div>

<style>
.modal-dialog {
    max-width: 800px;
}
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

