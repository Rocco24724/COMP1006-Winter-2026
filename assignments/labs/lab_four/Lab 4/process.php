<?php
require "includes/connect.php";
require "includes/header.php";

//Variable names match the names from the form in index.php
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];

//Insert statement to put placeholders in
$sql = "INSERT INTO subscribers (first_name, last_name, email)
        VALUES (:first_name, :last_name, :email)";

//Prepares SQL to be able to execute
$statement = $pdo->prepare($sql);

//Executes the statement with real values replacing the placeholders
$statement->execute([
    'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email
]);

?>
<main class="container mt-4">
    <h2>Thank You for Subscribing</h2>

    <!-- confirmation message -->
    <p>Thanks,
        <?php echo htmlspecialchars($first_name); ?>
        You have been added to our mailing list.</p>

    <p class="mt-3">
        <a href="subscribers.php">View Subscribers</a>
    </p>
</main>

<?php require "includes/footer.php"; ?>