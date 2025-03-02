<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <!-- Ensures responsive design on all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include jQuery library -->
    <script src="../assets/js/jquery-3.6.0.min.js"></script>

    <!-- Include styles -->
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>

<body>
    <!-- Placeholder for displaying alerts -->
    <div id="svu_alert"></div>

    <!-- Include JavaScript functions -->
    <script src="../assets/js/functions.js"></script>

    <script>
        $(document).ready(function() {

            // Call a function to check the SVU database status on page load
            checking_svu_db();
        });
    </script>
</body>

</html>