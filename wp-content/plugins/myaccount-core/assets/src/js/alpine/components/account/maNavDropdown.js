/**
 * My Account navigation dropdown (mobile).
 * Shows a trigger with the active menu label; toggles list on click.
 * Use on the nav wrapper: x-data="maNavDropdown"
 */
export default () => ({
    open: false,
    activeLabel: '',

    init() {
        const isDesktop = () => typeof window !== 'undefined' && window.innerWidth >= 992;
        if (isDesktop()) {
            this.open = true;
        }
        this.$nextTick(() => {
            const activeEl = this.$el.querySelector(
                '.woocommerce-MyAccount-navigation-list li.is-active .ma-nav-link__label'
            );
            this.activeLabel = activeEl
                ? activeEl.textContent.trim()
                : (this.$el.dataset.activeLabel || 'Menu');
        });
    },

    isMobile() {
        return typeof window !== 'undefined' && window.innerWidth < 992;
    },

    close() {
        if (this.isMobile()) {
            this.open = false;
        }
    },
});
