let cart = [];

function getBasePath() {
    const currentPath = window.location.pathname;
    if (currentPath.includes('/catalog/')) {
        return '../';
    }
    if (currentPath.endsWith('/site/') || currentPath.endsWith('/site/index.php')) {
        return '';
    }
    return '';
}

function getApiUrl(endpoint) {
    const currentPath = window.location.pathname;
    const isInSubfolder = currentPath.includes('/catalog/');
    return isInSubfolder ? `../get_cart.php` : `get_cart.php`;
}

// Загрузка корзины при инициализации
async function loadCart() {
    try {
        const basePath = getBasePath();
        const response = await fetch(basePath + 'get_cart.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        
        if (data.success && Array.isArray(data.items)) {
            cart = data.items.map(item => ({
                id: item.product_id,
                name: item.name_product,
                price: parseFloat(item.price),
                image: item.image_path,
                quantity: parseInt(item.quantity)
            }));
            updateCartCounter();
            updateCartDisplay();
        }
    } catch (error) {
        console.error('Ошибка при загрузке корзины:', error);
    }
}

async function fillOrderForm() {
    try {
        const basePath = getBasePath();
        const response = await fetch(basePath + 'get_user_data.php');
        const data = await response.json();
        
        if (data.success) {
            // Заполняем поля формы данными пользователя
            document.getElementById('name').value = data.data.name;
            document.getElementById('email').value = data.data.email;
            document.getElementById('phone').value = data.data.phone;
            document.getElementById('address').value = data.data.address || '';
            
            // Все поля доступны для редактирования
            const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
            inputs.forEach(input => {
                input.readOnly = false;
                input.classList.add('editable');
            });
        }
    } catch (error) {
        console.error('Ошибка при получении данных пользователя:', error);
    }
}

// Модифицируем функцию добавления в корзину
async function addToCart(productId, name, price, image) {
    try {
        const basePath = getBasePath();
        // Получаем только имя файла из URL изображения
        const imageName = image.split('/').pop();
        
        const response = await fetch(basePath + 'save_cart_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1,
                image_path: 'images/' + imageName // Сохраняем только относительный путь
            })
        });

        const data = await response.json();
        
        if (data.success) {
            await loadCart();
        }
    } catch (error) {
        console.error('Ошибка при добавлении товара:', error);
    }
}

// Модифицируем функцию удаления из корзины
async function removeFromCart(index) {
    try {
        const basePath = getBasePath();
        const item = cart[index];
        const response = await fetch(basePath + 'remove_cart_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: item.id
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Перезагружаем корзину вместо локального обновления
            await loadCart();
        }
    } catch (error) {
        console.error('Ошибка при удалении товара:', error);
    }
}

function updateCartCounter() {
    // Считаем общее количество товаров, а не количество позиций
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cart-counter').textContent = totalItems;
}

function updateCartDisplay() {
    const container = document.getElementById('cart-items-container');
    const totalElement = document.getElementById('total-amount');
    let total = 0;
    
    container.innerHTML = '';
    
    cart.forEach((item, index) => {
        total += parseFloat(item.price) * item.quantity;
        const basePath = getBasePath();
        
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-image">
                <img src="${basePath}${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p>${number_format(item.price)} ₽ × ${item.quantity} шт.</p>
                <button class="remove-item" onclick="removeFromCart(${index})">Удалить</button>
            </div>
        `;
        container.appendChild(cartItem);
    });
    
    totalElement.textContent = number_format(total);
    updateFinalTotal();
}

function number_format(number) {
    return new Intl.NumberFormat('ru-RU').format(number);
}

function updateFinalTotal() {
    const totalAmount = parseFloat(document.getElementById('total-amount').textContent.replace(/\s/g, ''));
    const deliveryMethod = document.getElementById('delivery').value;
    const deliveryCost = deliveryMethod === 'courier' ? 500 : 0;
    const discount = 0; // Здесь можно добавить логику расчета скидки
    
    document.getElementById('delivery-cost').textContent = number_format(deliveryCost);
    document.getElementById('discount-amount').textContent = number_format(discount);
    document.getElementById('final-total').textContent = number_format(totalAmount + deliveryCost - discount);
}

function showOrderModal() {
    const modal = document.getElementById('order-modal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Блокируем прокрутку
        document.body.classList.add('modal-open');
        updateCartDisplay();
        fillOrderForm();

        // Добавляем обработчик для клика вне модального окна
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideOrderModal();
            }
        });
    }
}

function hideOrderModal() {
    const modal = document.getElementById('order-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Возвращаем прокрутку
        document.body.classList.remove('modal-open');
    }
}

// Добавляем обработчик клавиши Escape для корзины
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('order-modal');
        if (modal && modal.style.display === 'block') {
            hideOrderModal();
        }
    }
});

// Обработчики событий
document.addEventListener('DOMContentLoaded', function() {
    loadCart(); // Загружаем корзину при загрузке страницы
    
    // Обработчик для копок "Купить"
    document.querySelectorAll('.buy-button').forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productId = this.dataset.productId;
            const name = productCard.querySelector('h3').textContent;
            const price = parseFloat(productCard.querySelector('p').textContent.replace(/[^\d]/g, ''));
            const image = productCard.querySelector('img').src;
            
            addToCart(productId, name, price, image);
        });
    });

    // Обработчик изменения способа доставки
    document.getElementById('delivery').addEventListener('change', updateFinalTotal);

    // Обновляем обработчик клика по кнопке корзины
    document.getElementById('cart-button').addEventListener('click', function(e) {
        e.preventDefault();
        showOrderModal();
    });

    // Обновляем обработчик для закрытия модального окна
    const closeButtons = document.querySelectorAll('.modal .close-button');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal.id === 'order-modal') {
                hideOrderModal();
            }
        });
    });

    // Закрытие модального окна при клике вне его области
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('order-modal');
        if (e.target === modal) {
            hideOrderModal();
        }
    });

    // Обновляем обработчик отправки формы заказа
    document.getElementById('order-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Проверяем, есть ли товары в корзине
        if (cart.length === 0) {
            alert('Ваша корзина пуста');
            return;
        }

        try {
            const basePath = getBasePath();
            
            // Проверяем наличие товаров в БД
            const checkResponse = await fetch(basePath + 'check_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ items: cart })
            });
            
            const checkResult = await checkResponse.json();
            
            if (!checkResult.success) {
                alert(checkResult.message);
                return;
            }

            // Очищаем корзину в БД
            await fetch(basePath + 'clear_cart.php', {
                method: 'POST'
            });
            
            // Очищаем локальную корзину
            cart = [];
            updateCartCounter();
            
            // Показываем сообщение об успехе
            alert('Спасибо за заказ!');
            
            // Перезагружаем страницу
            window.location.reload();
            
        } catch (error) {
            console.error('Ошибка при оформлении заказа:', error);
            alert('Произошла ошибка при оформлении заказа');
        }
    });
}); 