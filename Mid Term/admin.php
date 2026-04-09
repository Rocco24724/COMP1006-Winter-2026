<?php
require 'connect.php';

// Get all the reviews from the database
$stmt = $pdo->query("SELECT * FROM reviews");

// Store all the results
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Book Reviews</title>
</head>
<body>
    <h1>All Book Reviews</h1>
    <a href="index.php">Submit a New Review</a>
    <br><br>

    <!-- Display all reviews in a table -->
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Submitted At</th>
            <th>Actions</th>
        </tr>

        <!-- Loop through each review and display it as a row -->
        <?php foreach ($reviews as $review): ?>
        <tr>
            <td><?php echo $review['id']; ?></td>
            <td><?php echo $review['title']; ?></td>
            <td><?php echo $review['author']; ?></td>
            <td><?php echo $review['rating']; ?></td>
            <td><?php echo $review['review_text']; ?></td>
            <td><?php echo $review['created_at']; ?></td>
            <td>
                <!-- Pass the review ID to update and delete pages -->
                <a href="update.php?id=<?php echo $review['id']; ?>">Update</a> |
                <a href="delete.php?id=<?php echo $review['id']; ?>" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>