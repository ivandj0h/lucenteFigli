<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $role     = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Cek duplikat username
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah dipakai.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (name, username, email, phone, role, password, created_at)
            VALUES ('$name', '$username', '$email', '$phone', '$role', '$password', NOW())");

        if ($insert) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambahkan user.";
        }
    }
}
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Tambah User</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" required class="form-control">
    </div>
    <div class="mb-3">
        <label>No. HP</label>
        <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" required class="form-control">
    </div>
    <button class="btn btn-success" type="submit">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>