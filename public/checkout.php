<?php
// public/checkout.php
session_start();
require_once "../config/Database.php";
require_once "../classes/Order.php";
require_once "../classes/CreditCard.php";
require_once "../classes/CashOnDelivery.php";
require_once "../classes/Delivery.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Default values
$delivery_address = "";
$payment_method = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use isset() to avoid "undefined array key" warning
    $delivery_address = isset($_POST['delivery_address']) ? $_POST['delivery_address'] : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // Process payment
if ($payment_method == "CreditCard") {
    // Pass $total_amount to CreditCard constructor
    $payment = new CreditCard($total_amount);
} else {
    // Pass $total_amount to CashOnDelivery constructor
    $payment = new CashOnDelivery($total_amount);
}


    $payment->processTransaction();

    // Save order and delivery
    $order = new Order($conn);
    $order->user_id = $_SESSION['user_id'];
    $order->total_amount = $total_amount;
    $order->status = "pending";
    $order->create();

    $delivery = new Delivery($conn);
    $delivery->order_id = $order->id;
    $delivery->delivery_mode = "BikeDelivery";
    $delivery->address = $delivery_address;
    $delivery->status = "pending";
    $delivery->create();

    $_SESSION['order_id'] = $order->id;
    unset($_SESSION['cart']);
    header("Location: confirm_order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <h2>Checkout</h2>
    <form method="post" action="">
        <label for="delivery_address">Delivery Address:</label>
        <input type="text" name="delivery_address" value="<?php echo htmlspecialchars($delivery_address); ?>" required><br>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" required>
            <option value="CreditCard" <?php echo ($payment_method == "CreditCard") ? "selected" : ""; ?>>Credit Card</option>
            <option value="CashOnDelivery" <?php echo ($payment_method == "CashOnDelivery") ? "selected" : ""; ?>>Cash on Delivery</option>
        </select><br>

        <button type="submit">Confirm Order</button>
    </form>
</body>
</html>
