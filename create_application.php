<?php
require_once 'includes/init.php';
require_once 'db_connect.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = false;

// Значения полей по умолчанию
$course_name = '';
$desired_start_date = '';
$payment_method = 'наличные';

// Если пришел параметр с названием курса (например, с главной страницы)
if (isset($_GET['course_name'])) {
    $course_name = trim($_GET['course_name']);
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $desired_start_date = trim($_POST['desired_start_date'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'наличные');
    
    // Валидация
    if (empty($course_name)) {
        $errors['course_name'] = 'Укажите наименование курса';
    } elseif (strlen($course_name) < 3) {
        $errors['course_name'] = 'Название курса должно содержать минимум 3 символа';
    }
    
    if (empty($desired_start_date)) {
        $errors['desired_start_date'] = 'Укажите желаемую дату начала';
    } else {
        // Проверяем формат даты
        $date = DateTime::createFromFormat('Y-m-d', $desired_start_date);
        if (!$date || $date->format('Y-m-d') !== $desired_start_date) {
            $errors['desired_start_date'] = 'Некорректный формат даты (требуется ДД.ММ.ГГГГ)';
        } elseif ($date < new DateTime('today')) {
            $errors['desired_start_date'] = 'Дата начала не может быть в прошлом';
        }
    }
    
    if (!in_array($payment_method, ['наличные', 'перевод по номеру телефона'])) {
        $errors['payment_method'] = 'Выберите способ оплаты из предложенных';
    }
    
    // Если ошибок нет - создаем заявку
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO applications (user_id, course_name, desired_start_date, payment_method, status) 
                VALUES (?, ?, ?, ?, 'новая')
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $course_name,
                $desired_start_date,
                $payment_method
            ]);
            
            // Успешное создание заявки
            $success = true;
            
            // Редирект на личный кабинет
            $_SESSION['application_success'] = true;
            header('Location: personal.php');
            exit();
            
        } catch (PDOException $e) {
            $errors['database'] = 'Ошибка при создании заявки. Попробуйте позже.';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="application-page">
    <div class="page-header">
        <h1><i class="fas fa-file-plus"></i> Формирование заявки на обучение</h1>
        <p>Заполните форму для подачи заявки на курс</p>
    </div>
    
    <?php if (isset($errors['database'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $errors['database']; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Заявка успешно создана и направлена администратору!
        </div>
    <?php else: ?>
        <div class="application-form-container">
            <form method="POST" action="" class="application-form" id="applicationForm">
                <!-- Наименование курса -->
                <div class="form-group">
                    <label for="course_name">
                        <i class="fas fa-book"></i> Наименование курса *
                    </label>
                    <input type="text" 
                           id="course_name" 
                           name="course_name" 
                           class="form-control <?php echo isset($errors['course_name']) ? 'error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($course_name); ?>"
                           placeholder="Введите название курса"
                           required>
                    <?php if (isset($errors['course_name'])): ?>
                        <div class="error-message"><?php echo $errors['course_name']; ?></div>
                    <?php endif; ?>
                    <div class="form-hint">
                        Пример: "Веб-разработчик с нуля", "Data Science: анализ данных"
                    </div>
                </div>
                
                <!-- Желаемая дата начала -->
                <div class="form-group">
                    <label for="desired_start_date">
                        <i class="fas fa-calendar-alt"></i> Желаемая дата начала обучения *
                    </label>
                    <input type="date" 
                           id="desired_start_date" 
                           name="desired_start_date" 
                           class="form-control <?php echo isset($errors['desired_start_date']) ? 'error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($desired_start_date); ?>"
                           min="<?php echo date('Y-m-d'); ?>"
                           required>
                    <?php if (isset($errors['desired_start_date'])): ?>
                        <div class="error-message"><?php echo $errors['desired_start_date']; ?></div>
                    <?php endif; ?>
                    <div class="form-hint">
                        Выберите дату, когда вы готовы приступить к обучению
                    </div>
                </div>
                
                <!-- Способ оплаты -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-money-bill-wave"></i> Способ оплаты *
                    </label>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="наличные" 
                                   <?php echo $payment_method === 'наличные' ? 'checked' : ''; ?> 
                                   required>
                            <div class="payment-method-card">
                                <i class="fas fa-money-bill"></i>
                                <span>Наличные</span>
                                <p class="payment-description">Оплата наличными при встрече с менеджером</p>
                            </div>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="перевод по номеру телефона" 
                                   <?php echo $payment_method === 'перевод по номеру телефона' ? 'checked' : ''; ?>>
                            <div class="payment-method-card">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Перевод по номеру телефона</span>
                                <p class="payment-description">Оплата через СБП на номер телефона</p>
                            </div>
                        </label>
                    </div>
                    <?php if (isset($errors['payment_method'])): ?>
                        <div class="error-message"><?php echo $errors['payment_method']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Кнопки -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="fas fa-paper-plane"></i> Отправить заявку
                    </button>
                    <a href="personal.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
                
                <div class="form-info">
                    <p><i class="fas fa-info-circle"></i> После отправки заявка будет направлена на рассмотрение администратору портала.</p>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    /* Стили для страницы создания заявки */
    .application-page {
        max-width: 700px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .page-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 2.2rem;
    }
    
    .page-header p {
        color: #666;
        font-size: 1.1rem;
    }
    
    /* Оповещения */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        text-align: center;
        font-weight: 500;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .alert i {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    /* Форма заявки */
    .application-form-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 30px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1rem;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .form-control.error {
        border-color: #e74c3c;
    }
    
    .error-message {
        color: #e74c3c;
        font-size: 0.9rem;
        margin-top: 8px;
        display: block;
    }
    
    .form-hint {
        font-size: 0.9rem;
        color: #666;
        margin-top: 8px;
        font-style: italic;
    }
    
    /* Способы оплаты */
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }
    
    @media (max-width: 768px) {
        .payment-methods {
            grid-template-columns: 1fr;
        }
    }
    
    .payment-method {
        display: block;
        cursor: pointer;
    }
    
    .payment-method input {
        display: none;
    }
    
    .payment-method-card {
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s;
        background: white;
        height: 100%;
    }
    
    .payment-method input:checked + .payment-method-card {
        border-color: #3498db;
        background: #f0f7ff;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
    }
    
    .payment-method-card i {
        font-size: 2rem;
        color: #3498db;
        margin-bottom: 15px;
        display: block;
    }
    
    .payment-method-card span {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
        display: block;
        margin-bottom: 8px;
    }
    
    .payment-description {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.4;
    }
    
    /* Кнопки */
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        font-size: 1rem;
        min-width: 150px;
    }
    
    .btn-primary {
        background-color: #3498db;
        color: white;
        flex: 1;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }
    
    .btn-outline {
        background-color: transparent;
        color: #3498db;
        border-color: #3498db;
    }
    
    .btn-outline:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }
    
    .form-info {
        margin-top: 25px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }
    
    .form-info p {
        margin: 0;
        color: #666;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    
    .form-info i {
        color: #3498db;
        margin-top: 2px;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .application-page {
            padding: 10px;
        }
        
        .application-form-container {
            padding: 20px;
        }
        
        .page-header h1 {
            font-size: 1.8rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    // Валидация формы на клиенте
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('applicationForm');
        const courseNameInput = document.getElementById('course_name');
        const dateInput = document.getElementById('desired_start_date');
        
        // Автоматическая установка минимальной даты
        if (dateInput && !dateInput.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            dateInput.value = tomorrowStr;
        }
        
        // Валидация при отправке формы
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            // Очищаем предыдущие ошибки
            document.querySelectorAll('.form-control.error').forEach(el => {
                el.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(el => {
                el.remove();
            });
            
            // Валидация названия курса
            if (!courseNameInput.value.trim() || courseNameInput.value.trim().length < 3) {
                showError(courseNameInput, 'Название курса должно содержать минимум 3 символа');
                valid = false;
            }
            
            // Валидация даты
            if (!dateInput.value) {
                showError(dateInput, 'Укажите желаемую дату начала');
                valid = false;
            } else {
                const selectedDate = new Date(dateInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    showError(dateInput, 'Дата начала не может быть в прошлом');
                    valid = false;
                }
            }
            
            // Валидация способа оплаты
            const paymentSelected = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentSelected) {
                const paymentGroup = document.querySelector('.payment-methods');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'Выберите способ оплаты';
                paymentGroup.parentNode.appendChild(errorDiv);
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                // Прокручиваем к первой ошибке
                const firstError = document.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
        
        function showError(input, message) {
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            
            input.parentNode.appendChild(errorDiv);
        }
        
        // Очистка ошибок при вводе
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                    const errorDiv = this.parentNode.querySelector('.error-message');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });
        });
        
        // Очистка ошибок способа оплаты при выборе
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const errorDiv = document.querySelector('.payment-methods').parentNode.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            });
        });
    });
</script>