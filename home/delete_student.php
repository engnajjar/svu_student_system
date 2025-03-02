<?php
// Include the configuration file to access database credentials
require "../config.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    delete_student(); // Call the function to delete the student
}

function delete_student()
{
    // Access global database connection variables
    global $servername, $username, $password, $dbname;

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection is successful, otherwise terminate with an error message
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the student ID from the POST request
    $id = $_POST['id'];

    // Prepare the SQL statement to delete a student record by ID
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind the student ID parameter to the SQL query

    // Execute the delete query and check if it was successful
    if ($stmt->execute()) {
        // Return a JSON response indicating success
        echo json_encode(['success' => 1, 'message' => 'Record deleted successfully']);
    } else {
        // Return a JSON response with an error message if the query fails
        echo json_encode(['success' => 0, 'error' => $stmt->error]);
    }

    // Close the prepared statement and the database connection to free resources
    $stmt->close();
    $conn->close();
}
