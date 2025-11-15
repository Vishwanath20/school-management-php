<?php 

require_once('../../config/database.php');
include('../include/header.php');
// Fetch all reviews
$stmt = $pdo->query("SELECT * FROM toppers_reviews ORDER BY display_order ASC, created_at DESC");
$reviews = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Topper's Reviews</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Review</a>
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
                                    <th>Rank & Exam</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo $review['display_order']; ?></td>
                                    <td>
                                        <img src="../../uploads/toppers/<?php echo htmlspecialchars($review['photo']); ?>" 
                                             alt="topper" style="cursor: pointer; height:50px;width:50px;"
                                              onclick="showImagePopup(this.src)"/>
                                    </td>
                                    <td><?php echo htmlspecialchars($review['name']); ?></td>
                                    <td>
                                        AIR <?php echo $review['exam_rank']; ?>, 
                                        <?php echo htmlspecialchars($review['exam']); ?> <?php echo $review['year']; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $review_text = htmlspecialchars($review['review']);
                                        echo strlen($review_text) > 100 ? substr($review_text, 0, 100) . '...' : $review_text;
                                        ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $review['id']; ?>"
                                                   <?php echo $review['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $review['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-review" 
                                                data-id="<?php echo $review['id']; ?>">
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
                <img src="" id="popupImage" style="max-width: 100%;" alt="Topper Photo">
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
        var reviewId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/toppersreviews/update-status.php',
            type: 'POST',
            data: { 
                id: reviewId,
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
    $('.delete-review').on('click', function() {
        var reviewId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this review?')) {
            $.ajax({
                url: '../../api/toppersreviews/delete.php',
                type: 'POST',
                data: { id: reviewId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Review deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete review!');
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

