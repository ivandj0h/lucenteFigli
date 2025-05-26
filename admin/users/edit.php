<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

// Ambil data user berdasarkan id
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $role     = $_POST['role'];

    // Validasi username unik (kecuali user sekarang)
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan pengguna lain.";
    } else {
        $sql = "UPDATE users SET name='$name', username='$username', email='$email', phone='$phone', role='$role' WHERE id=$id";
        $update = mysqli_query($conn, $sql);

        if ($update) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal memperbarui data.";
        }
    }
}
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Edit User</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>No. HP</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control">
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>