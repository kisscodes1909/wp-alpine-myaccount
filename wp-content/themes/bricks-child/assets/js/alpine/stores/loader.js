/**
 * Loader Store - Global loading indicator
 * Usage: Alpine.store('loader').show() / hide()
 */
export default {
    isLoading: false,

    // Method to show the loader
    show() {
        this.isLoading = true;
    },

    // Method to hide the loader
    hide() {
        this.isLoading = false;
    }
};
