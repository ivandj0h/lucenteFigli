<?php
require 'config/db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Cek apakah username sudah ada
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = mysqli_query($conn, "INSERT INTO users (name, username, email, password, role) 
                        VALUES ('$name', '$username', '$email', '$hashed', 'user')");
        if ($query) {
            $_SESSION['user'] = [
                'name' => $name,
                'username' => $username,
                'role' => 'user'
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Registrasi gagal!";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<main class="container py-5">
    <h2 class="mb-4">Register</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
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
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-dark">Daftar</button>
        <a href="login.php" class="btn btn-link">Sudah punya akun? Login</a>
    </form>
</main>
<?php include 'includes/footer.php'; ?>