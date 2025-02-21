<?php
session_start();
$baseUrl = '/andrey2/site';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - iStore</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/modal-window/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/contacts.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../init.php" class="logo">
                <img src="../icons/logo.png" alt="iStore Logo">
                <span class="logo-text">iStore</span>
            </a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="../init.php">Главная</a></li>
            <li>
                <a href="#">Каталог</a>
                <ul class="dropdown">
                    <li><a href="../catalog/phones.php">Телефоны</a></li>
                    <li><a href="../catalog/tablets.php">Планшеты</a></li>
                    <li><a href="../catalog/laptops.php">Ноутбуки</a></li>
                    <li><a href="../catalog/headphones.php">Наушники</a></li>
                    <li><a href="../catalog/smartwatches.php">Смарт-часы</a></li>
                </ul>
            </li>
            <li><a href="../about/about_info.php">О нас</a></li>
            <li><a href="contacts.php">Контакты</a></li>
        </ul>
    </nav>

    <main>
        <div class="contacts-container">
            <div class="contacts-header">
                <h1>Наши контакты</h1>
            </div>

            <div class="contacts-info">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fa fa-map-marker"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Адрес</h3>
                        <p>г. Рязань, Первомайский проспект, 70к1<br>
                        ТРЦ "Виктория Плаза", 2 этаж</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Режим работы</h3>
                        <p>Ежедневно<br>10:00 - 22:00</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Телефон</h3>
                        <p><a href="tel:+79008081808">+7 (900) 808-18-08</a></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Email</h3>
                        <p><a href="mailto:info@istore.ru">info@istore.ru</a></p>
                    </div>
                </div>
            </div>

            <div class="contact-main">
                <div class="map-container">
                    <script type="text/javascript" charset="utf-8" async 
                        src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A1e7c2c0c5f7c0f0c1e6e4c2e4c2e4c2e4&amp;width=100%25&amp;height=100%25&amp;lang=ru_RU&amp;scroll=true&amp;center=54.636737,39.742197&amp;zoom=16&amp;placemark=54.636737,39.742197&amp;hint=iStore"></script>
                </div>

                <form class="contact-form">
                    <div class="form-group">
                        <input type="text" placeholder="Ваше имя" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Тема сообщения" required>
                    </div>
                    <div class="form-group">
                        <textarea placeholder="Ваше сообщение" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Отправить сообщение</button>
                </form>
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
                Ivakov & Klimkin © 2024 все права защищены || Разработано: Андрей & Игорь
            </div>
        </div>
    </footer>

    <script src="../js/modal-window.js"></script>
    <script src="../js/cart.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
