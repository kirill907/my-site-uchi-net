<?php
require_once 'includes/init.php';
require_once 'db_connect.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, full_name, phone, registration_date FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Пользователь не найден
    session_destroy();
    header('Location: login.php');
    exit();
}

// Получаем заявки пользователя
$stmt = $pdo->prepare("
    SELECT * 
    FROM applications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();

// Статистика заявок - используем правильные статусы
$stats = [
    'total' => count($applications),
    'new' => 0,
    'in_progress' => 0,
    'completed' => 0
];

foreach ($applications as $app) {
    switch ($app['status']) {
        case 'Новая':
            $stats['new']++;
            break;
        case 'Идет обучение':
            $stats['in_progress']++;
            break;
        case 'Обучение завершено':
            $stats['completed']++;
            break;
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="personal-container">
    <!-- Заголовок -->
    <div class="personal-header">
        <h1><i class="fas fa-user-circle"></i> Личный кабинет</h1>
        <p>Добро пожаловать, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
    </div>

    <!-- Сообщение об успешной отправке заявки -->
    <?php if(isset($_SESSION['application_success'])): ?>
        <div class="notification-banner success">
            <i class="fas fa-check-circle"></i>
            Заявка успешно отправлена и направлена на рассмотрение администратору!
            <?php unset($_SESSION['application_success']); ?>
        </div>
    <?php endif; ?>

    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Всего заявок</p>
            </div>
        </div>
        
        <div class="stat-card new">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['new']; ?></h3>
                <p>Новых</p>
            </div>
        </div>
        
        <div class="stat-card processing">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['in_progress']; ?></h3>
                <p>Идет обучение</p>
            </div>
        </div>
        
        <div class="stat-card approved">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['completed']; ?></h3>
                <p>Обучение завершено</p>
            </div>
        </div>
    </div>

    <!-- Основная информация двумя колонками -->
    <div class="personal-content">
        <div class="info-section">
            <div class="info-card">
                <h2><i class="fas fa-user"></i> Личная информация</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">ФИО:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Логин:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Телефон:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Дата регистрации:</span>
                        <span class="info-value"><?php echo date('d.m.Y', strtotime($user['registration_date'])); ?></span>
                    </div>
                </div>
                <div class="info-actions">
                    <a href="#" class="btn btn-outline"><i class="fas fa-edit"></i> Редактировать данные</a>
                    <a href="#" class="btn btn-outline"><i class="fas fa-key"></i> Сменить пароль</a>
                </div>
            </div>
        </div>

        <div class="applications-section">
            <div class="applications-card">
                <div class="applications-header">
                    <h2><i class="fas fa-file-alt"></i> Мои заявки</h2>
                    <a href="create_application.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Новая заявка
                    </a>
                </div>
                
                <?php if (empty($applications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-excel"></i>
                        <h3>Заявок пока нет</h3>
                        <p>У вас еще нет поданных заявок на обучение.</p>
                        <a href="create_application.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Подать первую заявку
                        </a>
                    </div>
                <?php else: ?>
                    <div class="applications-list">
                        <?php foreach ($applications as $application): ?>
                            <?php
                            // Формируем класс статуса
                            $status_class = str_replace(' ', '-', strtolower($application['status']));
                            ?>
                            <div class="application-item status-<?php echo $status_class; ?>">
                                <div class="application-header">
                                    <h4><?php echo htmlspecialchars($application['course_name']); ?></h4>
                                    <span class="application-status status-badge status-<?php echo $status_class; ?>">
                                        <?php 
                                        $status_names = [
                                            'Новая' => 'Новая',
                                            'Идет обучение' => 'Идет обучение',
                                            'Обучение завершено' => 'Обучение завершено'
                                        ];
                                        echo $status_names[$application['status']];
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="application-details">
                                    <div class="detail">
                                        <span class="detail-label">Желаемая дата начала:</span>
                                        <span class="detail-value"><?php echo date('d.m.Y', strtotime($application['desired_start_date'])); ?></span>
                                    </div>
                                    <div class="detail">
                                        <span class="detail-label">Способ оплаты:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($application['payment_method']); ?></span>
                                    </div>
                                    <div class="detail">
                                        <span class="detail-label">Дата подачи:</span>
                                        <span class="detail-value"><?php echo date('d.m.Y H:i', strtotime($application['created_at'])); ?></span>
                                    </div>
                                    <div class="detail">
                                        <span class="detail-label">ID заявки:</span>
                                        <span class="detail-value">#<?php echo $application['id']; ?></span>
                                    </div>
                                </div>
                                
                                <?php if (!empty($application['admin_comment'])): ?>
                                    <div class="application-comment">
                                        <strong><i class="fas fa-comment"></i> Комментарий администратора:</strong>
                                        <p><?php echo htmlspecialchars($application['admin_comment']); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="application-actions">
                                    <?php if ($application['status'] === 'Новая'): ?>
                                        <a href="#" class="btn btn-outline btn-small">Редактировать</a>
                                        <a href="#" class="btn btn-danger btn-small">Отозвать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="quick-actions">
        <h2><i class="fas fa-bolt"></i> Быстрые действия</h2>
        <div class="actions-grid">
            <a href="create_application.php" class="action-card">
                <div class="action-icon primary">
                    <i class="fas fa-file-plus"></i>
                </div>
                <h3>Подать заявку</h3>
                <p>Записаться на новый курс</p>
            </a>
            
            <a href="index.php#courses" class="action-card">
                <div class="action-icon secondary">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Каталог курсов</h3>
                <p>Посмотреть все доступные курсы</p>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon success">
                    <i class="fas fa-file-download"></i>
                </div>
                <h3>Мои документы</h3>
                <p>Скачать сертификаты и договоры</p>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon warning">
                    <i class="fas fa-cog"></i>
                </div>
                <h3>Настройки</h3>
                <p>Изменить параметры аккаунта</p>
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    /* Основные стили личного кабинета */
    .personal-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .personal-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .personal-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 2.5rem;
    }
    
    .personal-header p {
        color: #666;
        font-size: 1.2rem;
    }
    
    /* Уведомления */
    .notification-banner {
        padding: 15px 20px;
        margin: 20px 0;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .notification-banner.success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .notification-banner i {
        font-size: 1.2rem;
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
        border-radius: 10px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    
    .total .stat-icon { background: #3498db; color: white; }
    .new .stat-icon { background: #f39c12; color: white; }
    .processing .stat-icon { background: #9b59b6; color: white; }
    .approved .stat-icon { background: #2ecc71; color: white; }
    
    .stat-content h3 {
        font-size: 2rem;
        margin: 0;
        color: #2c3e50;
    }
    
    .stat-content p {
        margin: 5px 0 0;
        color: #666;
        font-weight: 500;
    }
    
    /* Основной контент (две колонки) */
    .personal-content {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    @media (max-width: 1100px) {
        .personal-content {
            grid-template-columns: 1fr;
        }
    }
    
    .info-card, .applications-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .info-card h2, .applications-card h2 {
        color: #2c3e50;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-grid {
        display: grid;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .info-label {
        font-weight: 500;
        color: #555;
    }
    
    .info-value {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .info-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    /* Заявки */
    .applications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #666;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        margin-bottom: 10px;
        color: #2c3e50;
    }
    
    .applications-list {
        display: grid;
        gap: 20px;
    }
    
    .application-item {
        border: 2px solid #eee;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s;
    }
    
    .application-item:hover {
        border-color: #3498db;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
    }
    
    .application-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .application-header h4 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.2rem;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-новая .status-badge { background: #f39c12; color: white; }
    .status-идет-обучение .status-badge { background: #3498db; color: white; }
    .status-обучение-завершено .status-badge { background: #2ecc71; color: white; }
    
    .application-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .detail {
        display: flex;
        flex-direction: column;
    }
    
    .detail-label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 3px;
    }
    
    .detail-value {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .application-comment {
        background: #f8f9fa;
        border-left: 4px solid #3498db;
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
    }
    
    .application-comment strong {
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    
    .application-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-danger {
        background: #e74c3c;
        color: white;
        border: none;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    /* Быстрые действия */
    .quick-actions {
        margin-top: 40px;
    }
    
    .quick-actions h2 {
        color: #2c3e50;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .action-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        border-color: #3498db;
        box-shadow: 0 5px 20px rgba(52, 152, 219, 0.15);
    }
    
    .action-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 15px;
        color: white;
    }
    
    .action-icon.primary { background: #3498db; }
    .action-icon.secondary { background: #9b59b6; }
    .action-icon.success { background: #2ecc71; }
    .action-icon.warning { background: #f39c12; }
    
    .action-card h3 {
        margin: 0 0 10px;
        color: #2c3e50;
    }
    
    .action-card p {
        margin: 0;
        color: #666;
        font-size: 0.95rem;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .application-details {
            grid-template-columns: 1fr;
        }
        
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .personal-header h1 {
            font-size: 2rem;
        }
        
        .applications-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .applications-header .btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .application-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .info-actions {
            flex-direction: column;
        }
        
        .info-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    // Анимации для личного кабинета
    document.addEventListener('DOMContentLoaded', function() {
        // Анимация появления элементов
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        // Анимируем карточки
        document.querySelectorAll('.stat-card, .info-card, .applications-card, .action-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });
        
        // Подтверждение отзыва заявки
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const applicationId = this.closest('.application-item').querySelector('.detail-value').textContent;
                if (confirm('Вы уверены, что хотите отозвать заявку ' + applicationId + '? Это действие нельзя отменить.')) {
                    // Здесь будет AJAX запрос для отзыва заявки
                    alert('Заявка отозвана');
                    this.closest('.application-item').remove();
                }
            });
        });
        
        // Автоматическое скрытие уведомлений через 5 секунд
        const notificationBanners = document.querySelectorAll('.notification-banner');
        notificationBanners.forEach(banner => {
            setTimeout(() => {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (banner.parentNode) {
                        banner.remove();
                    }
                }, 300);
            }, 5000);
        });
        
        // Фильтрация заявок по статусу
        const statusFilters = document.createElement('div');
        statusFilters.className = 'status-filters';
        statusFilters.innerHTML = `
            <button class="filter-btn active" data-status="all">Все</button>
            <button class="filter-btn" data-status="Новая">Новые</button>
            <button class="filter-btn" data-status="Идет обучение">Идет обучение</button>
            <button class="filter-btn" data-status="Обучение завершено">Завершено</button>
        `;
        
        const applicationsHeader = document.querySelector('.applications-header');
        if (applicationsHeader && document.querySelector('.applications-list')) {
            // Добавляем фильтры после кнопки
            applicationsHeader.parentNode.insertBefore(statusFilters, applicationsHeader.nextSibling);
            
            // Добавляем стили для фильтров
            const style = document.createElement('style');
            style.textContent = `
                .status-filters {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                    margin-bottom: 20px;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 10px;
                }
                .filter-btn {
                    padding: 8px 16px;
                    border: 2px solid #ddd;
                    background: white;
                    border-radius: 20px;
                    cursor: pointer;
                    font-size: 0.9rem;
                    transition: all 0.3s;
                }
                .filter-btn.active {
                    background: #3498db;
                    color: white;
                    border-color: #3498db;
                }
                .filter-btn:hover:not(.active) {
                    border-color: #3498db;
                    color: #3498db;
                }
            `;
            document.head.appendChild(style);
            
            // Функционал фильтрации
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const status = this.dataset.status;
                    const items = document.querySelectorAll('.application-item');
                    
                    items.forEach(item => {
                        if (status === 'all') {
                            item.style.display = 'block';
                        } else {
                            const itemStatus = item.querySelector('.status-badge').textContent;
                            if (itemStatus === status) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        }
    });
</script>