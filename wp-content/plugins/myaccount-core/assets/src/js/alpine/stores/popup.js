/**
 * Popup Store - modal center only. KISS: open + content.
 * Usage: Alpine.store('popup').openPopup(content)
 * UX: body scroll lock, Escape to close and focus trap are handled in template.
 */
const BODY_SCROLL_LOCK = 'ma-popup-scroll-lock';

export default {
    open: false,
    content: ``,

    openPopup(content) {
        this.content = content;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                this.open = true;
                document.body.classList.add(BODY_SCROLL_LOCK);
            });
        });
    },

    closePopup() {
        this.open = false;
        document.body.classList.remove(BODY_SCROLL_LOCK);
    }
};
