<?php
session_start();
$baseUrl = '/andrey2/site';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - iStore</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/modal-window/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/about.css">
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
            <li><a href="about_info.php">О нас</a></li>
            <li><a href="../contacts/contacts.php">Контакты</a></li>
        </ul>
    </nav>

    <main>
        <div class="about-container">
            <div class="about-header">
                <h1>О компании iStore</h1>
                <p>Мы создаем пространство технологий и инноваций для наших клиентов</p>
            </div>

            <div class="about-grid">
                <div class="about-text">
                    <div class="about-section">
                        <h2>Наша история</h2>
                        <p>iStore начал свою работу в 2024 году как небольшой магазин техники Apple. За короткое время мы выросли в надежного поставщика качественной техники и аксессуаров, заслужив доверие тысяч клиентов.</p>
                    </div>

                    <div class="about-section">
                        <h2>Наша миссия</h2>
                        <p>Мы стремимся сделать современные технологии доступными для каждого, предоставляя качественные продукты и профессиональное обслуживание. Наша цель - помочь каждому клиенту найти идеальное решение для его потребностей.</p>
                    </div>

                    <div class="about-section">
                        <h2>Почему выбирают нас</h2>
                        <ul class="features-list">
                            <li>Только оригинальная продукция с официальной гарантией</li>
                            <li>Профессиональные консультации и поддержка</li>
                            <li>Быстрая доставка по всей России</li>
                            <li>Удобные способы оплаты</li>
                            <li>Программа лояльности для постоянных клиентов</li>
                        </ul>
                    </div>
                </div>

                <div class="about-image">
                    <img src="../images/about-store.jpg" alt="Наш магазин">
                </div>
            </div>

            <div class="team-section">
                <h2>Наша команда</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="../images/team/ivakov.jpg" alt="Член команды">
                        <h3>Иваков Андрей</h3>
                        <p>Основатель и CEO</p>
                        <div class="social-links">
                            <a href="https://vk.com/your_vk" target="_blank"><i class="fa fa-vk"></i></a>
                            <a href="https://t.me/your_telegram" target="_blank"><i class="fa fa-telegram"></i></a>
                            <a href="mailto:ivakov@istore.ru"><i class="fa fa-envelope"></i></a>
                        </div>
                    </div>

                    <div class="team-member">
                        <img src="../images/team/klimkin.jpg" alt="Член команды">
                        <h3>Климкин Игорь</h3>
                        <p>Технический директор</p>
                        <div class="social-links">
                            <a href="https://vk.com/your_vk" target="_blank"><i class="fa fa-vk"></i></a>
                            <a href="https://t.me/your_telegram" target="_blank"><i class="fa fa-telegram"></i></a>
                            <a href="mailto:klimkin@istore.ru"><i class="fa fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
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
