<?php
//connects 
require_once 'dbConnection.php';
require_once 'appointment_class.php';
require_once 'test_class.php'; 

//initialize connections to db
$database = new Database();
$db = $database->getConnection();

//initialize tests object
$appointments = new labdiagnostic_appointments($db); 
$tests = new labdiagnostic_tests($db); 

//handle submission of form
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create'])) {
        // Validate required fields
        if (!isset($_POST['test_id']) || empty($_POST['test_id'])) {
            $error = "Test selection is required.";
        } 
        elseif (!isset($_POST['scheduled_datetime']) || empty($_POST['scheduled_datetime'])) {
            $error = "Start date and time is required.";
        }
        elseif (!isset($_POST['end_datetime']) || empty($_POST['end_datetime'])) {
            $error = "End date and time is required.";
        }
        elseif (!isset($_POST['status']) || empty($_POST['status'])) {
            $error = "Status is required.";
        }
        else {
            // All required fields are done proceed with creating appointment
            $appointments->test_id = $_POST['test_id'];
            $appointments->scheduled_datetime = $_POST['scheduled_datetime'];
            $appointments->end_datetime = $_POST['end_datetime'];
            $appointments->status = $_POST['status'];
            $appointments->notes = $_POST['notes'];

            if ($appointments->create()) {
                $message = "Appointment Created Successfully."; 
            } else {
                $error = "Unable to Create Appointment.";
            }
        }
    }
    elseif (isset($_POST['update'])) {
        //update appoint
        if (!isset($_POST['appointment_id']) || empty($_POST['appointment_id'])) {
            $error = "Appointment ID is required for update.";
        }
        elseif (!isset($_POST['test_id']) || empty($_POST['test_id'])) {
            $error = "Test selection is required.";
        } 
        elseif (!isset($_POST['scheduled_datetime']) || empty($_POST['scheduled_datetime'])) {
            $error = "Start date and time is required.";
        }
        elseif (!isset($_POST['end_datetime']) || empty($_POST['end_datetime'])) {
            $error = "End date and time is required.";
        }
        elseif (!isset($_POST['status']) || empty($_POST['status'])) {
            $error = "Status is required.";
        }
        else {
            $appointments->appointment_id = $_POST['appointment_id'];
            $appointments->test_id = $_POST['test_id'];
            $appointments->scheduled_datetime = $_POST['scheduled_datetime'];
            $appointments->end_datetime = $_POST['end_datetime'];
            $appointments->status = $_POST['status'];
            $appointments->notes = $_POST['notes'];

            if ($appointments->update()) {
                $message = "Appointment Updated Successfully.";
            } else {
                $error = "Unable to Update Appointment.";
            }
        }
    }
    elseif (isset($_POST['delete'])) {
        //delete appoint
        if (!isset($_POST['appointment_id']) || empty($_POST['appointment_id'])) {
            $error = "Appointment ID is required for deletion.";
        } else {
            $appointments->appointment_id = $_POST['appointment_id']; 

            if($appointments->delete()) {
                $message = "Appointment Deleted Successfully";
            } else {
                $error = "Unable to Delete Appointment.";
            }
        }
    }
}

// Get all appointments
$result = $appointments->getAll();
$appointmentsList = $result->fetchAll(PDO::FETCH_ASSOC); 

// Get tests for dropdowns
$testsResult = $tests->getAll(); 
$labdiagnostic_tests = $testsResult->fetchAll(PDO::FETCH_ASSOC);

// Get appointment statistics
$statusResult = $appointments->getCountByStatus();
$statusCounts = $statusResult->fetchAll(PDO::FETCH_ASSOC);
$statusStats = [];

foreach ($statusCounts as $row) { 
    $statusStats[$row['status']] = $row['count'];
}

// Default values if no data
if (empty($statusStats)) {
    $statusStats = [
        'booked' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'no_show' => 0
    ];
}

// Get current date for view of calendar
$currentDate = date('Y-m-d');
$currentMonth = date('Y-m');
$daysInMonth = date('t', strtotime($currentMonth));
$firstDayOfMonth = date('N', strtotime($currentMonth . '-01')); 
        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management System</title>
    <link rel="stylesheet" href="appointment.css">
</head>
<body>

   <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Appointment Management System</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="dashboard">
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Booked</h3>
                    <div class="stat-value"><?php echo $statusStats['booked'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Completed</h3>
                    <div class="stat-value"><?php echo $statusStats['completed'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Cancelled</h3>
                    <div class="stat-value"><?php echo $statusStats['cancelled'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3>No Show</h3>
                    <div class="stat-value"><?php echo $statusStats['no_show'] ?? 0; ?></div>
                </div>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab-btn active" data-tab="list">List View</button>
            <button class="tab-btn" data-tab="calendar">Calendar View</button>
            <button class="tab-btn" data-tab="new">New Appointment</button>
        </div>
        
        <div class="tab-content">
            <!-- List View Tab -->
            <div class="tab-pane active" id="list">
                <div class="card">
                    <h2>Appointment List</h2>
                    <div class="filters">
                        <div class="filter-group">
                            <label for="status-filter">Status:</label>
                            <select id="status-filter">
                                <option value="">All</option>
                                <option value="booked">Booked</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="date-filter">Date:</label>
                            <input type="date" id="date-filter">
                        </div>
                        <button id="reset-filters">Reset Filters</button>
                    </div>
                    <div class="table-responsive">
                        <table id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Test</th>
                                    <th>Scheduled Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($appointmentsList)): ?>
                                    <?php foreach ($appointmentsList as $row): ?>
                                        <tr data-status="<?php echo $row['status']; ?>" 
                                        data-date="<?php echo date('Y-m-d', strtotime($row['scheduled_datetime'])); ?>"
                                        data-notes="<?php echo htmlspecialchars($row['notes']); ?>"> 
                                            <td><?php echo $row['appointment_id']; ?></td>
                                            <td><?php echo $row['test_name'] ?? 'Unknown'; ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($row['scheduled_datetime'])); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($row['end_datetime'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn-view" data-id="<?php echo $row['appointment_id']; ?>">View</button>
                                                <button class="btn-edit" data-id="<?php echo $row['appointment_id']; ?>">Edit</button>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                                    <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                   
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Calendar View Tab -->
            <div class="tab-pane" id="calendar">
                <div class="card">
                    <h2>Calendar View</h2>
                    <div class="calendar-controls">
                        <button id="prev-month">&lt; Prev</button>
                        <h3 id="current-month"><?php echo date('F Y'); ?></h3>
                        <button id="next-month">Next &gt;</button>
                    </div>
                    <div class="calendar">
                        <div class="weekdays">
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                            <div>Sun</div>
                        </div>
                        <div class="days" id="calendar-days">
                            <!-- Calendar days will be generated by JavaScript -->
                        </div>
                    </div>
                    <div id="day-appointments" class="day-appointments">
                        <h3>Appointments for <span id="selected-date"><?php echo date('F d, Y'); ?></span></h3>
                        <div id="day-appointments-list">
                            <!-- Day appointments will be loaded here -->
                            <p class="no-appointments">No appointments for this date.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- New Appointment Tab -->
            <div class="tab-pane" id="new">
                <div class="card">
                    <h2>Schedule New Appointment</h2>
                    <form id="appointmentForm" method="POST">
                        <input type="hidden" id="appointment_id" name="appointment_id">
                        
                        <div class="form-group">
                            <label for="test_id">Test:</label>
                            <select id="test_id" name="test_id" required>
                                <option value="">Select Test</option>
                                <?php foreach ($labdiagnostic_tests as $test): ?>
                                    <option value="<?php echo $test['test_id']; ?>"><?php echo $test['test_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="scheduled_datetime">Start Date & Time:</label>
                                <input type="datetime-local" id="scheduled_datetime" name="scheduled_datetime" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="end_datetime">End Date & Time:</label>
                                <input type="datetime-local" id="end_datetime" name="end_datetime" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option value="booked">Booked</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="create" id="createBtn">Schedule Appointment</button>
                            <button type="submit" name="update" id="updateBtn" style="display: none;">Update Appointment</button>
                            <button type="button" id="cancelBtn" style="display: none;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Modal for viewing appointment details -->
        <div id="appointmentModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Appointment Details</h2>
                <div id="appointmentDetails">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Add active class to current button and pane
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                    
                    // If calendar tab, initialize calendar
                    if (tabId === 'calendar') {
                        generateCalendar();
                    }
                });
            });
            
            // Edit and view functionality
            const editButtons = document.querySelectorAll('.btn-edit');
            const viewButtons = document.querySelectorAll('.btn-view');
            const appointmentForm = document.getElementById('appointmentForm');
            const createBtn = document.getElementById('createBtn');
            const updateBtn = document.getElementById('updateBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const modal = document.getElementById('appointmentModal');
            const closeModal = document.querySelector('.close');
            
            // Add event listeners to all edit buttons
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const appointmentId = this.getAttribute('data-id');
                    fetchAppointmentDetails(appointmentId, 'edit');
                    
                    // Switch to new appointment tab
                    tabButtons.forEach(btn => {
                        if (btn.getAttribute('data-tab') === 'new') {
                            btn.click();
                        }
                    });
                });
            });
            
            // Add event listeners to all view buttons
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const appointmentId = this.getAttribute('data-id');
                    fetchAppointmentDetails(appointmentId, 'view');
                });
            });
            
            // Cancel button functionality
            cancelBtn.addEventListener('click', function() {
                resetForm();
            });
            
            // Close modal when clicking the X
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Function to fetch appointment details
            function fetchAppointmentDetails(appointmentId, action) {
            
                // Find the appointment in the table
                const rows = document.querySelectorAll('#appointmentsTable tbody tr');
                let appointmentData = null;
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells[0].textContent === appointmentId) {
                        appointmentData = {
                            appointment_id: cells[0].textContent,
                            test_name: cells[1].textContent,
                            scheduled_datetime: cells[2].textContent,
                            end_datetime: cells[3].textContent,
                            status: cells[4].querySelector('.status-badge').textContent.trim().toLowerCase(),
                            notes:row.dataset.notes 
                        };
                    }
                });
                
                if (appointmentData) {
                    if (action === 'edit') {
                        // Populate form for editing
                        document.getElementById('appointment_id').value = appointmentId;
                        
                        // Find the matching test in the dropdown
                        const testSelect = document.getElementById('test_id');
                        for (let i = 0; i < testSelect.options.length; i++) {
                            if (testSelect.options[i].text === appointmentData.test_name) {
                                testSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Set status dropdown
                        const statusSelect = document.getElementById('status');
                        for (let i = 0; i < statusSelect.options.length; i++) {
                            if (statusSelect.options[i].value === appointmentData.status) {
                                statusSelect.selectedIndex = i;
                                break;
                            }
                        }
                        
                        // Set dates - this is simplified for demo
                        // In a real app, you'd need to format the dates correctly
                        document.getElementById('scheduled_datetime').value = '2023-04-10T09:30';
                        document.getElementById('end_datetime').value = '2023-04-10T10:00';
                        
                        document.getElementById('notes').value = appointmentData.notes;
                        
                        // Show update and cancel buttons, hide create button
                        createBtn.style.display = 'none';
                        updateBtn.style.display = 'inline-block';
                        cancelBtn.style.display = 'inline-block';
                    } else if (action === 'view') {
                        // Show modal with appointment details
                        const detailsHTML = `
                            <div class="detail-row">
                                <strong>Test:</strong> ${appointmentData.test_name}
                            </div>
                            <div class="detail-row">
                                <strong>Scheduled Date:</strong> ${appointmentData.scheduled_datetime}
                            </div>
                            <div class="detail-row">
                                <strong>End Date:</strong> ${appointmentData.end_datetime}
                            </div>
                            <div class="detail-row">
                                <strong>Status:</strong> <span class="status-badge status-${appointmentData.status}">${appointmentData.status.charAt(0).toUpperCase() + appointmentData.status.slice(1)}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Notes:</strong>
                                <p>${appointmentData.notes}</p>
                            </div>
                            <div class="detail-row">
                                <strong>Created At:</strong> ${new Date().toLocaleString()}
                            </div>
                        `;
                        
                        document.getElementById('appointmentDetails').innerHTML = detailsHTML;
                        modal.style.display = 'block';
                    }
                }
            }
            
            // Function to reset form to create mode
            function resetForm() {
                appointmentForm.reset();
                document.getElementById('appointment_id').value = '';
                createBtn.style.display = 'inline-block';
                updateBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            }
            
            // Calculate end time based on test duration
            document.getElementById('test_id').addEventListener('change', function() {
                const testId = this.value;
                if (!testId) return;
                
                // In a real app, you would fetch the test duration from the server
                // For demo purposes, we'll use hardcoded values
                const testDurations = {
                    '1': 30, // Blood Test - 30 minutes
                    '2': 30, // X-Ray - 30 minutes
                    '3': 60,  // MRI Scan - 60 minutes
                    '4': 120
                };
                
                const scheduledDateInput = document.getElementById('scheduled_datetime');
                const endDatetimeInput = document.getElementById('end_datetime');
                
                scheduledDateInput.addEventListener('change', function() {
                    if (!this.value) return;
                    
                    const duration = testDurations[testId] || 30; // Default to 30 minutes
                    
                    // Calculate end time by adding duration to start time
                    const startDate = new Date(this.value);
                    const endDate = new Date(startDate.getTime() + duration * 60000);
                    
                    // Format end date for datetime-local input
                    const endDateString = endDate.toISOString().slice(0, 16);
                    endDatetimeInput.value = endDateString;
                });
            });
            
            // Filter functionality
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const resetFiltersBtn = document.getElementById('reset-filters');
            
            function applyFilters() {
                const statusValue = statusFilter.value;
                const dateValue = dateFilter.value;
                
                const rows = document.querySelectorAll('#appointmentsTable tbody tr');
                
                rows.forEach(row => {
                    let showRow = true;
                    
                    if (statusValue && row.getAttribute('data-status') !== statusValue) {
                        showRow = false;
                    }
                    
                    if (dateValue && row.getAttribute('data-date') !== dateValue) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            statusFilter.addEventListener('change', applyFilters);
            dateFilter.addEventListener('change', applyFilters);
            
            resetFiltersBtn.addEventListener('click', function() {
                statusFilter.value = '';
                dateFilter.value = '';
                applyFilters();
            });
            
            // Calendar functionality
            let currentDate = new Date();
            
            function generateCalendar() {
                const calendarDays = document.getElementById('calendar-days');
                const currentMonthElement = document.getElementById('current-month');
                
                // Clear previous calendar
                calendarDays.innerHTML = '';
                
                // Set current month display
                currentMonthElement.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
                
                // Get first day of month and number of days
                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                
                // Calculate first day of week (0 = Sunday, 1 = Monday, etc.)
                let firstDayIndex = firstDay.getDay();
                if (firstDayIndex === 0) firstDayIndex = 7; // Convert Sunday from 0 to 7
                
                // Add empty cells for days before first day of month
                for (let i = 1; i < firstDayIndex; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.classList.add('day', 'empty');
                    calendarDays.appendChild(emptyDay);
                }
                
                // Add days of month
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    const dayElement = document.createElement('div');
                    dayElement.classList.add('day');
                    
                    // Check if this is today
                    const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
                    if (dayDate.toDateString() === new Date().toDateString()) {
                        dayElement.classList.add('today');
                    }
                    
                    // Add date number
                    dayElement.textContent = i;
                    
                    // Add appointment indicators (simplified for demo)
                    // In a real app, you would check if there are appointments on this day
                    if (i % 3 === 0) {
                        const indicator = document.createElement('div');
                        indicator.classList.add('appointment-indicator');
                        dayElement.appendChild(indicator);
                    }
                    
                    // Add click event to show appointments for this day
                    dayElement.addEventListener('click', function() {
                        const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
                        showAppointmentsForDay(selectedDate);
                        
                        // Remove selected class from all days
                        document.querySelectorAll('.day').forEach(day => {
                            day.classList.remove('selected');
                
                        });
                        
                        // Add selected class to clicked day
                        this.classList.add('selected');
                    });
                    
                    calendarDays.appendChild(dayElement);
                }
            }
            
            function showAppointmentsForDay(date) {
                const selectedDateElement = document.getElementById('selected-date');
                const appointmentsList = document.getElementById('day-appointments-list');
                
                // Format date for display
                selectedDateElement.textContent = date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                // Format date for comparison
                const dateString = date.toISOString().split('T')[0];
                
                // Find appointments for this day
                const rows = document.querySelectorAll('#appointmentsTable tbody tr');
                let appointmentsHTML = '';
                let hasAppointments = false;
                
                rows.forEach(row => {
                    if (row.getAttribute('data-date') === dateString) {
                        hasAppointments = true;
                        const cells = row.querySelectorAll('td');
                        appointmentsHTML += `
                            <div class="day-appointment">
                                <div class="appointment-time">${cells[2].textContent.split(' ')[3] + ' ' + cells[2].textContent.split(' ')[4]}</div>
                                <div class="appointment-details">
                                    <div class="appointment-test">${cells[1].textContent}</div>
                                    <div class="appointment-status">${cells[4].innerHTML}</div>
                                </div>
                                <div class="appointment-actions">
                                    <button class="btn-view" data-id="${cells[0].textContent}">View</button>
                                </div>
                            </div>
                        `;
                    }
                });
                
                if (hasAppointments) {
                    appointmentsList.innerHTML = appointmentsHTML;
                    
                    // Add event listeners to view buttons
                    appointmentsList.querySelectorAll('.btn-view').forEach(button => {
                        button.addEventListener('click', function() {
                            const appointmentId = this.getAttribute('data-id');
                            fetchAppointmentDetails(appointmentId, 'view');
                        });
                    });
                } else {
                    appointmentsList.innerHTML = '<p class="no-appointments">No appointments for this date.</p>';
                }
            }
            
            // Month navigation
            document.getElementById('prev-month').addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                generateCalendar();
            });
            
            document.getElementById('next-month').addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                generateCalendar();
            });
            
            // Initialize calendar if calendar tab is active
            if (document.querySelector('.tab-btn[data-tab="calendar"]').classList.contains('active')) {
                generateCalendar();
            }
        });
    </script>
</body>
</html>