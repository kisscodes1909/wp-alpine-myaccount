/**
 * Toast Store - Global notification system
 * Usage: Alpine.store('toast').addToast('message', 'type', duration)
 */
export default {
    toasts: [],
    
    addToast(message, type = 'default', duration = 4000, fadeDuration = 300) {
        const id = Date.now() + Math.random();
        this.toasts.push({id, message, type, show: false});

        // Trigger the show transition after a short delay
        setTimeout(() => {
            this.updateToastVisibility(id, true);
        }, 100);

        // Hide the toast after the specified duration
        setTimeout(() => {
            this.updateToastVisibility(id, false);
        }, duration);

        // Remove the toast after it has faded out
        setTimeout(() => {
            //this.removeToast(id);
        }, duration + fadeDuration);
    },

    updateToastVisibility(id, visibility) {
        const toast = this.toasts.find(toast => toast.id === id);
        if (toast) {
            toast.show = visibility;
        }
    },

    removeToast(id) {
        this.toasts = this.toasts.filter(toast => toast.id !== id);
    }
};
