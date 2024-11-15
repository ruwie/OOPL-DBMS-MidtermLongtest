<?php
session_start();  // Start session

require_once "../config/Database.php";  // Include the database connection
require_once "../classes/MenuItem.php"; // Include MenuItem class
require_once "../classes/Order.php";  // Assuming you have Order class
require_once "../classes/CreditCard.php";
require_once "../classes/CashOnDelivery.php";
require_once "../classes/Delivery.php"; // Assuming Delivery class

$db = new Database();
$conn = $db->getConnection();
$menuItem = new MenuItem($conn);

// Fetch the order details
$order_id = $_SESSION['order_id'];
$query = "SELECT * FROM orders WHERE id = :order_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$total = 0;
foreach ($cart as $item_id => $quantity) {
    // Fetch item details
    $menuItem->id = $item_id;
    $stmt = $menuItem->read();
    if ($stmt && $item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $total += $item['price'] * $quantity;
    }
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryAddress = $_POST['delivery_address'];
    $paymentMethod = $_POST['payment_method'];

    // Process payment
    if ($paymentMethod === 'CreditCard') {
        $payment = new CreditCard();
    } else {
        $payment = new CashOnDelivery();
    }

    $payment->processTransaction($total);  // Process the payment

    // Save delivery details
    $delivery = new Delivery($conn);
    $delivery->order_id = $order_id;
    $delivery->delivery_mode = $_POST['delivery_mode'];
    $delivery->address = $deliveryAddress;
    $delivery->status = 'pending';
    $delivery->create();

    // Update order status
    $orderQuery = "UPDATE orders SET status = 'completed' WHERE id = :order_id";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    // Clear the cart session
    unset($_SESSION['cart']);
    header("Location: receipt.php");  // Redirect to confirmation page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Order Confirmation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-light">
                            Welcome, <?php echo $_SESSION['username']; ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Your Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <form action="logout.php" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Order Confirmation Form -->
    <div class="container mt-5">
        <h2>Confirm Your Order</h2>
        
        <div class="alert alert-info">
            <strong>Order ID:</strong> <?php echo $order_id; ?><br>
            <strong>Total Amount:</strong> $<?php echo number_format($total, 2); ?>
        </div>

        <form action="" method="POST">
            <!-- Delivery Address -->
            <div class="mb-3">
                <label for="delivery_address" class="form-label">Delivery Address:</label>
                <input type="text" name="delivery_address" class="form-control" required>
            </div>

            <!-- Delivery Mode -->
            <div class="mb-3">
                <label for="delivery_mode" class="form-label">Delivery Mode:</label>
                <select name="delivery_mode" class="form-select" required>
                    <option value="StandardDelivery">Standard Delivery</option>
                    <option value="ExpressDelivery">Express Delivery</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method:</label>
                <select name="payment_method" class="form-select" required>
                    <option value="CashOnDelivery">Cash on Delivery</option>
                    <option value="CreditCard">Credit Card</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Confirm Order</button>
        </form>

        <hr>
        <p><a href="menu.php" class="btn btn-secondary">Back to Menu</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
