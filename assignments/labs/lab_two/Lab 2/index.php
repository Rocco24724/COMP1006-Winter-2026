<?php
// This line is for the data for the nav.php file
$items = ["Home", "About", "Contact"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My PHP Page</title>
</head>
<body>

<h1>Welcome</h1>

<?php
//Makes the index include both the nav and footer files
include 'nav.php';
include 'footer.php';
?>

</body>
</html>
