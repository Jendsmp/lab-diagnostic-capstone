<?php

class labdiagnostic_tests {
    private $conn;
    private $table = "labdiagnostic_tests";

    //labdiagnostic_tests properties
    public $test_id;
    public $test_code;
    public $test_name;
    public $description;
    public $category;
    public $preparation_instructions;
    public $estimated_duration;
    public $is_active;

    public function __construct($db) {
        $this->conn=$db;
    }

    //get all labdiagnostic_tests
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //get single labdiagnostic_tests
    public function getSingle() {
        $query = "SELECT * FROM " . $this->table . " WHERE test_id = :test_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":test_id", $this->test_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC); //fetch test id

        if ($row) {
            $this->test_code = $row['test_code'];
            $this->test_name = $row['test_name'];
            $this->description = $row['description'];
            $this->category = $row['category'];
            $this->preparation_instructions = $row['preparation_instructions'];
            $this->estimated_duration = $row['estimated_duration'];
            $this->is_active =$row['is_active'];
            return true;
        }
        return false;

    }

    //create test
    public function create() {
        $query = "INSERT INTO " . $this->table . "
        (test_code, test_name, description, category, preparation_instructions, estimated_duration, is_active)
        VALUES (:test_code, :test_name, :description, :category, :preparation_instructions, :estimated_duration, :is_active) ";

        $stmt = $this->conn->prepare($query);

        //clean data and ensure safe before inserting to database
        $this->test_code = htmlspecialchars(strip_tags($this->test_code));
        $this->test_name = htmlspecialchars(strip_tags($this->test_name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->preparation_instructions = htmlspecialchars(strip_tags($this->preparation_instructions));

        //bind data ensures values are safe inserted to db
        $stmt->bindParam(':test_code', $this->test_code);
        $stmt->bindParam(':test_name', $this->test_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category',$this->category);
        $stmt->bindParam(':preparation_instructions',$this->preparation_instructions);
        $stmt->bindParam(':estimated_duration', $this->estimated_duration);
        $stmt->bindParam(':is_active', $this->is_active);

        if($stmt->execute()) {
            return true; //if record  found
        }

        return false; //if no row found

    }

     // For editing 
    public function getOne() {
        $query = "SELECT * FROM labdiagnostic_tests WHERE test_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->test_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //update test
    public function update() {
        $query = "UPDATE " . $this->table . "
        SET 
        test_code = :test_code,
        test_name = :test_name,
        description = :description,
        category = :category,
        preparation_instructions = :preparation_instructions,
        estimated_duration = :estimated_duration,
        is_active = :is_active
        WHERE
        test_id = :test_id";

        $stmt = $this->conn->prepare($query);

          //clean data and ensure safe before inserting to database
          $this->test_code = htmlspecialchars(strip_tags($this->test_code));
          $this->test_name = htmlspecialchars(strip_tags($this->test_name));
          $this->description = htmlspecialchars(strip_tags($this->description));
          $this->category = htmlspecialchars(strip_tags($this->category));
          $this->preparation_instructions = htmlspecialchars(strip_tags($this->preparation_instructions));
  
          //bind data ensures values are safe inserted to db
          $stmt->bindParam(':test_id', $this->test_id);
          $stmt->bindParam(':test_code', $this->test_code);
          $stmt->bindParam(':test_name', $this->test_name);
          $stmt->bindParam(':description', $this->description);
          $stmt->bindParam(':category',$this->category);
          $stmt->bindParam(':preparation_instructions',$this->preparation_instructions);
          $stmt->bindParam(':estimated_duration', $this->estimated_duration);
          $stmt->bindParam(':is_active', $this->is_active);
  
          if($stmt->execute()) {
              return true; //if record  found
          }
  
          return false; //if no row found
  
      }

      //delete tests
      public function delete() {
        try {
            // First delete all appointments using this test
            $query1 = "DELETE FROM labdiagnostic_appointments WHERE test_id = ?";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(1, $this->test_id);
            $stmt1->execute();
            
            // Then delete the test
            $query2 = "DELETE FROM labdiagnostic_tests WHERE test_id = ?";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(1, $this->test_id);
            
            return $stmt2->execute();
        } catch (PDOException $e) {
            error_log("Error deleting test: " . $e->getMessage());
            return false;
        }
    } 
}

?>


