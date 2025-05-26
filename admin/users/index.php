<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require '../../config/db.php';
include '../../includes/admin_header.php';

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<h2 class="mb-4">Manajemen User</h2>
<a href="create.php" class="btn btn-primary mb-3">+ Tambah User</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($u['name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['username'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button
                            type="button"
                            class="btn btn-sm btn-danger delete-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-id="<?= $u['id'] ?>"
                            data-username="<?= htmlspecialchars($u['username']) ?>">
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
                    <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUsername"></strong>?</p>
                    <input type="hidden" name="id" id="deleteUserId">
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
        const usernameSpan = document.getElementById('deleteUsername');
        const userIdInput = document.getElementById('deleteUserId');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                const userId = this.getAttribute('data-id');
                usernameSpan.textContent = username;
                userIdInput.value = userId;
            });
        });
    });
</script>