document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('report-modal');
    if (!modal) {
        return;
    }

    const closeButtons = [
        ...document.querySelectorAll('#close-report-modal-btn, [data-report-modal-close]'),
    ];

    const openButtons = [
        ...document.querySelectorAll('[data-report-modal-open]'),
    ];

    let backdrop = document.getElementById('report-modal-backdrop');

    const ensureBackdrop = () => {
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'report-modal-backdrop';
            backdrop.className = 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40 hidden';
            backdrop.addEventListener('click', closeModal);
            document.body.appendChild(backdrop);
        }
    };

    const openModal = () => {
        ensureBackdrop();
        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
    };

    function closeModal() {
        if (backdrop) {
            backdrop.classList.add('hidden');
        }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
    }

    for (const button of openButtons) {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            openModal();
        });
    }

    for (const button of closeButtons) {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            closeModal();
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});