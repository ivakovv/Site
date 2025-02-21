document.addEventListener('DOMContentLoaded', function() {
    // Получаем категорию из элемента на странице
    const categoryElement = document.getElementById('category');
    const category = categoryElement ? categoryElement.getAttribute('value') : '';

    // Обработчики для фильтров
    const sortSelect = document.getElementById('sort-by');
    const priceMinInput = document.getElementById('price-min');
    const priceMaxInput = document.getElementById('price-max');

    // Функция для применения фильтров
    async function applyFilters() {
        try {
            const basePath = getBasePath();
            const response = await fetch(basePath + 'api/filter_products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    category: category,
                    sortBy: sortSelect.value,
                    minPrice: priceMinInput.value ? Number(priceMinInput.value) : null,
                    maxPrice: priceMaxInput.value ? Number(priceMaxInput.value) : null
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                updateProductsDisplay(data.products);
            } else {
                console.error('Ошибка при получении данных:', data.message);
                alert('Произошла ошибка при фильтрации товаров');
            }
        } catch (error) {
            console.error('Ошибка при фильтрации:', error);
            alert('Произошла ошибка при обращении к серверу');
        }
    }

    // Функция обновления отображения товаров
    function updateProductsDisplay(products) {
        const productGrid = document.querySelector('.product-grid');
        if (!productGrid) return;

        console.log('Received products:', products);

        productGrid.innerHTML = products.map(product => {
            console.log('Processing image path:', product.image_path);
            return `
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="${product.image_path}" 
                             alt="${product.name_product}"
                             onerror="console.error('Failed to load image:', this.src)">
                    </div>
                    <div class="product-info">
                        <h3>${product.name_product}</h3>
                        <p class="price">${new Intl.NumberFormat('ru-RU').format(product.price)} ₽</p>
                        <p class="stock">В наличии: ${product.stock_quantity} шт.</p>
                        <div class="button-group">
                            <button class="buy-button" data-product-id="${product.product_id}">
                                Купить
                            </button>
                            <button class="details-button">Подробнее</button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        initializeButtonHandlers();
    }

    // Инициализация обработчиков кнопок
    function initializeButtonHandlers() {
        // Обработчики для кнопок "Купить"
        document.querySelectorAll('.buy-button').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                addToCart(productId);
            });
        });

        // Обработчики для кнопок "Подробнее"
        document.querySelectorAll('.details-button').forEach(button => {
            button.addEventListener('click', function() {
                // Логика для кнопки "Подробнее"
                // ...
            });
        });
    }

    // Добавляем обработчики событий
    if (sortSelect) {
        sortSelect.addEventListener('change', applyFilters);
    }

    // Функция для применения фильтра цены
    window.applyPriceFilter = function() {
        applyFilters();
    };

    // Инициализация обработчиков при загрузке страницы
    initializeButtonHandlers();
});

// Вспомогательная функция для определения базового пути
function getBasePath() {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/catalog/')) {
        return '../';
    }
    return '';
}

// Вспомогательная функция для получения имени файла из пути
function basename(path) {
    return path.split('/').pop();
}
