<?php
// classes/CreditCard.php
require_once "PaymentMethod.php";

class CreditCard extends PaymentMethod {

    private $cardNumber;
    private $expiryDate;
    private $cvv;

    // Constructor to initialize the amount and card details
    public function __construct($amount, $cardNumber, $expiryDate, $cvv) {
        parent::__construct($amount); // Call parent constructor to set amount
        $this->cardNumber = $cardNumber;
        $this->expiryDate = $expiryDate;
        $this->cvv = $cvv;
    }

    // Implement the processTransaction() method for CreditCard
    public function processTransaction() {
        // For now, let's just simulate a successful transaction.
        // Here, you would usually call a payment gateway API to process the payment.
        echo "Processing Credit Card payment of \$" . number_format($this->amount, 2) . " with card number ending in " . substr($this->cardNumber, -4) . "<br>";
        return true;  // Payment successful
    }
}
