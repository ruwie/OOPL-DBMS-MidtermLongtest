<?php
// classes/CashOnDelivery.php
require_once "PaymentMethod.php";

class CashOnDelivery extends PaymentMethod {

    // Constructor to initialize the amount (no need for card details)
    // public function __construct($amount) {
    //     parent::__construct($amount); // Call parent constructor to set amount
    // }

    // Implement the processTransaction() method for CashOnDelivery
    public function processTransaction() {
        // Simulate cash on delivery (no real transaction)
        echo "Cash on delivery for order of \$" . number_format($this->amount, 2) . "<br>";
        return true;  // Payment successful
    }
}
