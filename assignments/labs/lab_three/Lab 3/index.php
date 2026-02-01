<?php

require "connect.php";

?>
<!-- This is some HTML code that asks the user for their info so it can be used to create a contact for that person -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us</title>
</head>
<body>

  <h2>Contact Our Bakery</h2>

  <form action="contact.php" method="POST">
    
    <label>First Name:</label>
    <input type="text" name="firstName" required>

    <label>Last Name:</label>
    <input type="text" name="lastName" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Message:</label>
    <textarea name="message" required></textarea>

    <button type="submit">Send Message</button>
  </form>

</body>
</html>
