<?php

// Check if a POST request with the 'action' parameter is set
if (isset($_POST['action'])) {
    // Call the function to display the student view form
    view_student();
}

/**
 * Function to display a form for viewing student details.
 */
function view_student()
{
?>
    <!-- Start of the form for viewing student details -->
    <form id="add-student-form" action="" method="" enctype="multipart/form-data">

        <!-- Section to display student's name -->
        <div class="form-group viewstd">
            <label for="view_name">Name:</label>
            <div id="view_name" name="view_name"></div> <!-- Placeholder for the student's name -->
        </div>

        <!-- Section to display student's email -->
        <div class="form-group viewstd">
            <label for="view_email">Email:</label>
            <div id="view_email" name="view_email"></div> <!-- Placeholder for the student's email -->
        </div>

        <!-- Section to display student's phone number -->
        <div class="form-group viewstd">
            <label for="view_phone">Phone Number:</label>
            <div id="view_phone" name="view_phone"></div> <!-- Placeholder for the student's phone number -->
        </div>

        <!-- Section to display student's address -->
        <div class="form-group viewstd">
            <label for="view_address">Address:</label>
            <div id="view_address" name="view_address"></div> <!-- Placeholder for the student's address -->
        </div>

        <!-- Section to display student's profile picture -->
        <div class="form-group viewstd">
            <label for="profile_picture">Profile picture:</label>
            <div id="file-preview"></div> <!-- Placeholder for the student's profile picture preview -->
        </div>
    </form>
    <!-- End of the form -->
<?php
}
