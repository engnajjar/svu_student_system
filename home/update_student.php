<?php
// Include database connection details
require "../config.php";

// Check if the form was submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    update_student();  // Call the update function
}

// Function to handle the student update process
function update_student()
{
    // Global database connection credentials
    global $servername, $username, $password, $dbname;

    // Create a new connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the database connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);  // Stop if the connection fails
    }

    // Fetch form data from POST request
    $id = $_POST['id'];  // Student ID
    $name = $_POST['name'];  // Student Name
    $email = $_POST['email'];  // Student Email
    $phone = $_POST['phone'];  // Student Phone
    $address = $_POST['address'];  // Student Address
    $file_url = !isset($_FILES['profile_picture']) && strpos($_POST['profile_picture'], 'http') !== false ? $_POST['profile_picture'] : '';  // Check for profile picture URL or file

    // Initialize profile picture URL (if uploaded)
    $profile_picture = '';

    // If a profile picture was uploaded, handle the upload process
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        // Define the upload directory
        $target_dir = "uploads/";

        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  // Create the uploads directory with proper permissions
        }

        $file = $_FILES['profile_picture'];  // Get the uploaded file
        $base_file = $target_dir . basename($file['name']);  // Get the base name of the uploaded file
        $fileType = strtolower(pathinfo($base_file, PATHINFO_EXTENSION));  // Get the file extension

        // Generate the target file path using the student's email (to avoid filename conflicts)
        $target_file = $target_dir . $email . '.' . $fileType;

        // If the file already exists, delete it to avoid overwriting
        if (file_exists($target_file)) {
            unlink($target_file);  // Remove the existing file
        }

        // Validate the file type (optional)
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($fileType, $allowed_types)) {
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Construct the full URL of the uploaded file
                $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']);
                $file_url = $base_url . "/" . $target_file;  // Full URL of the uploaded file
            } else {
                // Return error if the file upload failed
                http_response_code(500);
                echo json_encode(['success' => 0, 'error' => 'Error uploading file.']);
                return;
            }
        } else {
            // Return error if the file type is invalid
            echo json_encode(['success' => 0, 'error' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            return;
        }
    }

    // Prepare SQL statement to update the student record
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $address, $file_url, $id);  // Bind parameters to the query

    // Execute the update query
    if ($stmt->execute()) {
        echo json_encode(['success' => 1]);  // Return success if the update was successful
    } else {
        echo json_encode(['success' => 0, 'error' => $stmt->error]);  // Return error if the update failed
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
