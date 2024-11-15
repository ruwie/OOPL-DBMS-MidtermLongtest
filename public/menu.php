<?php
session_start();  // Start session

require_once "../config/Database.php";  // Include the database connection
require_once "../classes/MenuItem.php"; // Include MenuItem class

$db = new Database();
$conn = $db->getConnection();
$menuItem = new MenuItem($conn);

// Fetch all menu items from the database
$query = "SELECT id, name, description, price FROM menu_items";
$stmt = $conn->prepare($query);
$stmt->execute();
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize the cart from session, or an empty array if not set
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Handle add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $quantity = isset($cart[$item_id]) ? $cart[$item_id] + 1 : 1;  // If item exists, increase quantity, else set to 1
    
    // Add/Update the item in the cart
    $cart[$item_id] = $quantity;
    $_SESSION['cart'] = $cart;  // Update session cart

    // Optionally, redirect to avoid form resubmission (e.g., to cart.php)
    header("Location: cart.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
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
                        <a class="nav-link" href="cart.php">View Cart</a>
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

    <!-- Menu Items Section -->
    <div class="container mt-5">
        <h3 class="text-center mb-4">Menu</h3>

        <div class="row">
            <?php
            // Loop through the menu items and display them in Bootstrap cards
            foreach ($menu_items as $item) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card'>";
                
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($item['name']) . "</h5>";
                echo "<p class='card-text'>" . htmlspecialchars($item['description']) . "</p>";
                echo "<p><strong>Price: $" . number_format($item['price'], 2) . "</strong></p>";
                echo "<form action='menu.php' method='POST'>
                        <input type='hidden' name='item_id' value='" . $item['id'] . "'>
                        <button type='submit' class='btn btn-primary w-100'>Add to Cart</button>
                      </form>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
