<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all leads
$stmt = $pdo->query("
    SELECT ce.*, au.name as assigned_to_name 
    FROM contact_enquiries ce 
    LEFT JOIN admin_users au ON ce.assigned_with = au.id 
    LEFT JOIN users u ON ce.email = u.email

    ORDER BY ce.created_at DESC
");
$leads = $stmt->fetchAll();

// Fetch staff and teachers for assignment
$stmt = $pdo->query("SELECT id, name, type FROM admin_users WHERE type IN ('teacher', 'staff') ORDER BY name");
$staff_members = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Leads Management</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Course Interest</th>
                                    <th>Source</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['course_interest']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['source']); ?></td>
                                    <td>
                                        <select class="form-select assign-lead" data-id="<?php echo $lead['id']; ?>">
                                            <option value="">Select Staff/Teacher</option>
                                            <?php foreach ($staff_members as $staff): ?>
                                                <option value="<?php echo $staff['id']; ?>" 
                                                    <?php echo ($lead['assigned_with'] == $staff['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($staff['name']); ?> (<?php echo ucfirst($staff['type']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select status-change" data-id="<?php echo $lead['id']; ?>">
                                            <option value="new" <?php echo ($lead['status'] == 'new') ? 'selected' : ''; ?>>New</option>
                                            <option value="in_progress" <?php echo ($lead['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="contacted" <?php echo ($lead['status'] == 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                                            <option value="qualified" <?php echo ($lead['status'] == 'qualified') ? 'selected' : ''; ?>>Qualified</option>
                                            <option value="not_interested" <?php echo ($lead['status'] == 'not_interested') ? 'selected' : ''; ?>>Not Interested</option>
                                            <option value="converted" <?php echo ($lead['status'] == 'converted') ? 'selected' : ''; ?>>Converted</option>
                                            <option value="on_hold" <?php echo ($lead['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                                        </select>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($lead['created_at'])); ?></td>
                                    <td><?php echo date('d M Y ', strtotime($lead['updated_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-lead" 
                                                data-id="<?php echo $lead['id']; ?>"
                                                data-message="<?php echo htmlspecialchars($lead['message']); ?>">
                                            <i class="mdi mdi-eye"></i>
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

<!-- Lead Details Modal -->
<div class="modal fade" id="leadDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <p id="leadMessage" class="text-dark"></p>
                <div class="form-group mt-3">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" id="remarks" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveRemarks">Save Remarks</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script>
$(document).ready(function() {
    // Handle lead assignment
    $('.assign-lead').on('change', function() {
        var leadId = $(this).data('id');
        var staffId = $(this).val();
        
        $.ajax({
            url: '../../api/leads/assign.php',
            type: 'POST',
            data: { 
                lead_id: leadId,
                staff_id: staffId
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Lead assigned successfully!');
                } else {
                    toastr.error(response.message || 'Failed to assign lead!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle status change
    $('.status-change').on('change', function() {
        var leadId = $(this).data('id');
        var status = $(this).val();
        
        $.ajax({
            url: '../../api/leads/update-status.php',
            type: 'POST',
            data: { 
                lead_id: leadId,
                status: status
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle view lead details
    $('.view-lead').on('click', function() {
        var message = $(this).data('message');
        var leadId = $(this).data('id');
        $('#leadMessage').text(message);
        
        // Fetch existing remarks
        $.ajax({
            url: '../../api/leads/get-remarks.php',
            type: 'GET',
            data: { lead_id: leadId },
            success: function(response) {
                $('#remarks').val(response.remarks || '');
                $('#remarks').data('lead-id', leadId);
            }
        });
        
        $('#leadDetailsModal').modal('show');
    });

    // Handle save remarks
    $('#saveRemarks').on('click', function() {
        var leadId = $('#remarks').data('lead-id');
        var remarks = $('#remarks').val();
        
        $.ajax({
            url: '../../api/leads/assign.php',
            type: 'POST',
            data: { 
                lead_id: leadId,
                staff_id: $('.assign-lead[data-id="' + leadId + '"]').val(),
                remarks: remarks
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Remarks saved successfully!');
                    $('#leadDetailsModal').modal('hide');
                } else {
                    toastr.error(response.message || 'Failed to save remarks!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>