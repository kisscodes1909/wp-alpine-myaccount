/**
 * Popup Store – modal center only. KISS: open + content.
 * Usage: Alpine.store('popup').openPopup(content)
 */
export default {
    open: false,
    content: ``,

    openPopup(content) {
        this.content = content;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => { this.open = true; });
        });
    },

    closePopup() {
        this.open = false;
    }
};
