<?php

class User {
    private $conn;
    public $id;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Function to check if the username already exists
    public function checkIfUserExists($username) {
        $query = "SELECT id FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; // User exists
        }
        return false; // User does not exist
    }

    // Login function (already implemented)
    public function login($username, $password) {
        $query = "SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];

                return true;  // Credentials are correct
            }
        }

        return false;  // Invalid credentials
    }

    // Other functions as needed
}
