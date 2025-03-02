<?php
// Include the database configuration file
require "../config.php";
global $servername, $username, $password, $dbname;

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST and if the 'search_query' parameter is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
    // Sanitize the input search query to prevent SQL injection
    $search_query = $conn->real_escape_string($_POST['search_query']);

    // Prepare the SQL query to search for the query in the database
    $sql = "SELECT id, name, email, phone, address, profile_picture FROM students 
               WHERE id LIKE '%$search_query%' 
               OR name LIKE '%$search_query%' 
               OR email LIKE '%$search_query%' 
               OR phone LIKE '%$search_query%'
            LIMIT 10";

    // Execute the query and store the result
    $result = $conn->query($sql);

    $html = '';  // Variable to store the HTML output
    if ($result->num_rows > 0) {

        // Start building the list HTML
        $html .= "<ul class='search-list'>";

        $i = 1;  // Counter for the list items
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];

            // Set the profile picture or use a default image if none is provided
            $avatar_fl = ($row["profile_picture"] !== '') ?  $row["profile_picture"] : '../assets/img/std_no_avt.png';
            $avatar = '<img src="' .  $avatar_fl . '" alt="profile_picture" class="item_avatar">';

            // Build the list item HTML
            $html .= '<div class="search-list-item"><div class="item_i">#' . $i . '</div>';
            $html .= '<div>' . $avatar . '</div>';
            $html .= '<div><li id="' . $id . '" data-item-name="' . $row['name'] . '" data-item-email="' . $row['email'] . '" data-item-phone="' . $row['phone'] . '" data-item-address="' . $row['address'] . '" data-item-avatar="' . $avatar_fl . '">';
            $html .= "<strong>Name:</strong> " . $row['name'] . "<br>";
            $html .= "<strong>Email:</strong> " . $row['email'] . "<br>";
            $html .= "<strong>Phone:</strong> " . $row['phone'] . "<br>";
            $html .= "<strong>Address:</strong> " . $row['address'];
            $html .= "</li>";
            $html .= "</div>";
            $html .= "</div>";
            $i++;  // Increment the counter for the next list item
        }
        $html .= "</ul>";
        // Add the result count to the HTML output
        $html .= '<div id="search_result_count">Results No. (' . $result->num_rows . ')</div>';
    } else {
        // If no results are found, display this message
        $html .= '<div class="no_results">No results found</div>';
    }

    // Output the HTML
    echo $html;
}
