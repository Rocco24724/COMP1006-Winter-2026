<?php
require 'connect.php';
// This checks if the form from index.php has been submitted to make it not run when the form has not been filled
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //Takes values from form and turns them into variables
    $first = $_POST["firstName"];
    $last = $_POST["lastName"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // checks if it is a valid email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Email details
    $to = "support@bakeittillyoumakeit.com";
    $subject = "New Contact Form Submission";
    //Using double slash to make the line go down 1 to make it look better in email
    $body = "From: $first $last\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: $email";

    // sending email
    if (mail($to, $subject, $body, $headers)) {
        echo "Thank you for contacting us";
        exit;
    } else {
        echo "There was an issue sending your message.";
    }
}
?>