<?php
// auth.php
// This file is included at the top of any page that requires the user to be logged in.
// If the user is not logged in they get sent to the login page.

// Start the session so we can check if the user is logged in
session_start();

// Check if the user_id is stored in the session
// If it's not there the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
