<?php
// ПЕРВАЯ СТРОКА ФАЙЛА
require_once 'includes/init.php';
require_once 'db_connect.php';
?>
<?php require_once 'includes/header.php'; ?>

<!-- Герой-секция -->
<section class="hero">
    <div class="hero-content">
        <h1>Дополнительное профессиональное образование онлайн</h1>
        <p class="hero-subtitle">Получите востребованную профессию или повысьте квалификацию с лучшими преподавателями. Запишитесь на курс за 5 минут.</p>
        <div class="hero-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create_application.php" class="btn btn-primary btn-large"><i class="fas fa-pencil-alt"></i> Подать заявку на курс</a>
                <a href="#courses" class="btn btn-secondary btn-large"><i class="fas fa-search"></i> Выбрать курс</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary btn-large"><i class="fas fa-user-plus"></i> Начать обучение</a>
                <a href="#courses" class="btn btn-secondary btn-large"><i class="fas fa-play-circle"></i> Смотреть курсы</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-image">
        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Онлайн обучение">
    </div>
</section>

<!-- Сообщения об успехе -->
<?php if(isset($_SESSION['registration_success'])): ?>
    <div class="notification-banner success">
        <i class="fas fa-check-circle"></i>
        Регистрация успешно завершена! Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        <?php unset($_SESSION['registration_success']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['login_success'])): ?>
    <div class="notification-banner success">
        <i class="fas fa-check-circle"></i>
        Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        <?php unset($_SESSION['login_success']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['info_message'])): ?>
    <div class="notification-banner info">
        <i class="fas fa-info-circle"></i>
        <?php echo htmlspecialchars($_SESSION['info_message']); ?>
        <?php unset($_SESSION['info_message']); ?>
    </div>
<?php endif; ?>

<!-- Преимущества -->
<section class="features">
    <h2>Почему выбирают «Учиться.net»</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-laptop-house"></i>
            </div>
            <h3>Учитесь из любого места</h3>
            <p>Все курсы доступны онлайн в удобное для вас время.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-file-certificate"></i>
            </div>
            <h3>Диплом установленного образца</h3>
            <p>По окончании курса вы получите документ о повышении квалификации.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Поддержка 24/7</h3>
            <p>Наши менеджеры всегда готовы помочь с техническими вопросами.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h3>Только практические навыки</h3>
            <p>Курсы разработаны вместе с экспертами из реального бизнеса.</p>
        </div>
    </div>
</section>

<!-- Популярные курсы -->
<section id="courses" class="courses">
    <h2>Популярные курсы</h2>
    <div class="courses-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM courses LIMIT 4");
            $courses = $stmt->fetchAll();
            
            foreach ($courses as $course):
        ?>
        <div class="course-card">
            <div class="course-header">
                <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                <span class="course-price"><?php echo number_format($course['base_price'], 0, '', ' '); ?> ₽</span>
            </div>
            <p class="course-description"><?php echo htmlspecialchars(mb_strimwidth($course['description'], 0, 120, '...')); ?></p>
            <div class="course-details">
                <span><i class="far fa-clock"></i> <?php echo $course['duration_hours']; ?> часов</span>
            </div>
            <div class="course-actions">
                <a href="#" class="btn btn-outline btn-small">Подробнее</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="create_application.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary btn-small">Записаться</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-small">Записаться</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php } catch (PDOException $e) { ?>
            <p class="error-message">Не удалось загрузить курсы. Попробуйте позже.</p>
        <?php } ?>
    </div>
    <div class="section-footer">
        <a href="#" class="btn btn-secondary">Смотреть все курсы <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- Как это работает -->
<section class="process">
    <h2>Как подать заявку на обучение</h2>
    <div class="process-steps">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Регистрация</h3>
            <p>Создайте аккаунт на портале, указав свои данные.</p>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <h3>Выбор курса</h3>
            <p>Изучите каталог и выберите подходящую программу.</p>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <h3>Заполнение заявки</h3>
            <p>Укажите желаемую дату начала и способ оплаты.</p>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <h3>Рассмотрение</h3>
            <p>Администратор проверит заявку и свяжется с вами.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>