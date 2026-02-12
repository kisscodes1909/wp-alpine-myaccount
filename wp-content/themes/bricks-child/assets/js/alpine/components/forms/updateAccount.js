/**
 * Update Account Component
 * Usage: <form x-data="updateAccount" @submit.prevent="handleSubmit">
 * Requires: userData (firstName, lastName, email), saveAccountDetailsNonce
 */
export default () => {
    // Get data from localized script
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || '';
    const ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';

    return {
        firstName: userData.firstName || '',
        lastName: userData.lastName || '',
        email: userData.email || '',
        password: '*********',
        saveAccountDetailsNonce: nonce,
        allowSubmit: false,
        isLoading: false,
        errors: {},
        
        async handleSubmit() {
            // Validate Form
            await this.validateForm();

            if (Object.keys(this.errors).length > 0) return;

            await this.ajaxSaveAccountDetails();
        },
        
        setAllowSubmit() {
            this.allowSubmit = true;
        },
        
        async ajaxSaveAccountDetails() {
            this.isLoading = true;
            const data = {
                action: 'save_account_details',
                nonce: this.saveAccountDetailsNonce,
                firstName: this.firstName,
                lastName: this.lastName,
                email: this.email,
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
                } else {
                    this.$store.toast.addToast('Error updating account details', 'error');
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
                firstName: yup.string().required('First name is required.'),
                lastName: yup.string().required('Last name is required.'),
                email: yup.string().email('Invalid email address.').required('Email is required.'),
            });

            // Set empty errors
            this.errors = {};

            // Yup validate the form data
            try {
                await schema.validate({
                    firstName: this.firstName,
                    lastName: this.lastName,
                    email: this.email,
                }, { abortEarly: false });

                this.allowSubmit = true;
                return true;
            } catch (err) {
                err.inner.forEach(error => {
                    this.errors[error.path] = error.message;
                });

                this.allowSubmit = false;
                return false;
            }
        }
    };
};
