<?php
// logout.php
// This page logs the user out by destroying their session.

// Start the session so we can destroy it
session_start();

// This logs the user out
session_destroy();

// Send the user to the login page
header('Location: login.php');
exit;
