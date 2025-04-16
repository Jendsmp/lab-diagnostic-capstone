<?php

// Include database connection and test class
require_once 'dbConnection.php';
require_once 'test_class.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize labdiagnostic_tests
$labdiagnostic_tests = new labdiagnostic_tests($db);

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Set the test ID
    $labdiagnostic_tests->test_id = $_GET['id'];
    
    // Get the test details
    $test = $labdiagnostic_tests->getOne();
    
    if ($test) {
        // Return test data as JSON
        header('Content-Type: application/json');
        echo json_encode($test);
    }
}
?>