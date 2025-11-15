<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Get current month and year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Fetch all admin users with their salary details
$stmt = $pdo->prepare("
    SELECT 
        u.*,
        s.basic_salary,
        s.allowances,
        s.deductions,
        s.net_salary,
        s.payment_status,
        s.payment_date,
        s.remarks
    FROM admin_users u
    LEFT JOIN salaries s ON u.id = s.user_id AND s.month = ?
    ORDER BY u.name ASC
");
$stmt->execute([$currentMonth]);
$users = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Salary Management</h4>
                        <div class="d-flex gap-2">
                            <input type="month" id="salaryMonth" class="form-control" 
                                   value="<?php echo $currentMonth; ?>" 
                                   max="<?php echo date('Y-m'); ?>">
                            <button id="exportExcel" class="btn btn-success">
                                <i class="mdi mdi-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="salaryTable">
                            <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Basic Salary</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../uploads/profile_pics/<?php echo $user['profile_pic'] ?: 'default.png'; ?>" 
                                                 class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                            <div>
                                                <?php echo htmlspecialchars($user['name']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($user['type']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control basic-salary" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo $user['basic_salary'] ?: ''; ?>"
                                               placeholder="Enter basic salary">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control allowances" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo $user['allowances'] ?: ''; ?>"
                                               placeholder="Enter allowances">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control deductions" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo $user['deductions'] ?: ''; ?>"
                                               placeholder="Enter deductions">
                                    </td>
                                    <td class="net-salary">
                                        ₹<?php echo $user['net_salary'] ?: '0.00'; ?>
                                    </td>
                                    <td>
                                        <select class="form-select payment-status" 
                                                data-user-id="<?php echo $user['id']; ?>">
                                            <option value="pending" <?php echo $user['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="paid" <?php echo $user['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control payment-date" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo $user['payment_date']; ?>"
                                               <?php echo $user['payment_status'] == 'pending' ? 'disabled' : ''; ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm save-salary" 
                                                data-user-id="<?php echo $user['id']; ?>">
                                            <i class="mdi mdi-content-save"></i> Save
                                        </button>
                                        <button class="btn btn-info btn-sm print-slip" 
                                                data-user="<?php echo htmlspecialchars(json_encode($user)); ?>">
                                            <i class="mdi mdi-printer"></i> Slip
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

<!-- Salary Slip Modal -->
<div class="modal fade" id="salarySlipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="width:100%;" >
            <div class="modal-header">
                <h5 class="modal-title text-dark" >Salary Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="salarySlipContent">
                <!-- Salary slip content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="printSlip">
                    <i class="mdi mdi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.0/jQuery.print.min.js"></script>

<script>
$(document).ready(function() {
    // Handle month change
    $('#salaryMonth').on('change', function() {
        window.location.href = 'salary.php?month=' + $(this).val();
    });

    // Calculate net salary
    function calculateNetSalary(row) {
        var basic = parseFloat(row.find('.basic-salary').val()) || 0;
        var allowances = parseFloat(row.find('.allowances').val()) || 0;
        var deductions = parseFloat(row.find('.deductions').val()) || 0;
        var net = basic + allowances - deductions;
        row.find('.net-salary').text('₹' + net.toFixed(2));
        return net;
    }

    // Update net salary on input change
    $('.basic-salary, .allowances, .deductions').on('input', function() {
        calculateNetSalary($(this).closest('tr'));
    });

    // Handle payment status change
    $('.payment-status').on('change', function() {
        var paymentDate = $(this).closest('tr').find('.payment-date');
        if ($(this).val() === 'paid') {
            paymentDate.prop('disabled', false).val(new Date().toISOString().split('T')[0]);
        } else {
            paymentDate.prop('disabled', true).val('');
        }
    });

    // Handle salary save
    $('.save-salary').on('click', function() {
        var row = $(this).closest('tr');
        var userId = $(this).data('user-id');
        var month = $('#salaryMonth').val();
        
        $.ajax({
            url: '../../api/users/save-salary.php',
            type: 'POST',
            data: {
                user_id: userId,
                month: month,
                basic_salary: row.find('.basic-salary').val(),
                allowances: row.find('.allowances').val(),
                deductions: row.find('.deductions').val(),
                net_salary: calculateNetSalary(row),
                payment_status: row.find('.payment-status').val(),
                payment_date: row.find('.payment-date').val(),
                remarks: ''
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.status === 'success') {
                    toastr.success('Salary details saved successfully!');
                    window.location.reload();
                } else {
                    toastr.error(response.message || 'Failed to save salary details!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle salary slip generation
    $('.print-slip').on('click', function() {

        var user = $(this).data('user');
        var month = $('#salaryMonth').val();
        var monthName = new Date(month + '-01').toLocaleString('default', { month: 'long' });
        var year = month.split('-')[0];
        
        var slipHtml = `
            <div class="salary-slip">
                <div class="text-center mb-4">
                    <h4 class="text-dark">Margdarshan Institute Sarangarh</h4>
                    <h5 class="text-dark">Salary Slip - ${monthName} ${year}</h5>
                </div>
                <div class="employee-details mb-4">
                    <p><strong>Employee Name:</strong> ${user.name}</p>
                    <p><strong>Designation:</strong> ${user.type}</p>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Basic Salary</th>
                        <td class="text-end">₹${user.basic_salary || '0.00'}</td>
                    </tr>
                    <tr>
                        <th>Allowances</th>
                        <td class="text-end">₹${user.allowances || '0.00'}</td>
                    </tr>
                    <tr>
                        <th>Deductions</th>
                        <td class="text-end">₹${user.deductions || '0.00'}</td>
                    </tr>
                    <tr class="table-active">
                        <th>Net Salary</th>
                        <td class="text-end"><strong>₹${user.net_salary || '0.00'}</strong></td>
                    </tr>
                </table>
                <div class="mt-4">
                    <p><strong>Payment Status:</strong> ${user.payment_status ? user.payment_status.toUpperCase() : 'PENDING'}</p>
                    <p><strong>Payment Date:</strong> ${user.payment_date || '-'}</p>
                </div>
            </div>
        `;
        
        $('#salarySlipContent').html(slipHtml);
        $('#salarySlipModal').modal('show');
    });

    // Handle print button click
    $('#printSlip').on('click', function() {
        $('#salarySlipContent').print();
    });

    // Handle Excel export
    $('#exportExcel').on('click', function() {
        var wb = XLSX.utils.table_to_book(document.getElementById('salaryTable'), {sheet: "Salary"});
        var wbout = XLSX.write(wb, {bookType:'xlsx', type: 'binary'});

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = 'salary_report_' + $('#salaryMonth').val() + '.xlsx';
        a.click();
    });
});
</script>