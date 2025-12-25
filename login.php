<?php
require_once 'includes/init.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['login_success'] = true;
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    } else {
        $error = 'Заполните все поля';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Учиться.net</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div style="max-width: 400px; margin: 50px auto; padding: 20px;">
        <h1 style="text-align: center;">Вход в систему</h1>
        
        <?php if(isset($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <input type="text" name="username" placeholder="Логин или Email" style="width: 100%; padding: 10px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <input type="password" name="password" placeholder="Пароль" style="width: 100%; padding: 10px;" required>
            </div>
            <button type="submit" style="width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px;">
                Войти
            </button>
            
            <div style="text-align: center; margin-top: 20px;">
                Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
            </div>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>