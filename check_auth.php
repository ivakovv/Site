<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once DIR . '/db_connection.php';

header('Content-Type: application/json');

// Проверяем наличие сессии
if (isset($_SESSION['user'])) {
    echo json_encode([
        'authenticated' => true,
        'user' => $_SESSION['user']
    ]);
    exit;
}

// Проверяем наличие куки
if (isset($_COOKIE['user_email']) && isset($_COOKIE['user_token'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT user_id, first_name, last_name, email, user_role, remember_token 
            FROM users 
            WHERE email = ?
        ");
        
        $stmt->execute([$_COOKIE['user_email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && hash_equals($user['remember_token'], $_COOKIE['user_token'])) {
            // Обновляем сессию
            $_SESSION['user'] = [
                'id' => $user['user_id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'role' => $user['user_role']
            ];
            
            echo json_encode([
                'authenticated' => true,
                'user' => $_SESSION['user']
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            'authenticated' => false,
            'error' => 'Ошибка проверки авторизации'
        ]);
        exit;
    }
}

echo json_encode(['authenticated' => false]);
?>