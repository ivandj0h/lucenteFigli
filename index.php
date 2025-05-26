<?php include 'config/db.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
$query = mysqli_query($conn, "SELECT * FROM products WHERE status = 1 ORDER BY created_at DESC");
$produk = [];
while ($row = mysqli_fetch_assoc($query)) {
    $produk[] = $row;
}
?>

<main>
    <!-- HERO SECTION -->
    <section class="py-5 text-center bg-light">
        <div class="container">
            <h1 class="display-4 fw-bold">Selamat Datang di Lucente Figli</h1>
            <p class="lead">Tampil elegan, penuh gaya. Temukan produk terbaik untukmu.</p>
            <a href="#produk" class="btn btn-primary btn-lg">Belanja Sekarang</a>
        </div>
    </section>

    <!-- PRODUK SECTION -->
    <section id="produk" class="py-5">
        <div class="container">
            <div class="row" id="product-list">
                <?php
                if (count($produk) == 0):
                ?>
                    <div class="col text-center text-muted">
                        <p>Belum ada produk yang tersedia saat ini.</p>
                    </div>
                    <?php
                else:
                    $limit = min(6, count($produk));
                    for ($i = 0; $i < $limit; $i++):
                        $p = $produk[$i];
                    ?>
                        <div class="col-md-4 mb-4 product-card">
                            <div class="card h-100">
                                <img src="admin/product/uploads/<?= $p['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= $p['name']; ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= $p['name']; ?></h5>
                                    <p class="card-text fw-bold text-danger">Rp <?= number_format($p['price'], 0, ',', '.'); ?></p>
                                    <a href="cart.php?add=<?= $p['id']; ?>" class="btn btn-outline-primary mt-auto">Tambah ke Keranjang</a>
                                </div>
                            </div>
                        </div>
                <?php endfor;
                endif; ?>
            </div>

            <?php if (count($produk) > 6): ?>
                <div class="text-center mt-4">
                    <button id="toggle-btn" class="btn btn-outline-primary">Tampilkan Lebih Banyak</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<script>
    const productsData = <?= json_encode($produk); ?>;
    window.productsData = productsData;
</script>

<?php include 'includes/footer.php'; ?>