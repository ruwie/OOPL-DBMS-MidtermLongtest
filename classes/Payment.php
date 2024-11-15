<?php
// classes/Payment.php
abstract class PaymentMethod {
    abstract public function processTransaction(float $amount);
}
