/**
 * Popup Store - Global modal/popup management
 * Usage: Alpine.store('popup').openPopup(content, direction)
 * Directions: 'left', 'right', 'center' (default)
 */
export default {
    open: false,
    content: ``,
    direction: 'center', // Default direction
    _scrollbarWidth: 0,

    openPopup(content, direction = 'center') {
        this.content = content;
        this.direction = direction;

        // Prevent layout shift when hiding scrollbar (keeps modal aligned)
        this._scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        if (this._scrollbarWidth > 0) {
            document.body.style.paddingRight = this._scrollbarWidth + 'px';
            document.documentElement.style.paddingRight = this._scrollbarWidth + 'px';
        }
        document.body.classList.add('overflow-y-hidden');

        setTimeout(() => {
            this.open = true;
        }, 50);
    },

    closePopup() {
        this.open = false;
        document.body.classList.remove('overflow-y-hidden');
        document.body.style.paddingRight = '';
        document.documentElement.style.paddingRight = '';
    }
};
