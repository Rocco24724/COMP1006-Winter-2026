<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basketball Team Tracker</title>

    <!-- Bootstrap 5 CSS - gives us layout, buttons, forms etc. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons - gives us small icons like trash, pencil etc. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Our custom CSS file -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- App name/logo on the left -->
        <a class="navbar-brand" href="index.php">
            🏀 Basketball Team Tracker
        </a>

        <!-- Hamburger button for mobile screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav links on the right -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">All Players</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add.php">Add Player</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<!-- Main content area - each page fills this in -->
<div class="container my-4">
