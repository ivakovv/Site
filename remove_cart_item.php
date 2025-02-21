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

    $stmt = $pdo->prepare("
        DELETE FROM cart 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->execute([$userId, $productId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
} 