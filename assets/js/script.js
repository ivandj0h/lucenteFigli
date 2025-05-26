
document.addEventListener('DOMContentLoaded', () => {
  const products = window.productsData || [];
  let showing = 6;
  const container = document.getElementById('produk-list');
  const toggleBtn = document.getElementById('toggle-btn');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      if (showing < products.length) {
        let limit = Math.min(showing + 6, products.length);
        for (let i = showing; i < limit; i++) {
          const p = products[i];
          const div = document.createElement('div');
          div.className = 'col-md-4 mb-4 product-card';
          div.innerHTML = `
            <div class="card h-100">
              <img src="public/images/produk/${p.image}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${p.name}">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">${p.name}</h5>
                <p class="card-text fw-bold text-danger">Rp ${Number(p.price).toLocaleString('id-ID')}</p>
                <a href="cart.php?add=${p.id}" class="btn btn-outline-primary mt-auto">Tambah ke Keranjang</a>
              </div>
            </div>
          `;
          container.appendChild(div);
        }
        showing += 6;
        if (showing >= products.length) {
          toggleBtn.innerText = 'Show Less';
        }
      } else {
        // Show Less
        const cards = document.querySelectorAll('.product-card');
        cards.forEach((card, i) => {
          if (i >= 6) card.remove();
        });
        showing = 6;
        toggleBtn.innerText = 'Show More';
      }
    });
  }
});
