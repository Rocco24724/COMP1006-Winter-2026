<?php
// register.php
// This page lets a new user create an account.
// Passwords are hashed using PHP's password_hash() before being stored in the database.
// This is more secure than storing plain text passwords.

// Start the session so we can log the user in right after registering
session_start();

$recaptchaSiteKey   = '6LdL7K0sAAAAAPwAFwK-u1xcohH6jrwLPJQcp66C';
$recaptchaSecretKey = '6LdL7K0sAAAAAOAEzfvOypF-8nqWeL5gsFMSeM3J';

require 'includes/connect.php';

// If the user is already logged in send them to the main page
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$username = '';
$email = '';

// Only run when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean the form values
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    // Checks if username is empty
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters.';
    }

    // Checks if email is empty and valid
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Checks if password is empty and long enough
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    // Checks if the passwords match
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check if username is already taken
    if (empty($errors['username'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $errors['username'] = 'This username is already taken.';
        }
    }

    // Check if email is already registered
    if (empty($errors['email'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    if (empty($recaptchaToken)) {
        $errors['recaptcha'] = 'Please complete the reCAPTCHA.';
    } else {
        $verifyResponse = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecretKey . '&response=' . $recaptchaToken
        );
        $verifyData = json_decode($verifyResponse, true);
        if (!$verifyData['success']) {
            $errors['recaptcha'] = 'reCAPTCHA failed. Please try again.';
        }
    }

    // If no errors create the account
    if (empty($errors)) {

        // Hash the password before storing it - never store plain text passwords
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);

        // Log the user in by storing their ID and username in the session
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;

        // Send them to the main page
        header('Location: index.php?success=registered');
        exit;
    }
}

require 'includes/header.php';
?>

<h2>Register</h2>

<!-- Registration form -->
<div class="card p-4" style="max-width: 500px;">
    <form action="register.php" method="POST" id="registerForm" novalidate>

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

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password *</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password *</label>
            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                id="confirm_password" name="confirm_password">
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Google reCAPTCHA -->
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="<?= $recaptchaSiteKey ?>"></div>
            <?php if (isset($errors['recaptcha'])): ?>
                <div class="text-danger small mt-1"><?= $errors['recaptcha'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-secondary">Already have an account?</a>

    </form>
</div>

<!-- Loads the Google reCAPTCHA widget on the page -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php require 'includes/footer.php'; ?>
