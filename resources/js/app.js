import './bootstrap';

// Prevenir doble envío en todos los formularios
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;

            // Si ya se envió, bloquea el segundo intento
            if (btn.dataset.submitted === 'true') {
                e.preventDefault();
                return;
            }

            btn.dataset.submitted = 'true';
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor  = 'not-allowed';

            // Reemplaza el texto por "Procesando..."
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Procesando...
            `;
        });
    });
});
