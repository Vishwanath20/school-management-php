<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all categories from course_categories table
// Update the SQL query to include course count
$stmt = $pdo->query("
    SELECT 
        cc.id, 
        cc.title, 
        cc.status, 
        cc.added_on,
        COUNT(c.id) as course_count
    FROM course_categories cc
    LEFT JOIN courses c ON cc.id = c.category_id AND c.status = 1
    GROUP BY cc.id, cc.title, cc.status, cc.added_on
    ORDER BY cc.added_on DESC
");
$categories = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Course Categories</h4>
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
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Courses</th>
                                    <th>Status</th>
                                    <th>Added On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                                    <td><?php echo htmlspecialchars($category['title']); ?></td>
                                    <td>
                                        <span class="badge bg-info"style="color:#fff;">
                                            <?php echo $category['course_count']; ?> courses
                                        </span>
                                    </td>
                                    <td><?php echo $category['status'] ? 'Active' : 'Inactive'; ?></td>
                                    <td><?php echo date('d M Y', strtotime($category['added_on'])); ?></td>
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
  
    // Handle delete
    $('.delete-category').on('click', function() {
        var categoryId = $(this).data('id');
        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: '../../api/course-category/delete_category.php',
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