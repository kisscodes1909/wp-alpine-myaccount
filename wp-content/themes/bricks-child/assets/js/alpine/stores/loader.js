/**
 * Loader Store – kept for backwards compatibility.
 * Prefer in-button loading: set isLoading/saving on the component or store
 * and show spinner + label (e.g. "Saving...") inside the button (app-style).
 */
export default {
    isLoading: false,
    show() {
        this.isLoading = true;
    },
    hide() {
        this.isLoading = false;
    }
};
