document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    const statusMessageDiv = document.getElementById('form-status-message');

    if (status && statusMessageDiv) {
        let alertClass = '';
        let alertContent = '';

        if (status === 'success') {
            alertClass = 'alert-success';
            alertContent = '<i class="fas fa-check-circle"></i> ¡Tu mensaje ha sido enviado con éxito! Te responderemos a la brevedad.';
        } else if (status === 'error') {
            alertClass = 'alert-error';
            alertContent = '<i class="fas fa-exclamation-circle"></i> Hubo un error al enviar tu mensaje. ';
            if (message) {
                alertContent += decodeURIComponent(message).replace(/<br>/g, '<br>');
            } else {
                alertContent += 'Por favor, inténtalo de nuevo.';
            }
        } else if (status === 'error_method') {
            alertClass = 'alert-error';
            alertContent = '<i class="fas fa-exclamation-circle"></i> Acceso inválido al formulario. Por favor, envíalo desde la página de contacto.';
        }

        if (alertContent) {
            statusMessageDiv.innerHTML = alertContent;
            statusMessageDiv.classList.add('alert', alertClass);
            statusMessageDiv.style.display = 'block';

            setTimeout(() => {
                statusMessageDiv.style.display = 'none';
                statusMessageDiv.classList.remove('alert', alertClass);
                history.replaceState({}, document.title, window.location.pathname);
            }, 8000);
        }
    }
});