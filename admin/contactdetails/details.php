<?php 
require_once('../../config/database.php');
include('../include/header.php');

    $stmt = $pdo->prepare("SELECT * FROM contact_details");
    $stmt->execute();
    $contacts = $stmt->fetchAll();

?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Contact details</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Contact details</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Office Address</th>
                                    <th>Phone Number</th>
                                    <th>Email Address</th>
                                    <th>Working Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($contact['id']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['address']); ?></td>
                                    <td><?php echo $contact['phone']; ?> </td>
                                    
                                    <td><?php echo $contact['email']; ?> </td>
                                    <td><?php echo $contact['working_hours']; ?> </td>
                                    <td><?php echo $contact['status']=="Active" ? 'Active' : 'Inactive'; ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $contact['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-category" 
                                                data-id="<?php echo $contact['id']; ?>">
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
                url: '../../api/contactdetails/delete_contactdetails.php',
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