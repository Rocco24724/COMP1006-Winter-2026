<?php
// delete.php
// This page removes a player from the database.

// Check if the user is logged in if not sends them to login page
require 'includes/auth.php';
require 'includes/connect.php';

// Get the player ID from the form and make sure it's a valid integer
$id = (int)$_POST['id'];

// If the ID is not valid go back to the main page with an error
if ($id < 1) {
    header('Location: index.php?error=delete_failed');
    exit;
}

// Get the player's photo filename before deleting so we can remove the file too
$stmt = $pdo->prepare("SELECT photo FROM players WHERE id = ?");
$stmt->execute([$id]);
$player = $stmt->fetch();

// If the player had a photo delete the file from the uploads folder
if ($player && $player['photo'] && file_exists('uploads/' . $player['photo'])) {
    unlink('uploads/' . $player['photo']);
}

// Delete the player from the database
$stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php?success=deleted');
exit;
