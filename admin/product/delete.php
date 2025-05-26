<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // 1. Hapus semua order_items yang pakai produk ini
    mysqli_query($conn, "DELETE FROM order_items WHERE product_id = $id");

    // 2. Hapus semua cart yang pakai produk ini (biar keranjang juga bersih)
    mysqli_query($conn, "DELETE FROM carts WHERE product_id = $id");

    // 3. Hapus produknya sendiri
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");

    header("Location: index.php");
    exit;
}
