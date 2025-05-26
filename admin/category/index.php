<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';
include '../../includes/admin_header.php';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>

<h2 class="mb-4">Manajemen Kategori</h2>
<a href="create.php" class="btn btn-primary mb-3">+ Tambah Kategori</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Gender</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($c = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($c['name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['gender'] ?? '-') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button
                            type="button"
                            class="btn btn-sm btn-danger delete-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-id="<?= $c['id'] ?>"
                            data-name="<?= htmlspecialchars($c['name']) ?>">
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
                    <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteName"></strong>?</p>
                    <input type="hidden" name="id" id="deleteCategoryId">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const nameSpan = document.getElementById('deleteName');
        const categoryIdInput = document.getElementById('deleteCategoryId');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const categoryId = this.getAttribute('data-id');
                nameSpan.textContent = name;
                categoryIdInput.value = categoryId;
            });
        });
    });
</script>