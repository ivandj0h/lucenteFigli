<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

$query = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 ORDER BY created_at DESC");
$produk = [];
while ($row = mysqli_fetch_assoc($query)) {
    $produk[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Beranda Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Lucente Figli</a>
            <div class="ms-auto">
                <span class="me-3">Halo, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a href="cart.php" class="btn btn-outline-primary btn-sm">Keranjang</a>
                <a href="orders.php" class="btn btn-outline-secondary btn-sm">Pesanan</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <h2 class="mb-4">Produk Tersedia</h2>
        <div class="row">
            <?php if (count($produk) === 0): ?>
                <div class="col text-center text-muted">Belum ada produk yang tersedia saat ini.</div>
            <?php endif; ?>

            <?php foreach ($produk as $p): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="../admin/product/uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="card-text fw-bold text-danger">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
                            <a href="cart.php?add=<?= $p['id'] ?>" class="btn btn-outline-primary mt-auto">Tambah ke Keranjang</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-light text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Lucente Figli</p>
        </div>
    </footer>
</body>

</html>