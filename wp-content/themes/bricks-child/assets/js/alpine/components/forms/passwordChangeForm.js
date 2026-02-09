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
        errors: {},
        
        async handleSubmit() {
            // Validate Form
            await this.validateForm();

            if (Object.keys(this.errors).length > 0) return;

            // Show loader
            this.$store.loader.show();

            // Prepare data to be sent
            const data = {
                action: 'change_password',
                nonce: this.changePasswordNonce,
                currentPassword: this.currentPassword,
                pass1: this.newPassword,
                pass2: this.confirmPassword,
                keepSignedIn: this.keepSignedIn,
            };

            // Send data to WordPress Server
            fetch(ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(data => {
                // Hide loader
                this.$store.loader.hide();

                if (data.success) {
                    this.$store.toast.addToast(data.data, 'success');
                    this.$store.popup.closePopup();
                } else {
                    this.$store.toast.addToast(data.data, 'error');
                }
            })
            .catch(error => {
                console.log('Error:', error);
                this.$store.loader.hide();
                this.$store.toast.addToast('AJAX request failed', 'error');
            });
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
