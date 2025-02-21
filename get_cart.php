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
    $userId = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("
        SELECT 
            c.cart_id, 
            c.quantity, 
            p.product_id, 
            p.name_product, 
            p.price, 
            p.image_path
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?
        ORDER BY c.added_date DESC
    ");
    
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Проверяем и форматируем данные
    foreach ($cartItems as &$item) {
        $item['quantity'] = intval($item['quantity']);
        $item['price'] = floatval($item['price']);
        $item['product_id'] = intval($item['product_id']);
    }

    echo json_encode([
        'success' => true,
        'items' => $cartItems
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
} 