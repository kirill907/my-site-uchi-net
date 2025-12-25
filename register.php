<?php
// ПЕРВАЯ строка файла - НИЧЕГО перед этим!
require_once 'includes/init.php';
require_once 'db_connect.php';

$errors = [];
$username = $email = $full_name = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Валидация
    if (empty($username) || strlen($username) < 6 || !preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $errors['username'] = 'Логин: латинские буквы и цифры, мин. 6 символов';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) $errors['username'] = 'Логин занят';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors['email'] = 'Email уже зарегистрирован';
    }
    
    if (empty($password) || strlen($password) < 8) {
        $errors['password'] = 'Пароль: минимум 8 символов';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Пароли не совпадают';
    }
    
    if (empty($full_name) || !preg_match('/^[А-Яа-яЁё\s\-]+$/u', $full_name) || strlen($full_name) < 5) {
        $errors['full_name'] = 'ФИО: кириллица, пробелы, дефисы, мин. 5 символов';
    }
    
    if (empty($phone) || !preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Формат: 8(XXX)XXX-XX-XX';
    }
    
    // Регистрация
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $password_hash, $full_name, $phone])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['registration_success'] = true;
            
            header('Location: index.php');
            exit();
        } else {
            $errors['database'] = 'Ошибка регистрации';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Учиться.net</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-container { max-width: 500px; margin: 40px auto; padding: 20px; }
        .register-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
        .btn { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="register-container">
        <div class="register-card">
            <h1 style="text-align: center; margin-bottom: 20px;">Регистрация</h1>
            
            <?php if(isset($errors['database'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $errors['database']; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Логин (латиница и цифры, мин. 6)" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                    <?php if(isset($errors['username'])): ?>
                        <div class="error"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" 
                           value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if(isset($errors['email'])): ?>
                        <div class="error"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Пароль (мин. 8 символов)" required>
                    <?php if(isset($errors['password'])): ?>
                        <div class="error"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Подтвердите пароль" required>
                    <?php if(isset($errors['confirm_password'])): ?>
                        <div class="error"><?php echo $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <input type="text" name="full_name" class="form-control" placeholder="ФИО (кириллица)" 
                           value="<?php echo htmlspecialchars($full_name); ?>" required>
                    <?php if(isset($errors['full_name'])): ?>
                        <div class="error"><?php echo $errors['full_name']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" class="form-control" placeholder="Телефон: 8(999)123-45-67" 
                           value="<?php echo htmlspecialchars($phone); ?>" required>
                    <?php if(isset($errors['phone'])): ?>
                        <div class="error"><?php echo $errors['phone']; ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Зарегистрироваться</button>
                
                <div style="text-align: center; margin-top: 20px;">
                    Уже есть аккаунт? <a href="login.php">Войти</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Маска для телефона
        document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '8(' + value.substring(1,4) + ')' + value.substring(4,7) + '-' + value.substring(7,9) + '-' + value.substring(9,11);
            }
            e.target.value = value.substring(0, 15);
        });
    </script>
</body>
</html>