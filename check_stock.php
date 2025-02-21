<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'];
    
    foreach ($items as $item) {
        // Проверяем наличие товара
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode([
                'success' => false, 
                'message' => 'Товар не найден в базе данных'
            ]);
            exit;
        }
        
        if ($product['stock_quantity'] < $item['quantity']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Недостаточное количество товара на складе'
            ]);
            exit;
        }
    }
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Ошибка при проверке наличия товаров'
    ]);
}
?> 