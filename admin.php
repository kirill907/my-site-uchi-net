<?php
session_start();
// РАСКОММЕНТИРУЙ СЛЕДУЮЩУЮ СТРОЧКУ:
$_SESSION['is_admin'] = true;
// ДАЛЬШЕ ТВОЙ КОД...


// ПРЯМАЯ ПРОВЕРКА В АДМИНКЕ - КОДИРОВАННЫЙ ПАРОЛЬ
$valid_login = false;

// Проверяем POST запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ПРОСТАЯ ПРОВЕРКА БЕЗ БД
    if ($login === 'Admin' && $password === 'Admin') {
        $valid_login = true;
        $_SESSION['admin_direct'] = true;
        $_SESSION['admin_name'] = 'Администратор';
        $_SESSION['is_admin'] = true;
        $_SESSION['user_id'] = 999;
    }
}

// Проверяем сессию
if (isset($_SESSION['admin_direct']) && $_SESSION['admin_direct'] === true) {
    $valid_login = true;
}

// Если не залогинен - показываем форму
if (!$valid_login) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход в панель администратора - Учиться.net</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            
            .login-container {
                width: 100%;
                max-width: 450px;
            }
            
            .login-box {
                background: white;
                border-radius: 20px;
                padding: 50px 40px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .login-box::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: linear-gradient(to right, #9b59b6, #8e44ad);
            }
            
            .admin-icon {
                font-size: 4rem;
                color: #9b59b6;
                margin-bottom: 20px;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            h1 {
                color: #2c3e50;
                margin-bottom: 10px;
                font-size: 2.2rem;
            }
            
            .subtitle {
                color: #666;
                margin-bottom: 40px;
                font-size: 1.1rem;
            }
            
            .admin-badge {
                display: inline-block;
                background: #9b59b6;
                color: white;
                padding: 8px 25px;
                border-radius: 25px;
                font-weight: 600;
                font-size: 0.9rem;
                margin-bottom: 30px;
                letter-spacing: 1px;
            }
            
            .form-group {
                margin-bottom: 25px;
                text-align: left;
            }
            
            .form-label {
                display: block;
                margin-bottom: 10px;
                font-weight: 600;
                color: #444;
                font-size: 1rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .form-input {
                width: 100%;
                padding: 16px 20px;
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                font-size: 1.1rem;
                transition: all 0.3s;
                background: #f8f9fa;
                font-family: inherit;
            }
            
            .form-input:focus {
                outline: none;
                border-color: #9b59b6;
                background: white;
                box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.2);
            }
            
            .btn-login {
                width: 100%;
                padding: 18px;
                background: linear-gradient(to right, #9b59b6, #8e44ad);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1.2rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s;
                margin-top: 10px;
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            
            .btn-login:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px rgba(155, 89, 182, 0.4);
            }
            
            .btn-login:active {
                transform: translateY(-1px);
            }
            
            .error-message {
                background: #ffeaea;
                color: #d63031;
                padding: 18px;
                border-radius: 12px;
                margin-bottom: 30px;
                border-left: 5px solid #d63031;
                text-align: left;
                display: flex;
                align-items: center;
                gap: 15px;
                animation: shake 0.5s;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            
            .credentials-box {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 15px;
                margin-top: 40px;
                border: 2px dashed #9b59b6;
                text-align: left;
            }
            
            .credentials-box h3 {
                color: #2c3e50;
                margin-bottom: 15px;
                font-size: 1.3rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .credential-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid #eee;
            }
            
            .credential-item:last-child {
                border-bottom: none;
            }
            
            .credential-label {
                color: #666;
                font-weight: 500;
            }
            
            .credential-value {
                color: #2c3e50;
                font-weight: 700;
                font-size: 1.1rem;
                background: white;
                padding: 8px 16px;
                border-radius: 8px;
                border: 2px solid #9b59b6;
            }
            
            .back-link {
                margin-top: 30px;
                padding-top: 25px;
                border-top: 1px solid #eee;
            }
            
            .back-link a {
                color: #9b59b6;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.1rem;
                display: inline-flex;
                align-items: center;
                gap: 12px;
                transition: color 0.3s;
            }
            
            .back-link a:hover {
                color: #8e44ad;
                text-decoration: underline;
            }
            
            @media (max-width: 480px) {
                .login-box {
                    padding: 40px 25px;
                }
                
                h1 {
                    font-size: 1.8rem;
                }
                
                .admin-icon {
                    font-size: 3.5rem;
                }
                
                .form-input, .btn-login {
                    font-size: 1rem;
                    padding: 14px 18px;
                }
            }
            
            .password-toggle {
                position: relative;
            }
            
            .toggle-btn {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #666;
                cursor: pointer;
                font-size: 1.2rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-box">
                <div class="admin-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                
                <h1>Панель администратора</h1>
                <p class="subtitle">Управление заявками на обучение</p>
                
                <span class="admin-badge">Только для администраторов</span>
                
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Ошибка входа!</strong><br>
                            Неверный логин или пароль. Проверьте вводимые данные.
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="adminLoginForm">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-shield"></i> Логин администратора
                        </label>
                        <input type="text" 
                               name="login" 
                               class="form-input" 
                               placeholder="Введите логин"
                               value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                               required
                               autofocus>
                    </div>
                    
                    <div class="form-group password-toggle">
                        <label class="form-label">
                            <i class="fas fa-key"></i> Пароль
                        </label>
                        <input type="password" 
                               name="password" 
                               class="form-input" 
                               id="passwordInput"
                               placeholder="Введите пароль"
                               required>
                        <button type="button" class="toggle-btn" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Войти в панель управления
                    </button>
                </form>
                
                <div class="credentials-box">
                    <h3><i class="fas fa-info-circle"></i> Данные для входа:</h3>
                    <div class="credential-item">
                        <span class="credential-label">Логин:</span>
                        <span class="credential-value">Admin</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Пароль:</span>
                        <span class="credential-value">Admin</span>
                    </div>
                </div>
                
                <div class="back-link">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Вернуться на главную страницу
                    </a>
                </div>
            </div>
        </div>
        
        <script>
            // Показать/скрыть пароль
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('passwordInput');
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
            
            // Автофокус на поле логина
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('input[name="login"]').focus();
                
                // Добавляем красивый эффект при вводе
                document.querySelectorAll('.form-input').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value.length > 0) {
                            this.style.borderColor = '#9b59b6';
                        } else {
                            this.style.borderColor = '#e0e0e0';
                        }
                    });
                });
                
                // Добавляем анимацию при отправке формы
                document.getElementById('adminLoginForm').addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-login');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Вход...';
                    btn.disabled = true;
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// ====================== ДАЛЬШЕ ОСНОВНОЙ КОД АДМИН-ПАНЕЛИ ======================
// Подключаем БД
require_once 'db_connect.php';

// Обработка изменения статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['new_status'])) {
    $application_id = (int)$_POST['application_id'];
    $new_status = trim($_POST['new_status']);
    
    // Проверяем, что статус корректен
    $allowed_statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $application_id]);
        $_SESSION['admin_message'] = 'Статус заявки успешно изменен';
        header('Location: admin.php');
        exit();
    }
}

// Обработка комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_application_id']) && isset($_POST['admin_comment'])) {
    $application_id = (int)$_POST['comment_application_id'];
    $admin_comment = trim($_POST['admin_comment']);
    
    $stmt = $pdo->prepare("UPDATE applications SET admin_comment = ? WHERE id = ?");
    $stmt->execute([$admin_comment, $application_id]);
    $_SESSION['admin_message'] = 'Комментарий успешно добавлен';
    header('Location: admin.php');
    exit();
}

// Получаем все заявки
$stmt = $pdo->query("
    SELECT a.*, u.full_name, u.email, u.phone 
    FROM applications a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC
");
$applications = $stmt->fetchAll();

// Статистика
$stats = [
    'total' => count($applications),
    'new' => 0,
    'in_progress' => 0,
    'completed' => 0
];

foreach ($applications as $app) {
    switch ($app['status']) {
        case 'Новая': $stats['new']++; break;
        case 'Идет обучение': $stats['in_progress']++; break;
        case 'Обучение завершено': $stats['completed']++; break;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Учиться.net</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Сайдбар */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #8e44ad 0%, #9b59b6 100%);
            color: white;
            padding: 30px 0;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            position: fixed;
            height: 100%;
        }
        
        .logo {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }
        
        .logo h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .logo i {
            color: #f1c40f;
        }
        
        .admin-info {
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }
        
        .admin-avatar {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 15px;
            border: 3px solid rgba(255,255,255,0.2);
        }
        
        .admin-name {
            text-align: center;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .admin-role {
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.8;
            background: rgba(255,255,255,0.1);
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            margin: 0 auto;
        }
        
        .nav-menu {
            padding: 0 20px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .nav-item:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .nav-item.active {
            background: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Основной контент */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-logout {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 2px solid rgba(231, 76, 60, 0.3);
        }
        
        .btn-logout:hover {
            background: #e74c3c;
            color: white;
        }
        
        /* Статистика */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .total .stat-icon { background: #3498db; color: white; }
        .new .stat-icon { background: #f39c12; color: white; }
        .in-progress .stat-icon { background: #9b59b6; color: white; }
        .completed .stat-icon { background: #2ecc71; color: white; }
        
        .stat-content h3 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-content p {
            color: #666;
            font-size: 0.95rem;
        }
        
        /* Таблица заявок */
        .applications-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-controls {
            display: flex;
            gap: 15px;
        }
        
        .search-input, .filter-select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            min-width: 200px;
        }
        
        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .applications-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .applications-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .applications-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .applications-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 120px;
        }
        
        .status-new { background: #fef9e7; color: #f39c12; border: 1px solid #f39c12; }
        .status-in-progress { background: #ebf5fb; color: #3498db; border: 1px solid #3498db; }
        .status-completed { background: #eafaf1; color: #27ae60; border: 1px solid #27ae60; }
        
        .user-info strong {
            display: block;
            margin-bottom: 5px;
        }
        
        .user-info small {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Формы */
        .status-form {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .status-select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            font-size: 0.9rem;
            min-width: 150px;
        }
        
        .btn-small {
            padding: 8px 15px;
            font-size: 0.85rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .comment-form {
            margin-top: 10px;
        }
        
        .comment-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }
        
        .comment-textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .admin-comment {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #3498db;
            font-size: 0.9rem;
        }
        
        .admin-comment strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }
        
        /* Сообщения */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.5s;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .message-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        /* Адаптивность */
        @media (max-width: 1200px) {
            .applications-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding: 20px 0;
            }
            
            .logo h2 span, .admin-name, .admin-role, .nav-item span {
                display: none;
            }
            
            .logo {
                padding: 0 10px 20px;
            }
            
            .admin-info, .nav-menu {
                padding: 0 10px;
            }
            
            .admin-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            
            .nav-item {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .table-controls {
                width: 100%;
                flex-direction: column;
            }
            
            .search-input, .filter-select {
                width: 100%;
            }
            
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .header-actions {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .status-form {
                flex-direction: column;
            }
        }
        
        /* Пустое состояние */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Сайдбар -->
        <aside class="sidebar">
            <div class="logo">
                <h2><i class="fas fa-shield-alt"></i> <span>Админ-панель</span></h2>
            </div>
            
            <div class="admin-info">
                <div class="admin-avatar">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="admin-name"><?php echo $_SESSION['admin_name'] ?? 'Администратор'; ?></div>
                <div class="admin-role">Супер-админ</div>
            </div>
            
            <nav class="nav-menu">
                <a href="admin.php" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Панель управления</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Пользователи</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-book"></i>
                    <span>Курсы</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Аналитика</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Настройки</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выйти</span>
                </a>
            </nav>
        </aside>
        
        <!-- Основной контент -->
        <main class="main-content">
            <!-- Шапка -->
            <div class="header">
                <h1><i class="fas fa-tachometer-alt"></i> Панель управления заявками</h1>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> На сайт
                    </a>
                    <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Выйти
                    </a>
                </div>
            </div>
            
            <!-- Сообщения -->
            <?php if (isset($_SESSION['admin_message'])): ?>
                <div class="message message-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['admin_message']; ?>
                    <?php unset($_SESSION['admin_message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Статистика -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Всего заявок</p>
                    </div>
                </div>
                
                <div class="stat-card new">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['new']; ?></h3>
                        <p>Новых</p>
                    </div>
                </div>
                
                <div class="stat-card in-progress">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['in_progress']; ?></h3>
                        <p>Идет обучение</p>
                    </div>
                </div>
                
                <div class="stat-card completed">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['completed']; ?></h3>
                        <p>Завершено</p>
                    </div>
                </div>
            </div>
            
            <!-- Таблица заявок -->
            <div class="applications-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Список заявок</h2>
                    <div class="table-controls">
                        <input type="text" id="searchInput" class="search-input" placeholder="Поиск по ФИО или курсу...">
                        <select id="statusFilter" class="filter-select">
                            <option value="">Все статусы</option>
                            <option value="Новая">Новые</option>
                            <option value="Идет обучение">Идет обучение</option>
                            <option value="Обучение завершено">Завершено</option>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($applications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-excel"></i>
                        <h3>Заявок нет</h3>
                        <p>Пользователи еще не подали ни одной заявки.</p>
                    </div>
                <?php else: ?>
                    <table class="applications-table" id="applicationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Дата подачи</th>
                                <th>Пользователь</th>
                                <th>Курс</th>
                                <th>Дата начала</th>
                                <th>Оплата</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <?php
                                $status_class = 'status-' . str_replace(' ', '-', strtolower($app['status']));
                                ?>
                                <tr class="application-row" data-status="<?php echo htmlspecialchars($app['status']); ?>">
                                    <td><strong>#<?php echo $app['id']; ?></strong></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?></td>
                                    <td class="user-info">
                                        <strong><?php echo htmlspecialchars($app['full_name'] ?? 'Неизвестно'); ?></strong>
                                        <small><?php echo htmlspecialchars($app['email'] ?? ''); ?></small><br>
                                        <small><?php echo htmlspecialchars($app['phone'] ?? ''); ?></small>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($app['course_name']); ?></strong></td>
                                    <td><?php echo date('d.m.Y', strtotime($app['desired_start_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($app['payment_method']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Форма изменения статуса -->
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                            <select name="new_status" class="status-select" required>
                                                <option value="Новая" <?php echo $app['status'] === 'Новая' ? 'selected' : ''; ?>>Новая</option>
                                                <option value="Идет обучение" <?php echo $app['status'] === 'Идет обучение' ? 'selected' : ''; ?>>Идет обучение</option>
                                                <option value="Обучение завершено" <?php echo $app['status'] === 'Обучение завершено' ? 'selected' : ''; ?>>Обучение завершено</option>
                                            </select>
                                            <button type="submit" class="btn-small btn-primary" onclick="return confirm('Изменить статус заявки #<?php echo $app['id']; ?>?')">
                                                <i class="fas fa-sync-alt"></i> Изменить
                                            </button>
                                        </form>
                                        
                                        <!-- Комментарий -->
                                        <div class="comment-form">
                                            <?php if (!empty($app['admin_comment'])): ?>
                                                <div class="admin-comment">
                                                    <strong><i class="fas fa-comment"></i> Комментарий:</strong>
                                                    <?php echo htmlspecialchars($app['admin_comment']); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <form method="POST">
                                                <input type="hidden" name="comment_application_id" value="<?php echo $app['id']; ?>">
                                                <textarea name="admin_comment" class="comment-textarea" 
                                                          placeholder="Введите комментарий..." 
                                                          rows="2"><?php echo isset($app['admin_comment']) ? htmlspecialchars($app['admin_comment']) : ''; ?></textarea>
                                                <button type="submit" class="btn-small btn-primary" style="margin-top: 5px;">
                                                    <i class="fas fa-save"></i> Сохранить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Фильтрация и поиск
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const applicationRows = document.querySelectorAll('.application-row');
            
            function filterApplications() {
                const searchTerm = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                
                applicationRows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowText = row.textContent.toLowerCase();
                    
                    let matchesStatus = !status || rowStatus === status;
                    let matchesSearch = !searchTerm || rowText.includes(searchTerm);
                    
                    if (matchesStatus && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            searchInput.addEventListener('input', filterApplications);
            statusFilter.addEventListener('change', filterApplications);
            
            // Автофокус на поиск
            searchInput.focus();
        });
    </script>
</body>
</html>