<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: flex;
    }

    .sidebar {
      width: 250px;
      background-color: #f8f9fa;
      padding: 20px;
      height: 100vh;
      position: fixed;
    }

    .main-content {
      margin-left: 250px;
      padding: 20px;
      flex-grow: 1;
    }

    .sidebar .nav-link.active {
      font-weight: bold;
      color: #0d6efd;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h4>Admin Panel</h4>
    <hr>
    <div class="mb-3">Halo, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="product/index.php">Produk</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="category/index.php">Kategori</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="users/index.php">User</a>
      </li>
      <li class="nav-item mt-3">
        <a class="btn btn-danger btn-sm w-100" href="../logout.php">Logout</a>
      </li>
    </ul>
  </div>

  <div class="main-content">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</p>
    <p>Silakan pilih menu di sidebar untuk mengelola konten.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>