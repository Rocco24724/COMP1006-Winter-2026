<?php
require 'connect.php';

// Get the review id
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Make sure it gets a id 
if (!$id) {
    die("No review ID provided.");
}

// Basic statement that uses prepare to make a placeholder first and then excutes it so the user can't SQL inject
$sql = "DELETE FROM reviews WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Redirect back to the admin page after deleting
header('Location: admin.php');
exit;
?>