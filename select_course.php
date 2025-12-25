<?php
require_once 'includes/init.php';
require_once 'db_connect.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Обработка добавления курса в заявку
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    // Здесь будет перенаправление на страницу создания заявки
    $course_id = (int)$_POST['course_id'];
    $_SESSION['selected_course_id'] = $course_id;
    header('Location: create_application.php');
    exit();
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="courses-page">
    <div class="page-header">
        <h1><i class="fas fa-book-open"></i> Выберите курс для обучения</h1>
        <p>Выберите интересующий вас курс из каталога</p>
    </div>

    <!-- Фильтры курсов -->
    <div class="courses-filters">
        <div class="filter-group">
            <input type="text" id="searchCourse" placeholder="Поиск по названию курса..." class="search-input">
        </div>
        <div class="filter-group">
            <select id="filterPrice" class="filter-select">
                <option value="">Все цены</option>
                <option value="0-20000">До 20 000 ₽</option>
                <option value="20000-40000">20 000 - 40 000 ₽</option>
                <option value="40000-100000">От 40 000 ₽</option>
            </select>
        </div>
        <div class="filter-group">
            <select id="filterDuration" class="filter-select">
                <option value="">Любая продолжительность</option>
                <option value="0-50">До 50 часов</option>
                <option value="50-100">50-100 часов</option>
                <option value="100-1000">Более 100 часов</option>
            </select>
        </div>
    </div>

    <!-- Список курсов -->
    <div class="courses-container">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM courses ORDER BY course_name");
            $courses = $stmt->fetchAll();
            
            if (empty($courses)): ?>
                <div class="empty-courses">
                    <i class="fas fa-book"></i>
                    <h3>Курсы не найдены</h3>
                    <p>В данный момент нет доступных курсов</p>
                </div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card-full">
                            <div class="course-card-header">
                                <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                                <span class="course-price"><?php echo number_format($course['base_price'], 0, '', ' '); ?> ₽</span>
                            </div>
                            
                            <div class="course-card-body">
                                <p class="course-description"><?php echo htmlspecialchars($course['description']); ?></p>
                                
                                <div class="course-meta">
                                    <div class="meta-item">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo $course['duration_hours']; ?> часов</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Диплом установленного образца</span>
                                    </div>
                                </div>
                                
                                <div class="course-features">
                                    <div class="feature">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Практические задания</span>
                                    </div>
                                    <div class="feature">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Поддержка преподавателя</span>
                                    </div>
                                    <div class="feature">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Доступ к материалам навсегда</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="course-card-footer">
                                <form method="POST" action="" class="course-select-form">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-select-course">
                                        <i class="fas fa-plus-circle"></i> Выбрать этот курс
                                    </button>
                                    <a href="course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline">
                                        <i class="fas fa-info-circle"></i> Подробнее
                                    </a>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        <?php } catch (PDOException $e) { ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                Не удалось загрузить курсы. Попробуйте позже.
            </div>
        <?php } ?>
    </div>
    
    <div class="page-footer">
        <a href="personal.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Вернуться в личный кабинет
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    /* Стили для страницы выбора курсов */
    .courses-page {
        max-width: 1400px;
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
        font-size: 2.5rem;
    }
    
    .page-header p {
        color: #666;
        font-size: 1.2rem;
    }
    
    /* Фильтры */
    .courses-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .search-input, .filter-select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
    }
    
    .search-input:focus, .filter-select:focus {
        outline: none;
        border-color: #3498db;
    }
    
    /* Сетка курсов */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .course-card-full {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
    }
    
    .course-card-full:hover {
        transform: translateY(-5px);
        border-color: #3498db;
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.15);
    }
    
    .course-card-header {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .course-card-header h3 {
        margin: 0;
        font-size: 1.4rem;
        flex: 1;
        margin-right: 15px;
    }
    
    .course-price {
        background: rgba(255,255,255,0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1.2rem;
        backdrop-filter: blur(5px);
    }
    
    .course-card-body {
        padding: 25px;
        flex-grow: 1;
    }
    
    .course-description {
        color: #555;
        line-height: 1.6;
        margin-bottom: 20px;
        font-size: 1.05rem;
    }
    
    .course-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
    }
    
    .meta-item i {
        color: #3498db;
    }
    
    .course-features {
        margin-bottom: 20px;
    }
    
    .feature {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: #444;
    }
    
    .feature i {
        color: #2ecc71;
    }
    
    .course-card-footer {
        padding: 20px 25px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }
    
    .course-select-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-select-course {
        flex: 1;
        min-width: 200px;
    }
    
    .empty-courses {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .empty-courses i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .empty-courses h3 {
        margin-bottom: 10px;
        color: #2c3e50;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin: 30px 0;
    }
    
    .page-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }
    
    /* Адаптивность */
    @media (max-width: 1200px) {
        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
        
        .course-card-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .course-select-form {
            flex-direction: column;
        }
        
        .btn-select-course, .btn-outline {
            width: 100%;
            justify-content: center;
        }
        
        .course-meta {
            flex-direction: column;
            gap: 10px;
        }
        
        .courses-filters {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Фильтрация и поиск курсов
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchCourse');
        const filterPrice = document.getElementById('filterPrice');
        const filterDuration = document.getElementById('filterDuration');
        const courseCards = document.querySelectorAll('.course-card-full');
        
        function filterCourses() {
            const searchTerm = searchInput.value.toLowerCase();
            const priceFilter = filterPrice.value;
            const durationFilter = filterDuration.value;
            
            courseCards.forEach(card => {
                const courseName = card.querySelector('h3').textContent.toLowerCase();
                const coursePrice = parseInt(card.querySelector('.course-price').textContent.replace(/[^\d]/g, ''));
                const courseDuration = parseInt(card.querySelector('.meta-item span').textContent);
                
                let matchesSearch = courseName.includes(searchTerm);
                let matchesPrice = true;
                let matchesDuration = true;
                
                // Фильтрация по цене
                if (priceFilter) {
                    const [min, max] = priceFilter.split('-').map(Number);
                    matchesPrice = coursePrice >= min && coursePrice <= max;
                }
                
                // Фильтрация по продолжительности
                if (durationFilter) {
                    const [min, max] = durationFilter.split('-').map(Number);
                    matchesDuration = courseDuration >= min && courseDuration <= max;
                }
                
                // Показываем/скрываем карточку
                if (matchesSearch && matchesPrice && matchesDuration) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Показываем сообщение, если ничего не найдено
            const visibleCards = Array.from(courseCards).filter(card => card.style.display !== 'none');
            const emptyMessage = document.querySelector('.empty-courses');
            
            if (visibleCards.length === 0) {
                if (!emptyMessage) {
                    const coursesContainer = document.querySelector('.courses-container');
                    const message = document.createElement('div');
                    message.className = 'empty-courses';
                    message.innerHTML = `
                        <i class="fas fa-search"></i>
                        <h3>Курсы не найдены</h3>
                        <p>Попробуйте изменить параметры поиска</p>
                    `;
                    coursesContainer.appendChild(message);
                }
            } else if (emptyMessage) {
                emptyMessage.remove();
            }
        }
        
        // События для фильтров
        searchInput.addEventListener('input', filterCourses);
        filterPrice.addEventListener('change', filterCourses);
        filterDuration.addEventListener('change', filterCourses);
        
        // Анимация карточек
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        courseCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });
        
        // Подтверждение выбора курса
        document.querySelectorAll('.course-select-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const courseName = this.closest('.course-card-full').querySelector('h3').textContent;
                if (!confirm(`Вы уверены, что хотите выбрать курс "${courseName}"?`)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>