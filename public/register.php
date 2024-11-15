<?php
session_start();
require_once "../config/Database.php";  // Include the database connection
require_once "../classes/User.php";    // Include the User class

// Initialize variables for errors and success message
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Simple validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create a new User object
        $db = new Database();
        $conn = $db->getConnection();
        $user = new User($conn);

        // Check if the username already exists
        if ($user->checkIfUserExists($username)) {
            $error = "Username already exists. Please choose a different one.";
        } else {
            // Default role is 'customer'
            $role = 'customer';

            // Prepare SQL query to insert the new user into the database
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);

            // Execute the query
            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
                header("Location: index.php");  // Redirect to the login page after registration
                exit();
            } else {
                $error = "There was an error registering the user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Link to your custom Bootstrap CSS file -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">User Registration</h2>

                <!-- Display error or success messages -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="card p-4">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password:</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <p class="text-center mt-3">Already have an account? <a href="index.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>
