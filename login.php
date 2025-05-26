<?php
session_start();
require 'config/db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // ✅ Redirect sesuai role
        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: user/index.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<?php include 'includes/header.php'; ?>
<main class="container py-5">
    <h2 class="mb-4">Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Belum punya akun?</a>
    </form>
</main>
<?php include 'includes/footer.php'; ?>