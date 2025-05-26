<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';
include '../../includes/admin_header.php';

$products = mysqli_query($conn, "SELECT p.*, c.name AS category_name 
                                FROM products p 
                                LEFT JOIN categories c ON p.category_id = c.id 
                                ORDER BY p.created_at DESC");
?>

<h2 class="mb-4">Manajemen Produk</h2>
<a href="create.php" class="btn btn-primary mb-3">+ Tambah Produk</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?php
                        $image_path = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . htmlspecialchars($p['image']);
                        if ($p['image'] && file_exists($image_path)): ?>
                            <img src="./uploads/<?= htmlspecialchars($p['image']) ?>" alt="Product Image" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            <span>Tidak ada gambar</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                    <td><?= number_format($p['price'] ?? 0, 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($p['stock'] ?? '-') ?></td>
                    <td><?= $p['status'] ? 'Aktif' : 'Nonaktif' ?></td>
                    <td>
                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button
                            type="button"
                            class="btn btn-sm btn-danger delete-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-id="<?= $p['id'] ?>"
                            data-name="<?= htmlspecialchars($p['name']) ?>">
                            Hapus
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="delete.php" id="deleteForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus produk <strong id="deleteName"></strong>?</p>
                    <input type="hidden" name="id" id="deleteProductId">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .table td {
        vertical-align: middle;
    }
</style>

<?php include '../../includes/admin_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const nameSpan = document.getElementById('deleteName');
        const productIdInput = document.getElementById('deleteProductId');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const productId = this.getAttribute('data-id');
                nameSpan.textContent = name;
                productIdInput.value = productId;
            });
        });
    });
</script>