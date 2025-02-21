document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sort-by');
    const priceMin = document.getElementById('price-min');
    const priceMax = document.getElementById('price-max');
    const productGrid = document.querySelector('.product-grid');

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            applyFilters();
        });
    }

    window.applyPriceFilter = function() {
        applyFilters();
    };

    function applyFilters() {
        const sortBy = sortSelect.value;
        const minPrice = priceMin.value ? parseFloat(priceMin.value) : null;
        const maxPrice = priceMax.value ? parseFloat(priceMax.value) : null;

        fetch('../api/filter_products.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                category: 'phone',
                sortBy: sortBy,
                minPrice: minPrice,
                maxPrice: maxPrice
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductGrid(data.products);
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateProductGrid(products) {
        productGrid.innerHTML = '';
        products.forEach(product => {
            const productCard = `
                <div class="product-card">
                    <img src="../${product.image_path}" 
                         alt="${escapeHtml(product.name_product)}">
                    <h3>${escapeHtml(product.name_product)}</h3>
                    <p class="price">${formatPrice(product.price)} ₽</p>
                    <p class="stock">В наличии: ${product.stock_quantity} шт.</p>
                    <div class="button-group">
                        <button class="buy-button" 
                                data-product-id="${product.product_id}">
                            Купить
                        </button>
                        <button class="details-button">Подробнее</button>
                    </div>
                </div>
            `;
            productGrid.innerHTML += productCard;
        });

        // Переинициализация обр��ботчиков кнопок покупки
        document.querySelectorAll('.buy-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                addToCart(productId);
            });
        });
    }

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('ru-RU').format(price);
    }
}); 