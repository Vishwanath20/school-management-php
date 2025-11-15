<?php
require_once('../../config/database.php');
include('../include/header.php');
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Exam List</h4>
                        <a href="manage.php" class="btn btn-primary">Create New Exam</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-control" id="courseFilter">
                                <option value="">Select Course</option>
                                <?php
                                       $stmt = $pdo->query("SELECT id, title FROM courses WHERE status = 1");
                                        while ($row = $stmt->fetch()) {
                                            echo "<option value='" . $row['id'] . "' >" . htmlspecialchars($row['title']) . "</option>";
                                        }
                                        ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="batchFilter">
                                <option value="">Select Batch</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filter by Date">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Exam Title">
                        </div>
                    </div>

                    <!-- Exams Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Date</th>
                                    <th>Duration (mins)</th>
                                    <th>Total Questions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="examTableBody">
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="dataTables_info" id="pageInfo"></div>
                        <div class="dataTables_paginate">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Load batches when course is selected
    $('#courseFilter').change(function() {
        const courseId = $(this).val();
        if (courseId) {
            $.get('../../api/batches/get_batches_by_course.php', {
                course_id: courseId
            }, function(data) {
                console.log(data)
                let options = '<option value="">Select Batch</option>';
                data.batches.forEach(batch => {
                    options += `<option value="${batch.id}">${batch.name}</option>`;
                });
                $('#batchFilter').html(options);
            });
        } else {
            $('#batchFilter').html('<option value="">Select Batch</option>');
        }
        loadExams();
    });

    // Load exams when filters change
    $('#batchFilter, #dateFilter').change(loadExams);
    $('#searchInput').on('input', debounce(loadExams, 500));

    function loadExams(page = 1) {
        const filters = {
            course_id: $('#courseFilter').val(),
            batch_id: $('#batchFilter').val(),
            date: $('#dateFilter').val(),
            search: $('#searchInput').val(),
            page: page
        };

        $.get('../../api/exams/get_exams.php', filters, function(response) {
            if (response.success) {
                displayExams(response.data);
                updatePagination(response.pagination);
            } else {
                toastr.error('Error loading exams');
            }
        });
    }

    function displayExams(exams) {
        let html = '';
        exams.forEach(exam => {
            html += `
                <tr>
                    <td>${exam.id}</td>
                    <td>${exam.title}</td>
                    <td>${exam.course_title}</td>
                    <td>${exam.batch_name}</td>
                    <td>${exam.exam_date}</td>
                    <td>${exam.duration}</td>
                    <td>${exam.total_questions}</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle" type="checkbox" 
                                data-id="${exam.id}" ${exam.status == 1 ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <a href="edit.php?id=${exam.id}" class="btn btn-sm btn-info"> <i class="mdi mdi-pencil"></i></a>
                        <a href="candidate-report.php?id=${exam.id}" class="btn btn-sm btn-info"> <i class="mdi mdi-eye"></i></a>
                        <button class="btn btn-sm btn-danger delete-exam" data-id="${exam.id}">  <i class="mdi mdi-delete"></i></button>
                    </td>
                </tr>
            `;
        });
        $('#examTableBody').html(html);
    }

    function updatePagination(pagination) {
        let html = '';
        for (let i = 1; i <= pagination.total_pages; i++) {
            html += `
                <li class="page-item ${pagination.current_page == i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        $('#pagination').html(html);
        $('#pageInfo').text(`Showing ${pagination.start} to ${pagination.end} of ${pagination.total} entries`);

        // Pagination click handler
        $('.page-link').click(function(e) {
            e.preventDefault();
            loadExams($(this).data('page'));
        });
    }

    // Status toggle handler
    $(document).on('change', '.status-toggle', function() {
        const examId = $(this).data('id');
        const status = $(this).prop('checked') ? 1 : 0;

        $.post('../../api/exams/update_status.php', {
            exam_id: examId,
            status: status
        }, function(response) {
            if (response.success) {
                toastr.success('Status updated successfully');
            } else {
                toastr.error('Error updating status');
                // Revert toggle if update failed
                $(this).prop('checked', !status);
            }
        });
    });

    // Delete exam handler
    $(document).on('click', '.delete-exam', function() {
        const examId = $(this).data('id');
        if (confirm('Are you sure you want to delete this exam?')) {
            $.post('../../api/exams/delete.php', {
                exam_id: examId
            }, function(response) {
                if (response.success) {
                    toastr.success('Exam deleted successfully');
                    loadExams();
                } else {
                    toastr.error('Error deleting exam');
                }
            });
        }
    });

    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Initial load
    loadExams();
});
</script>