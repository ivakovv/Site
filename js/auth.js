// В начале файла добавим определение базового пути
function getBasePath() {
    const currentPath = window.location.pathname;
    // Если мы в каталоге, возвращаем путь на уровень выше
    if (currentPath.includes('/catalog/')) {
        return '../';
    }
    // Если мы на главной странице
    if (currentPath.endsWith('/site/') || currentPath.endsWith('/site/init.php')) {
        return '';
    }
    // По умолчанию
    return '';
}

document.addEventListener('DOMContentLoaded', async function() {
    // Проверяем авторизацию
    try {
        const basePath = getBasePath();
        const response = await fetch(basePath + 'check_auth.php');
        const data = await response.json();
        
        if (data.authenticated) {
            updateUIForLoggedUser(data.user);
        }
    } catch (error) {
        console.error('Ошибка проверки авторизации:', error);
    }
    
    // Показ модального окна при клике на кнопку входа
    const loginButton = document.getElementById('login-button');
    const loginModal = document.getElementById('login-modal');
    const closeButtons = document.querySelectorAll('.close-button');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginButton && loginModal) {
        loginButton.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'block';
        });

        // Закрытие модального окна
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                loginModal.style.display = 'none';
            });
        });

        // Закрытие при клике вне модального окна
        window.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });
    }

    // Переключение между формами
    const authTabs = document.querySelectorAll('.auth-tab');
    if (authTabs.length > 0) {
        authTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                authTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.auth-form').forEach(form => {
                    form.classList.remove('active');
                });
                
                const formId = this.dataset.tab + '-form-container';
                const formContainer = document.getElementById(formId);
                if (formContainer) {
                    formContainer.classList.add('active');
                }
            });
        });
    }

    // Обработчики форм
    if (loginForm) {
        // Код для формы входа...
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateLoginForm()) {
                const formData = new FormData(this);
                loginUser(formData);
            }
        });
    }

    if (registerForm) {
        // Код для формы регистрации...
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateRegisterForm()) {
                const formData = new FormData(this);
                registerUser(formData);
            }
        });
    }

    // Добавляем обработчик для кнопки выхода
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            try {
                const basePath = getBasePath();
                const response = await fetch(basePath + 'logout.php');
                const data = await response.json();
                
                if (data.success) {
                    // Обновляем UI после успешного выхода
                    const loginButton = document.getElementById('login-button');
                    const iconPath = basePath ? basePath + 'icons/user.png' : 'icons/user.png';
                    
                    loginButton.innerHTML = `
                        <div class="login-icon">
                            <img src="${iconPath}" alt="Login Icon" />
                        </div>
                        <div class="login-text">Вход</div>
                    `;
                    
                    // Очищаем корзину
                    if (typeof cart !== 'undefined') {
                        cart = [];
                        updateCartCounter();
                        updateCartDisplay();
                    }
                    
                    // Перезагружаем страницу
                    window.location.reload();
                }
            } catch (error) {
                console.error('Ошибка при выходе:', error);
            }
        });
    }

    // Остальной код...
});

// Вспомогательные функции
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidPhone(phone) {
    const re = /^\+?[0-9]{11}$/;
    return re.test(phone.replace(/\D/g, ''));
}

function isValidPassword(password) {
    return password.length >= 8 && 
           /[A-Za-z]/.test(password) && 
           /[0-9]/.test(password);
}

function validateLoginForm() {
    const loginEmail = document.getElementById('login-email');
    const loginPassword = document.getElementById('login-password');
    let isValid = true;

    // Проверка email
    if (!isValidEmail(loginEmail.value)) {
        showError('login-email-error', 'Введите корректный email');
        loginEmail.classList.add('invalid');
        loginEmail.classList.remove('valid');
        isValid = false;
    }

    // Прверка пароля
    if (loginPassword.value.length < 8) {
        showError('login-password-error', 'Пароль должен содержать минимум 8 символов');
        loginPassword.classList.add('invalid');
        loginPassword.classList.remove('valid');
        isValid = false;
    }

    return isValid;
}

function validateRegisterForm() {
    const registerName = document.getElementById('register-name');
    const registerEmail = document.getElementById('register-email');
    const registerPhone = document.getElementById('register-phone');
    const registerPassword = document.getElementById('register-password');
    const registerPasswordConfirm = document.getElementById('register-password-confirm');
    let isValid = true;

    // Проверка ФИО
    if (registerName.value.length < 2) {
        showError('register-name-error', 'ФИО должно содержать не менее 2 символов');
        registerName.classList.add('invalid');
        registerName.classList.remove('valid');
        isValid = false;
    }

    // Проверка email
    if (!isValidEmail(registerEmail.value)) {
        showError('register-email-error', 'Введите корректный email');
        registerEmail.classList.add('invalid');
        registerEmail.classList.remove('valid');
        isValid = false;
    }

    // Проверка телефона
    if (!isValidPhone(registerPhone.value)) {
        showError('register-phone-error', 'Введите корректный номер телефона (11 цифр)');
        registerPhone.classList.add('invalid');
        registerPhone.classList.remove('valid');
        isValid = false;
    }

    // Проверка пароля
    if (!isValidPassword(registerPassword.value)) {
        showError('register-password-error', 
            'Пароль должен содержать минимум 8 символов, включая буквы и цифры');
        registerPassword.classList.add('invalid');
        registerPassword.classList.remove('valid');
        isValid = false;
    }

    // Проверка совпадения паролей
    if (registerPassword.value !== registerPasswordConfirm.value) {
        showError('register-password-confirm-error', 'Пароли не совпадают');
        registerPasswordConfirm.classList.add('invalid');
        registerPasswordConfirm.classList.remove('valid');
        isValid = false;
    }

    return isValid;
}

// Функция для регистрации
async function registerUser(formData) {
    try {
        // Очищаем все предыдущие ошибки
        clearAllErrors();
        const basePath = getBasePath();
        
        const response = await fetch(basePath + 'register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: formData.get('password')
            })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            // Показываем ошибку в форме
            if (data.error.includes('email')) {
                showError('register-email-error', data.error);
                document.getElementById('register-email').classList.add('invalid');
            } else {
                // Общая ошибка регистрации
                showFormError('register-form', data.error || 'Ошибка регистрации');
            }
            return;
        }
        
        // Успешная регистрация
        showFormSuccess('register-form', 'Регистрация успешно завершена!');
        setTimeout(() => {
            // Переключаемся на вкладку хода
            document.querySelector('[data-tab="login"]').click();
        }, 2000);
        
    } catch (error) {
        showFormError('register-form', 'Произошла ошибка при регистрации');
    }
}

// Функция для входа
async function loginUser(formData) {
    try {
        clearAllErrors();
        const basePath = getBasePath();
        
        const response = await fetch(basePath + 'login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
                remember: formData.get('remember') === 'on'
            })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            showFormError('login-form', data.error || 'Неверный email или пароль');
            return;
        }
        
        if (data.success) {
            // Если пользователь админ, перенаправляем его
            if (data.redirect) {
                window.location.href = basePath + data.redirect;
                return;
            }
            
            // Обновляем UI после успешного входа
            const loginButton = document.getElementById('login-button');
            const iconPath = basePath ? basePath + 'icons/user.png' : 'icons/user.png';
            
            loginButton.innerHTML = `
                <div class="login-icon">
                    <img src="${iconPath}" alt="User Icon" />
                </div>
                <div class="login-text">
                    ${data.user.name}
                    <span class="logout-btn">(Выйти)</span>
                </div>
            `;
            
            // Показываем сообщение об успехе
            showFormSuccess('login-form', 'Вход выполнен успешно!');
            
            // Закрываем модальное окно через небольшую задержку
            setTimeout(() => {
                document.getElementById('login-modal').style.display = 'none';
                // Перезагружаем страницу для обновления данных
                window.location.reload();
            }, 1500);
        }
    } catch (error) {
        showFormError('login-form', 'Произошла ошибка при входе');
    }
}

// Вспомогательные функции для работы с ошибками
function showFormError(formId, message) {
    const form = document.getElementById(formId);
    let errorDiv = form.querySelector('.form-error');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        form.insertBefore(errorDiv, form.firstChild);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function showFormSuccess(formId, message) {
    const form = document.getElementById(formId);
    let successDiv = form.querySelector('.form-success');
    
    if (!successDiv) {
        successDiv = document.createElement('div');
        successDiv.className = 'form-success';
        form.insertBefore(successDiv, form.firstChild);
    }
    
    successDiv.textContent = message;
    successDiv.style.display = 'block';
}

function clearAllErrors() {
    // Очищаем все сообщения об ошибках
    document.querySelectorAll('.form-error, .form-success').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
    
    // Очищаем ошибки полей
    document.querySelectorAll('.error').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
    
    // Убираем классы invalid у полей
    document.querySelectorAll('.invalid').forEach(el => {
        el.classList.remove('invalid');
    });
}

// Функция обновления UI для автризованного пользователя
function updateUIForLoggedUser(user) {
    const loginButton = document.getElementById('login-button');
    const basePath = getBasePath();
    // Формируем правильный путь к иконке
    const iconPath = basePath ? basePath + 'icons/user.png' : 'icons/user.png';
    
    loginButton.innerHTML = `
        <div class="login-icon">
            <img src="${iconPath}" alt="User Icon" />
        </div>
        <div class="login-text">
            ${user.name}
            <span class="logout-btn">(Выйти)</span>
        </div>
    `;
    
    // Добавляем обработчик для кнопки выхода
    const logoutBtn = loginButton.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            await logout();
        });
    }
}

// Функция для выхода
async function logout() {
    try {
        const basePath = getBasePath();
        const response = await fetch(basePath + 'logout.php');
        const data = await response.json();
        
        if (data.success) {
            // Возвращаем исходный вид кнопки входа
            document.getElementById('login-button').innerHTML = `
                <div class="login-icon">
                    <img src="icons/user.png" alt="Login Icon" />
                </div>
                <div class="login-text">Вход</div>
            `;
            
            // Очищаем корзину
            cart = [];
            updateCartCounter();
            updateCartDisplay();
        }
    } catch (error) {
        console.error('Ошибка при выходе:', error);
    }
}

// Функция для проверки статуса авторизации
function checkAuthStatus() {
    const basePath = getBasePath();
    fetch(basePath + 'check_auth.php')
        .then(response => response.json())
        .then(data => {
            const loginButton = document.getElementById('login-button');
            const loginText = loginButton.querySelector('.login-text');
            
            if (data.isAuthenticated) {
                // Пользователь авторизован
                loginText.textContent = data.user.name;
                // Добавляем кнопку выхода, если её ещё нет
                if (!document.querySelector('.logout-btn')) {
                    const logoutBtn = document.createElement('span');
                    logoutBtn.className = 'logout-btn';
                    logoutBtn.textContent = '(Выход)';
                    logoutBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        logout();
                    };
                    loginButton.appendChild(logoutBtn);
                }
            } else {
                // Пользователь не авторизован
                loginText.textContent = 'Вход';
                // Удаляем кнопку выхода, если она есть
                const logoutBtn = document.querySelector('.logout-btn');
                if (logoutBtn) {
                    logoutBtn.remove();
                }
            }
        })
        .catch(error => {
            console.error('Ошибка при проверке авторизации:', error);
        });
}

// Вызываем функцию при загрузке страницы
document.addEventListener('DOMContentLoaded', checkAuthStatus);

// Добавляем функцию logout
function logout() {
    fetch('logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем статус авторизации
                checkAuthStatus();
                // Дополнительные действия после выхода
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка при выходе:', error);
        });
}

async function checkAuth() {
    try {
        const response = await fetch(BASE_URL + '/check_auth.php');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Ошибка при проверке авторизации:', error);
        return { authenticated: false };
    }
} 