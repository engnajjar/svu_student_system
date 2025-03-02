<?php
// Include configuration for database credentials
require "config.php";

// Start the session
session_start();

// Access global variables for database connection
global $servername, $username, $password, $dbname;

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve login credentials from the POST request
    $email_or_username = $_POST['login_email'];
    $password = $_POST['login_password'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $stmt->store_result();

    // Check if any user matches the email or username
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $email, $hashed_password);
        $stmt->fetch();

        // Verify the entered password with the hashed password in the database
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session and cookie
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Set a secure cookie with HTTPOnly and Secure flags
            setcookie("loggedin_username", $username, time() + (7 * 24 * 60 * 60), "/", "", true, true);

            // Redirect to the home page
            header("Location: home/index.php?loggedin_user=" . urlencode($username));
            exit();
        } else {
            // Invalid password, redirect to login page with an error
            header("Location: index.php?error=1");
            exit();
        }
    } else {
        // No user found with the provided credentials
        header("Location: index.php?error=1");
        exit();
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
