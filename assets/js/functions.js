// Function to check if the database exists or needs to be created
function checking_svu_db() {
  // Display an alert indicating that the database check is in progress
  svu_alert("Checking database, please wait", "alert");

  // After a 2-second delay, proceed with the AJAX request
  setTimeout(function () {
    var formData = new FormData();
    formData.append("action", "create_svu_db");

    // Send a POST request to db.php to check or create the database
    $.ajax({
      url: "db.php",
      type: "POST",
      data: formData,
      contentType: false, // Prevent jQuery from overriding content type
      processData: false, // Prevent jQuery from processing the data
      success: function (response) {
        var result = JSON.parse(response);
        if (result.success == "1") {
          // If database is created successfully, show success alert and redirect after 1 second
          svu_alert(result.message, "done");
          setTimeout(function () {
            var url = window.location.href.replace("/index.php", "");
            url = url.replace("/db", "");
            location.href = url;
          }, 1000);
        } else {
          // If error occurred, show an error alert with the message
          svu_alert(result.error, "error", 4000);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }, 2000);
}

// Function to handle user registration
function register(username, register_email, register_password) {
  var formData = new FormData();
  formData.append("action", "svu_register");
  formData.append("username", username);
  formData.append("register_email", register_email);
  formData.append("register_password", register_password);

  // Send a POST request to register.php to register the user
  $.ajax({
    url: "register.php",
    type: "POST",
    data: formData,
    contentType: false, // Prevent jQuery from overriding content type
    processData: false, // Prevent jQuery from processing the data
    success: function (response) {
      console.log(response);
      var result = JSON.parse(response);
      if (result.success) {
        // If registration is successful, show success alert and redirect to home page
        svu_alert(result.message, "done");
        var encodedUsername = encodeURIComponent(username);
        window.location.href =
          "home/index.php?loggedin_user=" + encodedUsername;
      } else {
        // If error occurred during registration, show error alert with the message
        svu_alert(result.error, "error", 4000);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to load the student addition form via AJAX
function add_student_form() {
  $(".svu_loading").show();
  $("#tab_container").fadeOut("fast");
  $.ajax({
    url: "add_student_form.php",
    type: "POST",
    data: {
      action: "add_student_form",
    },
    success: function (response) {
      $(".svu_loading").hide();
      $("#tab_container").html(response).fadeIn("fast");
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to add a new student
function add_student(name, email, phone, address, profile_picture) {
  var formData = new FormData();
  formData.append("action", "add_student");
  formData.append("name", name);
  formData.append("email", email);
  formData.append("phone", phone);
  formData.append("address", address);
  formData.append("profile_picture", profile_picture);

  // Send a POST request to add_student.php to save the new student
  $.ajax({
    url: "add_student.php",
    type: "POST",
    data: formData,
    contentType: false, // Prevent jQuery from overriding content type
    processData: false, // Prevent jQuery from processing the data
    success: function (response) {
      var result = JSON.parse(response);
      console.log(response);
      if (result.success) {
        // If student is added successfully, show success alert and reload the page
        svu_alert("Student is added successfully", "done", 4000);
        location.reload(true);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to load the student update form
function update_student_form(id, name, email, phone, address, profile_picture) {
  $(".svu_loading").show();
  $("#tab_container").fadeOut("fast");
  $.ajax({
    url: "update_student_form.php",
    type: "POST",
    data: {
      action: "update_student",
    },
    success: function (response) {
      // Enable the update student tab and display the form
      $("#update_student").parent().removeClass("disabled-tab");
      $("#update_student").css({
        "pointer-events": "",
        cursor: "",
        color: "",
        opacity: "",
      });
      $("#update_student").click();
      $(".svu_loading").hide();
      $("#tab_container").html(response).fadeIn("fast");

      // Fill the form with the student's current data
      $("#studentId").val(id);
      $("#update_name").val(name);
      $("#update_email").val(email);
      $("#update_phone").val(phone);
      $("#update_address").val(address);

      // Show email validation message
      $("#email-error").text("Email is valid ✔");
      $("#email-error").css("color", "#00ac33");

      // Display profile picture if available
      if (profile_picture) {
        $("#file-preview").html(`
          <div style="display: inline-block; position: relative;">
              <img src="${profile_picture}" alt="Image Preview" style="max-width: 200px; margin-top: 10px;">
              <button id="reset-preview" style="
                  position: absolute;
                  top: 0;
                  right: 0;
                  background: red;
                  color: white;
                  border: none;
                  border-radius: 50%;
                  cursor: pointer;
                  width: 20px;
                  height: 20px;
                  font-size: 14px;
                  line-height: 18px;
                  text-align: center;
              ">&times;</button>
          </div>
      `);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to update the student's data
function update_student(id, name, email, phone, address, profile_picture) {
  var formData = new FormData();
  formData.append("action", "add_student");
  formData.append("id", id);
  formData.append("name", name);
  formData.append("email", email);
  formData.append("phone", phone);
  formData.append("address", address);
  formData.append("profile_picture", profile_picture);

  // Send a POST request to update_student.php to save the updates
  $.ajax({
    url: "update_student.php",
    type: "POST",
    data: formData,
    contentType: false, // Prevent jQuery from overriding content type
    processData: false, // Prevent jQuery from processing the data
    success: function (response) {
      var result = JSON.parse(response);
      console.log(response);
      if (result.success) {
        // If student data is updated successfully, show success alert and reload the page
        svu_alert("Successfully updated", "done", 4000);
        location.reload(true);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to delete a student
function delete_student(id) {
  // Confirm with the user before deleting the student
  var stdConfirm = window.confirm(
    "Are you sure you want to delete this student?"
  );
  if (stdConfirm) {
    var formData = new FormData();
    formData.append("action", "delete_student");
    formData.append("id", id);

    // Send a POST request to delete_student.php to remove the student from the database
    $.ajax({
      url: "delete_student.php",
      type: "POST",
      data: formData,
      contentType: false, // Prevent jQuery from overriding content type
      processData: false, // Prevent jQuery from processing the data
      success: function (response) {
        var result = JSON.parse(response);
        console.log(response);
        if (result.success) {
          // If student is deleted successfully, show success alert and reload the page
          svu_alert("Successfully deleted", "done", 4000);
          location.reload(true);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }
}

// Function to view the list of students
function view_students(page) {
  $(".svu_loading").show();
  $("#tab_container").fadeOut("fast");

  /* Send a POST request to view_students.php
  to fetch the student list for the given page*/
  $.ajax({
    url: "view_students.php",
    type: "POST",
    data: {
      action: "view_students",
      page: page,
    },
    success: function (response) {
      $(".svu_loading").hide();
      $("#tab_container").html(response).fadeIn("fast");
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to display student details
function view_student_form(id, name, email, phone, address, profile_picture) {
  $(".svu_loading").show();
  $("#tab_container").fadeOut("fast");

  $.ajax({
    url: "view_student_form.php",
    type: "POST",
    data: {
      action: "view_student",
    },
    success: function (response) {
      // Enable the view student tab and display the details
      $("#view_student").parent().removeClass("disabled-tab");
      $("#view_student").css({
        "pointer-events": "",
        cursor: "",
        color: "",
        opacity: "",
      });
      $("#view_student").click();
      $(".svu_loading").hide();
      $("#tab_container").html(response).fadeIn("fast");

      // Display the student's details in the form
      $("#view_name").text(name);
      $("#view_email").text(email);
      $("#view_phone").text(phone);
      $("#view_address").text(address);

      // Show the profile picture if available
      if (profile_picture) {
        $("#file-preview").html(`
          <div style="display: inline-block; position: relative;">
              <img src="${profile_picture}" alt="Image Preview" style="max-width: 200px; margin-top: 10px;">
          </div>
      `);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}

// Function to toggle the dropdown visibility
function showDropdown(element) {
  // Hide all dropdown contents except the one related to the clicked element
  document.querySelectorAll(".dropdown-content").forEach(function (dropdown) {
    // Check if the dropdown isn't the one we want to show and hide it
    if (dropdown !== element.nextElementSibling) {
      dropdown.classList.remove("show");
    }
  });

  // Toggle the visibility of the related dropdown
  element.nextElementSibling.classList.toggle("show");
}

// Function to display custom alerts
function svu_alert(message, type, duration = 3000) {
  // Get the alert box element by its ID
  var alertBox = document.getElementById("svu_alert");

  // Set the message to be displayed in the alert box
  alertBox.textContent = message;

  // Change the background color of the alert box based on the alert type
  switch (type) {
    case "done":
      // Success: Green background
      alertBox.style.backgroundColor = "#388c00";
      break;
    case "alert":
      // Warning: Orange background
      alertBox.style.backgroundColor = "#ff7b00";
      break;
    default:
      // Error: Red background (default case)
      alertBox.style.backgroundColor = "#dc3545";
      break;
  }

  // Display the alert box
  alertBox.style.display = "block";

  // Hide the alert box after the specified duration (default: 3000ms)
  setTimeout(function () {
    alertBox.style.display = "none";
  }, duration);
}

// Function to validate email address
function validate_email(check_email, mode) {
  var url =
    mode == 0
      ? "validate_email.php"
      : window.location.href.split("?")[0] + "/home/validate_email.php";
  url = url.replace("index.php/home", "/home");
  console.log("new url: " + url);
  $.ajax({
    url: url,
    type: "POST",
    data: {
      action: "validate_email",
      check_email: check_email,
    },
    success: function (response) {
      var result = JSON.parse(response);
      console.log("result.status: " + result.status);
      // Show the appropriate email validation message
      if (result.status == "valid") {
        $("#email-error").css("color", "#00ac33");
        $("#email-error").text("Email is valid ✔");
      } else {
        $("#email-error").css("color", "red");
        $("#email-error").text("Email is not valid ✖");
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: " + status + error);
    },
  });
}
