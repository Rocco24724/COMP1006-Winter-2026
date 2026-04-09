<?php
// login.php
// This page lets existing users log in to the application.

// Start the session so we can store the logged in user's info
session_start();

require 'includes/connect.php';

// If the user is already logged in send them to the main page
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {

        // Look up the user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            // Password is correct - store user info in the session to keep them logged in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;

        } else {
            // Username or password was wrong
            $errors['login'] = 'Incorrect username or password.';
        }
    }
}

require 'includes/header.php';
?>

<h2>Login</h2>

<?php if (isset($errors['login'])): ?>
    <div class="alert alert-danger"><?= $errors['login'] ?></div>
<?php endif; ?>

<!-- Login form -->
<div class="card p-4" style="max-width: 500px;">
    <form action="login.php" method="POST">

        <div class="mb-3">
            <label for="username" class="form-label">Username *</label>
            <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                id="username" name="username" value="<?= htmlspecialchars($username) ?>">
            <?php if (isset($errors['username'])): ?>
                <div class="invalid-feedback"><?= $errors['username'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password *</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-secondary">Create an account</a>

    </form>
</div>

<?php require 'includes/footer.php'; ?>
