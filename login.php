<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $remember = $data['remember'] ?? false;
    
    // Проверяем email и получаем данные пользователя
    $stmt = $pdo->prepare("
        SELECT user_id, first_name, last_name, email, password_user, user_role 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_user'])) {
        // Создаем сессию
        $_SESSION['user'] = [
            'id' => $user['user_id'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email'],
            'role' => $user['user_role']
        ];
        
        // Если пользователь выбрал "Запомнить меня"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('user_email', $email, time() + 30*24*60*60, '/');
            setcookie('user_token', $token, time() + 30*24*60*60, '/');
            
            // Сохраняем токен в базе данных
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
            $stmt->execute([$token, $user['user_id']]);
        }
        
        echo json_encode([
            'success' => true,
            'user' => [
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'role' => $user['user_role']
            ],
            'redirect' => $user['user_role'] === 'admin' ? 'admin_panel.php' : null
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Неверный email или пароль'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Ошибка при входе'
    ]);
} 