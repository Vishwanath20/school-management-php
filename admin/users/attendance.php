<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Get current date
$currentDate = date('Y-m-d');
$selectedDate = isset($_GET['date']) ? $_GET['date'] : $currentDate;

// Fetch all admin users
$stmt = $pdo->query("SELECT * FROM admin_users ORDER BY name ASC");
$users = $stmt->fetchAll();

// Fetch attendance for selected date
$stmt = $pdo->prepare("
    SELECT user_id, status, remarks, in_time, out_time 
    FROM attendance 
    WHERE date = ?
");
$stmt->execute([$selectedDate]);
$attendance = $stmt->fetchAll(PDO::FETCH_GROUP);
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Staff Attendance Management</h4>
                    <div style="display:flex;">
                        <a class="btn btn-info" href="attendancedetails.php">List</a>
                        <input type="date" id="attendanceDate" class="form-control" 
                               value="<?php echo $selectedDate; ?>" 
                               max="<?php echo $currentDate; ?>">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Status</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Remarks</th>
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
                                        <select class="form-select attendance-status" 
                                                data-user-id="<?php echo $user['id']; ?>">
                                            <option value="">Select Status</option>
                                            <option value="present" <?php echo isset($attendance[$user['id']][0]) && $attendance[$user['id']][0]['status'] == 'present' ? 'selected' : ''; ?>>Present</option>
                                            <option value="absent" <?php echo isset($attendance[$user['id']]) && $attendance[$user['id']][0]['status'] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                                            <option value="leave" <?php echo isset($attendance[$user['id']]) && $attendance[$user['id']][0]['status'] == 'leave' ? 'selected' : ''; ?>>Leave</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control in-time" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo isset($attendance[$user['id']][0]) ? $attendance[$user['id']][0]['in_time'] : ''; ?>">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control out-time" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               value="<?php echo isset($attendance[$user['id']][0]) ? $attendance[$user['id']][0]['out_time'] : ''; ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control remarks" 
                                               data-user-id="<?php echo $user['id']; ?>"
                                               placeholder="Add remarks"
                                               value="<?php echo isset($attendance[$user['id']][0]) ? $attendance[$user['id']][0]['remarks'] : ''; ?>">
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm save-attendance" 
                                                data-user-id="<?php echo $user['id']; ?>">
                                            <i class="mdi mdi-content-save"></i> Save
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
    // Handle date change
    $('#attendanceDate').on('change', function() {
        window.location.href = 'attendance.php?date=' + $(this).val();
    });

    // Handle attendance save
    $('.save-attendance').on('click', function() {
        var userId = $(this).data('user-id');
        var status = $('.attendance-status[data-user-id="' + userId + '"]').val();
        var inTime = $('.in-time[data-user-id="' + userId + '"]').val();
        var outTime = $('.out-time[data-user-id="' + userId + '"]').val();
        var remarks = $('.remarks[data-user-id="' + userId + '"]').val();
        var date = $('#attendanceDate').val();

        $.ajax({
            url: '../../api/users/save-attendance.php',
            type: 'POST',
            data: {
                user_id: userId,
                date: date,
                status: status,
                in_time: inTime,
                out_time: outTime,
                remarks: remarks
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.status === 'success') {
                    toastr.success('Attendance saved successfully!');
                } else {
                    toastr.error(response.message || 'Failed to save attendance!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>