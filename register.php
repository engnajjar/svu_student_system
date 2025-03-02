<?php
require "config.php"; // Include the configuration file for database connection

// Check if the request method is POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    svu_register(); // Call the registration function
}

function svu_register()
{
    // Use global variables for database connection
    global $servername, $username, $password, $dbname;

    // Create a new connection to the MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); // If connection fails, terminate the script
    }

    // Retrieve form data from the POST request
    $username = $_POST['username'];
    $email = $_POST['register_email'];
    $password = $_POST['register_password'];

    // Check if the provided username or email already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email); // Bind the parameters to the prepared statement
    $stmt->execute(); // Execute the query
    $stmt->store_result(); // Store the result of the query

    // If the username or email already exists, return an error message
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => 0, 'error' => "Username or email already exists."]); // Return JSON response with error message
    } else {

        // Hash the password using PASSWORD_DEFAULT
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement to insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $email, $hashed_password); // Bind the parameters to the prepared statement

        // Execute the query to insert the new user
        if ($stmt->execute()) {
            // Return success message in JSON format if registration is successful
            echo json_encode(['success' => 1, 'message' => 'Registration successful']);
        } else {
            // Return error message in JSON format if there is an issue with the query
            echo json_encode(['success' => 0, 'error' => $stmt->error]);
        }
    }

    // Close the prepared statement
    $stmt->close();
}
