/**
 * Password Change Form Component
 * Usage: <form x-data="passwordChangeForm" @submit.prevent="handleSubmit">
 * Requires: changePasswordNonce
 */
export default () => {
    const nonce = window.changePasswordNonce || '';
    const ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';

    return {
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        keepSignedIn: true,
        changePasswordNonce: nonce,
        isLoading: false,
        errors: {},
        
        async handleSubmit() {
            await this.validateForm();
            if (Object.keys(this.errors).length > 0) return;

            this.isLoading = true;
            const data = {
                action: 'change_password',
                nonce: this.changePasswordNonce,
                currentPassword: this.currentPassword,
                pass1: this.newPassword,
                pass2: this.confirmPassword,
                keepSignedIn: this.keepSignedIn,
            };

            try {
                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(data)
                });
                const result = await response.json();
                if (result.success) {
                    this.$store.toast.addToast(result.data, 'success');
                    this.$store.popup.closePopup();
                } else {
                    this.$store.toast.addToast(result.data, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.$store.toast.addToast('AJAX request failed', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        async validateForm() {
            const yup = window.yup;

            const schema = yup.object().shape({
                currentPassword: yup.string().required('The current password is required.'),
                newPassword: yup.string().min(8, 'The new password must be at least 8 characters long.').required('The new password is required.'),
                confirmPassword: yup.string()
                    .oneOf([yup.ref('newPassword'), null], 'Passwords must match.')
                    .required('Confirming your new password is required.')
            });

            // Set empty errors
            this.errors = {};

            // Yup validate the form data
            try {
                await schema.validate({
                    currentPassword: this.currentPassword,
                    newPassword: this.newPassword,
                    confirmPassword: this.confirmPassword
                }, { abortEarly: false });
                return true;
            } catch (err) {
                err.inner.forEach(error => {
                    this.errors[error.path] = error.message;
                });
                return false;
            }
        }
    };
};
