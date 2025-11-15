<?php
require_once('../../config/database.php');
include('../include/header.php');

// Get exam ID from URL
$exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($exam_id == 0) {
    echo "<div class='content-wrapper'><div class='card'><div class='card-body'><h4 class='card-title'>Exam ID not provided.</h4></div></div></div>";
    include('../include/footer.php');
    exit();
}

// Fetch exam details
try {
    $stmt = $pdo->prepare("SELECT e.title as exam_title, c.title as course_title, b.name as batch_name, e.exam_date, e.duration 
                           FROM exams e
                           JOIN courses c ON e.course_id = c.id
                           JOIN batches b ON e.batch_id = b.id
                           WHERE e.id = ?");
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        echo "<div class='content-wrapper'><div class='card'><div class='card-body'><h4 class='card-title'>Exam not found.</h4></div></div></div>";
        include('../include/footer.php');
        exit();
    }

    // Fetch total questions for this exam
    $total_questions_stmt = $pdo->prepare("SELECT COUNT(id) as total_questions FROM exam_questions WHERE exam_id = ?");
    $total_questions_stmt->execute([$exam_id]);
    $total_questions_row = $total_questions_stmt->fetch(PDO::FETCH_ASSOC);
    $total_questions = $total_questions_row['total_questions'];

    // Fetch candidate results for this exam
    $candidate_results_stmt = $pdo->prepare("SELECT u.name as candidate_name, u.email as candidate_email, 
                                                    er.obtained_marks as score, er.total_marks, 
                                                    CASE 
                                                        WHEN er.total_marks > 0 THEN (er.obtained_marks * 100.0 / er.total_marks) 
                                                        ELSE 0 
                                                    END as percentage, 
                                                    er.status
                                             FROM exam_results er
                                             JOIN users u ON er.user_id = u.id
                                             WHERE er.exam_id = ? ORDER BY er.obtained_marks DESC");
    $candidate_results_stmt->execute([$exam_id]);
    $candidate_results = $candidate_results_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='content-wrapper'><div class='card'><div class='card-body'><h4 class='card-title'>Database Error: " . htmlspecialchars($e->getMessage()) . "</h4></div></div></div>";
    include('../include/footer.php');
    exit();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Candidate Report for Exam:
                            <?php echo htmlspecialchars($exam['exam_title']); ?></h4>
                        <a href="details.php" class="btn btn-secondary">Back to Exam List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-dark">Exam Details:</h5>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($exam['course_title']); ?></p>
                        <p><strong>Batch:</strong> <?php echo htmlspecialchars($exam['batch_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($exam['exam_date']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($exam['duration']); ?> minutes</p>
                        <p><strong>Total Questions:</strong> <?php echo htmlspecialchars($total_questions); ?>
                        </p>
                    </div>

                    <h5 class="text-dark">Candidate Results:</h5>
                    <?php if (count($candidate_results) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Candidate Name</th>
                                    <th>Email</th>
                                    <th>Score</th>
                                    <th>Total Marks</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidate_results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['candidate_name']); ?></td>
                                    <td><?php echo htmlspecialchars($result['candidate_email']); ?></td>
                                    <td><?php echo htmlspecialchars($result['score']); ?></td>
                                    <td><?php echo htmlspecialchars($result['total_marks']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($result['percentage'], 2)); ?>%</td>
                                    <td><?php echo htmlspecialchars($result['status']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>No candidate results found for this exam.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>
