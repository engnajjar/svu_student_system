<?php
require "../config.php";
global $servername, $username, $password, $dbname;

// Establish a connection to the MySQL server
$conn = new mysqli($servername, $username, $password);

// Check for connection errors
if ($conn->connect_error) {
  // If connection fails, send a JSON response and terminate script
  response_json(['success' => 0, 'error' => "Connection failed: " . $conn->connect_error]);
  die("Connection failed: " . $conn->connect_error);
}

// Check if the database exists
$dbSelected = $conn->select_db($dbname);

// If the database is selected
if ($dbSelected) {
  // Initialize logged-in user variable
  $loggedin_user = '';

  // Retrieve the logged-in user from the query string (if set)
  if (isset($_GET['loggedin_user'])) {
    $loggedin_user = $_GET['loggedin_user'];
  } else {
    // If no user is logged in, redirect to the homepage
    header("Location: ../index.php");
  }

?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />

    <!-- Ensures responsive design on all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SVU Student System</title>

    <!-- Includeing Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Encode+Sans+Semi+Condensed:wght@100;200;300;400;500;600;700;800;900&family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet" />

    <!-- Include Styles -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/styles.css" />

    <!-- Include jQuery library -->
    <script src="../assets/js/jquery-3.6.0.min.js"></script>

    <!-- Include bootstrap library -->
    <script src="../assets/js/bootstrap.min.js"></script>
  </head>

  <body>
    <!-- Logo and Navigation -->
    <a href="../index.php" class="logo-link">
      <img src="../assets/img/svu_logo.png" alt="svu_logo" class="svu_logo">
      <h1 class="logo">
        <span class="logo_1">Student</span><span class="logo_2"> ★ ★ ★ </span><span class="logo_3">System</span>
      </h1>
    </a>

    <!-- Tabs for navigation between different sections -->
    <div class="svu_container flex">
      <ul class="nav nav-tabs" style="width: 100%">
        <li class="active"><a data-toggle="tab" href="#view_students" id="view_students">View Students</a></li>
        <li class="disabled-tab"><a data-toggle="tab" href="#view_student" id="view_student">View Student</a></li>
        <li><a data-toggle="tab" href="#add_student" id="add_student">Add Student</a></li>
        <li class="disabled-tab"><a data-toggle="tab" href="update_student" id="update_student" aria-expanded="false">Update Student</a></li>
      </ul>

      <!-- User Avatar and Dropdown for settings/logging out -->
      <div class="dropdown-wrapper">
        <div class="dropdown">
          <div class="user_icon_settings" onclick="showDropdown(this)">
            <div class="profile-svu_container settings" id="avatar">
              <div class="user-avatar" id="current_user_pic">
                <div class="avatar"></div>
              </div>
              <div id="current_user_status" class="status-indicator online"></div>
            </div>
            <div class="user_icon_settings username"><?php echo $loggedin_user; ?></div>
          </div>
          <?php if ($loggedin_user !== '') { ?>
            <div class="dropdown-content">
              <a class="dropdown-item" id="logout" href="../logout.php">
                <span>Log out</span>
              </a>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="svu_container">

      <!-- Loading icon -->
      <div class="svu_loading"></div>

      <!-- The container of all tabs contents -->
      <div id="tab_container"></div>
    </div>

    <!-- Alert and Footer -->
    <div id="svu_alert"></div>
    <footer>
      <div class="svu_container svu_footer">Contributors: Mouhammad Alnajjar (mouhammad_232243) | C4 and Talal Awad (Talal_196890) | C2</div>
    </footer>

    <script src="../assets/js/functions.js"></script>
    <script>
      // jQuery ready function
      $(document).ready(function() {
        // Initialize the page with 'View Students' section
        view_students(1);

        // Define a mapping for each tab's function
        var tabMap = {
          view_students: "View Students",
          add_student: "Add Student",
          update_student: "Update Student"
        };

        // Event listener for clicking tabs
        $(".nav-tabs a").click(function() {
          var tabId = $(this).attr("href").replace("#", ""); // Extract the tab ID
          var param = tabMap[tabId]; // Get the corresponding function

          // Disable the "View" and "Update" student tabs
          if (tabId === "view_students") {
            disable_view_update_content_tab();
            view_students(1);
          } else if (tabId === "add_student") {
            disable_view_update_content_tab();
            add_student_form();
          } else {
            console.error("No mapping found for tab id: " + tabId);
          }
        });

        // Function to disable "View" and "Update" tabs
        function disable_view_update_content_tab() {
          $("#update_student, #view_student").parent().addClass("disabled-tab");
          $("#update_student, #view_student").css({
            "pointer-events": "none",
            cursor: "not-allowed",
            color: "gray",
            opacity: "0.6",
          });
        }

        // Search functionality for the search bar
        $(document).on('input', '#search-bar', function() {
          let query = $(this).val().trim(); // Get the search query

          if (query) {
            $.ajax({
              url: "search.php",
              type: "POST",
              data: {
                search_query: query
              },
              success: function(response) {
                $('#reset-search').show();
                $('#search-results').show().html(response);
              },
              error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
              }
            });
          } else {
            $('#search-results').empty().hide();
            $('#reset-search').hide();
          }
        });

        // Reset search results when clicking the reset button
        $(document).on('click', '#reset-search', function() {
          $('#search-bar').val('');
          $('#search-results').hide();
          $('#reset-search').hide();
        });

        // Event listener for selecting a student from the list
        $(document).on("click", ".search-list li", function() {
          var itemId = $(this).attr("id");
          var item_name = $(this).data("item-name");
          var item_email = $(this).data("item-email");
          var item_phone = $(this).data("item-phone");
          var item_address = $(this).data("item-address");
          var item_avatar = $(this).data("item-avatar");

          if (itemId) {
            view_student_form(itemId, item_name, item_email, item_phone, item_address, item_avatar);
          }
        });

        // Event listener for clicking student rows to view details
        $(document).on("click", "#students-table tr td", function() {
          const col_label = $(this).data('label');
          if (col_label !== 'Actions') {
            const row = $(this).closest('tr');
            const id = row.attr("id");
            const name = row.find('td[data-name]').data("name");
            const email = row.find('td[data-email]').data("email");
            const phone = row.find('td[data-phone]').data("phone");
            const address = row.find('td[data-address]').data("address");
            const profile_picture = row.find('td[data-profile-picture]').data("profile-picture");

            if (id) {
              view_student_form(id, name, email, phone, address, profile_picture);
            }
          }
        });

        // Function to handle table sorting
        $(document).on("click", ".sortable th", function() {
          const th = $(this);
          const table = th.closest("table");
          const tbody = table.find("tbody");
          const rows = tbody.find("tr").toArray();
          const columnIndex = th.data("column");
          const isNumeric = !isNaN($(rows[0]).find("td").eq(columnIndex).text().trim());

          // Clear previous sort indicators
          $(".sortable th").removeClass("asc desc");

          // Toggle sort direction
          const direction = $('#table_sort_attr').data('table-sort') === "asc" ? "desc" : "asc";
          th.data("sort", direction);
          $('#table_sort_attr').data('table-sort', direction);
          $('#table_sort_attr').data('table-col-sort', columnIndex);

          // Set the sorting indicator
          th.addClass(direction);

          // Sort rows based on column data type (numeric or text)
          rows.sort((rowA, rowB) => {
            const cellA = $(rowA).find("td").eq(columnIndex).text().trim();
            const cellB = $(rowB).find("td").eq(columnIndex).text().trim();
            if (isNumeric) {
              return direction === "asc" ? cellA - cellB : cellB - cellA;
            } else {
              return direction === "asc" ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            }
          });

          // Append sorted rows back to tbody
          tbody.append(rows);
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
  header("Location: ../db/index.php");
}
?>