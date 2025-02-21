<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Необходима авторизация']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Обновляем данные пользователя, если они изменились
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            phone = ?,
            address = ?
        WHERE user_id = ?
    ");
    
    $stmt->execute([
        $data['phone'],
        $data['address'],
        $_SESSION['user']['id']
    ]);
    
    // Здесь код для сохранения заказа
    // ...

    echo json_encode(['success' => true, 'message' => 'Заказ успешно оформлен']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?> 