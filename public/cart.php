<?php
session_start();  // Start session

require_once "../config/Database.php";  // Include the database connection
require_once "../classes/MenuItem.php"; // Include MenuItem class

$db = new Database();
$conn = $db->getConnection();
$menuItem = new MenuItem($conn);

// Initialize the cart from session, or an empty array if not set
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Handle remove item from cart
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    // Remove the item from the cart
    if (isset($cart[$remove_id])) {
        unset($cart[$remove_id]);
        $_SESSION['cart'] = $cart;  // Update the session
    }

    // Redirect to avoid form resubmission
    header("Location: cart.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Menu</a>
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

    <!-- Cart Content -->
    <div class="container mt-5">
        <h2>Your Cart</h2>
        
        <?php if (empty($cart)) : ?>
            <div class="alert alert-info">
                Your cart is empty.
            </div>
        <?php else: ?>
            <form action="checkout.php" method="post">
                <?php 
                $total = 0;
                foreach ($cart as $item_id => $quantity): 
                    // Fetch item details from the database
                    $menuItem->id = $item_id;
                    $stmt = $menuItem->read();  // Fetch item details using the read() method

                    // Check if the query returned a result
                    if ($stmt && $item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $total += $item['price'] * $quantity;
                ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text">
                                <strong>Price: $<?php echo number_format($item['price'], 2); ?></strong><br>
                                Quantity: <?php echo $quantity; ?><br>
                                <a href="cart.php?remove_id=<?php echo $item_id; ?>" class="text-danger">Remove</a>
                            </p>
                        </div>
                    </div>
                <?php 
                    } else {
                        // If no item was found, display a message
                        echo "<p>Item with ID $item_id not found in the database.</p>";
                    }
                endforeach; 
                ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                    <button type="submit" class="btn btn-success">Proceed to Checkout</button>
                </div>
            </form>
        <?php endif; ?>
        
        <p><a href="menu.php" class="btn btn-secondary mt-3">Back to Menu</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
