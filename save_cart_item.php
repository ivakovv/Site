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
    $userId = $_SESSION['user']['id'];
    $productId = $data['product_id'];
    $quantity = $data['quantity'] ?? 1;

    // Проверяем, есть ли уже такой товар в корзине
    $stmt = $pdo->prepare("
        SELECT cart_id, quantity 
        FROM cart 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingItem) {
        // Обновляем количество
        $stmt = $pdo->prepare("
            UPDATE cart 
            SET quantity = quantity + ?
            WHERE cart_id = ?
        ");
        $stmt->execute([$quantity, $existingItem['cart_id']]);
    } else {
        // Добавляем новый товар
        $stmt = $pdo->prepare("
            INSERT INTO cart (user_id, product_id, quantity)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $productId, $quantity]);
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
} 