/**
 * Popup Store - modal center only. KISS: open + content.
 * Usage: Alpine.store('popup').openPopup(content)
 * UX: Escape to close and focus trap are handled in template.
 */

export default {
    open: false,
    content: ``,

    openPopup(content) {
        this.content = content;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                this.open = true;
            });
        });
    },

    closePopup() {
        this.open = false;
    }
};
