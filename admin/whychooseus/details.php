<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all features
$stmt = $pdo->query("SELECT * FROM why_choose_us ORDER BY display_order ASC, created_at DESC");
$features = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Why Choose Us Features</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add why choose us</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Icon</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($features as $feature): ?>
                                <tr>
                                    <td><?php echo $feature['display_order']; ?></td>
                                    <td>
                                        <img src="../../uploads/features/<?php echo htmlspecialchars($feature['icon']); ?>" 
                                             alt="feature" style="width: 50px;border-radius: 0px;cursor: pointer;"
                                             class="feature-image" onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td><?php echo htmlspecialchars($feature['title']); ?></td>
                                    <td>
                                        <?php 
                                        $description = htmlspecialchars($feature['description']);
                                        echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $feature['id']; ?>"
                                                   <?php echo $feature['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $feature['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-feature" 
                                                data-id="<?php echo $feature['id']; ?>">
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
                <img src="" id="popupImage" style="max-width: 100%;" alt="Feature Icon">
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
        var featureId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/whychooseus/update-status.php',
            type: 'POST',
            data: { 
                id: featureId,
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
    $('.delete-feature').on('click', function() {
        var featureId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this feature?')) {
            $.ajax({
                url: '../../api/whychooseus/delete.php',
                type: 'POST',
                data: { id: featureId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Feature deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete feature!');
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

