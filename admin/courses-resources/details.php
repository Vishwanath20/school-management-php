<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Get course filter
$course_id = isset($_GET['course']) ? $_GET['course'] : null;

// Fetch resources with course info
$query = "
    SELECT r.*, c.title as course_name 
    FROM course_resources r 
    LEFT JOIN courses c ON r.course_id = c.id 
";

if ($course_id) {
    $query .= " WHERE r.course_id = :course_id";
}
$query .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($query);
if ($course_id) {
    $stmt->bindParam(':course_id', $course_id);
}
$stmt->execute();
$resources = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Course Resources</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Resources</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Resources</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resources as $resource): 
                                    // Fetch resource items
                                    $stmt = $pdo->prepare("SELECT * FROM resource_items WHERE resource_id = ?");
                                    $stmt->execute([$resource['id']]);
                                    $items = $stmt->fetchAll();
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resource['title']); ?></td>
                                    <td><?php echo htmlspecialchars($resource['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($resource['description']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" 
                                                onclick="viewResources(<?php echo htmlspecialchars(json_encode($items)); ?>)">
                                            View Resources (<?php echo count($items); ?>)
                                        </button>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $resource['id']; ?>"
                                                   <?php echo $resource['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($resource['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $resource['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-resource" 
                                                data-id="<?php echo $resource['id']; ?>">
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

<!-- Resources Preview Modal -->
<div class="modal fade" id="resourcesPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resource Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resourceItemsList"></div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script>
function viewResources(items) {
    let html = '<div class="row">';
    
    items.forEach(item => {
        html += '<div class="col-md-6 mb-4">';
        html += '<div class="card">';
        html += '<div class="card-body">';
        
        if (item.type === 'video') {
            // Extract video ID and create embed
            const videoId = extractVideoId(item.content);
            if (videoId) {
                html += `<div class="embed-responsive embed-responsive-16by9 mb-2">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/${videoId}" 
                            allowfullscreen style="width: 100%; height: 200px;"></iframe>
                </div>`;
            }
        } else if (item.type === 'pdf') {
            html += `<div class="d-flex align-items-center">
                <i class="mdi mdi-file-pdf text-danger me-2" style="font-size: 24px;"></i>
                <a href="../../uploads/resources/${item.content}" target="_blank" class="btn btn-outline-primary btn-sm">
                    View PDF
                </a>
            </div>`;
            
        } 
        else if (item.type === 'googleDrive') {
            html += `<div class="d-flex align-items-center">
                <i class="mdi mdi-file-pdf text-danger me-2" style="font-size: 24px;"></i>
                <a href="${item.content}" target="_blank" class="btn btn-outline-primary btn-sm">
                   Google Drive
                </a>
            </div>`;
            
        } 
        else if (item.type === 'image') {
            html += `<img src="../../uploads/resources/${item.content}" class="img-fluid mb-2" 
                         style="max-height: 200px; width: 100%; object-fit: cover;">`;
        }
        
        html += `<p class="mb-0"><strong>Type:</strong> ${item.type}</p>`;
        html += '</div></div></div>';
    });
    
    html += '</div>';
    
    $('#resourceItemsList').html(html);
    $('#resourcesPreviewModal').modal('show');
}

function extractVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').on('change', function() {
        const resourceId = $(this).data('id');
        const status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/courses-resources/update_status.php',
            type: 'POST',
            data: { 
                id: resourceId,
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
    $('.delete-resource').on('click', function() {
        const resourceId = $(this).data('id');

        if (confirm("Are you sure? This action cannot be undone!")) {
            $.ajax({
                url: '../../api/courses-resources/delete.php',
                type: 'POST',
                data: { id: resourceId },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Resource has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete resource!');
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
        "order": [[5, "desc"]],
        "pageLength": 25
    });
});
</script>