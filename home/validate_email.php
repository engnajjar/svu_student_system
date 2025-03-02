<?php

// Check if the form was submitted via POST request and the 'check_email' field is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_email'])) {

    $email = $_POST['check_email'];  // Get the email to be validated
    $validation_result = validate_email($email);  // Validate the email using the function

    // Return the validation result in JSON format
    echo json_encode(['status' => $validation_result, 'check_email' => $email]);
    exit;  // Terminate the script after sending the response
}

// Function to validate the email address using PHP's filter_var() function
function validate_email($email)
{
    // Return 'valid' if email is valid, otherwise 'not valid'
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? 'valid' : 'not valid';
}
