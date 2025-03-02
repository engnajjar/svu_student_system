<?php

// Include the configuration file for database credentials
require "../config.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trigger the function to create the database
    create_svu_db();
}

/**
 * Function to create the SVU database and tables.
 */
function create_svu_db()
{
    global $servername, $username, $password, $dbname;

    // Establish a connection to the MySQL server
    $conn = new mysqli($servername, $username, $password);

    // Check for connection errors
    if ($conn->connect_error) {
        response_json(['success' => 0, 'error' => "Connection failed: " . $conn->connect_error]);
        return;
    }

    // Check if the database already exists
    $dbSelected = $conn->select_db($dbname);

    if ($dbSelected) {
        // Database exists, send a success response
        response_json(['success' => 1, 'message' => "Database already exists"]);
    } else {
        // Absolute path to the SQL file
        $sqlFilePath = __DIR__ . '/db.sql';

        // Check if the SQL file exists
        if (!file_exists($sqlFilePath)) {
            response_json(['success' => 0, 'error' => "SQL file not found."]);
            return;
        }

        // Read the contents of the SQL file
        $sql = file_get_contents($sqlFilePath);
        if (!$sql) {
            response_json(['success' => 0, 'error' => "Error reading SQL file."]);
            return;
        }

        // Execute the SQL statements using multi_query
        if ($conn->multi_query($sql)) {
            do {
                // Free any results from the current query
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->next_result()); // Move to the next query
            response_json(['success' => 1, 'message' => "Database and tables created successfully"]);
        } else {
            // Handle any errors during the query execution
            response_json(['success' => 0, 'error' => $conn->error]);
        }
    }

    // Close the database connection
    $conn->close();
}

/**
 * Function to send a JSON response and terminate the script.
 *
 * @param array $data The data to send as a JSON response.
 */
function response_json($data)
{
    echo json_encode($data);
    exit;
}
