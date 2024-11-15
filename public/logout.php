<?php
session_start();  // Start the session

// Preserve the cart by saving it into a separate session variable
if (isset($_SESSION['cart'])) {
    $_SESSION['cart_backup'] = $_SESSION['cart'];
}

// Destroy the session (logout the user)
session_unset();
session_destroy();

// After logging out, redirect to the login page
header("Location: index.php");
exit();
