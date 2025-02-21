<?php
session_start();
require_once 'check_admin.php';
require_once 'db_connection.php';
checkAdminAccess();

// Обработка загрузки изображения
function handleImageUpload($file) {
    // Проверяем, был ли файл загружен
    if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ["success" => false, "message" => "Файл не был загружен."];
    }

    $target_dir = "images/";
    // Создаем директорию, если она не существует
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Генерируем уникальное имя файла
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid() . '.' . $imageFileType;
    
    // Проверка типа файла
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        return ["success" => false, "message" => "Разрешены только JPG, JPEG, PNG & GIF фай��ы."];
    }
    
    // Проверка размера файла (5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "Файл слишком большой."];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "path" => $target_file];
    }
    
    return ["success" => false, "message" => "Ошибка загрузки файла."];
}

// Обработка добавления/редактирования товара
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                try {
                    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                        throw new Exception("Ошибка загрузки изображения");
                    }

                    $image_result = handleImageUpload($_FILES["image"]);
                    if (!$image_result["success"]) {
                        throw new Exception($image_result["message"]);
                    }

                    $stmt = $pdo->prepare("INSERT INTO products (category, name_product, price, stock_quantity, image_path, is_popular) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['category'],
                        $_POST['name'],
                        $_POST['price'],
                        $_POST['quantity'],
                        $image_result["path"],
                        $_POST['is_popular']
                    ]);

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;

                } catch (Exception $e) {
                    echo "<div class='error-message'>" . htmlspecialchars($e->getMessage()) . "</div>";
                }
                break;

            case 'edit_product':
                try {
                    $sql = "UPDATE products SET category = ?, name_product = ?, price = ?, stock_quantity = ?, is_popular = ?";
                    $params = [
                        $_POST['category'], 
                        $_POST['name'], 
                        $_POST['price'], 
                        $_POST['quantity'],
                        $_POST['is_popular']
                    ];
                    
                    // Если загружено новое изображение
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $image_result = handleImageUpload($_FILES["image"]);
                        if (!$image_result["success"]) {
                            throw new Exception($image_result["message"]);
                        }
                        $sql .= ", image_path = ?";
                        $params[] = $image_result["path"];
                    }
                    
                    $sql .= " WHERE product_id = ?";
                    $params[] = $_POST['product_id'];
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;

                } catch (Exception $e) {
                    echo "<div class='error-message'>" . htmlspecialchars($e->getMessage()) . "</div>";
                }
                break;

            case 'delete_product':
                $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->execute([$_POST['product_id']]);
                break;

            case 'add_user':
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users 
                    (first_name, last_name, email, phone, password_user, user_role, address) 
                    VALUES 
                    (?, ?, ?, ?, ?, ?, ?) 
                    RETURNING user_id
                ");
                
                try {
                    $stmt->execute([
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $password_hash,
                        $_POST['user_role'],
                        $_POST['address']
                    ]);
                    
                    // Можно получить ID нового пользователя, если нужно
                    $new_user_id = $stmt->fetchColumn();
                    
                } catch (PDOException $e) {
                    // Проверяем, является ли ошибка нарушением уникальности email
                    if ($e->getCode() == '23505' && strpos($e->getMessage(), 'users_email_key') !== false) {
                        echo "Пользователь с таким email уже существует";
                    } else {
                        // Другие ошибки
                        echo "Произошла ошибка при добавлении пользователя: " . $e->getMessage();
                    }
                    exit;
                }
                break;

            case 'edit_user':
                $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, user_role = ?, address = ?";
                $params = [
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['user_role'],
                    $_POST['address']
                ];

                if (!empty($_POST['password'])) {
                    $sql .= ", password_user = ?";
                    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $sql .= " WHERE user_id = ?";
                $params[] = $_POST['user_id'];

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                break;

            case 'delete_user':
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$_POST['user_id']]);
                break;

            case 'toggle_popular':
                try {
                    $stmt = $pdo->prepare("UPDATE products SET is_popular = ? WHERE product_id = ?");
                    $is_popular = (int)$_POST['is_popular']; // Преобразуем в целое число
                    $stmt->execute([$is_popular, $_POST['product_id']]);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } catch (Exception $e) {
                    echo "<div class='error-message'>" . htmlspecialchars($e->getMessage()) . "</div>";
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Панель администратора</h1>
            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="admin-tabs">
                <button class="tab-button active" data-tab="products">Товары</button>
                <button class="tab-button" data-tab="users">Пользователи</button>
            </div>

            <!-- Управление товарами -->
            <div class="tab-content active" id="products">
                <h2>Управление товарами</h2>
                <div class="product-controls">
                    <button class="add-button" onclick="showAddProductForm()">Добавить товар</button>
                    
                    <div class="product-filters">
                        <select id="category-filter" onchange="filterProducts()">
                            <option value="">Все категории</option>
                            <option value="phone">Телефоны</option>
                            <option value="headphones">Наушники</option>
                            <option value="tablet">Планшеты</option>
                            <option value="laptop">Ноутбуки</option>
                            <option value="watch">Часы</option>
                            <option value="accessories">Аксессуары</option>
                        </select>
                        
                        <select id="popular-filter" onchange="filterProducts()">
                            <option value="">Все товары</option>
                            <option value="1">Популярные</option>
                            <option value="0">Обычные</option>
                        </select>
                        
                        <input type="text" 
                               id="search-product" 
                               placeholder="Поиск по названию..." 
                               oninput="filterProducts()">
                    </div>
                </div>
                
                <div class="products-list">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
                    while ($product = $stmt->fetch()) {
                        echo "<div class='product-item' data-category='" . htmlspecialchars($product['category']) . "' data-popular='" . ($product['is_popular'] ? '1' : '0') . "'>";
                        if ($product['is_popular']) {
                            echo "<span class='is-popular'>Популярный товар</span>";
                        }
                        echo "<img src='" . htmlspecialchars($product['image_path']) . "' alt='Product'>";
                        echo "<div class='product-info'>";
                        echo "<h3>" . htmlspecialchars($product['name_product']) . "</h3>";
                        echo "<p>Категория: " . htmlspecialchars($product['category']) . "</p>";
                        echo "<p>Цена: " . htmlspecialchars($product['price']) . " ₽</p>";
                        echo "<p>Количество: " . htmlspecialchars($product['stock_quantity']) . "</p>";
                        echo "<div class='product-buttons'>";
                        echo "<div class='main-buttons'>";
                        echo "<button class='edit-btn' onclick='editProduct(" . json_encode($product) . ")'>Редактировать</button>";
                        echo "<button class='delete-btn' onclick='deleteProduct(" . $product['product_id'] . ")'>Удалить</button>";
                        echo "</div>";
                        echo "<button class='popular-btn " . ($product['is_popular'] ? 'active' : '') . "' 
                            onclick='togglePopular(" . $product['product_id'] . ", " . ($product['is_popular'] ? '0' : '1') . ")'>" 
                            . ($product['is_popular'] ? 'Убрать из популярных' : 'В популярное') . "</button>";
                        echo "</div>";
                        echo "</div></div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Управление пользователями -->
            <div class="tab-content" id="users">
                <h2>Управление пользователями</h2>
                <div class="user-controls">
                    <button class="add-button" onclick="showAddUserForm()">Добавить пользователя</button>
                    
                    <div class="user-filters">
                        <select id="role-filter">
                            <option value="">Все роли</option>
                            <option value="user">Пользователь</option>
                            <option value="admin">Администратор</option>
                        </select>
                        
                        <input type="text" 
                               id="search-user" 
                               placeholder="Поиск по имени или email...">
                    </div>
                </div>
                
                <div class="users-list">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
                    while ($user = $stmt->fetch()) {
                        echo "<div class='user-item' data-role='" . htmlspecialchars($user['user_role']) . "'>";
                        echo "<div class='user-info'>";
                        echo "<h3>" . htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']) . "</h3>";
                        echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
                        echo "<p>Телефон: " . htmlspecialchars($user['phone']) . "</p>";
                        echo "<p>Роль: " . htmlspecialchars($user['user_role']) . "</p>";
                        echo "<div class='user-buttons'>";
                        echo "<button onclick='editUser(" . json_encode($user) . ")'>Редактировать</button>";
                        echo "<button onclick='deleteUser(" . $user['user_id'] . ")'>Удалить</button>";
                        echo "</div>";
                        echo "</div></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Модальные окна для форм -->
    <div id="productModal" class="modal">
        <!-- Форма добавления/редактирования товара -->
    </div>

    <div id="userModal" class="modal">
        <!-- Форма добавления/редактирования пользователя -->
    </div>

    <script src="js/admin.js"></script>
</body>
</html> 