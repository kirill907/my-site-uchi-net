<?php
session_start();
require_once 'db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Введите ваш email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email адрес';
    } else {
        // Проверяем, существует ли пользователь с таким email
        $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Здесь должна быть логика отправки email с ссылкой для сброса пароля
            // Для демонстрации просто показываем сообщение
            $message = "Инструкции по восстановлению пароля отправлены на $email";
        } else {
            $error = "Пользователь с email $email не найден";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - Портал «Учиться.net»</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-key"></i> Восстановление пароля</h1>
                <p>Введите email, указанный при регистрации</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="example@mail.ru"
                           required>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-paper-plane"></i> Отправить инструкции
                    </button>
                </div>
            </form>
            
            <div class="form-links">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Вернуться ко входу</a>
                <br>
                <a href="register.php">Еще не зарегистрированы? Создать аккаунт</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>