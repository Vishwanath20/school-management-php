<?php 

require_once('../../config/database.php');
include('../include/header.php');
// Fetch course data if editing
$course = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $course = $stmt->fetch();
}
?>

<!-- Include Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
p {
    color: black;
}

li {
    color: black;
}
</style>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $course ? 'Edit Course' : 'Add Course'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="courseForm" class="forms-sample">
                        <?php if ($course): ?>
                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">Course Title<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?php echo $course ? htmlspecialchars($course['title']) : ''; ?>"
                                        placeholder="Enter course title" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category_id">Course Category<span style="color:red;">*</span></label>
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT id, title FROM course_categories WHERE status = 1");
                                        while ($category = $stmt->fetch()) {
                                            $selected = ($course && $course['category_id'] == $category['id']) ? 'selected' : '';
                                            echo "<option value='" . $category['id'] . "' " . $selected . ">" . htmlspecialchars($category['title']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price">Selling Price<span style="color:red;">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price"
                                        value="<?php echo $course ? $course['price'] : ''; ?>"
                                        placeholder="Enter course price" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="original_price">Original Price<span style="color:red;">*</span></label>
                                    <input type="number" class="form-control" id="original_price" name="original_price"
                                        value="<?php echo $course ? $course['original_price'] : ''; ?>"
                                        placeholder="Enter original price" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="is_price_display">Is Price Display</label>
                                    <select class="form-control" name="is_price_display" id="is_price_display" required>
                                        <option value="1" <?php echo ($course && $course['is_price_display'] == 1) ? 'selected' : ''; ?>>Yes</option>
                                        <option value="0" <?php echo ($course && $course['is_price_display'] == 0) ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="<?php echo $course ? date('Y-m-d', strtotime($course['start_date'])) : ''; ?>"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="<?php echo $course ? date('Y-m-d', strtotime($course['end_date'])) : ''; ?>"
                                        required>
                                </div>
                            </div> -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course Thumbnail<span style="color:red;">*</span></label>

                                    <input type="file" name="thumbnail" id="thumbnail"
                                        class="file-upload-default upload-thumbnail" accept="image/*"
                                        <?php echo !$course ? 'required' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Thumbnail">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                type="button">Browse</button>
                                        </span>

                                    </div>

                                    <small class="form-text text-muted">Recommended:(Width:720pxHeight:360px)</small>
                                    <?php if ($course && $course['thumbnail']): ?>
                                    <div class="mb-2">
                                        <img src="../../uploads/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>"
                                            alt="Current thumbnail" style="max-width: 200px;">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Curriculum PDF</label>

                                    <input type="file" name="curriculum_pdf" id="curriculum_pdf"
                                        class="file-upload-default uploadpdf" accept=".pdf"
                                       >
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Curriculum PDF">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse-pdf btn btn-primary"
                                                type="button">Browse</button>
                                        </span>
                                    </div>
                                    <?php if ($course && $course['curriculum_pdf']): ?>

                                    <a href="../../uploads/courses/curriculum/<?php echo htmlspecialchars($course['curriculum_pdf']); ?>"
                                        target="_blank">View Current PDF</a>

                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>YouTube Video Links</label>
                                    <div id="video-links-container">
                                        <?php
                                        if ($course && $course['video_links']) {
                                            $videoLinks = json_decode($course['video_links'], true);
                                            foreach ($videoLinks as $index => $link) {
                                                echo '<div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="video_links[]" 
                                                               value="' . htmlspecialchars($link) . '" placeholder="Enter YouTube video link">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger remove-video-link">Remove</button>
                                                        </div>
                                                    </div>';
                                            }
                                        }
                                        ?>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="video_links[]"
                                                placeholder="Enter YouTube video link">
                                            <div class="input-group-append">
                                                <button type="button"
                                                    class="btn btn-danger remove-video-link">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-info" id="add-video-link">Add Another Video
                                        Link</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Course Description<span style="color:red;">*</span></label>
                                    <textarea class="form-control" id="description" name="description"
                                        required><?php echo $course ? $course['description'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-primary mr-2"><?php echo $course ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<!-- Include Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote
    $('#description').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // File upload handling
    $('.file-upload-browse').on('click', function() {
        var file = $(this).parents().find('.upload-thumbnail');
        file.trigger('click');
    });

    $('.upload-thumbnail').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents('.form-group').find('.file-upload-info').val(fileName);
    });


    $('.file-upload-browse-pdf').on('click', function() {
        var file = $(this).parents().find('.uploadpdf');
        file.trigger('click');
    });

    $('.uploadpdf').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents('.form-group').find('.file-upload-info').val(fileName);
    });
    // Form submission
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();

        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/courses/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#courseForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Course ' + (isEdit ? 'updated' :
                        'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' :
                        'add') + ' course!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#courseForm').find('button[type="submit"]').prop('disabled', false);

                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    toastr.error(errorResponse.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });


    // Handle video links
    $('#add-video-link').on('click', function() {
        var newVideoLink = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="video_links[]" 
                       placeholder="Enter YouTube video link">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-video-link">Remove</button>
                </div>
            </div>
        `;
        $('#video-links-container').append(newVideoLink);
    });

    $(document).on('click', '.remove-video-link', function() {
        if ($('#video-links-container .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        } else {
            $(this).closest('.input-group').find('input').val('');
        }
    });
});
</script>