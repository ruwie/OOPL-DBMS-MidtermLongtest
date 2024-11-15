<?php
class DeliveryMode {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getDeliveryModes() {
        // Example query to fetch available delivery modes
        $query = "SELECT id, name, price FROM delivery_modes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
