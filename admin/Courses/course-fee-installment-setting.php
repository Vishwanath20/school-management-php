<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all active courses
$stmt = $pdo->query("SELECT id, title, price FROM courses WHERE status = 1");
$courses = $stmt->fetchAll();

// Fetch installments if course is selected
$installments = [];
if (isset($_GET['course_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM course_installments WHERE course_id = ? ORDER BY installment_number");
    $stmt->execute([$_GET['course_id']]);
    $installments = $stmt->fetchAll();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Course Fee Installment Settings</h4>
                </div>
                <div class="card-body">
                    <!-- Course Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <select class="form-control" id="courseSelect" onchange="window.location.href='?course_id=' + this.value">
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" 
                                            <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $course['id']) ? 'selected' : ''; ?>
                                            data-price="<?php echo $course['price']; ?>">
                                        <?php echo htmlspecialchars($course['title']); ?> (₹<?php echo $course['price']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php if (isset($_GET['course_id'])): ?>
                        <!-- Installment Form -->
                        <form id="installmentForm">
                            <input type="hidden" name="course_id" value="<?php echo $_GET['course_id']; ?>">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Installment No.</th>
                                            <th>Amount (₹)</th>
                                            <th>Due Days (from enrollment)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="installmentRows">
                                        <?php foreach ($installments as $inst): ?>
                                            <tr>
                                                <td><?php echo $inst['installment_number']; ?></td>
                                                <td>
                                                    <input type="number" class="form-control amount-input" 
                                                           name="amounts[]" value="<?php echo $inst['amount']; ?>" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" 
                                                           name="due_days[]" value="<?php echo $inst['due_days']; ?>" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-info" onclick="addInstallmentRow()">Add Installment</button>
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                            <div class="mt-3">
                                <p>Total Amount: ₹<span id="totalAmount">0</span></p>
                                <p>Course Price: ₹<span id="coursePrice">0</span></p>
                                <p>Difference: ₹<span id="amountDifference">0</span></p>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script>
$(document).ready(function() {
    updateAmounts();
    
    $('#installmentForm').on('submit', function(e) {
        e.preventDefault();
        
        var coursePrice = parseFloat($('#courseSelect option:selected').data('price'));
        var totalAmount = parseFloat($('#totalAmount').text());
        
        if (totalAmount !== coursePrice) {
            toastr.error('Total installment amount must equal course price!');
            return;
        }
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            url: '../../api/courses/save-installments.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#installmentForm').find('button[type="submit"]').prop('disabled', false);
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to save settings!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#installmentForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});

function addInstallmentRow() {
    var rowCount = $('#installmentRows tr').length + 1;
    var newRow = `
        <tr>
            <td>${rowCount}</td>
            <td><input type="number" class="form-control amount-input" name="amounts[]" required></td>
            <td><input type="number" class="form-control" name="due_days[]" required></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                    <i class="mdi mdi-delete"></i>
                </button>
            </td>
        </tr>
    `;
    $('#installmentRows').append(newRow);
}

function removeRow(btn) {
    $(btn).closest('tr').remove();
    updateInstallmentNumbers();
    updateAmounts();
}

function updateInstallmentNumbers() {
    $('#installmentRows tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function updateAmounts() {
    var total = 0;
    $('.amount-input').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    
    var coursePrice = parseFloat($('#courseSelect option:selected').data('price')) || 0;
    var difference = coursePrice - total;
    
    $('#totalAmount').text(total.toFixed(2));
    $('#coursePrice').text(coursePrice.toFixed(2));
    $('#amountDifference').text(difference.toFixed(2));
}

$(document).on('input', '.amount-input', updateAmounts);
</script>