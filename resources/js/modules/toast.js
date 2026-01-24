/**
 * Toast Notifications Module
 *
 * Zobrazuje notifikácie (success, error, info)
 */

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const Toast = {
    container: null,

    init() {
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(this.container);
    },

    show(message, type = 'success', duration = 3000) {
        this.init();

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';

        toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <span>${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}</span>
                <span>${message}</span>
            </div>
        `;

        this.container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Animate out & remove
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    success(message) { this.show(message, 'success'); },
    error(message) { this.show(message, 'error'); },
    info(message) { this.show(message, 'info'); },
};

export { Toast, csrfToken };
export default Toast;

