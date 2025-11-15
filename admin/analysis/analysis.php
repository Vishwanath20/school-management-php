<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Revenue Analysis
$revenueData = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
           SUM(amount) as total_revenue,
           COUNT(*) as order_count
    FROM orders
    WHERE status = 'completed'
    GROUP BY month
    ORDER BY month DESC
    LIMIT 12
")->fetchAll();

// Course Enrollment Stats
$courseStats = $pdo->query("
    SELECT c.title, COUNT(o.id) as enrollments
    FROM courses c
    LEFT JOIN orders o ON c.id = o.course_id AND o.status = 'completed'
    GROUP BY c.id
    ORDER BY enrollments DESC
    LIMIT 10
")->fetchAll();

// Enquiry Source Analysis
$enquiryStats = $pdo->query("
    SELECT source, COUNT(*) as count
    FROM contact_enquiries
    GROUP BY source
")->fetchAll();

// Attendance Overview
$attendanceStats = $pdo->query("
    SELECT DATE_FORMAT(date, '%Y-%m-%d') as date,
           COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
           COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_count,
           COUNT(CASE WHEN status = 'leave' THEN 1 END) as leave_count
    FROM attendance
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY date
    ORDER BY date
")->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-dark">Revenue Analysis</h4>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-dark">Course Enrollments</h4>
                    <canvas id="courseChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-dark">Enquiry Sources</h4>
                    <canvas id="enquiryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-dark">Attendance Overview (Last 30 Days)</h4>
                    <canvas id="attendanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart - Changed to column bar chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column(array_reverse($revenueData), 'month')); ?>,
        datasets: [{
            label: 'Monthly Revenue (₹)',
            data: <?php echo json_encode(array_column(array_reverse($revenueData), 'total_revenue')); ?>,
            backgroundColor: '#4CAF50',
            borderWidth: 1
        }, {
            label: 'Orders Count',
            data: <?php echo json_encode(array_column(array_reverse($revenueData), 'order_count')); ?>,
            backgroundColor: '#2196F3',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Course Enrollment Chart - Changed to vertical bar chart
const courseCtx = document.getElementById('courseChart').getContext('2d');
new Chart(courseCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($courseStats, 'title')); ?>,
        datasets: [{
            label: 'Enrollments',
            data: <?php echo json_encode(array_column($courseStats, 'enrollments')); ?>,
            backgroundColor: '#673AB7',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Enquiry Source Chart
const enquiryCtx = document.getElementById('enquiryChart').getContext('2d');
new Chart(enquiryCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($enquiryStats, 'source')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($enquiryStats, 'count')); ?>,
            backgroundColor: ['#FF9800', '#E91E63', '#009688', '#795548', '#9C27B0']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Attendance Chart - Enhanced version
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($attendanceStats, 'date')); ?>,
        datasets: [{
            label: 'Present',
            data: <?php echo json_encode(array_column($attendanceStats, 'present_count')); ?>,
            backgroundColor: 'rgba(76, 175, 80, 0.8)',
            borderColor: '#4CAF50',
            borderWidth: 1,
            borderRadius: 4
        }, {
            label: 'Absent',
            data: <?php echo json_encode(array_column($attendanceStats, 'absent_count')); ?>,
            backgroundColor: 'rgba(244, 67, 54, 0.8)',
            borderColor: '#F44336',
            borderWidth: 1,
            borderRadius: 4
        }, {
            label: 'Leave',
            data: <?php echo json_encode(array_column($attendanceStats, 'leave_count')); ?>,
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: '#FFC107',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            x: {
                stacked: true,
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

