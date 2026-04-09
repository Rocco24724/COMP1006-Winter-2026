<?php
// This page lets a logged in user update their username/email/password
// or delete their account entirely.

// Check if the user is logged in if not send them to login page
require 'includes/auth.php';
require 'includes/connect.php';

$errors = [];
$successMsg = '';

// Get the current logged in user's info from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$username = $user['username'];
$email    = $user['email'];

// Handle the update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    // Get and clean submitted values
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    // Checks if username is empty
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    }

    // Checks if email is empty and valid
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Check if username is taken by another user
    if (empty($errors['username'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check->execute([$username, $_SESSION['user_id']]);
        if ($check->fetch()) {
            $errors['username'] = 'This username is already taken.';
        }
    }

    // Check if email is taken by another user
    if (empty($errors['email'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $_SESSION['user_id']]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    // If a new password was entered make sure it matches the confirm field
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
    }

    // If no errors then update the user in the database
    if (empty($errors)) {
        if (!empty($password)) {
            // Update with a new hashed password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
            $stmt->execute([$username, $email, $hashedPassword, $_SESSION['user_id']]);
        } else {
            // Update without changing the password
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
        }

        // Update the username in the session too
        $_SESSION['username'] = $username;
        $successMsg = 'Account updated successfully!';
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {

    // Delete the user from the database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Log them out by destroying the session
    session_destroy();

    // Send them to the login page
    header('Location: login.php');
    exit;
}

require 'includes/header.php';
?>

<h2>My Account</h2>
<p class="text-muted">Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>

<?php if ($successMsg): ?>
    <div class="alert alert-success"><?= $successMsg ?></div>
<?php endif; ?>

<!-- Update account form -->
<div class="card p-4 mb-4" style="max-width: 500px;">
    <h5 class="mb-3">Update Info</h5>
    <form action="account.php" method="POST">

        <!-- Hidden field to tell PHP which form was submitted -->
        <input type="hidden" name="action" value="update">

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">Username *</label>
            <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                id="username" name="username" value="<?= htmlspecialchars($username) ?>">
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= $errors['username'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="password" class="form-label">New Password <span class="text-muted">(leave blank to keep current)</span></label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Confirm New Password -->
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                id="confirm_password" name="confirm_password">
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<!-- Delete account section -->
<div class="card p-4 border-danger" style="max-width: 500px;">
    <h5 class="text-danger mb-3">Delete Account</h5>
    <p>This will permanently delete your account. This cannot be undone.</p>
    <form action="account.php" method="POST">
        <input type="hidden" name="action" value="delete">
        <button type="submit" class="btn btn-danger"
            onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
            Delete My Account
        </button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
