<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];

    $check = mysqli_query($conn, "SELECT id FROM categories WHERE name = '$name'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Nama kategori sudah dipakai.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO categories (name, gender) VALUES ('$name', '$gender')");
        if ($insert) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambahkan kategori.";
        }
    }
}
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Tambah Kategori</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-select" required>
            <option value="pria">Pria</option>
            <option value="wanita">Wanita</option>
            <option value="unisex">Unisex</option>
        </select>
    </div>
    <button class="btn btn-success" type="submit">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>