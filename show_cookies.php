<?php
header('Content-Type: text/plain');

echo "=== Все куки ===\n\n";

if (empty($_COOKIE)) {
    echo "Куки отсутствуют\n";
} else {
    foreach ($_COOKIE as $name => $value) {
        echo "Имя: " . $name . "\n";
        echo "Значение: " . $value . "\n";
        echo "---------------\n";
    }
}

echo "\n=== Детали сессии ===\n\n";
session_start();
if (empty($_SESSION)) {
    echo "Сессия пуста\n";
} else {
    echo "ID сессии: " . session_id() . "\n";
    echo "Данные сессии:\n";
    print_r($_SESSION);
}