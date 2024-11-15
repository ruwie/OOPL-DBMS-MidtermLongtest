<?php
// classes/Delivery.php
class Delivery {
    private $conn;
    private $table_name = "deliveries";

    public $id;
    public $order_id;
    public $delivery_mode;
    public $address;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET order_id=:order_id, delivery_mode=:delivery_mode, address=:address, status=:status";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":delivery_mode", $this->delivery_mode);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }
}
?>
