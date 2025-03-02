<?php
// Include the configuration file for database credentials
require "../config.php";

// Check if a POST request contains an 'action' parameter
if (isset($_POST['action'])) {
    // Retrieve the current page number from the POST request
    $page = $_POST['page'];

    // Call the function to display students for the requested page
    view_students($page);
}

function view_students($page)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Pagination settings
    $results_per_page = 5;
    $sql = "SELECT COUNT(id) AS total FROM students";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total_items =  $row["total"];
    $total_pages = ceil($total_items / $results_per_page);

?>
    <div id="search-container">
        <div><input class="form-control mb-3" id="search-bar" type="text" placeholder="Search by name, email, or phone"></div>
        <div id="reset-search">x</div>
    </div>
    <div id="search-results"></div>
    <table class="table table-bordered svu_table sortable" id="students_table">
        <thead>
            <tr>
                <!--<th data-column="0">#</th>-->
                <th data-column="0">Name</th>
                <th data-column="1">Email</th>
                <th data-column="2">Phone</th>
                <th data-column="3">Address</th>
                <th data-column="4">Profile Picture</th>
                <th data-column="5" class="desc">Date</th>
                <th data-column="6">Actions</th>
            </tr>
        </thead>
        <tbody id="students-table">
            <!-- Student rows will be populated here -->
            <?php
            $start_from = ($page - 1) * $results_per_page;

            // Fetch students, ordered by id in descending order
            $sql = "SELECT * FROM students ORDER BY id DESC LIMIT $start_from, $results_per_page";
            $result = $conn->query($sql);
            $i = 1;
            $html = '';
            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $avatar_fl = ($row["profile_picture"] !== '') ?  $row["profile_picture"] : '../assets/img/std_no_avt.png';
                    $avatar = '<img src="' . $avatar_fl  . '" alt="' . $row["name"] . '_picture" class="profile_picture">';
                    $html .= '<tr id="' . $row["id"] . '">';
                    $html .= '<td data-label="Name" data-name="' . $row["name"] . '">' . $row["name"] . "</td>";
                    $html .= '<td data-label="Email" data-email="' . $row["email"] . '">' . $row["email"] . "</td>";
                    $html .= '<td data-label="Phone" data-phone="' . $row["phone"] . '">' . $row["phone"] . "</td>";
                    $html .= '<td data-label="Address" data-address="' . $row["address"] . '">' . $row["address"] . "</td>";
                    $html .= '<td data-label="Profile Picture" data-profile-picture="' . $avatar_fl . '">' . $avatar . '</td>';
                    $html .= '<td data-label="Date">' . formatDate($row["created_at"])  . '</td>';
                    //----------------------------------------
                    $id = "'" . $row["id"] . "'";
                    $name = "'" . $row["name"] . "'";
                    $email = "'" . $row["email"] . "'";
                    $phone = "'" . $row["phone"] . "'";
                    $address = "'" . $row["address"] . "'";
                    $profile_picture = "'" . $row["profile_picture"] . "'";
                    //----------------------------------------
                    $html .= '<td data-label="Actions">
                            <a class="btn edit_btn" onclick="update_student_form(' . $id . ',' . $name . ',' . $email . ',' . $phone . ',' . $address . ',' . $profile_picture . ')">Edit</a>
                            <a class="btn del_btn" onclick="delete_student(' . $id . ')">Delete</a>
                          </td>';
                    $html .= "</tr>";
                    $i++;
                }
            } else {
                $html .= "<tr><td colspan='7'>No students found</td></tr>";
            }
            echo $html;
            $conn->close();

            $colmulative_count = ($page == $total_pages) ? $total_items : ($i - 1) * $page;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">page <?php echo $page . '/' . $total_pages . ' - (' . $colmulative_count . '/' . $total_items . ') items'; ?></th>
            </tr>
        </tfoot>
    </table>
    <nav>
        <ul class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item"><a class="page-link" id="page_' . $i . '" onclick="view_students(' . $i . ')">' . $i . '</a></li>';
            }
            ?>
        </ul>
    </nav>
<?php

}


function formatDate($dateString)
{
    // Create a DateTime object from the input string
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);

    // Format the date to the desired output
    return $date->format('Y/m/d \a\t g:i a');
}
