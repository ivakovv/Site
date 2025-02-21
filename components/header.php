<header>
    <div class="container">
        <div class="header-content">
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
    </div>
</header> 