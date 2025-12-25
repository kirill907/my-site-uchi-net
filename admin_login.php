<?php
require_once 'includes/init.php';

// Если администратор уже авторизован, перенаправляем в админ-панель
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header('Location: admin.php');
    exit();
}

$error = '';
$username = '';

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Проверяем логин и пароль
    if ($username === 'Admin' && $password === 'Admin') {
        // Устанавливаем сессию администратора
        $_SESSION['user_id'] = 0; // Специальный ID для админа
        $_SESSION['username'] = 'Admin';
        $_SESSION['user_name'] = 'Администратор';
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_login_success'] = true;
        
        header('Location: admin.php');
        exit();
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для администратора - Портал «Учиться.net»</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
        }
        
        .admin-login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #9b59b6;
        }
        
        .admin-login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .admin-login-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .admin-login-header p {
            color: #666;
        }
        
        .admin-badge {
            display: inline-block;
            background: #9b59b6;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #9b59b6;
        }
        
        .btn-admin {
            width: 100%;
            padding: 14px;
            background: #9b59b6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-admin:hover {
            background: #8e44ad;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #dc3545;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #666;
            text-decoration: none;
        }
        
        .back-link a:hover {
            color: #9b59b6;
            text-decoration: underline;
        }
        
        .password-hint {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <h1><i class="fas fa-shield-alt"></i> Панель администратора</h1>
                <p>Войдите для управления заявками</p>
                <span class="admin-badge">Доступ только для администраторов</span>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-shield"></i> Логин администратора
                    </label>
                    <input type="text" 
                           name="username" 
                           class="form-control" 
                           placeholder="Введите логин"
                           value="<?php echo htmlspecialchars($username); ?>"
                           required
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Пароль
                    </label>
                    <input type="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Введите пароль"
                           required>
                    <div class="password-hint">
                        По умолчанию: логин и пароль - "Admin"
                    </div>
                </div>
                
                <button type="submit" class="btn-admin">
                    <i class="fas fa-sign-in-alt"></i> Войти как администратор
                </button>
            </form>
            
            <div class="back-link">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Вернуться на главную</a>
            </div>
        </div>
    </div>
</body>
</html>