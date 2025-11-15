<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Get category filter
$category_id = isset($_GET['category']) ? $_GET['category']: null;

// Fetch photos with category info
$query = "
    SELECT p.*, c.title as category_name 
    FROM gallery_photos p 
    LEFT JOIN gallery_categories c ON p.category_id = c.id 
";

if ($category_id) {
    $query .= " WHERE p.category_id = :category_id";
}
$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
if ($category_id) {
    $stmt->bindParam(':category_id', $category_id);
}
$stmt->execute();
$photos = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Gallery Photos</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Photos</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($photos as $photo): ?>
                                <tr>
                                    <td>
                                        <img src="../../uploads/gallery/<?php echo $photo['image_path']; ?>" 
                                             style="width: 100px; height: 70px; object-fit: cover;"
                                             onclick="viewImage('../../uploads/gallery/<?php echo $photo['image_path']; ?>')"
                                             class="cursor-pointer">
                                    </td>

                                    <td><?php echo htmlspecialchars($photo['category_name']); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $photo['id']; ?>"
                                                   <?php echo $photo['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($photo['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $photo['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-photo" 
                                                data-id="<?php echo $photo['id']; ?>"
                                                data-image="<?php echo $photo['image_path']; ?>">
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

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="previewImage" style="max-width: 100%;">
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
function viewImage(imagePath) {
    $('#previewImage').attr('src', imagePath);
    $('#imagePreviewModal').modal('show');
}

$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var photoId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/gallery/update_photo_status.php',
            type: 'POST',
            data: { 
                id: photoId,
                status: status
            },
            success: function(response) {
                if (response.success) {
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
    $('.delete-photo').on('click', function() {
    var photoId = $(this).data('id');
    var imagePath = $(this).data('image');

    // JavaScript confirm box
    if (confirm("Are you sure? This action cannot be undone!")) {
        $.ajax({
            url: '../../api/gallery/delete_photo.php',
            type: 'POST',
            data: {
                id: photoId,
                image_path: imagePath
            },
            success: function(response) {
                if (response.success) {
                    // Toastr success message
                    toastr.success('Photo has been deleted.', 'Deleted!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Toastr error message
                    toastr.error(response.message || 'Failed to delete photo!', 'Error!');
                }
            },
            error: function() {
                // Toastr error message
                toastr.error('Something went wrong!', 'Error!');
            }
        });
    }
});


    // Initialize DataTable
    $('.table').DataTable({
        "order": [[5, "desc"]],
        "pageLength": 25
    });
});
</script>
