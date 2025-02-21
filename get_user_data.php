<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT first_name, last_name, middle_name, email, phone, address 
            FROM users 
            WHERE user_id = ?
        ");
        
        $stmt->execute([$_SESSION['user']['id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            // Формируем полное имя
            $fullName = trim($userData['last_name'] . ' ' . 
                           $userData['first_name'] . ' ' . 
                           $userData['middle_name']);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'name' => $fullName,
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'address' => $userData['address']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Пользователь не найден']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка базы данных']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
} 