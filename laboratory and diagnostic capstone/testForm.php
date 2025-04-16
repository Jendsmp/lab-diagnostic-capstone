<?php

//connects 
require_once 'dbConnection.php';
require_once 'test_class.php';

//initialize connections to db
$database = new Database();
$db = $database->getConnection();

//initialize tests object
$labdiagnostic_tests = new labdiagnostic_tests($db);

//handle submiassion of form
$message =  '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {

        // Create test
        $labdiagnostic_tests->test_code = $_POST['test_code'];
        $labdiagnostic_tests->test_name = $_POST['test_name'];
        $labdiagnostic_tests->description = $_POST['description'];
        $labdiagnostic_tests->category = $_POST['category'];
        $labdiagnostic_tests->preparation_instructions = $_POST['preparation_instructions'];
        $labdiagnostic_tests->estimated_duration = $_POST['estimated_duration'];
        $labdiagnostic_tests->is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($labdiagnostic_tests->create()) {
            $message = "Test created successfully.";
        } else {
            $error = "Unable to create test.";
        }

    } 
    
    elseif (isset($_POST['update'])) {
        // Update test
        $labdiagnostic_tests->test_id = $_POST['test_id'];
        $labdiagnostic_tests->test_code = $_POST['test_code'];
        $labdiagnostic_tests->test_name = $_POST['test_name'];
        $labdiagnostic_tests->description = $_POST['description'];
        $labdiagnostic_tests->category = $_POST['category'];
        $labdiagnostic_tests->preparation_instructions = $_POST['preparation_instructions'];
        $labdiagnostic_tests->estimated_duration = $_POST['estimated_duration'];
        $labdiagnostic_tests->is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($labdiagnostic_tests->update()) {
            $message = "Test updated successfully.";
        } else {
            $error = "Unable to update test.";
        }
    } 
    
    elseif (isset($_POST['delete'])) {
        // Delete test
        $labdiagnostic_tests->test_id = $_POST['test_id'];
        
        if ($labdiagnostic_tests->delete()) {
            $message = "Test deleted successfully.";
        } else {
            $error = "Unable to delete test.";
        }
    }
}

// Get all tests
$result = $labdiagnostic_tests->getAll();
$labdiagnostic_tests = $result->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Test Management</title>
    <link rel="stylesheet" href="testForm.css">
    <style>
        /* Add modal styles if not already in your CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover
        .close:focus {
            color: black;
            text-decoration:none;
        }
        
        #testDetails {
            margin-top: 20px;
            
        }
        
        .detail-row {
            margin-bottom: 15px;
            
        }
    
        
        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 200px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Laboratory Test Management</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Add New Test</h2>
            <form id="testForm" method="POST">
                <input type="hidden" id="test_id" name="test_id">
                
                <div class="form-group">
                            <label for="test_code">Test Code:</label>
                            <select id="test_code" name="test_code" required>
                            <option value="">Select Test</option>
                                <option value="CBC01">CBC01</option>
                                <option value="URINE02">URINE02</option>
                                <option value="CTSCAN03">CTSCAN03</option>
                                <option value="HIV04">HIV04</option>
                                <option value="XRAY05">XRAY05</option>
                                <option value="ULTRASOUND06">ULTRASOUND06</option>
                            </select>
                </div>

                <div class="form-group">
                            <label for="test_name">Test Name:</label>
                            <select id="test_name" name="test_name" required>
                            <option value="">Select Test</option>
                                <option value="COMPLETE BLOOD COUNT">COMPLETE BLOOD COUNT</option>
                                <option value="URINALYSIS">URINALYSIS</option>
                                <option value="CT SCAN">CT SCAN</option>
                                <option value="HIV TEST">HIV TEST</option>
                                <option value="XRAY">XRAY</option>
                                <option value="ULTRASOUND">ULTRASOUND</option>
                            </select>
                </div>
            
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category" required>
                            <option value="">Select Category</option>
                                <option value="HEMATOLOGY">HEMATOLOGY</option>
                                <option value="URINE TEST">URINE TEST</option>
                                <option value="RADIOLOGY / IMAGING">RADIOLOGY / IMAGING</option>
                                <option value="INFECTIOUS DISEASE">INFECTIOUS DISEASE</option>
                            </select>
                </div>
                
                <div class="form-group">
                    <label for="preparation_instructions">Preparation Instructions:</label>
                    <textarea id="preparation_instructions" name="preparation_instructions" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="estimated_duration">Estimated Duration (minutes):</label>
                    <input type="number" id="estimated_duration" name="estimated_duration" min="1">
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_active" name="is_active" checked>
                    <label for="is_active">Active</label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" name="create" id="createBtn">Create Test</button>
                    <button type="submit" name="update" id="updateBtn" style="display: none;">Update Test</button>
                    <button type="button" id="cancelBtn" style="display: none;">Cancel</button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Test List</h2>
            <div class="table-responsive">
                <table id="testsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Duration (min)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labdiagnostic_tests as $row): ?>
                            <tr>
                                <td><?php echo $row['test_id']; ?></td>
                                <td><?php echo $row['test_code']; ?></td>
                                <td><?php echo $row['test_name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['estimated_duration']; ?></td>
                                <td><?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                <td>
                                <button class="btn-view" data-id="<?php echo $row['test_id']; ?>">View</button>
                                <button class="btn-edit" data-id="<?php echo $row['test_id']; ?>">Edit</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="test_id" value="<?php echo $row['test_id']; ?>">
                                        
                                        <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete this test?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   <!-- Modal for viewing test details -->
<div id="testModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Test Details</h2>
        <div id="testDetails">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>
<script>
    // JavaScript for handling edit functionality
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        const viewButtons = document.querySelectorAll('.btn-view');
        const testForm = document.getElementById('testForm');
        const createBtn = document.getElementById('createBtn');
        const updateBtn = document.getElementById('updateBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const modal = document.getElementById('testModal');
        const closeBtn = document.querySelector('.close');
        
        // Add event listeners to all edit buttons
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const testId = this.getAttribute('data-id');
                fetchTestDetails(testId, 'edit');
            });
        });

        // Add event listeners to all view buttons
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const testId = this.getAttribute('data-id');
                fetchTestDetails(testId, 'view');
            });
        });
        
        // Close modal when clicking X
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
        
        // Cancel button functionality
        cancelBtn.addEventListener('click', function() {
            resetForm();
        });
        
        // Function to fetch test details for editing or viewing
        function fetchTestDetails(testId, mode) {
            // Using fetch API to get test details
            fetch(`get_test.php?id=${testId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        if (mode === 'edit') {
                            // Populate form with test details for editing
                            document.getElementById('test_id').value = data.test_id;
                            document.getElementById('test_code').value = data.test_code;
                            document.getElementById('test_name').value = data.test_name;
                            document.getElementById('description').value = data.description;
                            document.getElementById('category').value = data.category;
                            document.getElementById('preparation_instructions').value = data.preparation_instructions;
                            document.getElementById('estimated_duration').value = data.estimated_duration;
                            document.getElementById('is_active').checked = data.is_active == 1;
                            
                            // Show update and cancel buttons, hide create button
                            createBtn.style.display = 'none';
                            updateBtn.style.display = 'inline-block';
                            cancelBtn.style.display = 'inline-block';
                            
                            // Scroll to form
                            testForm.scrollIntoView({ behavior: 'smooth' });
                        } else if (mode === 'view') {
                            // Display details in modal for viewing
                            const detailsDiv = document.getElementById('testDetails');
                            detailsDiv.innerHTML = `
                                <div class="detail-row">
                                    <span class="detail-label">Test ID:</span>
                                    <span>${data.test_id}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Test Code:</span>
                                    <span>${data.test_code}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Test Name:</span>
                                    <span>${data.test_name}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Description:</span>
                                    <span>${data.description || 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Category:</span>
                                    <span>${data.category}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Preparation Instructions:</span>
                                    <span>${data.preparation_instructions || 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Estimated Duration:</span>
                                    <span>${data.estimated_duration} minutes</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Status:</span>
                                    <span>${data.is_active ? 'Active' : 'Inactive'}</span>
                                </div>
                            `;
                            modal.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching test details:', error);
                    alert('Failed to load test details. Please try again.');
                });
        }
        
        // Function to reset form to create mode
        function resetForm() {
            testForm.reset();
            document.getElementById('test_id').value = '';
            createBtn.style.display = 'inline-block';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        }
    });
</script>
</body>
</html>
