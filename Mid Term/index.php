<?php
// Load the database connection
require 'connect.php';

$error = "";
$success = "";

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Grab and sanitize each field from the form
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
    $author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS));
    $rating = trim(filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT));
    $review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_SPECIAL_CHARS));

    // makes sure that none of the fields are blank
    if (empty($title) || empty($author) || empty($rating) || empty($review_text)) {
        $error = "All fields are required.";
    // Make sure the rating is a number between 1 and 5
    } elseif (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $error = "Rating must be a number between 1 and 5.";
    } else {
        // Basic statement that uses prepare to make a placeholder first and then executes it so the user can't SQL inject
        $sql = "INSERT INTO reviews (title, author, rating, review_text) VALUES (:title, :author, :rating, :review_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review_text', $review_text);
        $stmt->execute();

        $success = "Review submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit a Book Review</title>
</head>
<body>
    <h1>Submit a Book Review</h1>

    <!-- Show error or success messages -->
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- form for user to enter in info about their book -->
    <form method="POST" action="index.php">
        <label>Book Title:</label><br>
        <input type="text" name="title"><br><br>

        <label>Author:</label><br>
        <input type="text" name="author"><br><br>

        <label>Rating (1-5):</label><br>
        <input type="number" name="rating" min="1" max="5"><br><br>

        <label>Review:</label><br>
        <textarea name="review_text" rows="5" cols="40"></textarea><br><br>

        <button type="submit">Submit Review</button>
    </form>

    <br>
    <a href="admin.php">Go to Admin Page</a>
</body>
</html>