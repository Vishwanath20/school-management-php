<?php
require_once('../../config/database.php');
include('../include/header.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$question = null;
$options = [];

if ($id > 0) {
    // Fetch question
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch options
    if ($question) {
        $stmt = $pdo->prepare("SELECT * FROM question_options WHERE question_id = ?");
        $stmt->execute([$id]);
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Fetch course categories
$stmt = $pdo->query("SELECT id, title FROM course_categories WHERE status = 1");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch subjects
$stmt = $pdo->query("SELECT id, name FROM subjects WHERE status = 1");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $id  ? 'Edit Questions' : 'Add Questions'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">

                    <form class="forms-sample" id="questionForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_category_id">Course Category</label>
                                    <select class="form-control" id="course_category_id" name="course_category_id"
                                        required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($question && $question['course_category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subject_id">Subject</label>
                                    <select class="form-control" id="subject_id" name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject['id']; ?>"
                                            <?php echo ($question && $question['subject_id'] == $subject['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subject['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="topic">Topic</label>
                                    <input type="text" class="form-control" id="topic" name="topic"
                                        value="<?php echo $question ? htmlspecialchars($question['topic']) : ''; ?>"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="question_text">Question Text</label>
                                    <textarea class="form-control" id="question_text" name="question_text" rows="4"
                                        required><?php echo $question ? htmlspecialchars($question['question_text']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="question_image">Question Image (Optional)</label>
                                    <input type="file" class="form-control" id="question_image" name="question_image"
                                        accept="image/*">
                                    <?php if ($question && $question['question_image']): ?>
                                    <img src="../../uploads/questions/<?php echo $question['question_image']; ?>"
                                        class="mt-2" style="max-width: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="marks">Marks</label>
                                    <input type="number" class="form-control" id="marks" name="marks"
                                        value="<?php echo $question ? $question['marks'] : '1'; ?>" required min="1">
                                </div>
                            </div>
                     
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="difficulty_level">Difficulty Level</label>
                                <select class="form-control" id="difficulty_level" name="difficulty_level" required>
                                    <option value="easy"
                                        <?php echo ($question && $question['difficulty_level'] == 'easy') ? 'selected' : ''; ?>>
                                        Easy</option>
                                    <option value="medium"
                                        <?php echo (!$question || $question['difficulty_level'] == 'medium') ? 'selected' : ''; ?>>
                                        Medium</option>
                                    <option value="hard"
                                        <?php echo ($question && $question['difficulty_level'] == 'hard') ? 'selected' : ''; ?>>
                                        Hard</option>
                                </select>
                            </div>
                        </div>
              
                     <div class="col-md-3">
                <div class="form-group">
                    <label for="asked_date">Asked Date (Optional)</label>
                    <input type="date" class="form-control" id="asked_date" name="asked_date"
                        value="<?php echo $question ? $question['asked_date'] : ''; ?>">
                </div>
         </div>
                </div>
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description"
                        rows="3"><?php echo $question ? htmlspecialchars($question['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="option_count">Number of Options</label>
                    <select class="form-control" id="option_count" name="option_count" required>
                        <option value="4" <?php echo (!$question || count($options) == 4) ? 'selected' : ''; ?>>
                            4 Options</option>
                        <option value="5" <?php echo ($question && count($options) == 5) ? 'selected' : ''; ?>>5
                            Options</option>
                    </select>
                </div>

                <div id="options-container">
                    <?php 
                                $maxOptions = 5;
                                for ($i = 0; $i < $maxOptions; $i++): 
                                    $option = isset($options[$i]) ? $options[$i] : null;
                                ?>
                    <div class="option-group" id="option-group-<?php echo $i; ?>">
                        <div class="form-group">
                            <label>Option <?php echo $i + 1; ?></label>
                            <div class="d-flex align-items-center">
                                <textarea class="form-control mr-2" name="options[<?php echo $i; ?>][text]" rows="2"
                                    <?php echo $i < 4 ? 'required' : ''; ?>><?php echo $option ? htmlspecialchars($option['option_text']) : ''; ?></textarea>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correct_option"
                                        value="<?php echo $i; ?>"
                                        <?php echo ($option && $option['is_correct']) ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Correct</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Option <?php echo $i + 1; ?> Image (Optional)</label>
                            <input type="file" class="form-control" name="option_images[]" accept="image/*">
                            <?php if ($option && $option['option_image']): ?>
                            <img src="../../uploads/options/<?php echo $option['option_image']; ?>" class="mt-2"
                                style="max-width: 150px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1" <?php echo ($question && $question['status'] == 1) ? 'selected' : ''; ?>>
                            Active
                        </option>
                        <option value="0" <?php echo ($question && $question['status'] == 0) ? 'selected' : ''; ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <a href="details.php" class="btn btn-danger">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</div>


<?php require_once '../include/footer.php'; ?>
<script>
$(document).ready(function() {
    // Handle option count change
    $('#option_count').change(function() {
        var count = $(this).val();
        $('.option-group').each(function(index) {
            if (index < count) {
                $(this).show();
                $(this).find('textarea').attr('required', 'required');
            } else {
                $(this).hide();
                $(this).find('textarea').removeAttr('required');
            }
        });
    }).trigger('change');

    // Form submission
    $('#questionForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '../../api/questions/' + (formData.get('id')>0 ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Question saved successfully');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Error saving question');
                }
            },
            error: function() {
                toastr.error('Error saving question');
            }
        });
    });
});
</script>
