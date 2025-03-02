<?php
// Include the configuration file to access database credentials
require "../config.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_student(); // Call the function to add a new student
}

function add_student()
{
    // Access global database connection variables
    global $servername, $username, $password, $dbname;

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection is successful, otherwise terminate with an error message
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data from the POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $file_url = ''; // Initialize an empty string for the profile picture URL

    // Handle file upload for the profile picture
    $profile_picture = ''; // Initialize the profile picture variable
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {

        // Directory where the uploaded files will be stored
        $target_dir = "uploads/";

        // Create the uploads directory if it doesn't already exist
        if (!is_dir($target_dir)) {

            // Create the directory with full read/write/execute permissions
            mkdir($target_dir, 0777, true);
        }

        // Get the uploaded file information
        $file = $_FILES['profile_picture'];

        // The file path with its name
        $base_file = $target_dir . basename($file['name']);

        // Get the file extension (e.g., jpg, png)
        $fileType = strtolower(pathinfo($base_file, PATHINFO_EXTENSION));

        // Define the target file path with the email as the filename to ensure uniqueness
        $target_file = $target_dir . $email . '.' . $fileType;

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {

            // Generate the full URL of the uploaded file
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']);

            // Construct the full URL for the uploaded file
            $file_url = $base_url . "/" . $target_file;
        } else {

            // Send a 500 error response if the file upload fails
            http_response_code(500);
        }
    }

    // Prepare the SQL statement to insert the student details into the database
    $stmt = $conn->prepare("INSERT INTO students (name, email, phone, address, profile_picture) VALUES (?, ?, ?, ?, ?)");

    // Bind the parameters to the SQL query
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $file_url);

    // Execute the SQL query and check if the insertion is successful
    if ($stmt->execute()) {
        //echo "New student added successfully."; 
        echo json_encode(['success' => 1]); // Return a JSON response indicating success
    } else {
        //echo "Error: " . $stmt->error; 
        echo json_encode(['success' => 0, 'error' => $stmt->error]); // Return a JSON response with the error message
    }

    // Close the prepared statement to free resources
    $stmt->close();
}
