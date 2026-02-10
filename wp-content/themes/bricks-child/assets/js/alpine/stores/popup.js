/**
 * Popup Store – modal center only. KISS: open + content.
 * Usage: Alpine.store('popup').openPopup(content)
 */
export default {
    open: false,
    content: ``,
    _closeTimeout: null,

    openPopup(content) {
        this.content = content;
        const scrollbar = window.innerWidth - document.documentElement.clientWidth;
        if (scrollbar > 0) {
            document.body.style.paddingRight = scrollbar + 'px';
            document.documentElement.style.paddingRight = scrollbar + 'px';
        }
        document.body.classList.add('overflow-y-hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => { this.open = true; });
        });
    },

    closePopup() {
        this.open = false;
        if (this._closeTimeout) clearTimeout(this._closeTimeout);
        this._closeTimeout = setTimeout(() => {
            document.body.classList.remove('overflow-y-hidden');
            document.body.style.paddingRight = '';
            document.documentElement.style.paddingRight = '';
            this._closeTimeout = null;
        }, 500);
    }
};
