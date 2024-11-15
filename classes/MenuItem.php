<?php
class MenuItem {
    private $conn;
    public $id;
    public $name;
    public $description;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        // Query to fetch details of a single menu item
        $query = "SELECT id, name, description, price FROM menu_items WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        // Check if the query found a row
        if ($stmt->rowCount() > 0) {
            return $stmt;  // Return the PDO statement with item details
        } else {
            return false;  // Return false if no matching item was found
        }
    }
}
