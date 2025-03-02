<?php
require "config.php";
global $servername, $username, $password, $dbname;

// Helper function for JSON responses
function response_json($response)
{
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  response_json(['success' => 0, 'error' => "Connection failed: " . $conn->connect_error]);
  die("Connection failed: " . $conn->connect_error);
}

// Check if the database exists
$dbSelected = $conn->select_db($dbname);

if ($dbSelected) {
  // Check if the user is logged in via session or cookie
  session_start();
  if (isset($_SESSION['user_id'])) {
    header("Location: home/index.php?loggedin_user=" . urlencode($_SESSION['username']));
    exit;
  } elseif (isset($_COOKIE['loggedin_username'])) {
    $_SESSION['username'] = $_COOKIE['loggedin_username'];
    header("Location: home/index.php?loggedin_user=" . urlencode($_SESSION['username']));
    exit;
  }
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />

    <!-- Ensures responsive design on all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>SVU Student System</title>

    <!-- Includeing Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Encode+Sans+Semi+Condensed:wght@100;900&family=Noto+Kufi+Arabic:wght@100..900&display=swap"
      rel="stylesheet" />

    <!-- Including Styles -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />

    <!-- Include jQuery library -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Include Botstrap library -->
    <script src="assets/js/bootstrap.min.js"></script>
  </head>

  <body>
    <div>
      <a href="index.php" class="logo-link">
        <img src="assets/img/svu_logo.png" alt="svu_logo" class="svu_logo">
        <h1 class="logo">
          <span class="logo_1">Student</span><span class="logo_2"> ★ ★ ★ </span><span class="logo_3">System</span>
        </h1>
      </a>
      <div class="svu_container login">
        <ul class="nav nav-tabs flex">
          <li class="active"><a data-toggle="tab" href="#login">Login</a></li>
          <li><a data-toggle="tab" href="#register">Register</a></li>
        </ul>

        <div class="tab-content">
          <div class="loader_bg">
            <div class="loader"></div>
          </div>

          <!-- Login Tab -->
          <div id="login" class="tab-pane fade in active">
            <div class="spacer"></div>
            <form action="login.php" method="post">
              <div class="form-group">
                <label for="login_email">Email or Username:</label>
                <input type="text" class="form-control" id="login_email" name="login_email" required />
              </div>
              <div class="form-group">
                <label for="login_password">Password:</label>
                <input type="password" class="form-control" id="login_password" name="login_password" required />
              </div>
              <div class="btn_submit">
                <button type="submit" class="btn" id="login_btn">Login</button>
              </div>
            </form>
            <div id="error-message" class="mt-3 text-danger"></div>
          </div>

          <!-- Register Tab -->
          <div id="register" class="tab-pane fade">
            <div class="spacer"></div>
            <form action="" method="post" id="register-form">
              <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required />
              </div>
              <div class="form-group">
                <label for="register_email">Email:</label>
                <input type="email" class="form-control" id="register_email" name="register_email" required />
              </div>
              <div id="email-error" class="text-danger"></div>
              <div class="form-group">
                <label for="register_password">Password:</label>
                <input type="password" class="form-control" id="register_password" name="register_password" required />
              </div>
              <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required />
              </div>
              <div id="password-error" class="text-danger"></div>
              <div class="btn_submit">
                <button class="btn" id="register_btn">Register</button>
              </div>
            </form>
            <div id="error-message" class="mt-3 text-danger"></div>
          </div>
        </div>
      </div>
    </div>
    <footer>
      <div class="svu_container login svu_footer">Contributors:
        <ul class="author">
          <li>Mouhammad Alnajjar (mouhammad_232243) | C4 </li>
          <li>Talal Awad (Talal_196890) | C2</li>
        </ul>
      </div>
    </footer>
    <div id="svu_alert"></div>
    <script src="assets/js/functions.js"></script>
    <script>
      $(document).ready(function() {
        // Handle URL error parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("error")) {
          document.getElementById("error-message").textContent = "Invalid login credentials. Please try again.";
        }

        // Validate email on input
        $("#register_email").keyup(function() {
          var email = $(this).val();
          validate_email(email, 1);
        });

        // Password match validation
        $("#confirm_password").keyup(function() {
          var confirmPassword = $(this).val();
          var password = $("#register_password").val();
          if (password && confirmPassword) {
            if (password === confirmPassword) {
              $("#password-error").text("Passwords match ✔").css("color", "#00ac33");
            } else {
              $("#password-error").text("Passwords don't match ✖").css("color", "red");
            }
          }
        });

        // Register form submition 
        $("#register-form").on("submit", function(event) {
          event.preventDefault();
          if ($("#email-error").text() !== "Email is valid ✔" || $("#password-error").text() !== "Passwords match ✔") {
            $("#error-message").text("Please fix the errors before submitting.");
          } else {
            var username = $("#username").val();
            var email = $("#register_email").val();
            var password = $("#register_password").val();
            register(username, email, password);
          }
        });
      });
    </script>
  </body>

  </html>

<?php
} else {
  // If database selection fails, expire the logged-in cookie and redirect to the login page
  if (isset($_COOKIE['loggedin_username'])) {
    setcookie("loggedin_username", "", time() - 3600, "/"); // Expire the cookie
  }

  // Redirect to database checker to create if possible 
  header("Location: db/index.php");
}
?>