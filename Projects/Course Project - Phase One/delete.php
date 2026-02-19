<?php
// This page is used to remove a player from the database.

require 'includes/connect.php';

// Get the player ID from the form and make sure it's a valid integer
$id = (int)$_POST['id'];

if ($id < 1) {
    header('Location: index.php?error=delete_failed');
    exit;
}

// Delete the player with that ID
$stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
$stmt->execute([$id]);

// Redirect back to the main page with a success message
header('Location: index.php?success=deleted');
exit;
