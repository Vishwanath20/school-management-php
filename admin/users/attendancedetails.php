<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Get filter parameters
$filterType = isset($_GET['filter']) ? $_GET['filter'] : 'monthly';
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Build query based on filter
$query = "
    SELECT 
        a.*, 
        u.name as user_name,
        u.type as user_type,
        u.profile_pic
    FROM attendance a
    JOIN admin_users u ON a.user_id = u.id
    WHERE 1=1
";

if ($filterType == 'monthly') {
    $query .= " AND DATE_FORMAT(a.date, '%Y-%m') = ?";
    $params = [$month];
} else if ($filterType == 'weekly') {
    $query .= " AND a.date BETWEEN ? AND ?";
    $params = [$startDate, $endDate];
}

$query .= " ORDER BY a.date DESC, u.name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$attendances = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Attendance Details</h4>
                        <div class="d-flex gap-2">
                            <select id="filterType" class="form-select">
                                <option value="monthly" <?php echo $filterType == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                <option value="weekly" <?php echo $filterType == 'weekly' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                            
                            <div id="monthFilter" class="<?php echo $filterType == 'weekly' ? 'd-none' : ''; ?>">
                                <input type="month" id="month" class="form-control" 
                                       value="<?php echo $month; ?>" max="<?php echo date('Y-m'); ?>">
                            </div>
                            
                            <div id="dateRangeFilter" class="d-flex gap-2 <?php echo $filterType == 'monthly' ? 'd-none' : ''; ?>">
                                <input type="date" id="startDate" class="form-control" 
                                       value="<?php echo $startDate; ?>" max="<?php echo date('Y-m-d'); ?>">
                                <input type="date" id="endDate" class="form-control" 
                                       value="<?php echo $endDate; ?>" max="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <button id="exportExcel" class="btn btn-success">
                                <i class="mdi mdi-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Staff Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Working Hours</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendances as $attendance): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($attendance['date'])); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../uploads/profile_pics/<?php echo $attendance['profile_pic'] ?: 'default.png'; ?>" 
                                                 class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                                            <?php echo htmlspecialchars($attendance['user_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($attendance['user_type']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $attendance['status'] == 'present' ? 'bg-success' : 
                                                ($attendance['status'] == 'absent' ? 'bg-danger' : 'bg-warning'); 
                                        ?>">
                                            <?php echo ucfirst($attendance['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $attendance['in_time'] ? date('h:i A', strtotime($attendance['in_time'])) : '-'; ?></td>
                                    <td><?php echo $attendance['out_time'] ? date('h:i A', strtotime($attendance['out_time'])) : '-'; ?></td>
                                    <td>
                                        <?php
                                        if ($attendance['in_time'] && $attendance['out_time']) {
                                            $in = strtotime($attendance['in_time']);
                                            $out = strtotime($attendance['out_time']);
                                            $hours = round(($out - $in) / 3600, 2);
                                            echo $hours . ' hrs';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($attendance['remarks'] ?: '-'); ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    function updateFilters() {
        var filterType = $('#filterType').val();
        var params = new URLSearchParams();
        params.append('filter', filterType);
        
        if (filterType === 'monthly') {
            params.append('month', $('#month').val());
            $('#monthFilter').removeClass('d-none');
            $('#dateRangeFilter').addClass('d-none');
        } else {
            params.append('start_date', $('#startDate').val());
            params.append('end_date', $('#endDate').val());
            $('#monthFilter').addClass('d-none');
            $('#dateRangeFilter').removeClass('d-none');
        }
        
        window.location.href = 'attendancedetails.php?' + params.toString();
    }

    $('#filterType, #month, #startDate, #endDate').on('change', updateFilters);

    // Export to Excel
    $('#exportExcel').on('click', function() {
        var wb = XLSX.utils.table_to_book(document.getElementById('attendanceTable'), {sheet: "Attendance"});
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
        a.download = 'attendance_report_' + new Date().toISOString().split('T')[0] + '.xlsx';
        a.click();
    });
});
</script>