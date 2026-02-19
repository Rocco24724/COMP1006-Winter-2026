<?php

// These are my keys for the Google reCAPTCHA to make the user confirm they are not a robot when adding to the form
// I replaced my google catcha keys with 'My Key' because i'm putting it on github there is a secruity risk for me to have them public but I did have them set as mine when testing it
$recaptchaSiteKey   = 'My Key';
$recaptchaSecretKey = 'My Key';

require 'includes/connect.php';

// This tells footer.php to load validate.js on this page
$loadValidation = true;

// List of the 5 positions in basketball so when the user inputs it, it can show it in a list 
$positions = ['Point Guard', 'Shooting Guard', 'Small Forward', 'Power Forward', 'Center'];

// Makes all the values start with nothing to make sure no errors happen 
$errors = [];
$first_name = '';
$last_name = '';
$position = '';
$phone = '';
$email = '';
$team_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and clean the form values
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $position   = trim($_POST['position']);
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email']);
    $team_name  = trim($_POST['team_name']);

    // Checks if first name is empty and if it is a vaild name using regex 
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $first_name)) {
        $errors['first_name'] = 'Enter valid first name.';
    }

    // Checks if last name is empty and if it is a vaild name using regex 
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (!preg_match("/^[A-Za-z\s\-']+$/", $last_name)) {
        $errors['last_name'] = 'Enter valid last name.';
    }

    // Checks to make sure the user has picked one of the positions from the list
    if (empty($position) || !in_array($position, $positions)) {
        $errors['position'] = 'Please pick a position from the drop down menu.';
    }

    // Checks if phone number is empty and if it is a valid phone number using regex
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!preg_match('/^\+?[\d\s\-().]{7,20}$/', $phone)) {
        $errors['phone'] = 'Please enter a real phone number.';
    }

    // Checks to see if email is empty, if it is a valid email, and if that email is already in the database
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a real email address.';
    } else {
        // This checks the database to see if the email is already in the database using a Select SQL statement
        $check = $pdo->prepare("SELECT id FROM players WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already in the database.';
        }
    }

    // Checks if user has filled in team name
    if (empty($team_name)) {
        $errors['team_name'] = 'Team name is required.';
    }

    // reCAPTCHA: verify the token with Google's server
    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    if (empty($recaptchaToken)) {
        $errors['recaptcha'] = 'Please complete the reCAPTCHA.';
    } else {
        // Send the token to Google to confirm it's valid
        $verifyResponse = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecretKey . '&response=' . $recaptchaToken
        );
        $verifyData = json_decode($verifyResponse, true);
        if (!$verifyData['success']) {
            $errors['recaptcha'] = 'reCAPTCHA failed. Please try again.';
        }
    }

    // If everything is how its meant to be add the player into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO players (first_name, last_name, position, phone, email, team_name)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$first_name, $last_name, $position, $phone, $email, $team_name]);

        // Sends user to index.php page once the player is added to the database
        header('Location: index.php?success=added');
        exit;
    }
}

//requires the header of the page
require 'includes/header.php';
?>

<h2>Add Player</h2>

<!-- Form for adding player -->
<div class="card p-4">
    <form action="add.php" method="POST" id="playerForm" novalidate>

        <!-- First name form -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
            <?php if (isset($errors['first_name'])): ?>
                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Last name form -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
            <?php if (isset($errors['last_name'])): ?>
                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Position dropdown form -->
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

        <!-- Team name form -->
        <div class="mb-3">
            <label for="team_name" class="form-label">Team Name *</label>
            <input type="text" class="form-control <?= isset($errors['team_name']) ? 'is-invalid' : '' ?>"
                id="team_name" name="team_name" value="<?= htmlspecialchars($team_name) ?>">
            <?php if (isset($errors['team_name'])): ?>
                <div class="invalid-feedback"><?= $errors['team_name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Phone form -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number *</label>
            <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
            <?php if (isset($errors['phone'])): ?>
                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Email form -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Google captcha -->
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="<?= $recaptchaSiteKey ?>"></div>
            <?php if (isset($errors['recaptcha'])): ?>
                <div class="text-danger small mt-1"><?= $errors['recaptcha'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Adds buttons for adding the player and to cancel -->
        <button type="submit" class="btn btn-primary">Add Player</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>

    </form>
</div>

<!-- This calls on the google captcha -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php require 'includes/footer.php'; ?>
