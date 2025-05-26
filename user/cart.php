<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

$user_id = $_SESSION['user']['id'];

// ✅ Handler untuk tambah produk ke keranjang
if (isset($_GET['add'])) {
    $product_id = (int) $_GET['add'];

    $cek = mysqli_query($conn, "SELECT * FROM carts WHERE user_id = $user_id AND product_id = $product_id");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE carts SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        mysqli_query($conn, "INSERT INTO carts (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }

    header("Location: cart.php");
    exit;
}

// ✅ Ambil isi keranjang
$result = mysqli_query($conn, "
    SELECT c.id AS cart_id, p.name, p.price, p.image, c.quantity 
    FROM carts c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = $user_id
");

include '../includes/header.php';
?>

<main class="container py-5">
    <h2 class="mb-4">Keranjang Belanja</h2>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <div class="alert alert-info">Keranjang kamu masih kosong.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php while ($item = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="../admin/product/uploads/<?= htmlspecialchars($item['image']) ?>" alt="" width="60" class="me-2">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td colspan="2" class="fw-bold">Rp <?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="checkout.php" class="btn btn-success mt-3">Checkout</a>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

<?php
// ✅ Handler hapus item
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    mysqli_query($conn, "DELETE FROM carts WHERE id = $id AND user_id = $user_id");
    header("Location: cart.php");
    exit;
}
?>