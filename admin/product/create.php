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

log_message("Memulai proses upload gambar...");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = trim($_POST['category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $status = isset($_POST['status']) ? 1 : 0;

    $image = $_FILES['image']['name'];
    if (!file_exists($upload_dir)) {
        log_message("Folder $upload_dir tidak ada, mencoba membuat...");
        if (!mkdir($upload_dir, 0755, true)) {
            log_message("Gagal membuat folder $upload_dir");
            $error = "Gagal membuat folder upload.";
        } else {
            log_message("Folder $upload_dir berhasil dibuat.");
        }
    }

    $image_name = time() . '_' . basename($image);
    $target_file = $upload_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowed_types)) {
        $error = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
        log_message("Error: Format gambar tidak diizinkan - $imageFileType");
    } elseif ($_FILES['image']['size'] > 5000000) {
        $error = "Ukuran gambar maksimal 5MB.";
        log_message("Error: Ukuran gambar terlalu besar - " . $_FILES['image']['size'] . " bytes");
    } else {
        log_message("Mencoba mengunggah file ke $target_file...");
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            log_message("File berhasil diunggah ke $target_file");
            $check = mysqli_query($conn, "SELECT id FROM products WHERE name = '$name'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Nama produk sudah dipakai.";
                log_message("Error: Nama produk sudah dipakai - $name");
            } else {
                $insert = mysqli_query($conn, "INSERT INTO products (category_id, name, description, image, price, stock, status, created_at) 
                    VALUES ('$category_id', '$name', '$description', '$image_name', '$price', '$stock', '$status', NOW())");
                if ($insert) {
                    log_message("Produk berhasil ditambahkan ke database: $name");
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Gagal menambahkan produk.";
                    log_message("Error: Gagal menambahkan produk ke database - " . mysqli_error($conn));
                }
            }
        } else {
            $error = "Gagal mengunggah gambar. Pastikan folder uploads/ di dalam admin/product/ memiliki izin tulis.";
            log_message("Error: Gagal mengunggah gambar - " . error_get_last()['message']);
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<?php include '../../includes/admin_header.php'; ?>

<h2 class="mb-4">Tambah Produk</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Kategori</label>
        <select name="category_id" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label>Gambar</label>
        <input type="file" name="image" accept="image/*" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="price" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stock" required class="form-control">
    </div>
    <div class="mb-3">
        <label>Status</label>
        <div>
            <input type="checkbox" name="status" value="1" checked> Aktif
        </div>
    </div>
    <button class="btn btn-success" type="submit">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/admin_footer.php'; ?>