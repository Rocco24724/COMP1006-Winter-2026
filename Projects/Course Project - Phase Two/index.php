<?php
// index.php
// It shows all players in the database
// Only logged in users can see this page

// Check if the user is logged in if not sends them to login page
require 'includes/auth.php';
require 'includes/connect.php';
require 'includes/header.php';
?>

<h2>Team Roster</h2>

<?php
// Shows a success message based on what the user just did
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        echo '<div class="alert alert-success">Player successfully added!</div>';
    } else if ($_GET['success'] === 'updated') {
        echo '<div class="alert alert-success">Player successfully updated!</div>';
    } else if ($_GET['success'] === 'deleted') {
        echo '<div class="alert alert-success">Player successfully deleted!</div>';
    } else if ($_GET['success'] === 'registered') {
        echo '<div class="alert alert-success">Account created! Welcome, ' . htmlspecialchars($_SESSION['username']) . '!</div>';
    }
}

// Shows an error message if the delete failed
if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') {
    echo '<div class="alert alert-danger">Could not delete the player. Please try again.</div>';
}
?>

<!-- Button to go to the add player page -->
<div class="mb-3">
    <a href="add.php" class="btn btn-primary">+ Add Player</a>
</div>

<?php
// Fetches all players from the database sorted by team name then last name
$stmt = $pdo->query("SELECT * FROM players ORDER BY team_name ASC, last_name ASC");
$players = $stmt->fetchAll();

// If no players found show a message if not show the table
if (count($players) === 0) {
    echo '<p>No players found. Add one above!</p>';
} else {
?>
    <!-- Table that displays all players -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Photo</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Position</th>
                <th>Team</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $i => $player): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <?php if ($player['photo']): ?>
                        <!-- Show the player's photo if they have one -->
                        <img src="uploads/<?= htmlspecialchars($player['photo']) ?>" alt="Player Photo" class="player-photo">
                    <?php else: ?>
                        <!-- Show a placeholder if no photo uploaded -->
                        <span class="text-muted small">No photo</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($player['first_name']) ?></td>
                <td><?= htmlspecialchars($player['last_name']) ?></td>
                <td><?= htmlspecialchars($player['position']) ?></td>
                <td><?= htmlspecialchars($player['team_name']) ?></td>
                <td><?= htmlspecialchars($player['phone']) ?></td>
                <td><?= htmlspecialchars($player['email']) ?></td>
                <td>
                    <!-- Edit button -->
                    <a href="edit.php?id=<?= $player['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>

                    <!-- Delete button: submits directly to delete.php -->
                    <form action="delete.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $player['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this player?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php } ?>

<?php require 'includes/footer.php'; ?>
