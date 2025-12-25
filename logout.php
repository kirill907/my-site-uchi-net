<?php
session_start();

// Сохраняем имя пользователя для сообщения
$user_name = $_SESSION['user_name'] ?? '';

// Полностью уничтожаем сессию
$_SESSION = array();

// Если требуется уничтожить куки сессии
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем на главную с сообщением
$_SESSION['info_message'] = !empty($user_name) 
    ? "До свидания, $user_name! Вы успешно вышли из системы." 
    : "Вы успешно вышли из системы.";

header('Location: index.php');
exit();
?>