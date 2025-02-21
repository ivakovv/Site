<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = '/andrey2/site';
require_once __DIR__ . '/../db_connection.php';

// Проверка авторизации пользователя
$is_logged_in = isset($_SESSION['user']);
$user_data = null;
if ($is_logged_in) {
    $user_data = [
        'first_name' => $_SESSION['user']['name'] ?? '',
        'last_name' => '',
        'email' => $_SESSION['user']['email'] ?? ''
    ];
}

// Получение списка телефонов
try {
    $stmt = $pdo->prepare("
        SELECT product_id, name_product, price, image_path, stock_quantity 
        FROM products 
        WHERE category = 'watch'
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="../css/products.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-base" content="<?php echo $base_url; ?>">
    <title>Смарт часы - iStore</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/modal-window/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        // Передаем данные о пользователе в JavaScript
        const userIsLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        let userData = <?php echo $user_data ? json_encode([
            'firstName' => $user_data['first_name'],
            'lastName' => $user_data['last_name'],
            'email' => $user_data['email']
        ]) : 'null'; ?>;
    </script>
    <script>
        // Определяем базовый URL для API
        const BASE_URL = '<?php echo $baseUrl; ?>';
        // Передаем данные о пользователе в JavaScript
        const userIsLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        let userData = <?php echo $user_data ? json_encode([
            'firstName' => $user_data['first_name'],
            'lastName' => $user_data['last_name'],
            'email' => $user_data['email']
        ]) : 'null'; ?>;
    </script>
</head>
<body>
    <!-- Модальное окно -->
    <div id="login-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login">Вход</button>
                <button class="auth-tab" data-tab="register">Регистрация</button>
            </div>
            
            <!-- Форма входа -->
            <div id="login-form-container" class="auth-form active">
                <h2>Авторизация</h2>
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-email">Email:</label>
                        <input type="email" id="login-email" name="email" required>
                        <span class="error" id="login-email-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Пароль:</label>
                        <input type="password" id="login-password" name="password" required>
                        <span class="error" id="login-password-error"></span>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember">
                        <label for="remember">Запомнить меня</label>
                    </div>
                    
                    <button type="submit">Войти</button>
                    <a href="#" class="forgot-password">Забыли пароль?</a>
                </form>
            </div>
            
            <!-- Форма регистрации -->
            <div id="register-form-container" class="auth-form">
                <h2>Регистрация</h2>
                <form id="register-form">
                    <div class="form-group">
                        <label for="register-name">ФИО:</label>
                        <input type="text" id="register-name" name="name" required>
                        <span class="error" id="register-name-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">Email:</label>
                        <input type="email" id="register-email" name="email" required>
                        <span class="error" id="register-email-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-phone">Телефон:</label>
                        <input type="tel" id="register-phone" name="phone" required>
                        <span class="error" id="register-phone-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password">Пароль:</label>
                        <input type="password" id="register-password" name="password" required>
                        <span class="error" id="register-password-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password-confirm">Подтверждение пароля:</label>
                        <input type="password" id="register-password-confirm" name="password_confirm" required>
                        <span class="error" id="register-password-confirm-error"></span>
                    </div>
                    
                    <button type="submit">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>

    <div id="order-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Оформление заказа</h2>
            <form id="order-form" action="process_order.php" method="POST">
                <div class="form-group">
                    <label for="name">ФИО:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" id="phone" name="phone" pattern="[0-9]{11}" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Адрес доставки:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="delivery">Способ доставки:</label>
                    <select id="delivery" name="delivery">
                        <option value="courier">Курьером (500₽)</option>
                        <option value="pickup">Самовывоз (бесплатно)</option>
                    </select>
                </div>
                
                <div id="cart-items-container">
                    <!-- Товары будут добавляться динамически -->
                </div>
                
                <div class="total-container">
                    <p>Сумма заказа: <span id="total-amount">0</span>₽</p>
                    <p>Скидка: <span id="discount-amount">0</span>₽</p>
                    <p>Доставка: <span id="delivery-cost">0</span>₽</p>
                    <p>Итого: <span id="final-total">0</span>₽</p>
                </div>
                
                <input type="hidden" id="cart-data" name="cart-data">
                <button type="submit">Оформить заказ</button>
            </form>
        </div>
    </div>

    <header>
    <div class="container">
        <!-- Добавляем логотип -->
        <a href="../init.php" class="logo">
            <img src="../icons/logo.png" alt="iStore Logo">
            <span class="logo-text">iStore</span>
        </a>

        <div class="header-buttons">
            <a href="#" class="login-button-container" id="login-button">
                <div class="login-icon">
                    <img src="../icons/user.png" alt="Login Icon" />
                </div>
                <div class="login-text">
                    <?php if ($is_logged_in): ?>
                        <?php echo htmlspecialchars($user_data['first_name']); ?>
                        <span class="logout-btn">(Выйти)</span>
                    <?php else: ?>
                        Вход
                    <?php endif; ?>
                </div>
            </a>
            <a href="#" class="cart-button-container" id="cart-button">
                <div class="cart-icon">
                    <img src="../icons/korzina.png" alt="Cart Icon" />
                    <span class="cart-counter" id="cart-counter">0</span>
                </div>
                <div class="cart-text">Корзина</div>
            </a>
        </div>
    </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li>
                    <a href="#">Каталог</a>
                    <ul class="dropdown">
                        <li><a href="phones.php">Телефоны</a></li>
                        <li><a href="headphones.php">Наушники</a></li>
                        <li><a href="tablets.php">Планшеты</a></li>
                        <li><a href="smartwatches.php">Смарт часы</a></li>
                        <li><a href="laptops.php">Ноутбуки</a></li>
                        <li><a href="accessories.php">Аксессуары</a></li>
                    </ul>
                </li>
                <li><a href="../about/about_info.php">О нас</a></li>
                <li><a href="../contacts/contacts.php">Контакты</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1 id="category" value="watch">Смарт часы</h1>
            
            <!-- Фильтры -->
            <div class="filters">
                <select id="sort-by">
                    <option value="price-asc">По цене (возрастание)</option>
                    <option value="price-desc">По цене (убывание)</option>
                    <option value="name">По названию</option>
                    <option value="new">Сначала новые</option>
                </select>
                
                <div class="price-filter">
                    <input type="number" id="price-min" placeholder="Цена от">
                    <input type="number" id="price-max" placeholder="Цена до">
                    <button onclick="applyPriceFilter()">Применить</button>
                </div>
            </div>

            <!-- Сисок товаров -->
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo $baseUrl . '/images/' . basename($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name_product']); ?>">
                        <h3><?php echo htmlspecialchars($product['name_product']); ?></h3>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</p>
                        <p class="stock">В наличии: <?php echo $product['stock_quantity']; ?> шт.</p>
                        <div class="button-group">
                            <button class="buy-button" 
                                    data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                                Купить
                            </button>
                            <button class="details-button">Подробнее</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer">
            <div class="row">
                <ul class="social-links">
                    <li><a href="https://vk.com/your_vk_group" target="_blank" rel="noopener"><i class="fa fa-vk"></i></a></li>
                    <li><a href="https://ok.ru/group/your_ok_group" target="_blank" rel="noopener"><i class="fa fa-odnoklassniki"></i></a></li>
                    <li><a href="https://t.me/your_telegram_channel" target="_blank" rel="noopener"><i class="fa fa-telegram"></i></a></li>
                    <li><a href="https://www.youtube.com/channel/your_youtube_channel" target="_blank" rel="noopener"><i class="fa fa-youtube-play"></i></a></li>
                </ul>
            </div>
    
            <div class="row">
                <ul>
                    <li><a href="#">Связаться с нами</a></li>
                    <li><a href="#">Наши услуги</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Правила и условия</a></li>
                    <li><a href="#">Карьера</a></li>
                </ul>
            </div>
    
            <div class="row">
                Ivakov © 2024 все права защищены || Разработано: Андрей
            </div>
        </div>
    </footer>

    <script>
        // Обновляем пути к API
        const API_PATH = '../';  // Добавляем эту константу
    </script>
    <script src="../js/modal-window.js"></script>
    <script src="../js/cart.js"></script>
    <script src="../js/auth.js"></script>
    <script src="../js/products.js"></script>
</body>
</html> 