<?php
require_once('../../config/database.php');
include('../include/header.php');

// Get filters
$course_category_id = isset($_GET['course_category_id']) ? intval($_GET['course_category_id']) : 0;
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
$difficulty_level = isset($_GET['difficulty_level']) ? $_GET['difficulty_level'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch course categories
$stmt = $pdo->query("SELECT id, title FROM course_categories WHERE status = 1");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch subjects
$stmt = $pdo->query("SELECT id, name FROM subjects WHERE status = 1");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                     <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Questions Bank</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>Add New Question</a>
                    </div>
                </div>
                    <div class="card-body">
                       
                        <!-- Filters -->
                        <form class="mb-4" method="GET" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" name="course_category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo ($course_category_id == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="subject_id">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>" <?php echo ($subject_id == $subject['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($subject['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="difficulty_level">
                                        <option value="">All Levels</option>
                                        <option value="easy" <?php echo ($difficulty_level == 'easy') ? 'selected' : ''; ?>>Easy</option>
                                        <option value="medium" <?php echo ($difficulty_level == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                        <option value="hard" <?php echo ($difficulty_level == 'hard') ? 'selected' : ''; ?>>Hard</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="search" placeholder="Search questions..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <!-- Questions Table -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Subject</th>
                                        <th>Topic</th>
                                        <th>Question</th>
                                        <th>Marks</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Build query
                                    $sql = "SELECT q.*, c.title as category_name, s.name as subject_name 
                                           FROM questions q 
                                           LEFT JOIN course_categories c ON q.course_category_id = c.id 
                                           LEFT JOIN subjects s ON q.subject_id = s.id 
                                           WHERE 1=1";
                                    $params = [];

                                    if ($course_category_id) {
                                        $sql .= " AND q.course_category_id = ?";
                                        $params[] = $course_category_id;
                                    }
                                    if ($subject_id) {
                                        $sql .= " AND q.subject_id = ?";
                                        $params[] = $subject_id;
                                    }
                                    if ($difficulty_level) {
                                        $sql .= " AND q.difficulty_level = ?";
                                        $params[] = $difficulty_level;
                                    }
                                    if ($search) {
                                        $sql .= " AND (q.question_text LIKE ? OR q.topic LIKE ?)";
                                        $params[] = "%$search%";
                                        $params[] = "%$search%";
                                    }

                                    $sql .= " ORDER BY q.id DESC";
                                    
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute($params);
                                    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($questions as $question):
                                    ?>
                                    <tr>
                                        <td><?php echo $question['id']; ?></td>
                                        <td><?php echo htmlspecialchars($question['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($question['subject_name']); ?></td>
                                        <td><?php echo htmlspecialchars($question['topic']); ?></td>
                                        <td>
                                            <?php 
                                            $text = strip_tags($question['question_text']);
                                            echo htmlspecialchars(strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text);
                                            ?>
                                        </td>
                                        <td><?php echo $question['marks']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $question['difficulty_level'] == 'easy' ? 'success' : 
                                                    ($question['difficulty_level'] == 'medium' ? 'warning' : 'danger');
                                            ?>">
                                                <?php echo ucfirst($question['difficulty_level']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input status-toggle" 
                                                       data-id="<?php echo $question['id']; ?>" 
                                                       <?php echo $question['status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="manage.php?id=<?php echo $question['id']; ?>" class="btn btn-sm btn-info"><i class="mdi mdi-pencil"></i></a>
                                            <button type="button" class="btn btn-sm btn-danger delete-question" 
                                                    data-id="<?php echo $question['id']; ?>"><i class="mdi mdi-delete"></i></button>
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

<?php require_once '../include/footer.php'; ?>
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('.form-control').change(function() {
        $('#filterForm').submit();
    });

    // Handle status toggle
    $('.status-toggle').change(function() {
        var id = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;

        $.ajax({
            url: '../../api/questions/update_status.php',
            type: 'POST',
            data: { id: id, status: status },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Status updated successfully');
                } else {
                    toastr.error(response.message || 'Error updating status');
                }
            },
            error: function() {
                toastr.error('Error updating status');
            }
        });
    });

    // Handle delete
    $('.delete-question').click(function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this question?')) {
            $.ajax({
                url: '../../api/questions/delete.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Question deleted successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Error deleting question');
                    }
                },
                error: function() {
                    toastr.error('Error deleting question');
                }
            });
        }
    });
});
</script>

