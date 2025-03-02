<?php

// Check if the 'action' POST parameter is set, which indicates the form submission
if (isset($_POST['action'])) {
    add_student_form(); // Call the function to display the student registration form
}

function add_student_form()
{
?>
    <!-- The form to add a student with various fields for input -->
    <form id="add-student-form" action="add_student.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <!-- Name input field -->
            <label for="add_name">Name:</label>
            <input type="text" class="form-control" id="add_name" name="add_name" required>
        </div>
        <div class="form-group">
            <!-- Email input field with a live error message area -->
            <label for="add_email">Email:</label>
            <input type="email" class="form-control" id="add_email" name="add_email" required>
            <div id="email-error" class="text-danger"></div> <!-- Error message display for email -->
        </div>
        <div class="form-group">
            <!-- Phone number input field -->
            <label for="add_phone">Phone Number:</label>
            <input type="text" class="form-control" id="add_phone" name="add_phone" required>
        </div>
        <div class="form-group">
            <!-- Address input field -->
            <label for="add_address">Address:</label>
            <textarea class="form-control" id="add_address" name="add_address" required></textarea>
        </div>
        <div class="form-group">
            <!-- File input for profile picture with a preview feature -->
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept=".jpg, .jpeg, .png, .gif">
            <div id="file-preview"></div> <!-- Preview of the selected image file -->
        </div>
        <!-- Submit button for the form -->
        <button type="submit" class="btn btn-primary" id="add_student_btn">Add Student</button>
    </form>
    <div id="error-message" class="mt-3 text-danger"></div> <!-- Area to show form submission error messages -->
    </div>

    <script>
        $(document).ready(function() {
            // Handle the form submission event
            $('#add-student-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior
                var emailError = $('#email-error').text(); // Get the text of the email error message
                // If the email is not valid, show an error message
                if (emailError !== 'Email is valid ✔') {
                    $('#error-message').text('Please fix the errors before submitting.');
                } else {
                    // Gather the form data
                    var name = $('#add_name').val();
                    var email = $('#add_email').val();
                    var phone = $('#add_phone').val();
                    var address = $('#add_address').val();

                    // Get the file input for profile picture
                    var profile_picture = $('#profile_picture')[0].files[0];

                    // Call a function to handle the student addition 
                    add_student(name, email, phone, address, profile_picture);
                }

            });

            // Handle file input change (when a user selects a file)
            $('#profile_picture').on('change', function(event) {
                var file = event.target.files[0]; // Get the selected file
                if (file) {
                    var reader = new FileReader(); // Create a new FileReader to read the file
                    reader.onload = function(e) {
                        // Show the image preview with a close button after loading the file
                        $('#file-preview').html(`
                            <div style="display: inline-block; position: relative;">
                                <img src="${e.target.result}" alt="Image Preview" style="max-width: 200px; margin-top: 10px;">
                                <button id="reset-preview">&times;</button> <!-- Close button to remove the preview -->
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file); // Read the file as a data URL for preview
                } else {
                    $('#file-preview').html(''); // If no file is selected, clear the preview
                }
            });

            // Reset the image preview when the close button is clicked
            $('#file-preview').on('click', '#reset-preview', function() {
                $('#profile_picture').val(''); // Clear the file input
                $('#file-preview').html(''); // Clear the preview
            });

            // Validate email input on every keyup event (to check the validity as the user types)
            $("#add_email").keyup(function() {
                var email = $(this).val(); // Get the current email value
                validate_email(email, 0); // Call a function to validate the email
            });
        });
    </script>
<?php
}
