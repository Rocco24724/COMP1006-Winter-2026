<?php
// Load the database connection
require 'connect.php';

$error = "";
$success = "";

// Get the review id
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Get the existing reviews using the id
$sql = "SELECT * FROM reviews WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$review = $stmt->fetch(PDO::FETCH_ASSOC);

// If no review was found stop the program
if (!$review) {
    die("Review not found.");
}

// Check if the update form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Grab and sanitize the updated values
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
    $author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS));
    $rating = trim(filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT));
    $review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_SPECIAL_CHARS));

    // Make sure no fields are empty
    if (empty($title) || empty($author) || empty($rating) || empty($review_text)) {
        $error = "All fields are required.";
    // Make sure the rating is a number between 1 and 5
    } elseif (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $error = "Rating must be a number between 1 and 5.";
    } else {
        // Basic statement that uses prepare to make a placeholder first and then executes it so the user can't SQL inject
        $sql = "UPDATE reviews SET title = :title, author = :author, rating = :rating, review_text = :review_text WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review_text', $review_text);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $success = "Review updated successfully!";

        // Update the reviews so the form shows the new values
        $review['title'] = $title;
        $review['author'] = $author;
        $review['rating'] = $rating;
        $review['review_text'] = $review_text;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Review</title>
</head>
<body>
    <h1>Update Review</h1>

    <!-- Show error or success messages -->
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Pre-fill the form with the existing review data -->
    <form method="POST" action="update.php?id=<?php echo $id; ?>">
        <label>Book Title:</label><br>
        <input type="text" name="title" value="<?php echo $review['title']; ?>"><br><br>

        <label>Author:</label><br>
        <input type="text" name="author" value="<?php echo $review['author']; ?>"><br><br>

        <label>Rating (1-5):</label><br>
        <input type="number" name="rating" min="1" max="5" value="<?php echo $review['rating']; ?>"><br><br>

        <label>Review:</label><br>
        <textarea name="review_text" rows="5" cols="40"><?php echo $review['review_text']; ?></textarea><br><br>

        <button type="submit">Save Changes</button>
    </form>

    <br>
    <a href="admin.php">Back to Admin</a>
</body>
</html>