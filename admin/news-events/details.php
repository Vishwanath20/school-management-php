<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all news and events
$stmt = $pdo->query("
    SELECT 
        id, 
        photo, 
        youtube_url,
        event_name, 
        event_date, 
        status, 
        added_on
    FROM news_events
    ORDER BY added_on DESC
");
$newsEvents = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
<h4 class="card-title">News and Events</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add News/Event</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Media</th>
                                    <th>Event Name</th>
                                    <th>Event Date</th>
                                    <th>Status</th>
                                    <th>Added On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($newsEvents as $newsEvent): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($newsEvent['id']); ?></td>
                                    <td>
                                        <?php if (!empty($newsEvent['photo'])): ?>
                                            <img src="../../uploads/news-events/<?php echo htmlspecialchars($newsEvent['photo']); ?>" alt="Event Photo" style="width: 50px; height: auto;">
                                        <?php elseif (!empty($newsEvent['youtube_url'])): ?>
                                            <iframe width="100" height="75" src="<?php echo htmlspecialchars($newsEvent['youtube_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        <?php else: ?>
                                            No Media
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($newsEvent['event_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($newsEvent['event_date'])); ?></td>
                                    <td><?php echo $newsEvent['status'] ? 'Active' : 'Inactive'; ?></td>
                                    <td><?php echo date('d M Y', strtotime($newsEvent['added_on'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $newsEvent['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-news-event" 
                                                data-id="<?php echo $newsEvent['id']; ?>">
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
    // Handle delete
    $('.delete-news-event').on('click', function() {
        var newsEventId = $(this).data('id');
        if (confirm('Are you sure you want to delete this news/event?')) {
            $.ajax({
                url: '../../api/news-events/delete_news_event.php',
                type: 'POST',
                data: { id: newsEventId },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        toastr.success('News/Event has been deleted.');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to delete news/event!');
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
