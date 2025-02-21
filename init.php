<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connection.php';
$baseUrl = '/andrey2/site/';

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
try {
    $stmt = $pdo->query("SELECT product_id, name_product, price, image_path 
                         FROM products 
                         WHERE is_popular = true 
                         LIMIT 9");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
     <!--FONT AWESOME-->
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script> 
    <meta charset="UTF-8">
    <title>iStore</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/slider.css">
    <link rel="stylesheet" href="css/modal-window/style.css">
    <script src="js/slider.js"></script>
    <script src="js/modal-window.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script>
        const BASE_URL = '<?php echo $baseUrl; ?>';
    </script>
    <script src="js/cart.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/form-validation.js"></script>
    <script>
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
                    <input type="tel" id="phone" name="phone" required>
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
            <a href="init.php" class="logo">
                <img src="icons/logo.png" alt="iStore Logo">
                <span class="logo-text">iStore</span>
            </a>
            <div class="header-buttons">
                <a href="#" class="login-button-container" id="login-button">
                    <div class="login-icon">
                        <img src="icons/user.png" alt="Login Icon" />
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
                        <img src="icons/korzina.png" alt="Cart Icon" />
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
                        <li><a href="catalog/phones.php">Телефоны</a></li>
                        <li><a href="catalog/headphones.php">Наушники</a></li>
                        <li><a href="catalog/tablets.php">Планшеты</a></li>
                        <li><a href="catalog/smartwatches.php">Смарт часы</a></li>
                        <li><a href="catalog/laptops.php">Ноутбуки</a></li>
                        <li><a href="catalog/accessories.php">Аксессуары</a></li>
                    </ul>
                </li>
                <li><a href="about/about_info.php">О нас</a></li>
                <li><a href="contacts/contacts.php">Контакты</a></li>
            </ul>
        </div>
    </nav>

    <section class="banner">
        <div class="container">
            <blockquote>
                <p>Дизайн - это не просто как выглядит вещь. Дизайн - это то, как вещь работает.</p>
                <footer><cite>Стив Джобс</cite></footer>
            </blockquote>
        </div>
    </section>    

    <section class="products">
    <div class="container">
        <h2>Популярные товары</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" onmouseover="this.classList.add('hover')" onmouseout="this.classList.remove('hover')">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name_product']); ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name_product']); ?></h3>
                        <p class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</p>
                        <div class="button-group">
                            <button class="buy-button" 
                                    data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                                Купить
                            </button>
                            <button class="details-button">Подробнее</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </div>
    </section>
    <source src="videos/main-video.mp4" type="video/mp4">
        </video>
    </section>
    <section class="about">
        <div class="container">
            <h2>О нас</h2>
            <p>Создавая комфорт и вдохновение в вашем доме.

                Мы - команда страстных профессионалов, которые любят все, что связано с технологиями, и верим, что они должны быть доступны всем. 
                
                Мы забоимся о том, чтобы вы нашли в нашем магазине идеальную технику для решения ваших задач и создания комфорта в вашем доме. 
                
                Наши консультанты всегда готовы помочь вам сделать правильный выбор, а цены вас приятно удивят.</p>
        </div>
    </section>
    <section>
        <div class="all">
            <input checked type="radio" name="respond" id="desktop">
            <article id="slider">
                <input checked type="radio" name="slider" id="switch1">
                <input type="radio" name="slider" id="switch2">
                <input type="radio" name="slider" id="switch3">
                <input type="radio" name="slider" id="switch4">
                <div id="slides">
                    <div id="overflow">
                        <div class="image">
                            <article>
                                <img src="videos/slider/slide1.gif">
                                <div class="text">A17 Pro.<br>
                                    Чип, меняющий правила игры. <br>Потрясающая производительность.</div>
                            </article>
                            <article>
                                <video autoplay loop muted>
                                    <source src="videos/slider/slide2.mp4" type="video/mp4">
                                </video>
                                <div class="text">Титан. <br> Сильный. Легкий. Профессиональный.</div>
                            </article>
                            <article>
                                <video autoplay loop muted>
                                    <source src="videos/slider/slide3.mp4" type="video/mp4">
                                </video>
                                <div class="text">iPhone 15 Pro Max оснащен самым <br> большим оптическим зумом в истории iPhone.</div>
                            </article>
                            <article>
                                <video autoplay loop muted>
                                    <source src="videos/slider/slide4.mp4" type="video/mp4">
                                </video>
                                <div class="text">Action Button. <br> Что будет делать ваша кнопка?</div>
                            </article>
                        </div>
                    </div>
                </div>
                <div id="controls">
                    <label for="switch1"></label>
                    <label for="switch2"></label>
                    <label for="switch3"></label>
                    <label for="switch4"></label>
                </div>
                <div id="active">
                    <label for="switch1"></label>
                    <label for="switch2"></label>
                    <label for="switch3"></label>
                    <label for="switch4"></label>
                </div>
            </article>
        </div>
    </section>

    <section class="feedback">
        <div class="container">
            <h2>Оставьте свой отзыв</h2>
            <form action="#">
                <input type="text" name="name" placeholder="Ваше имя" required>
                <input type="email" name="email" placeholder="Ваш email" required>
                <textarea name="message" placeholder="Ваше сообщение" required></textarea>
                <button type="submit">Отправить</button>
            </form>
        </div>
    </section>

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
</body>
</html>
