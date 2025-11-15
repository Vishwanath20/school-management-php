<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all categories with photo counts
$stmt = $pdo->query("
    SELECT c.*, COUNT(p.id) as photo_count 
    FROM gallery_categories c 
    LEFT JOIN gallery_photos p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
$categories = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Gallery Categories</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Category</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Photos Count</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['title']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                                    <td>
                                        <a href="../photogalery/photos.php?category=<?php echo $category['id']; ?>" 
                                           class="badge badge-info">
                                            <?php echo $category['photo_count']; ?> Photos
                                        </a>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $category['id']; ?>"
                                                   <?php echo $category['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $category['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-category" 
                                                data-id="<?php echo $category['id']; ?>">
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
        var categoryId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/gallery/update_category_status.php',
            type: 'POST',
            data: { 
                id: categoryId,
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
    $('.delete-category').on('click', function() {
        var categoryId = $(this).data('id');
        // JavaScript confirm box for deletion
        if (confirm('Are you sure you want to delete this category? This will delete all photos in this category!')) {
            $.ajax({
                url: '../../api/gallery/delete_category.php',
                type: 'POST',
                data: { id: categoryId },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Category has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete category!');
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
        "order": [[4, "desc"]],
        "pageLength": 25
    });
});
</script>