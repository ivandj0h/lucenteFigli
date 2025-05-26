<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil isi cart
$cart_items = mysqli_query($conn, "
    SELECT c.*, p.name, p.price, p.image
    FROM carts c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = $user_id
");

if (mysqli_num_rows($cart_items) === 0) {
    header('Location: cart.php');
    exit;
}

// Proses checkout saat submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total = 0;
    $items = [];
    while ($item = mysqli_fetch_assoc($cart_items)) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $items[] = $item;
    }

    // Insert ke tabel orders
    mysqli_query($conn, "INSERT INTO orders (user_id, total) VALUES ($user_id, $total)");
    $order_id = mysqli_insert_id($conn);

    // Insert ke tabel order_items
    foreach ($items as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
    }

    // Kosongkan cart user
    mysqli_query($conn, "DELETE FROM carts WHERE user_id = $user_id");

    // Redirect ke halaman sukses
    header("Location: orders.php?success=1");
    exit;
}

include '../includes/header.php';
?>

<main class="container py-5">
    <h2 class="mb-4">Checkout</h2>
    <form method="post">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grandTotal = 0; ?>
                    <?php mysqli_data_seek($cart_items, 0);
                    while ($item = mysqli_fetch_assoc($cart_items)): ?>
                        <?php $subtotal = $item['price'] * $item['quantity'];
                        $grandTotal += $subtotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td><strong>Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success mt-3">Konfirmasi Checkout</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>