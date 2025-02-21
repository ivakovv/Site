<?php
session_start();

// Сохраняем информацию о том, был ли пользователь админом
$wasAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';

// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Удаляем куки авторизации, если они есть
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}
if (isset($_COOKIE['user_token'])) {
    setcookie('user_token', '', time() - 3600, '/');
}

// Проверяем, был ли запрос AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Если это AJAX запрос, отправляем JSON ответ
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    // Если это обычный запрос (например, из админ-панели)
    if ($wasAdmin) {
        // Если пользователь был админом, перенаправляем на главную
        header('Location: init.php');
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]); 
    }
}
