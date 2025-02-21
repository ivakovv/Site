document.addEventListener('DOMContentLoaded', function() {
    const loginButton = document.getElementById('login-button');
    const loginModal = document.getElementById('login-modal');
    const closeButtons = document.querySelectorAll('.close-button');

    if (loginButton && loginModal) {
        loginButton.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'block';
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                loginModal.style.display = 'none';
            });
        });

        window.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });
    }
});