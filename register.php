<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Разбиваем полное имя на части
    $fullName = explode(' ', $data['name']);
    $lastName = $fullName[0] ?? '';
    $firstName = $fullName[1] ?? '';
    $middleName = $fullName[2] ?? '';
    
    // Хешируем пароль
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Проверяем, существует ли уже пользователь с таким email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Пользователь с таким email уже существует']);
        exit;
    }
    
    // Подготавливаем и выполняем запрос на вставку
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, middle_name, email, phone, password_user, address, user_role)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'user')
    ");
    
    $stmt->execute([
        $firstName,
        $lastName,
        $middleName,
        $data['email'],
        $data['phone'],
        $hashedPassword,
        'Адрес не указан' // Временно, можно добавить поле в форму регистрации
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Регистрация успешно завершена']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
} 