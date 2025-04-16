<?php

class labdiagnostic_appointments{
    private $conn;
    private $table = "labdiagnostic_appointments";

    //appointments properties
    public $appointment_id;
    public $test_id;
    public $scheduled_datetime;
    public $end_datetime;
    public $status;
    public $notes;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    //get all appointments with tests infos from labdiagnostic tests
    // a. is my alias for labdiagnostic tests
    public function getAll() {
        $query = "SELECT a.*, t.test_name 
        FROM " . $this->table . " a
        LEFT JOIN labdiagnostic_tests t ON a.test_id = t.test_id
            ORDER BY a.scheduled_datetime DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
    }


    //get single appoint
    public function getSingle() {
        $query = "SELECT a.*, t.test_name
        FROM  " . $this->table . " a
        LEFT JOIN labdiagnostic_tests t ON a.test_id = t.test_id
        WHERE a.appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":appointment_id", $this->appointment_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row)  {
            $this->test_id = $row['test_id'];
            $this->scheduled_datetime = $row['scheduled_datetime'];
            $this->end_datetime = $row['end_datetime'];
            $this->status = $row['status'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];

            return true; 
        }

        return false;
    }

    //create appoint
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
        (test_id, scheduled_datetime, end_datetime, status, notes)
        VALUES
        (:test_id, :scheduled_datetime, :end_datetime, :status, :notes)";

        $stmt = $this->conn->prepare($query);

        //clean data and ensure safe before inserting to database
        $this->test_id = htmlspecialchars(strip_tags($this->test_id));
        $this->scheduled_datetime = htmlspecialchars(strip_tags($this->scheduled_datetime));
        $this->end_datetime = htmlspecialchars(strip_tags($this->end_datetime));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        //bind data ensures values are safe inserted to db
        $stmt->bindParam(':test_id', $this->test_id);
        $stmt->bindParam(':scheduled_datetime', $this->scheduled_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes',$this->notes);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    //update appoint
    public function update() {
        $query = "UPDATE " . $this->table . "
        SET 
        test_id = :test_id,
        scheduled_datetime = :scheduled_datetime,
        end_datetime = :end_datetime,
        status = :status,
        notes = :notes
        WHERE 
        appointment_id = :appointment_id";

        $stmt = $this->conn->prepare($query);

       //clean data and ensure safe before inserting to database
        $this->appointment_id = htmlspecialchars(strip_tags($this->appointment_id));
        $this->test_id = htmlspecialchars(strip_tags($this->test_id));
        $this->scheduled_datetime = htmlspecialchars(strip_tags($this->scheduled_datetime));
        $this->end_datetime = htmlspecialchars(strip_tags($this->end_datetime));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        //bind data ensures values are safe inserted to db
        $stmt->bindParam(':appointment_id', $this->appointment_id);
        $stmt->bindParam(':test_id', $this->test_id);
        $stmt->bindParam(':scheduled_datetime', $this->scheduled_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes', $this->notes);

        if($stmt->execute()) {
            return true;  //if record  found
        }

        return false;  //if no row found   
    }

    //delete appointments
    public function delete() {
        $query = "DELETE FROM " . $this->table . "
        WHERE appointment_id = :appointment_id";
        $stmt = $this->conn->prepare($query);

    //clean data and ensure safe before inserting to database
    $this->appointment_id = htmlspecialchars(strip_tags($this->appointment_id));

    //bind data ensures values are safe inserted to db
    $stmt->bindParam(':appointment_id', $this->appointment_id);

    if($stmt->execute()) {
        return true;
    }

    return false;
    }

    //get all test for dropdown
    public function getTests() {
        $query = "SELECT test_id, test_name FROM labdiagnostic_tests
        WHERE is_active = 1 ORDER BY test_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Add this method to match what's being called into main file
    public function getlabdiagnostic_tests() {
        return $this->getTests();
    }

    //get date by range
    public function getByDateRange($startDate, $endDate) {
        $query = "SELECT a.*, t.test_name
        FROM " . $this->table . " a
        LEFT JOIN labdiagnostic_tests t ON a.test_id = t.test_id
        WHERE a.scheduled_datetime BETWEEN :start_date AND :end_date
        ORDER BY a.scheduled_datetime ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt;
    }

    //get appoint count by status
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as count 
        FROM " . $this->table . "
        GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
 }

 ?>