<?php
require_once 'includes/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Настройки PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Установка кодировки
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET time_zone = '" . TIMEZONE . "'");
    
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    } else {
        // В продакшене показываем пользовательское сообщение
        error_log("Database Error: " . $e->getMessage());
        die("Временные технические неполадки. Пожалуйста, попробуйте позже.");
    }
}
?>