<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM categories WHERE id = $id");
$category = mysqli_fetch_assoc($query);

if (!$category) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];

    $check = mysqli_query($conn, "SELECT id FROM categories WHERE name = '$name' AND id != $id");
    if (mysqli_num_rows($check) > 0) {
        $error = "Nama kategori sudah digunakan.";
    } else {
        $sql = "UPDATE categories SET name='$name', gender='$gender' WHERE id=$id";
        $update = mysqli_query($conn, $sql);

        if ($update) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal memperbarui kategori.";
        }
    }
}
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Edit Kategori</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-select" required>
            <option value="pria" <?= $category['gender'] === 'pria' ? 'selected' : '' ?>>Pria</option>
            <option value="wanita" <?= $category['gender'] === 'wanita' ? 'selected' : '' ?>>Wanita</option>
            <option value="unisex" <?= $category['gender'] === 'unisex' ? 'selected' : '' ?>>Unisex</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>