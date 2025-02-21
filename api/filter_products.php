<?php
session_start();
require_once '../db_connection.php';

header('Content-Type: application/json');

// Добавляем базовый URL
$baseUrl = '/andrey2/site';
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

// В начале файла добавим
error_log('Document Root: ' . $_SERVER['DOCUMENT_ROOT']);

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Проверка входных данных
    if (!isset($data['category'])) {
        throw new Exception('Category is required');
    }
    
    $category = $data['category']; 
    $sortBy = $data['sortBy'] ?? '';
    $minPrice = isset($data['minPrice']) && is_numeric($data['minPrice']) ? (float)$data['minPrice'] : null;
    $maxPrice = isset($data['maxPrice']) && is_numeric($data['maxPrice']) ? (float)$data['maxPrice'] : null;

    $sql = "SELECT product_id, name_product, price, image_path, stock_quantity 
            FROM products 
            WHERE category = :category";
    $params = [':category' => $category];

    if ($minPrice !== null) {
        $sql .= " AND price >= :min_price";
        $params[':min_price'] = $minPrice;
    }
    if ($maxPrice !== null) {
        $sql .= " AND price <= :max_price";
        $params[':max_price'] = $maxPrice;
    }

    switch ($sortBy) {
        case 'price-asc':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price-desc':
            $sql .= " ORDER BY price DESC";
            break;
        case 'name':
            $sql .= " ORDER BY name_product ASC";
            break;
        case 'new':
            $sql .= " ORDER BY created_at DESC";
            break;
        default:
            $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // В начале файла добавим логирование запроса
    error_log('Debug - SQL Query: ' . $sql);

    // После выполнения запроса добавим логирование результатов
    error_log('Debug - Raw database results: ' . print_r($products, true));

    // Изменяем обработку путей к изображениям
    foreach ($products as &$product) {
        if (!empty($product['image_path'])) {
            $original_path = $product['image_path'];
            error_log("Original image path from DB: " . $original_path);
            
            // Получаем только имя файла
            $image_name = basename($original_path);
            
            // Формируем путь относительно текущей страницы
            $product['image_path'] = "../images/" . $image_name;
            
            error_log("Final image path: " . $product['image_path']);
            
            // Проверяем существование файла
            $physical_path = __DIR__ . '/../images/' . $image_name;
            error_log("Physical path exists: " . (file_exists($physical_path) ? 'Yes' : 'No'));
        }
    }

    // Добавим отладочную информацию
    error_log('Debug - Image paths: ' . print_r($products, true));
    echo json_encode([
        'success' => true,
        'products' => $products,
        'debug' => [
            'paths' => array_column($products, 'image_path')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
