// Ждем полной загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    // Переключение вкладок
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            button.classList.add('active');
            document.getElementById(button.dataset.tab).classList.add('active');
        });
    });

    // Добавляем обработчики для фильтров товаров
    const categoryFilter = document.getElementById('category-filter');
    const searchProduct = document.getElementById('search-product');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }

    if (searchProduct) {
        searchProduct.addEventListener('input', filterProducts);
    }

    // Добавляем обработчики для фильтров пользователей
    const roleFilter = document.getElementById('role-filter');
    const searchUser = document.getElementById('search-user');

    if (roleFilter) {
        roleFilter.addEventListener('change', filterUsers);
    }

    if (searchUser) {
        searchUser.addEventListener('input', filterUsers);
    }
});

// Функция фильтрации товаров
function filterProducts() {
    const categoryFilter = document.getElementById('category-filter').value;
    const popularFilter = document.getElementById('popular-filter').value;
    const searchQuery = document.getElementById('search-product').value.toLowerCase();
    const products = document.querySelectorAll('.product-item');

    console.log('Filtering with:', { categoryFilter, popularFilter, searchQuery }); // Для отладки

    products.forEach(product => {
        const category = product.dataset.category;
        const isPopular = product.dataset.popular === '1';
        const productName = product.querySelector('.product-info h3').textContent.toLowerCase();
        
        console.log('Product data:', { category, isPopular, productName }); // Для отладки
        
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        const matchesPopular = popularFilter === '' || 
            (popularFilter === '1' && isPopular) || 
            (popularFilter === '0' && !isPopular);
        const matchesSearch = productName.includes(searchQuery);

        console.log('Matches:', { matchesCategory, matchesPopular, matchesSearch }); // Для отладки

        product.style.display = (matchesCategory && matchesPopular && matchesSearch) ? '' : 'none';
    });
}

// Инициализация фильтров
function initializeFilters() {
    // Фильтры для товаров
    const categoryFilter = document.getElementById('category-filter');
    const productSearch = document.getElementById('search-product');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => {
            console.log('Category changed:', categoryFilter.value); // Для отладки
            filterProducts();
        });
    }
    
    if (productSearch) {
        productSearch.addEventListener('input', () => {
            console.log('Product search:', productSearch.value); // Для отладки
            filterProducts();
        });
    }

    // Фильтры для пользователей
    const roleFilter = document.getElementById('role-filter');
    const userSearch = document.getElementById('search-user');
    
    if (roleFilter) {
        roleFilter.addEventListener('change', () => {
            console.log('Role changed:', roleFilter.value); // Для отладки
            filterUsers();
        });
    }
    
    if (userSearch) {
        userSearch.addEventListener('input', () => {
            console.log('User search:', userSearch.value); // Для отладки
            filterUsers();
        });
    }
}

// Функция фильтрации пользователей
function filterUsers() {
    const roleFilter = document.getElementById('role-filter').value;
    const searchQuery = document.getElementById('search-user').value.toLowerCase();
    const users = document.querySelectorAll('.user-item');

    users.forEach(user => {
        const role = user.dataset.role;
        const userInfo = user.querySelector('.user-info').textContent.toLowerCase();
        
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesSearch = !searchQuery || userInfo.includes(searchQuery);

        user.style.display = (matchesRole && matchesSearch) ? '' : 'none';
    });
}

// Функция сброса фильтров
function resetFilters() {
    document.getElementById('category-filter').value = '';
    document.getElementById('search-product').value = '';
    filterProducts();
    
    document.getElementById('role-filter').value = '';
    document.getElementById('search-user').value = '';
    filterUsers();
}

// Функции для работы с товарами
function showAddProductForm() {
    const modal = document.getElementById('productModal');
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Добавить товар</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">
                
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category" required>
                        <option value="">Выберите категорию</option>
                        <option value="phone">Телефоны</option>
                        <option value="headphones">Наушники</option>
                        <option value="tablet">Планшеты</option>
                        <option value="laptop">Ноутбуки</option>
                        <option value="watch">Часы</option>
                        <option value="accessories">Аксессуары</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" required placeholder="Введите название товара">
                </div>
                
                <div class="form-group">
                    <label>Цена</label>
                    <input type="number" name="price" required placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label>Количество</label>
                    <input type="number" name="quantity" required placeholder="0">
                </div>
                
                <div class="form-group">
                    <label>Изображение</label>
                    <input type="file" name="image" required accept="image/*">
                    <small>Поддерживаемые форматы: JPG, PNG, GIF</small>
                </div>
                
                <div class="form-group">
                    <label>Популярный товар</label>
                    <select name="is_popular" required>
                        <option value="0">Нет</option>
                        <option value="1">Да</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('productModal')">Отмена</button>
                    <button type="submit">Добавить</button>
                </div>
            </form>
        </div>
    `;
    modal.style.display = 'block';
}

function editProduct(product) {
    const modal = document.getElementById('productModal');
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Редактировать товар</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="product_id" value="${product.product_id}">
                
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category" required>
                        <option value="phone" ${product.category === 'phone' ? 'selected' : ''}>Телефоны</option>
                        <option value="headphones" ${product.category === 'headphones' ? 'selected' : ''}>Наушники</option>
                        <option value="tablet" ${product.category === 'tablet' ? 'selected' : ''}>Планшеты</option>
                        <option value="laptop" ${product.category === 'laptop' ? 'selected' : ''}>Ноутбуки</option>
                        <option value="watch" ${product.category === 'watch' ? 'selected' : ''}>Часы</option>
                        <option value="accessories" ${product.category === 'accessories' ? 'selected' : ''}>Аксессуары</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" value="${product.name_product}" required>
                </div>
                
                <div class="form-group">
                    <label>Цена</label>
                    <input type="number" name="price" value="${product.price}" required>
                </div>
                
                <div class="form-group">
                    <label>Количество</label>
                    <input type="number" name="quantity" value="${product.stock_quantity}" required>
                </div>
                
                <div class="form-group">
                    <label>Изображение</label>
                    <input type="file" name="image" accept="image/*">
                    <small>Оставьте пустым, чтобы сохранить текущее изображение</small>
                </div>
                
                <div class="form-group">
                    <label>Популярный товар</label>
                    <select name="is_popular" required>
                        <option value="0" ${!product.is_popular ? 'selected' : ''}>Нет</option>
                        <option value="1" ${product.is_popular ? 'selected' : ''}>Да</option>
                    </select>
                </div>
                
                <button type="submit">Сохранить</button>
                <button type="button" onclick="closeModal('productModal')">Отмена</button>
            </form>
        </div>
    `;
    modal.style.display = 'block';
}

function deleteProduct(productId) {
    if (confirm('Вы уверены, что хотите удалить этот товар?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_product">
            <input type="hidden" name="product_id" value="${productId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Функции для работы с пользователями
function showAddUserForm() {
    const modal = document.getElementById('userModal');
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Добавить пользователя</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add_user">
                
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label>Фамилия</label>
                    <input type="text" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label>Адрес</label>
                    <input type="text" name="address" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Роль</label>
                    <select name="user_role" required>
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                
                <button type="submit">Добавить</button>
                <button type="button" onclick="closeModal('userModal')">Отмена</button>
            </form>
        </div>
    `;
    modal.style.display = 'block';
}

function editUser(user) {
    const modal = document.getElementById('userModal');
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Редактировать пользователя</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" value="${user.user_id}">
                
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="first_name" value="${user.first_name}" required>
                </div>
                
                <div class="form-group">
                    <label>Фамилия</label>
                    <input type="text" name="last_name" value="${user.last_name}" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="${user.email}" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" value="${user.phone}" required>
                </div>
                
                <div class="form-group">
                    <label>Адрес</label>
                    <input type="text" name="address" value="${user.address}" required>
                </div>
                
                <div class="form-group">
                    <label>Новый пароль</label>
                    <input type="password" name="password">
                    <small>Оставьте пустым, чтобы сохранить текущий пароль</small>
                </div>
                
                <div class="form-group">
                    <label>Роль</label>
                    <select name="user_role" required>
                        <option value="user" ${user.user_role === 'user' ? 'selected' : ''}>Пользователь</option>
                        <option value="admin" ${user.user_role === 'admin' ? 'selected' : ''}>Администратор</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('userModal')">Отмена</button>
                    <button type="submit">Сохранить</button>
                </div>
            </form>
        </div>
    `;
    modal.style.display = 'block';
}

function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Обновляем функцию переключения популярности
function togglePopular(productId, setPopular) {
    console.log('Toggling popular:', { productId, setPopular }); // Для отладки
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="toggle_popular">
        <input type="hidden" name="product_id" value="${productId}">
        <input type="hidden" name="is_popular" value="${setPopular}">
    `;
    document.body.appendChild(form);
    form.submit();
}
 