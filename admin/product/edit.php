<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';

// Path ke folder upload di dalam admin/product/uploads/
$upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$log_file = __DIR__ . DIRECTORY_SEPARATOR . 'upload_log.txt';

// Fungsi untuk logging
function log_message($message)
{
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

log_message("Memulai proses edit produk...");

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($query);

if (!$product) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = trim($_POST['category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $status = isset($_POST['status']) ? 1 : 0;

    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        if (!file_exists($upload_dir)) {
            log_message("Folder $upload_dir tidak ada, mencoba membuat...");
            if (!mkdir($upload_dir, 0755, true)) {
                log_message("Gagal membuat folder $upload_dir");
                $error = "Gagal membuat folder upload.";
            } else {
                log_message("Folder $upload_dir berhasil dibuat.");
            }
        }
        $target_file = $upload_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
            log_message("Error: Format gambar tidak diizinkan - $imageFileType");
        } elseif ($_FILES['image']['size'] > 5000000) {
            $error = "Ukuran gambar maksimal 5MB.";
            log_message("Error: Ukuran gambar terlalu besar - " . $_FILES['image']['size'] . " bytes");
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $error = "Gagal mengunggah gambar. Pastikan folder uploads/ di dalam admin/product/ memiliki izin tulis.";
            log_message("Error: Gagal mengunggah gambar - " . error_get_last()['message']);
        } else {
            log_message("File baru berhasil diunggah ke $target_file");
        }
    }

    if (!$error) {
        $check = mysqli_query($conn, "SELECT id FROM products WHERE name = '$name' AND id != $id");
        if (mysqli_num_rows($check) > 0) {
            $error = "Nama produk sudah digunakan.";
            log_message("Error: Nama produk sudah dipakai - $name");
        } else {
            $sql = "UPDATE products SET category_id='$category_id', name='$name', description='$description', image='$image', price='$price', stock='$stock', status='$status' WHERE id=$id";
            $update = mysqli_query($conn, $sql);

            if ($update) {
                log_message("Produk berhasil diperbarui: $name");
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal memperbarui produk.";
                log_message("Error: Gagal memperbarui produk - " . mysqli_error($conn));
            }
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Edit Produk</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Kategori</label>
        <select name="category_id" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
        <label>Gambar Saat Ini</label>
        <div>
            <img src="./uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
        </div>
    </div>
    <div class="mb-3">
        <label>Gambar Baru (opsional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>
    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Status</label>
        <div>
            <input type="checkbox" name="status" value="1" <?= $product['status'] ? 'checked' : '' ?>> Aktif
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>