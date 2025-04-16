<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOSPITAL MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" type="text/css" href="sidebar.css">
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="hospitallogo.jpg" alt="Logo"> 
        <h2>Hospital Management System</h2>
    </div>

    <nav class="nav">
        <div class="menu">
            <p class="title">Main Menu</p>
            <ul>

            <li class="dropdown">
    <div class="dropdown-toggle">
        <a href="#"><span class="text">Patient Management</span></a>
   </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>
                    
                    <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Doctor And Nurse Management</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>


                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">HR Management</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>


                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Billing And Insurance Management</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="#"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>

                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Pharmacy Management</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>

                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Laboratory And Diagnostic Management</span></a>
                        </div>
                <div class="dropdown-content">
                        <a href="#"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="testForm.php"><span class="text"> > Booking</span></a>
                        <a href="appointment.php"><span class="text"> > Appointment</span></a>
                        <a href="#"><span class="text"> > Slot</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>


                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Inventory And Supply Chain Management</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>

                <li class="dropdown">
                    <div class="dropdown-toggle">
                        <a href="#"><span class="text">Report And Analytics</span></a>
                </div>
                <div class="dropdown-content">
                        <a href="testbooking.php"><span class="text">Test Booking And Scheduling</span></a>
                        <a href="#"><span class="text">Sample Collection And Tracking</span></a>
                        <a href="#"><span class="text">Report Generation And Tracking</span></a>
                        <a href="#"><span class="text">Equipment Maintenance</span></a>
                </div>
                </li>
            </ul>
        </div>

        <div class="menu">
            <p class="title">Account</p>
            <ul>
                <li>
                    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');" aria-label="Logout">
                        <i class="icon ph-bold ph-sign-out"></i>
                        <span class="text">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<script>
    // Add dropdown toggle functionality
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const dropdown = this.parentElement;
            dropdown.classList.toggle('active');
        });
    });
</script>

</body>
</html>
