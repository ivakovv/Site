document.addEventListener('DOMContentLoaded', function() {
    // Правила валидации
    const validationRules = {
        'login-email': {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Введите корректный email адрес'
        },
        'login-password': {
            pattern: /.{8,}/,
            message: 'Пароль должен содержать минимум 8 символов'
        },
        'register-email': {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Введите корректный email адрес'
        },
        'register-password': {
            pattern: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/,
            message: 'Пароль должен содержать минимум 8 символов, включая буквы и цифры'
        },
        'register-name': {
            pattern: /^[А-Яа-яA-Za-z\s]{2,}$/,
            message: 'Имя должно содержать минимум 2 символа'
        },
        'register-phone': {
            pattern: /^\+?[78]\s?\(?\d{3}\)?\s?\d{3}[-\s]?\d{2}[-\s]?\d{2}$/,
            message: 'Введите корректный номер телефона'
        }
    };

    // Функция валидации поля
    function validateField(input) {
        const rule = validationRules[input.id];
        if (!rule) return true;

        const isValid = rule.pattern.test(input.value);
        const errorElement = document.getElementById(input.id + '-error');
        
        if (errorElement) {
            if (!isValid) {
                input.classList.add('invalid');
                input.classList.remove('valid');
                errorElement.textContent = rule.message;
                errorElement.style.display = 'block';
            } else {
                input.classList.remove('invalid');
                input.classList.add('valid');
                errorElement.style.display = 'none';
            }
        }

        return isValid;
    }

    // Добавляем обработчики для всех полей ввода в формах
    const forms = document.querySelectorAll('#login-form, #register-form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input');
        
        inputs.forEach(input => {
            // Создаем элемент для ошибки, если его нет
            if (!document.getElementById(input.id + '-error')) {
                const errorDiv = document.createElement('div');
                errorDiv.id = input.id + '-error';
                errorDiv.className = 'error-message';
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }

            // Валидация при вводе
            input.addEventListener('input', function() {
                validateField(this);
            });

            // Валидация при потере фокуса
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Валидация при отправке формы
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (isValid) {
                // Если все поля валидны, отправляем форму
                const formData = new FormData(this);
                if (this.id === 'login-form') {
                    loginUser(formData);
                } else if (this.id === 'register-form') {
                    registerUser(formData);
                }
            }
        });
    });
}); 