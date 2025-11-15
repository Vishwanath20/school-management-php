<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$topic = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM topic_wise_syllabus_topics WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $topic = $stmt->fetch();
}

// Fetch all active main categories
$stmt = $pdo->query("SELECT id, title FROM topic_wise_syllabus_master WHERE status = 1 ORDER BY display_order ASC");
$masters = $stmt->fetchAll();

// Get max display order
$stmt = $pdo->query("SELECT MAX(display_order) as max_order FROM topic_wise_syllabus_topics");
$maxOrder = $stmt->fetch();
$nextOrder = (isset($maxOrder['max_order']) ? $maxOrder['max_order'] : 0) + 1;
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $topic ? 'Edit Topic' : 'Add Topic'; ?></h4>
                    <div>
                        <a href="../topicwisesyllabusmasters/details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="topicForm" class="forms-sample">
                        <?php if ($topic): ?>
                            <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="master_id">Main Category</label>
                                    <select class="form-control" id="master_id" name="master_id" required>
                                        <option value="">Select Main Category</option>
                                        <?php foreach ($masters as $master): ?>
                                            <option value="<?php echo $master['id']; ?>" 
                                                <?php echo ($topic && $topic['master_id'] == $master['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($master['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_id">Sub Category</label>
                                    <select class="form-control" id="sub_id" name="sub_id" required>
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $topic ? htmlspecialchars($topic['title']) : ''; ?>" 
                                           placeholder="Enter title" required>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $topic ? $topic['display_order'] : $nextOrder; ?>" 
                                           min="1" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                              placeholder="Enter description"><?php echo $topic ? htmlspecialchars($topic['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mr-2">
                            <?php echo $topic ? 'Update' : 'Submit'; ?>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='../topicwisesyllabusmasters/details.php'">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    function loadSubCategories(masterId, selectedSubId = '') {
        $('#sub_id').html('<option value="">Select Sub Category</option>');
        
        if (masterId) {
            $.ajax({
                url: '../../api/topicwisesyllabus/get-sub-categories.php',
                type: 'POST',
                data: { master_id: masterId },
                success: function(response) {
                    if (response.status === 'success') {
                        response.data.forEach(function(sub) {
                            var selected = (selectedSubId == sub.id) ? 'selected' : '';
                            $('#sub_id').append(`<option value="${sub.id}" ${selected}>${sub.title}</option>`);
                        });
                    }
                }
            });
        }
    }

    <?php if ($topic): ?>
    loadSubCategories(<?php echo $topic['master_id']; ?>, <?php echo $topic['sub_id']; ?>);
    <?php endif; ?>

    $('#master_id').on('change', function() {
        loadSubCategories($(this).val());
    });

    $('#topicForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: '../../api/topicwisesyllabus/' + ($(this).find('input[name="id"]').length ? 'update-topic.php' : 'create-topic.php'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Spinner.hide();
                $('#topicForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Topic ' + ($('#topicForm input[name="id"]').length ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = '../topicwisesyllabusmasters/details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + ($('#topicForm input[name="id"]').length ? 'update' : 'add') + ' topic!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#topicForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>

