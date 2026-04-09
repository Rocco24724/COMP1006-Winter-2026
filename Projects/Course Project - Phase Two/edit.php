<?php
// edit.php
// This page lets a logged in user update an existing player's info and photo.

// Check if the user is logged in if not sends them to login page
require 'includes/auth.php';
require 'includes/connect.php';

$loadValidation = true;

// List of the 5 positions in basketball for the dropdown
$positions = ['Point Guard', 'Shooting Guard', 'Small Forward', 'Power Forward', 'Center'];

$errors = [];

// Get the player ID from the URL
$id = $_GET['id'] ?? $_POST['id'] ?? 0;
$id = (int)$id;

// If no valid ID was provided go back to the main page
if ($id < 1) {
    header('Location: index.php');
    exit;
}

// Look up the player in the database using their ID
$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$stmt->execute([$id]);
$player = $stmt->fetch();

// If no player was found go back to the main page
if (!$player) {
    header('Location: index.php');
    exit;
}

// Fill the form fields with the player's current data
$first_name = $player['first_name'];
$last_name  = $player['last_name'];
$position   = $player['position'];
$phone      = $player['phone'];
$email      = $player['email'];
$team_name  = $player['team_name'];

// Only run when the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean submitted values
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $position   = trim($_POST['position']);
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email']);
    $team_name  = trim($_POST['team_name']);

    // Checks if first name is empty and if it is a valid name using regex
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $first_name)) {
        $errors['first_name'] = 'First name can only contain letters.';
    }

    // Checks if last name is empty and if it is a valid name using regex
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $last_name)) {
        $errors['last_name'] = 'Last name can only contain letters.';
    }

    // Checks to make sure the user has picked one of the positions from the list
    if (empty($position) || !in_array($position, $positions)) {
        $errors['position'] = 'Please select a valid position.';
    }

    // Checks if phone number is empty and if it is a valid phone number using regex
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^\+?[\d\s\-().]{7,20}$/', $phone)) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    // Checks if email is empty, valid, and not used by another player
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        // AND id != ? makes sure we don't flag the current player's own email
        $check = $pdo->prepare("SELECT id FROM players WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already used by another player.';
        }
    }

    if (empty($team_name)) {
        $errors['team_name'] = 'Team name is required.';
    }

    // Handle photo upload if a new file was submitted
    $photoFilename = $player['photo']; // Keep the existing photo by default
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['photo']['type'];
        $fileSize = $_FILES['photo']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors['photo'] = 'Only JPG, PNG, GIF, or WEBP images are allowed.';
        // Check the file is not too large (2MB max)
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $errors['photo'] = 'Photo must be smaller than 2MB.';
        } else {
            // Delete the old photo file if there was one
            if ($player['photo'] && file_exists('uploads/' . $player['photo'])) {
                unlink('uploads/' . $player['photo']);
            }

            // Create a unique filename and move the new file to uploads
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoFilename = uniqid('player_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photoFilename);
        }
    }

    // If no errors, update the player in the database
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE players SET first_name=?, last_name=?, position=?, phone=?, email=?, team_name=?, photo=? WHERE id=?"
        );
        $stmt->execute([$first_name, $last_name, $position, $phone, $email, $team_name, $photoFilename, $id]);

        header('Location: index.php?success=updated');
        exit;
    }
}

require 'includes/header.php';
?>

<h2>Edit Player</h2>
<p class="text-muted">Editing: <strong><?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?></strong></p>

<!-- Form for editing player  - enctype is needed for file uploads to work -->
<div class="card p-4">
    <form action="edit.php?id=<?= $id ?>" method="POST" id="playerForm" enctype="multipart/form-data" novalidate>

        <!-- Hidden field to keep track of which player we are editing -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- First name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
            <?php if (isset($errors['first_name'])): ?>
                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Last name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
            <?php if (isset($errors['last_name'])): ?>
                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Position dropdown -->
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

        <!-- Team name -->
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

        <!-- Photo -->
        <div class="mb-3">
            <label for="photo" class="form-label">Player Photo <span class="text-muted">(optional, JPG/PNG, max 2MB)</span></label>
            <?php if ($player['photo']): ?>
                <div class="mb-2">
                    <img src="uploads/<?= htmlspecialchars($player['photo']) ?>" alt="Current Photo" class="player-photo">
                    <small class="text-muted d-block">Current photo — upload a new one to replace it</small>
                </div>
            <?php endif; ?>
            <input type="file" class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>"
                id="photo" name="photo" accept="image/*">
            <?php if (isset($errors['photo'])): ?>
                <div class="invalid-feedback"><?= $errors['photo'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>

    </form>
</div>

<?php require 'includes/footer.php'; ?>
