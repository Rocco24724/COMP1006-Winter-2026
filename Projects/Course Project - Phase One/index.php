<?php
require 'includes/connect.php';
require 'includes/header.php';
?>

<h2>Team Roster</h2>

<?php
// Gives different messages based on what the user did
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        echo '<div class="alert alert-success">Player successfully added!</div>';
    } else if ($_GET['success'] === 'updated') {
        echo '<div class="alert alert-success">Player successfully updated!</div>';
    } else if ($_GET['success'] === 'deleted') {
        echo '<div class="alert alert-success">Player successfully deleted!</div>';
    }
}

// Shows a error message if there was a error deleting a player
if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') {
    echo '<div class="alert alert-danger">Could not delete the player. Please try again.</div>';
}
?>

<!-- Button to go to the add player page -->
<div class="mb-3">
    <a href="add.php" class="btn btn-primary">+ Add Player</a>
</div>

<?php
// Displays all players in the database using a SQL query
$stmt = $pdo->query("SELECT * FROM players ORDER BY team_name ASC, last_name ASC");
$players = $stmt->fetchAll();

// Check if there are any players to display
if (count($players) === 0) {
    echo '<p>No players found. Add one above!</p>';
} else {
?>
    <!-- Players Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
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
                <td><?= htmlspecialchars($player['first_name']) ?></td>
                <td><?= htmlspecialchars($player['last_name']) ?></td>
                <td><?= htmlspecialchars($player['position']) ?></td>
                <td><?= htmlspecialchars($player['team_name']) ?></td>
                <td><?= htmlspecialchars($player['phone']) ?></td>
                <td><?= htmlspecialchars($player['email']) ?></td>
                <td>
                    <!-- Edit button -->
                    <a href="edit.php?id=<?= $player['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>

                    <button
                        type="button"
                        class="btn btn-sm btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-player-id="<?= $player['id'] ?>"
                        data-player-name="<?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?>"
                    >
                    <!-- Adds delete button text -->
                    Delete
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php } ?>

<!-- Delete button -->
<!-- This pops up when the user clicks a delete button asking them to confirm -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deletePlayerName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <!-- This form submits to delete.php with the player's ID -->
                <form action="delete.php" method="POST">
                    <input type="hidden" name="id" id="deletePlayerId">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script to fill in the player name and ID in the delete -->
<script>
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(e) {
        const button = e.relatedTarget;
        document.getElementById('deletePlayerId').value = button.getAttribute('data-player-id');
        document.getElementById('deletePlayerName').textContent = button.getAttribute('data-player-name');
    });
</script>

<?php require_once 'includes/footer.php'; ?>
