<?php
require 'includes/connect.php';

// This tells footer.php to load validate.js on this page
$loadValidation = true;

// Basketball positions for the dropdown
$positions = ['Point Guard', 'Shooting Guard', 'Small Forward', 'Power Forward', 'Center'];

$errors = [];

// Get the player ID from the URL (e.g. edit.php?id=3)
$id = $_GET['id'] ?? $_POST['id'] ?? 0;
$id = (int)$id; // Make sure it's an integer for safety

// If no valid ID was provided, go back to the main page
if ($id < 1) {
    header('Location: index.php');
    exit;
}

// Look up the player in the database
$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$stmt->execute([$id]);
$player = $stmt->fetch();

// If no player was found with that ID, go back to main page
if (!$player) {
    header('Location: index.php');
    exit;
}

// Pre-fill the form fields with the player's current data
$first_name = $player['first_name'];
$last_name  = $player['last_name'];
$position   = $player['position'];
$phone      = $player['phone'];
$email      = $player['email'];
$team_name  = $player['team_name'];

// Only run this when the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean the submitted form values
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $position   = trim($_POST['position']);
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email']);
    $team_name  = trim($_POST['team_name']);

    // Checks to see if the different fields match what needs to be inputted

    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $first_name)) {
        $errors['first_name'] = 'First name can only contain letters.';
    }

    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $last_name)) {
        $errors['last_name'] = 'Last name can only contain letters.';
    }

    if (empty($position) || !in_array($position, $positions)) {
        $errors['position'] = 'Please select a valid position.';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^\+?[\d\s\-().]{7,20}$/', $phone)) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        $check = $pdo->prepare("SELECT id FROM players WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already used by another player.';
        }
    }

    if (empty($team_name)) {
        $errors['team_name'] = 'Team name is required.';
    }

    // If no errors, update the player record in the database
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE players SET first_name=?, last_name=?, position=?, phone=?, email=?, team_name=? WHERE id=?"
        );
        $stmt->execute([$first_name, $last_name, $position, $phone, $email, $team_name, $id]);

        // Redirect back to the main page with a success message
        header('Location: index.php?success=updated');
        exit;
    }
}

require 'includes/header.php';
?>

<h2>Edit Player</h2>
<p class="text-muted">Editing: <strong><?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?></strong></p>

<div class="card p-4">
    <form action="edit.php?id=<?= $id ?>" method="POST" id="playerForm" novalidate>

        <!-- Hidden field to pass the player ID along with the form submission -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
            <?php if (isset($errors['first_name'])): ?>
                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
            <?php if (isset($errors['last_name'])): ?>
                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Position Dropdown -->
        <div class="mb-3">
            <label for="position" class="form-label">Position *</label>
            <select class="form-select <?= isset($errors['position']) ? 'is-invalid' : '' ?>"
                id="position" name="position">
                <option value="">-- Select Position --</option>
                <?php foreach ($positions as $pos): ?>
                    <option value="<?= $pos ?>" <?= $position === $pos ? 'selected' : '' ?>><?= $pos ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['position'])): ?>
                <div class="invalid-feedback"><?= $errors['position'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Team Name -->
        <div class="mb-3">
            <label for="team_name" class="form-label">Team Name *</label>
            <input type="text" class="form-control <?= isset($errors['team_name']) ? 'is-invalid' : '' ?>"
                id="team_name" name="team_name" value="<?= htmlspecialchars($team_name) ?>">
            <?php if (isset($errors['team_name'])): ?>
                <div class="invalid-feedback"><?= $errors['team_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number *</label>
            <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
            <?php if (isset($errors['phone'])): ?>
                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>

    </form>
</div>

<?php require 'includes/footer.php'; ?>
