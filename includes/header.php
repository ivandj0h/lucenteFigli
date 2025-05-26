<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start(); // 💡 biar aman dari double-start
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lucente Figli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <!-- STICKY TOP NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Lucente Figli</a>

      <div class="ms-auto d-flex align-items-center gap-2">
        <?php if (!isset($_SESSION['user'])): ?>
          <a href="login.php" class="btn btn-outline-dark btn-sm">Login</a>
          <a href="register.php" class="btn btn-dark btn-sm">Register</a>
        <?php else: ?>
          <span class="text-dark me-2">Halo, <?= htmlspecialchars($_SESSION['user']['name']); ?></span>
          <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>