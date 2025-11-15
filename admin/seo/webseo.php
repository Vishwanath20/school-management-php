<?php
require_once('../../config/database.php');
include('../include/header.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $page_url = $_POST['page_url'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $og_title = $_POST['og_title'];
    $og_description = $_POST['og_description'];
    $canonical_url = $_POST['canonical_url'];
    $robots_tag = $_POST['robots_tag'];
    $schema_markup = $_POST['schema_markup'];

    // Handle og_image upload
    $og_image = '';
    if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] == 0) {
        $target_dir = "../../uploads/seo/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $og_image = time() . '_' . basename($_FILES['og_image']['name']);
        move_uploaded_file($_FILES['og_image']['tmp_name'], $target_dir . $og_image);
    }

    // Insert or update SEO settings
    $stmt = $pdo->prepare("INSERT INTO seo_settings 
        (page_url, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, canonical_url, robots_tag, schema_markup) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        meta_keywords = VALUES(meta_keywords),
        og_title = VALUES(og_title),
        og_description = VALUES(og_description),
        og_image = COALESCE(NULLIF(VALUES(og_image), ''), og_image),
        canonical_url = VALUES(canonical_url),
        robots_tag = VALUES(robots_tag),
        schema_markup = VALUES(schema_markup)");

    $stmt->execute([$page_url, $meta_title, $meta_description, $meta_keywords, $og_title, $og_description, $og_image, $canonical_url, $robots_tag, $schema_markup]);
}

// Fetch existing SEO settings
$seo_settings = $pdo->query("SELECT * FROM seo_settings ORDER BY created_at DESC")->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-dark">SEO Settings</h4>
                    <p class="card-description text-muted">Manage SEO settings for different pages</p>

                    <form method="POST" enctype="multipart/form-data" class="forms-sample">
                    <input type="hidden" name="id" id="seo_id">
                    <div class="form-group">
                            <label for="page_url">Page URL</label>
                            <input type="text" class="form-control" id="page_url" name="page_url" placeholder="e.g., /about-us" required>
                        </div>

                        <div class="form-group">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="80">
                            <small class="text-muted">Recommended length: 50-60 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="meta_description">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="180"></textarea>
                            <small class="text-muted">Recommended length: 150-160 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                        </div>

                        <div class="form-group">
                            <label for="og_title">OG Title</label>
                            <input type="text" class="form-control" id="og_title" name="og_title">
                        </div>

                        <div class="form-group">
                            <label for="og_description">OG Description</label>
                            <textarea class="form-control" id="og_description" name="og_description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="og_image">OG Image</label>
                            <input type="file" class="form-control" id="og_image" name="og_image" accept="image/*">
                            <small class="text-muted">Recommended size: 1200x630 pixels</small>
                        </div>

                        <div class="form-group">
                            <label for="canonical_url">Canonical URL</label>
                            <input type="url" class="form-control" id="canonical_url" name="canonical_url">
                        </div>

                        <div class="form-group">
                            <label for="robots_tag">Robots Tag</label>
                            <select class="form-control" id="robots_tag" name="robots_tag">
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, follow">NoIndex, Follow</option>
                                <option value="index, nofollow">Index, NoFollow</option>
                                <option value="noindex, nofollow">NoIndex, NoFollow</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="schema_markup">Schema Markup</label>
                            <textarea class="form-control" id="schema_markup" name="schema_markup" rows="20"></textarea>
                            <small class="text-muted">Enter JSON-LD schema markup</small>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Settings List -->
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Existing SEO Settings</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Page URL</th>
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($seo_settings as $seo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($seo['page_url']); ?></td>
                                        <td><?php echo htmlspecialchars($seo['meta_title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($seo['meta_description'], 0, 100)) . '...'; ?></td>
                                        <td><?php echo date('d M Y', strtotime($seo['updated_at'] ?: $seo['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info edit-seo" data-id="<?php echo $seo['id']; ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-seo" data-id="<?php echo $seo['id']; ?>">Delete</button>
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
    // Character count indicators
    $('#meta_title').on('input', function() {
        let remaining = 80 - $(this).val().length;
        $(this).next('small').text(`Remaining characters: ${remaining}`);
    });
 // Handle edit button click
 $('.edit-seo').on('click', function() {
        var seoId = $(this).data('id');
        
        // Fetch SEO data
        $.ajax({
            url: '../../api/seo/get-seo.php',
            type: 'GET',
            data: { id: seoId },
            success: function(response) {
                if (response.success) {
                    var seo = response.data;
                    $('#page_url').val(seo.page_url);
                    $('#meta_title').val(seo.meta_title);
                    $('#meta_description').val(seo.meta_description);
                    $('#meta_keywords').val(seo.meta_keywords);
                    $('#og_title').val(seo.og_title);
                    $('#og_description').val(seo.og_description);
                    $('#canonical_url').val(seo.canonical_url);
                    $('#robots_tag').val(seo.robots_tag);
                    $('#schema_markup').val(seo.schema_markup);
                    
                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $(".forms-sample").offset().top - 100
                    }, 500);
                } else {
                    toastr.error('Failed to fetch SEO data');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });


    $('.delete-seo').on('click', function() {
    var seoId = $(this).data('id');
    if (confirm('Are you sure you want to delete this SEO setting?')) {
        $.ajax({
            url: '../../api/seo/delete-seo.php',
            type: 'POST',
            data: { id: seoId },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    }
});
    $('#meta_description').on('input', function() {
        let remaining = 180 - $(this).val().length;
        $(this).next('small').text(`Remaining characters: ${remaining}`);
    });
});
</script>

