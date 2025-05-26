<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';
$user_id = $_SESSION['user']['id'];

// Ambil semua pesanan user
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

function getItems($order_id, $conn)
{
    $query = mysqli_query($conn, "
        SELECT oi.quantity, oi.price, p.name, p.image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = $order_id
    ");
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img {
            max-width: 100px;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Lucente Figli</a>
            <div class="ms-auto">
                <span class="me-3">Halo, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a href="cart.php" class="btn btn-outline-primary btn-sm">Keranjang</a>
                <a href="orders.php" class="btn btn-outline-secondary btn-sm">Pesanan</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <h2 class="mb-4">Riwayat Pesanan Kamu</h2>

        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <?php $items = getItems($order['id'], $conn); ?>
            <div class="border rounded p-3 mb-4">
                <div>
                    <strong>Order ID:</strong> <?= $order['id'] ?> |
                    <strong>Total:</strong> Rp <?= number_format($order['total'], 0, ',', '.') ?> |
                    <strong>Status:</strong>
                    <span class="badge bg-warning text-dark status-badge"><?= ucfirst($order['status']) ?></span> |
                    <strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                </div>
                <hr>
                <strong>Item:</strong>
                <ul class="mt-2">
                    <?php foreach ($items as $item): ?>
                        <li class="d-flex align-items-center mb-2">
                            <img src="../admin/product/uploads/<?= htmlspecialchars($item['image']) ?>" class="card-img" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div>
                                <?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?> – Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endwhile; ?>
    </main>

    <script>
        const badges = document.querySelectorAll('.status-badge');

        badges.forEach((badge, i) => {
            setTimeout(() => {
                badge.classList.replace('bg-warning', 'bg-info');
                badge.textContent = 'Dikirim';
            }, 2000 + (i * 200));

            setTimeout(() => {
                badge.classList.replace('bg-info', 'bg-success');

                // Ganti text jadi putih supaya kontras sama hijau
                badge.classList.remove('text-dark');
                badge.classList.add('text-white');

                badge.textContent = 'Selesai';
            }, 4000 + (i * 200));
        });
    </script>
</body>

</html>