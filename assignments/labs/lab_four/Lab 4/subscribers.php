<?php
require "includes/connect.php";
require "includes/header.php";

//A select query to get the subscribers and orders them by the newest subscriber
$sql = "SELECT id, first_name, last_name, email, subscribed_at FROM subscribers ORDER BY subscribed_at DESC";

//Prepares the sql statement
$statement = $pdo->prepare($sql);

//Execute
$statement->execute();

//takes the results and puts them into $subscribers and using fetchAll() puts them into a array
$subscribers = $statement->fetchAll();

?>

<main class="container mt-4">
  <h1>Subscribers</h1>

  <?php if (count($subscribers) === 0): ?>
    <p>No subscribers yet.</p>
  <?php else: ?>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Subscribed</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loops through $subscribers and outputs them on each row -->
        <?php foreach ($subscribers as $subscriber): ?>
          <tr>
            <td><?php echo htmlspecialchars($subscriber['id']); ?></td>
            <td><?php echo htmlspecialchars($subscriber['first_name']); ?></td>
            <td><?php echo htmlspecialchars($subscriber['last_name']); ?></td>
            <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
            <td><?php echo htmlspecialchars($subscriber['subscribed_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p class="mt-3">
    <a href="index.php">Back to Subscribe Form</a>
  </p>
</main>

<?php require "includes/footer.php"; ?>