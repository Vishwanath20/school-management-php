<?php 
include('../include/header.php');
require_once('../../config/database.php');

// Fetch all masters with their subs and topics
$stmt = $pdo->query("
    SELECT 
        m.id as master_id, 
        m.title as master_title,
        m.display_order as master_order,
        m.status as master_status,
        s.id as sub_id,
        s.title as sub_title,
        s.display_order as sub_order,
        s.status as sub_status,
        t.id as topic_id,
        t.title as topic_title,
        t.description as topic_description,
        t.display_order as topic_order,
        t.status as topic_status
    FROM topic_wise_syllabus_master m
    LEFT JOIN topic_wise_syllabus_sub s ON m.id = s.master_id
    LEFT JOIN topic_wise_syllabus_topics t ON s.id = t.sub_id
    ORDER BY m.display_order ASC, s.display_order ASC, t.display_order ASC
");
$results = $stmt->fetchAll();

// Organize data into hierarchical structure
$organized = [];
foreach ($results as $row) {
    if (!isset($organized[$row['master_id']])) {
        $organized[$row['master_id']] = [
            'title' => $row['master_title'],
            'order' => $row['master_order'],
            'status' => $row['master_status'],
            'subs' => []
        ];
    }
    
    if ($row['sub_id'] && !isset($organized[$row['master_id']]['subs'][$row['sub_id']])) {
        $organized[$row['master_id']]['subs'][$row['sub_id']] = [
            'title' => $row['sub_title'],
            'order' => $row['sub_order'],
            'status' => $row['sub_status'],
            'topics' => []
        ];
    }
    
    if ($row['topic_id']) {
        $organized[$row['master_id']]['subs'][$row['sub_id']]['topics'][] = [
            'id' => $row['topic_id'],
            'title' => $row['topic_title'],
            'description' => $row['topic_description'],
            'order' => $row['topic_order'],
            'status' => $row['topic_status']
        ];
    }
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Topic-Wise UPSC Syllabus</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Main topics</a>
                        <a href="../topicwisesyllabussub/manage.php" class="btn btn-info btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Sub Topics</a>
                        <a href="../topicwisesyllabustopics/manage.php" class="btn btn-success btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Points</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="accordion" id="syllabusAccordion">
                        <?php foreach ($organized as $masterId => $master): ?>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#fff;">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" 
                                            data-target="#master<?php echo $masterId; ?>">
                                        <?php echo htmlspecialchars($master['title']); ?>
                                    </button>
                                </h2>
                                <div>
                                    <div class="form-check form-switch d-inline-block mr-2">
                                        <input type="checkbox" class="form-check-input master-status-toggle" 
                                               data-id="<?php echo $masterId; ?>"
                                               <?php echo $master['status'] ? 'checked' : ''; ?>>
                                    </div>
                                    <a href="manage.php?id=<?php echo $masterId; ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                </div>
                            </div>

                            <div id="master<?php echo $masterId; ?>" class="collapse" data-parent="#syllabusAccordion">
                                <div class="card-body">
                                    <?php foreach ($master['subs'] as $subId => $sub): ?>
                                    <div class="sub-category mb-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 style="color:black;"><?php echo htmlspecialchars($sub['title']); ?></h5>
                                            <div>
                                                <div class="form-check form-switch d-inline-block mr-2">
                                                    <input type="checkbox" class="form-check-input sub-status-toggle" 
                                                           data-id="<?php echo $subId; ?>"
                                                           <?php echo $sub['status'] ? 'checked' : ''; ?>>
                                                </div>
                                                <a href="../topicwisesyllabussub/manage.php?id=<?php echo $subId; ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <ul class="list-group mt-2">
                                            <?php foreach ($sub['topics'] as $topic): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1" style="color:black;"><?php echo htmlspecialchars($topic['title']); ?></h6>
                                                        <?php if ($topic['description']): ?>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($topic['description']); ?>
                                                        </small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div style="display: flex;">
                                                        <div class="form-check form-switch d-inline-block mr-2">
                                                            <input type="checkbox" class="form-check-input topic-status-toggle" 
                                                                   data-id="<?php echo $topic['id']; ?>"
                                                                   <?php echo $topic['status'] ? 'checked' : ''; ?>>
                                                        </div>
                                                        <a href="../topicwisesyllabustopics/manage.php?id=<?php echo $topic['id']; ?>" 
                                                           class="btn btn-info btn-sm">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm delete-topic" 
                                                                data-id="<?php echo $topic['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Status toggle handlers
    $('.master-status-toggle').on('change', function() {
        updateStatus('master', $(this).data('id'), $(this).prop('checked') ? 1 : 0);
    });

    $('.sub-status-toggle').on('change', function() {
        updateStatus('sub', $(this).data('id'), $(this).prop('checked') ? 1 : 0);
    });

    $('.topic-status-toggle').on('change', function() {
        updateStatus('topic', $(this).data('id'), $(this).prop('checked') ? 1 : 0);
    });

    function updateStatus(type, id, status) {
        $.ajax({
            url: '../../api/topicwisesyllabus/update-status.php',
            type: 'POST',
            data: { 
                type: type,
                id: id,
                status: status
            },
            success: function(response) {
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
    }

    // Delete topic handler
    $('.delete-topic').on('click', function() {
        var topicId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this topic?')) {
            $.ajax({
                url: '../../api/topicwisesyllabus/delete.php',
                type: 'POST',
                data: { 
                    type: 'topic',
                    id: topicId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Topic deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete topic!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
</script>

