<?php
// Library Member Management System
session_start();

require_once '../db.php';
include 'validation.php';

// Get all members
$members = [];
$stmt = $pdo->query('SELECT member_id, first_name, last_name, birthday, email FROM member ORDER BY member_id ASC');
$members = $stmt->fetchAll();

// Get messages from session
$success = '';
$error = '';
$validation_errors = [];
$form_data = [];
$edit_member = null;
$show_form = false;

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
    $show_form = true;
}

if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

// Handle edit mode
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    foreach ($members as $member) {
        if ($member['member_id'] === $edit_id) {
            $edit_member = $member;
            $form_data = $member;
            $show_form = true;
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Member Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        
        .btn-create {
            margin-bottom: 20px;
        }
        
        .alert {
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 10px 12px;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .table {
            margin-top: 20px;
        }
        
        .table thead {
            background-color: #007bff;
            color: white;
        }
        
        .table th {
            font-weight: 600;
            border: none;
            padding: 15px;
            text-align: center;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            text-align: center;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f0f0f0;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        
        .btn-edit {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #218838;
            border-color: #218838;
            color: white;
        }
        
        .btn-delete {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: white;
        }
        
        .no-members {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn-submit {
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .btn-cancel {
            padding: 10px 25px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include_once '../navigation.php'; ?>
    <div class="container">
        <h1>📚 Library Member Registration</h1>
        
        <!-- Success Message -->
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?php echo nl2br(htmlspecialchars($error)); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- Create/Edit Form -->
        <div id="formSection" class="form-section" style="<?php echo !$show_form ? 'display: none;' : ''; ?>">
            <h3><?php echo $edit_member ? 'Edit Member' : 'Create New Member'; ?></h3>
            
            <form method="POST" action="members_handler.php">
                <?php if ($edit_member): ?>
                <input type="hidden" name="is_edit" value="1">
                <input type="hidden" name="original_member_id" value="<?php echo htmlspecialchars($edit_member['member_id']); ?>">
                <?php endif; ?>
                
                <div class="row">
                    <!-- Member ID -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="member_id" class="form-label">Member ID *</label>
                            <input type="text" 
                                   class="form-control <?php echo isset($validation_errors['member_id']) ? 'is-invalid' : ''; ?>"
                                   id="member_id" 
                                   name="member_id" 
                                   placeholder="e.g., M001"
                                   value="<?php echo htmlspecialchars($form_data['member_id'] ?? ''); ?>"
                                   <?php echo $edit_member ? 'readonly' : 'required'; ?>>
                            <?php if (isset($validation_errors['member_id'])): ?>
                            <div class="invalid-feedback" style="display: block;">
                                <?php echo htmlspecialchars($validation_errors['member_id']); ?>
                            </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Format: M followed by 3 digits (M001, M002, etc.)</small>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control <?php echo isset($validation_errors['email']) ? 'is-invalid' : ''; ?>"
                                   id="email" 
                                   name="email" 
                                   placeholder="example@mail.com"
                                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                   required>
                            <?php if (isset($validation_errors['email'])): ?>
                            <div class="invalid-feedback" style="display: block;">
                                <?php echo htmlspecialchars($validation_errors['email']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- First Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" 
                                   class="form-control <?php echo isset($validation_errors['first_name']) ? 'is-invalid' : ''; ?>"
                                   id="first_name" 
                                   name="first_name" 
                                   placeholder="John"
                                   value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>"
                                   required>
                            <?php if (isset($validation_errors['first_name'])): ?>
                            <div class="invalid-feedback" style="display: block;">
                                <?php echo htmlspecialchars($validation_errors['first_name']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" 
                                   class="form-control <?php echo isset($validation_errors['last_name']) ? 'is-invalid' : ''; ?>"
                                   id="last_name" 
                                   name="last_name" 
                                   placeholder="Doe"
                                   value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>"
                                   required>
                            <?php if (isset($validation_errors['last_name'])): ?>
                            <div class="invalid-feedback" style="display: block;">
                                <?php echo htmlspecialchars($validation_errors['last_name']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Birthday -->
                <div class="form-group">
                    <label for="birthday" class="form-label">Birthday *</label>
                    <input type="text" 
                           class="form-control <?php echo isset($validation_errors['birthday']) ? 'is-invalid' : ''; ?>"
                           id="birthday" 
                           name="birthday" 
                           placeholder="DD/MM/YYYY or YYYY-MM-DD"
                           value="<?php echo htmlspecialchars($form_data['birthday'] ?? ''); ?>"
                           required>
                    <?php if (isset($validation_errors['birthday'])): ?>
                    <div class="invalid-feedback" style="display: block;">
                        <?php echo htmlspecialchars($validation_errors['birthday']); ?>
                    </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Format: DD/MM/YYYY (e.g., 15/08/2000) or YYYY-MM-DD (e.g., 2000-08-15)</small>
                </div>
                
                <!-- Buttons -->
                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary btn-cancel" onclick="cancelForm()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <?php echo $edit_member ? 'Update Member' : 'Create Member'; ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Create Button -->
        <button type="button" class="btn btn-primary btn-create" id="createBtn" style="<?php echo $show_form ? 'display: none;' : ''; ?>" onclick="showForm()">
            + Create New Member
        </button>
        
        <!-- Members Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Birthday</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($members) > 0): ?>
                        <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['member_id']); ?></td>
                            <td><?php echo htmlspecialchars($member['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['birthday']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="members.php?edit=<?php echo htmlspecialchars($member['member_id']); ?>" 
                                       class="btn btn-sm btn-edit">Edit</a>
                                    <button type="button" 
                                            class="btn btn-sm btn-delete" 
                                            onclick="deleteMember('<?php echo htmlspecialchars($member['member_id']); ?>', '<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-members">
                            No members registered yet. Click "Create New Member" to add one.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this member?</p>
                    <p><strong id="memberNameDisplay"></strong></p>
                    <p style="color: #999; font-size: 0.9rem;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="members_delete.php" id="deleteForm" style="display: inline;">
                        <input type="hidden" id="deleteMemberId" name="delete_member_id" value="">
                        <button type="submit" class="btn btn-danger">Delete Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm() {
            document.getElementById('formSection').style.display = 'block';
            document.getElementById('createBtn').style.display = 'none';
            document.getElementById('member_id').focus();
            // Reset form
            document.querySelector('form').reset();
        }
        
        function cancelForm() {
            // Reset form if not in edit mode
            <?php if (!$edit_member): ?>
            document.getElementById('formSection').style.display = 'none';
            document.getElementById('createBtn').style.display = 'block';
            document.querySelector('form').reset();
            <?php else: ?>
            window.location.href = 'members.php';
            <?php endif; ?>
        }
        
        function deleteMember(memberId, memberName) {
            document.getElementById('memberNameDisplay').textContent = memberName + ' (' + memberId + ')';
            document.getElementById('deleteMemberId').value = memberId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
