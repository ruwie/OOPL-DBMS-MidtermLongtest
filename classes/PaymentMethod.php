<?php
// classes/PaymentMethod.php

abstract class PaymentMethod {
    protected $amount;

    // Constructor to initialize the amount
    // public function __construct($amount) {
    //     $this->amount = $amount;
    // }

    // Abstract method to process the transaction
    abstract public function processTransaction();

}
