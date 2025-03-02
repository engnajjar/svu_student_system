<?php

// Check if the 'action' parameter is set in the POST request
if (isset($_POST['action'])) {
    update_student(); // Call the update_student function if action is set
}

// Function to render the student update form
function update_student()
{
?>
    <!-- HTML form for updating student details -->
    <form id="add-student-form" action="update_student.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="update_name">Name:</label>
            <input type="text" class="form-control" id="update_name" name="update_name" required>
        </div>
        <div class="form-group">
            <label for="update_email">Email:</label>
            <input type="email" class="form-control" id="update_email" name="update_email" required>
            <div id="email-error" class="text-danger"></div>
        </div>
        <div class="form-group">
            <label for="update_phone">Phone Number:</label>
            <input type="text" class="form-control" id="update_phone" name="update_phone" required>
        </div>
        <div class="form-group">
            <label for="update_address">Address:</label>
            <textarea class="form-control" id="update_address" name="update_address" required></textarea>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept=".jpg, .jpeg, .png, .gif">
            <div id="file-preview"></div>
        </div>
        <div class="form-group">
            <input type="hidden" id="studentId" name="studentId" value="">
        </div>
        <button type="submit" class="btn btn-primary" id="update_student_btn">Update Student</button>
    </form>
    <div id="error-message" class="mt-3 text-danger"></div>
    </div>

    <!-- jQuery for validation and AJAX functionality -->
    <script>
        $(document).ready(function() {
            // Form submission handler
            $('#add-student-form').on('submit', function(event) {
                event.preventDefault(); // Prevent the form from submitting normally

                // Check if email is valid
                var emailError = $('#email-error').text();
                if (emailError !== 'Email is valid ✔') {
                    $('#error-message').text('Please fix the errors before submitting.');
                } else {
                    // Gather form data
                    var id = $('#studentId').val();
                    var name = $('#update_name').val();
                    var email = $('#update_email').val();
                    var phone = $('#update_phone').val();
                    var address = $('#update_address').val();
                    var profile_picture = $('#profile_picture')[0].files[0] ? $('#profile_picture')[0].files[0] : $('#file-preview img').attr('src');

                    // Call the function to update the student details
                    update_student(id, name, email, phone, address, profile_picture);
                }
            });

            // Validate email on keyup (input)
            $("#update_email").keyup(function() {
                var email = $(this).val();
                validate_email(email, 0);
            });

            // Handle file input for profile picture preview
            $('#profile_picture').on('change', function(event) {
                var file = event.target.files[0]; // Get the selected file
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        // Display image preview with a close button
                        $('#file-preview').html(`
                            <div style="display: inline-block; position: relative;">
                                <img src="${e.target.result}" alt="Image Preview" style="max-width: 200px; margin-top: 10px;">
                                <button id="reset-preview">&times;</button>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#file-preview').html('');
                }
            });

            // Reset the file preview when the close button is clicked
            $('#file-preview').on('click', '#reset-preview', function() {
                $('#profile_picture').val(''); // Clear the file input
                $('#file-preview').html(''); // Remove the preview
            });
        });
    </script>
<?php
}
?>