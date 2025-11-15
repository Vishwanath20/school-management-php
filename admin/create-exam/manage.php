<?php
require_once('../../config/database.php');
include('../include/header.php');

// Get all courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE status = 1");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Create Exam</h4>
                        <a href="details.php" class="btn btn-primary">List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="forms-sample" id="examForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course">Select Course</label>
                                    <select class="form-control" id="course" name="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php foreach($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="batch">Select Batch</label>
                                    <select class="form-control" id="batch" name="batch_id" required disabled>
                                        <option value="">Select Batch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="examTitle">Exam Title</label>
                                    <input type="text" class="form-control" id="examTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="examDate">Exam Date</label>
                                    <input type="datetime-local" class="form-control" id="examDate" name="exam_date"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration">Duration (in minutes)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" required>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label>Questions</label>
                            <div class="question-filters mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" id="filterSubject">
                                            <option value="">Select Subject</option>
                                        </select>
                                    </div>
                                   
                                    <div class="col-md-3">
                                        <select class="form-control" id="filterDifficulty">
                                            <option value="">Select Difficulty</option>
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                    </div>
                                     <div class="col-md-5">
                                        <input type="text" class="form-control" id="filterTopic" placeholder="Topic"/>
                                          
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-primary"
                                            id="filterQuestions">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <div class="question-list" style="max-height: 400px; overflow-y: auto;">
                                <!-- Questions will be loaded here -->
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Create Exam</button>
                        <button type="button" class="btn btn-light"
                            onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../include/footer.php"; ?>
<script>
$(document).ready(function() {
    // Array to store selected question IDs globally
    let selectedQuestionIds = [];

    // Function to update selectedQuestionIds from checkboxes and localStorage
    function updateSelectedQuestions() {
        selectedQuestionIds = $('input[name="questions[]"]:checked').map(function() {
            return this.value;
        }).get();
        localStorage.setItem('selectedExamQuestions', JSON.stringify(selectedQuestionIds));
    }

    // Load previously selected questions from localStorage on page load
    if (localStorage.getItem('selectedExamQuestions')) {
        selectedQuestionIds = JSON.parse(localStorage.getItem('selectedExamQuestions'));
    }

    // Event listener for checkbox changes
    $(document).on('change', 'input[name="questions[]"]', function() {
        if (this.checked) {
            if (!selectedQuestionIds.includes(this.value)) {
                selectedQuestionIds.push(this.value);
            }
        } else {
            selectedQuestionIds = selectedQuestionIds.filter(id => id !== this.value);
        }
        localStorage.setItem('selectedExamQuestions', JSON.stringify(selectedQuestionIds));
    });

    // Load batches when course is selected
    $('#course').change(function() {
        const courseId = $(this).val();
        const batchSelect = $('#batch');

        if (courseId) {
            $.ajax({
                url: '../../api/batches/get_batches_by_course.php',
                type: 'GET',
                data: {
                    course_id: courseId
                },
                success: function(response) {
                    const data = response
                    console.log(data)
                    batchSelect.empty().append('<option value="">Select Batch</option>');

                    if (data.success) {
                        console.log(data)
                        console.log("............", data.batches)
                        data.batches.forEach(function(batch) {
                            batchSelect.append(
                                `<option value="${batch.id}">${batch.name}</option>`
                            );
                        });
                        batchSelect.prop('disabled', false);
                    } else {
                        toastr.error('Error loading batches');
                        batchSelect.prop('disabled', true);
                    }
                },
                error: function() {
                    toastr.error('Error loading batches');
                    batchSelect.prop('disabled', true);
                }
            });

            // Load subjects for the selected course
            $.ajax({
                url: '../../api/questions/get_subjects.php',
                type: 'POST',
                data: {
                    course_id: courseId
                },
                success: function(response) {
                    const data = response;
                    console.log(data)
                    $('#filterSubject').empty().append(
                        '<option value="">Select Subject</option>');

                    if (data.status === 'success') {
                        data.subjects.forEach(function(subject) {
                            $('#filterSubject').append(
                                `<option value="${subject.id}">${subject.name}</option>`
                            );
                        });
                    }
                }
            });
        } else {
            batchSelect.empty().append('<option value="">Select Batch</option>').prop('disabled', true);
            $('#filterSubject').empty().append('<option value="">Select Subject</option>');
        }
    });

    // Load topics when subject is selected
    //$('#filterSubject').change(function() {
        //const subjectId = $(this).val();

        //if (subjectId) {
           // $.ajax({
               // url: '../../api/questions/get_topics.php',
                //type: 'POST',
                //data: {
                   // subject_id: subjectId
                //},
                //success: function(response) {
                    //const data = JSON.parse(response);
                    //$('#filterTopic').empty().append(
                        //'<option value="">Select Topic</option>');

                    //if (data.status === 'success') {
                        //data.topics.forEach(function(topic) {
                            //$('#filterTopic').append(
                                //`<option value="${topic.id}">${topic.name}</option>`
                            //);
                        //});
                    //}
                //}
            //});
        //} else {
            //$('#filterTopic').empty().append('<option value="">Select Topic</option>');
        //}
   // });

    // Filter questions
    $('#filterQuestions').click(function() {
        const filters = {
            subject_id: $('#filterSubject').val(),
            topic_id: $('#filterTopic').val(),
            difficulty: $('#filterDifficulty').val()
        };

        $.ajax({
            url: '../../api/questions/filter_questions.php',
            type: 'POST',
            data: filters,
            success: function(response) {
                const data = response;
                const questionList = $('.question-list');
                questionList.empty();

                if (data.status === 'success') {
                    data.questions.forEach(function(question) {
                        const isChecked = selectedQuestionIds.includes(String(
                            question.id)) ? 'checked' : '';
                        questionList.append(`
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="questions[]" value="${question.id}" ${isChecked}>
                                        <label class="form-check-label">
                                            <strong>${question.question_text}</strong><br>
                                            <small>Subject: ${question.subject_name} | Topic: ${question.topic_name} | Difficulty: ${question.difficulty_level} | Marks: ${question.marks}</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    questionList.append(
                        '<div class="alert alert-info">No questions found</div>');
                }
            }
        });
    });

    // Submit exam form
    $('#examForm').submit(function(e) {
        e.preventDefault();
        const selectedQuestions = $('input[name="questions[]"]:checked').map(function() {
            return this.value;
        }).get();

        if (selectedQuestions.length === 0) {
            toastr.error('Please select at least one question');
            return;
        }

        const formData = $(this).serializeArray();
        formData.push({
            name: 'questions',
            value: selectedQuestions
        });

        $.ajax({
            url: '../../api/exams/create.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const data = response
                if (data.status === 'success') {
                    toastr.success('Exam created successfully');
                    // Clear localStorage on successful exam creation
                    localStorage.removeItem('selectedExamQuestions');
                    // Also clear the in-memory array
                    selectedQuestionIds = [];
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(data.message || 'Error creating exam');
                }
            },
            error: function() {
                toastr.error('Error creating exam');
            }
        });
    });
});
</script>