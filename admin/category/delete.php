<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $check = mysqli_query($conn, "SELECT * FROM categories WHERE id = '$id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM categories WHERE id = '$id'");
    }
    header("Location: index.php");
    exit;
}
