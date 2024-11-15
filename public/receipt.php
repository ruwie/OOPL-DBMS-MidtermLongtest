<?php
// public/receipt.php
session_start();

if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once "../config/Database.php";

// Create a database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch the order details using the order ID from the session
$order_id = $_SESSION['order_id'];

// Get order details from the 'orders' table
$orderQuery = "SELECT * FROM orders WHERE id = :order_id LIMIT 1";
$stmt = $conn->prepare($orderQuery);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order items from the 'order_items' table
$orderItemsQuery = "SELECT oi.item_id, oi.quantity, m.name, m.price
                    FROM order_items oi
                    JOIN menu_items m ON oi.item_id = m.id
                    WHERE oi.order_id = :order_id";
$stmt = $conn->prepare($orderItemsQuery);
$stmt->bindParam(':order_id', $order_id);
// $stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total order amount (in case it's needed for some reason)
$totalAmount = 0;
foreach ($orderItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">Order Receipt</h2>
                
                <!-- Display Order Details -->
                <div class="order-details mb-4">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

                    <!-- Check if 'delivery_address' exists -->
                    <?php if (isset($order['delivery_address'])): ?>
                        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <?php else: ?>
                        <p><strong>Delivery Address:</strong> Not provided</p>
                    <?php endif; ?>

                    <!-- Check if 'payment_method' exists -->
                    <?php if (isset($order['payment_method'])): ?>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <?php else: ?>
                        <p><strong>Payment Method:</strong> Not selected</p>
                    <?php endif; ?>
                </div>

                <h3>Items Ordered:</h3>
                <ul class="list-group mb-4">
                    <?php 
                    // Loop through the items and display them
                    foreach ($orderItems as $item) {
                        $itemTotal = $item['price'] * $item['quantity'];
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                <span><strong>" . htmlspecialchars($item['name']) . "</strong> - $"
                                . number_format($item['price'], 2) . " x " . $item['quantity']
                                . "</span>
                                <span><strong>$" . number_format($itemTotal, 2) . "</strong></span>
                              </li>";
                    }
                    ?>
                </ul>

                <div class="d-flex justify-content-between">
                    <h4><strong>Grand Total:</strong> $<?php echo number_format($totalAmount, 2); ?></h4>
                    <a href="menu.php" class="btn btn-primary">Back to Menu</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
