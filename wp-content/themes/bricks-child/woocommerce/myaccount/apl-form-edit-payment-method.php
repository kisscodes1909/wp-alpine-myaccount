<template id="form-change-payment-method" x-data>
    <form
        x-data="paymentMethodChangeForm()"
        class="w-full apl-form-refined flex flex-col gap-8"
        @keyup.enter="handleSubmit()"
        @submit.prevent="handleSubmit"
        @keyup="validateForm()"

    >
        <div class="flex justify-between items-center">
            <h2 class="apl-heading-chip-sm">Update Payment</h2>
            <button @click="$store.popup.closePopup()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                    <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="#4D4D4D"/>
                </svg>
            </button>
        </div>
        <!-- Current Password -->
        <div x-data="{showPassword:false}">
            <label class="block" for="current-password">New Password</label>
            <div class="relative">
                <input
                    x-model="currentPassword"
                    class="input-text"
                    id="current-password"
                    type="password"
                    placeholder="*********"
                    :type="showPassword === true ? 'text' : 'password'"
                    :class="{'field-invalid': errors.currentPassword}"

                >
                <span @click="showPassword=!showPassword" class="password-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="13" viewBox="0 0 17 13" fill="none">
                          <path d="M10.6105 6.64321L11.5761 5.67766C11.6463 5.94049 11.6875 6.21528 11.6875 6.50003C11.6875 8.25767 10.2576 9.68753 8.49998 9.68753C8.21523 9.68753 7.94045 9.64636 7.67761 9.57611L8.64316 8.61056C9.15351 8.57589 9.63403 8.35749 9.99573 7.99578C10.3574 7.63408 10.5758 7.15356 10.6105 6.64321ZM16.933 6.24198C16.8677 6.12444 15.9409 4.50054 14.1294 3.12434L13.3563 3.89744C14.6772 4.8687 15.5142 6.00145 15.8437 6.50083C15.2141 7.4596 12.744 10.75 8.49998 10.75C7.86355 10.75 7.27173 10.6686 6.71485 10.5387L5.84188 11.4117C6.64446 11.6578 7.5266 11.8125 8.49998 11.8125C14.0612 11.8125 16.8184 6.96435 16.933 6.75809C16.9769 6.67916 17 6.59035 17 6.50003C17 6.40972 16.9769 6.3209 16.933 6.24198ZM14.7193 1.03188L3.03183 12.7194C2.9281 12.8231 2.7921 12.875 2.65623 12.875C2.52037 12.875 2.38437 12.8231 2.28064 12.7194C2.23128 12.6701 2.19212 12.6115 2.1654 12.5471C2.13869 12.4826 2.12493 12.4136 2.12493 12.3438C2.12493 12.274 2.13869 12.2049 2.1654 12.1405C2.19212 12.076 2.23128 12.0175 2.28064 11.9682L3.76761 10.4812C1.3637 9.02998 0.142227 6.89369 0.0669225 6.75809C0.023033 6.67914 0 6.5903 0 6.49997C0 6.40964 0.023033 6.3208 0.0669225 6.24185C0.18154 6.03572 2.93873 1.18753 8.49998 1.18753C9.93795 1.18753 11.1835 1.51558 12.2482 2.00061L13.9681 0.280689C14.1757 0.0731035 14.5119 0.0731035 14.7193 0.280689C14.9268 0.488275 14.9269 0.824424 14.7193 1.03188ZM4.55984 9.689L5.90483 8.344C5.53335 7.82285 5.31248 7.18734 5.31248 6.50003C5.31248 4.74239 6.74234 3.31253 8.49998 3.31253C9.18729 3.31253 9.8228 3.5334 10.344 3.90474L11.4316 2.81714C10.5723 2.47303 9.59582 2.25003 8.49998 2.25003C4.25596 2.25003 1.78591 5.54046 1.15638 6.49924C1.55615 7.10513 2.70524 8.63885 4.55984 9.689ZM6.67607 7.57263L9.57258 4.67612C9.25662 4.48952 8.89284 4.37503 8.49998 4.37503C7.32831 4.37503 6.37498 5.32836 6.37498 6.50003C6.37498 6.89289 6.48947 7.25667 6.67607 7.57263Z" fill="black"/>
                        </svg>
                    </span>
            </div>
            <span class="text-red-600 mt-1 block" x-show="errors.currentPassword" x-text="errors.currentPassword"></span>
        </div>

        <!-- New Password -->
        <div x-data="{showPassword:false}">
            <label class="block" for="new-password">Confirm Password</label>
            <div class="relative">
                <input
                    x-model="newPassword"
                    class="input-text"
                    id="new-password"
                    type="password"
                    placeholder="*********"
                    :type="showPassword === true ? 'text' : 'password'"
                    :class="{'field-invalid': errors.newPassword}"

                >
                <span @click="showPassword=!showPassword" class="password-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="13" viewBox="0 0 17 13" fill="none">
                          <path d="M10.6105 6.64321L11.5761 5.67766C11.6463 5.94049 11.6875 6.21528 11.6875 6.50003C11.6875 8.25767 10.2576 9.68753 8.49998 9.68753C8.21523 9.68753 7.94045 9.64636 7.67761 9.57611L8.64316 8.61056C9.15351 8.57589 9.63403 8.35749 9.99573 7.99578C10.3574 7.63408 10.5758 7.15356 10.6105 6.64321ZM16.933 6.24198C16.8677 6.12444 15.9409 4.50054 14.1294 3.12434L13.3563 3.89744C14.6772 4.8687 15.5142 6.00145 15.8437 6.50083C15.2141 7.4596 12.744 10.75 8.49998 10.75C7.86355 10.75 7.27173 10.6686 6.71485 10.5387L5.84188 11.4117C6.64446 11.6578 7.5266 11.8125 8.49998 11.8125C14.0612 11.8125 16.8184 6.96435 16.933 6.75809C16.9769 6.67916 17 6.59035 17 6.50003C17 6.40972 16.9769 6.3209 16.933 6.24198ZM14.7193 1.03188L3.03183 12.7194C2.9281 12.8231 2.7921 12.875 2.65623 12.875C2.52037 12.875 2.38437 12.8231 2.28064 12.7194C2.23128 12.6701 2.19212 12.6115 2.1654 12.5471C2.13869 12.4826 2.12493 12.4136 2.12493 12.3438C2.12493 12.274 2.13869 12.2049 2.1654 12.1405C2.19212 12.076 2.23128 12.0175 2.28064 11.9682L3.76761 10.4812C1.3637 9.02998 0.142227 6.89369 0.0669225 6.75809C0.023033 6.67914 0 6.5903 0 6.49997C0 6.40964 0.023033 6.3208 0.0669225 6.24185C0.18154 6.03572 2.93873 1.18753 8.49998 1.18753C9.93795 1.18753 11.1835 1.51558 12.2482 2.00061L13.9681 0.280689C14.1757 0.0731035 14.5119 0.0731035 14.7193 0.280689C14.9268 0.488275 14.9269 0.824424 14.7193 1.03188ZM4.55984 9.689L5.90483 8.344C5.53335 7.82285 5.31248 7.18734 5.31248 6.50003C5.31248 4.74239 6.74234 3.31253 8.49998 3.31253C9.18729 3.31253 9.8228 3.5334 10.344 3.90474L11.4316 2.81714C10.5723 2.47303 9.59582 2.25003 8.49998 2.25003C4.25596 2.25003 1.78591 5.54046 1.15638 6.49924C1.55615 7.10513 2.70524 8.63885 4.55984 9.689ZM6.67607 7.57263L9.57258 4.67612C9.25662 4.48952 8.89284 4.37503 8.49998 4.37503C7.32831 4.37503 6.37498 5.32836 6.37498 6.50003C6.37498 6.89289 6.48947 7.25667 6.67607 7.57263Z" fill="black"/>
                        </svg>
                    </span>
            </div>
            <span class="text-red-600 mt-1 block" x-show="errors.newPassword" x-text="errors.newPassword"></span>
        </div>

        <!-- Confirm Password -->
        <div x-data="{showPassword:false}">
            <label class="block" for="confirm-password">Current Password</label>
            <div class="relative">
                <input
                    id="confirm-password"
                    x-model="confirmPassword"
                    class="input-text"
                    type="password"
                    placeholder="*********"
                    :type="showPassword === true ? 'text' : 'password'"
                    :class="{'field-invalid': errors.confirmPassword}"

                >
                <span @click="showPassword=!showPassword" class="password-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="13" viewBox="0 0 17 13" fill="none">
                          <path d="M10.6105 6.64321L11.5761 5.67766C11.6463 5.94049 11.6875 6.21528 11.6875 6.50003C11.6875 8.25767 10.2576 9.68753 8.49998 9.68753C8.21523 9.68753 7.94045 9.64636 7.67761 9.57611L8.64316 8.61056C9.15351 8.57589 9.63403 8.35749 9.99573 7.99578C10.3574 7.63408 10.5758 7.15356 10.6105 6.64321ZM16.933 6.24198C16.8677 6.12444 15.9409 4.50054 14.1294 3.12434L13.3563 3.89744C14.6772 4.8687 15.5142 6.00145 15.8437 6.50083C15.2141 7.4596 12.744 10.75 8.49998 10.75C7.86355 10.75 7.27173 10.6686 6.71485 10.5387L5.84188 11.4117C6.64446 11.6578 7.5266 11.8125 8.49998 11.8125C14.0612 11.8125 16.8184 6.96435 16.933 6.75809C16.9769 6.67916 17 6.59035 17 6.50003C17 6.40972 16.9769 6.3209 16.933 6.24198ZM14.7193 1.03188L3.03183 12.7194C2.9281 12.8231 2.7921 12.875 2.65623 12.875C2.52037 12.875 2.38437 12.8231 2.28064 12.7194C2.23128 12.6701 2.19212 12.6115 2.1654 12.5471C2.13869 12.4826 2.12493 12.4136 2.12493 12.3438C2.12493 12.274 2.13869 12.2049 2.1654 12.1405C2.19212 12.076 2.23128 12.0175 2.28064 11.9682L3.76761 10.4812C1.3637 9.02998 0.142227 6.89369 0.0669225 6.75809C0.023033 6.67914 0 6.5903 0 6.49997C0 6.40964 0.023033 6.3208 0.0669225 6.24185C0.18154 6.03572 2.93873 1.18753 8.49998 1.18753C9.93795 1.18753 11.1835 1.51558 12.2482 2.00061L13.9681 0.280689C14.1757 0.0731035 14.5119 0.0731035 14.7193 0.280689C14.9268 0.488275 14.9269 0.824424 14.7193 1.03188ZM4.55984 9.689L5.90483 8.344C5.53335 7.82285 5.31248 7.18734 5.31248 6.50003C5.31248 4.74239 6.74234 3.31253 8.49998 3.31253C9.18729 3.31253 9.8228 3.5334 10.344 3.90474L11.4316 2.81714C10.5723 2.47303 9.59582 2.25003 8.49998 2.25003C4.25596 2.25003 1.78591 5.54046 1.15638 6.49924C1.55615 7.10513 2.70524 8.63885 4.55984 9.689ZM6.67607 7.57263L9.57258 4.67612C9.25662 4.48952 8.89284 4.37503 8.49998 4.37503C7.32831 4.37503 6.37498 5.32836 6.37498 6.50003C6.37498 6.89289 6.48947 7.25667 6.67607 7.57263Z" fill="black"/>
                        </svg>
                    </span>
            </div>
            <span class="text-red-600 mt-1 block" x-show="errors.confirmPassword" x-text="errors.confirmPassword"></span>
        </div>

        <!-- Keep Me Signed In Checkbox -->
        <div class="flex items-center">
            <label class="jk-checkbox">
                <input
                    x-model="keepSignedIn"
                    type="checkbox"
                    id="keep-signed-in"
                />
                <span><span class="leading-snug">Keep me signed in. <br/><i class="text-sm not-italic">Uncheck if you are using a public device<i></i></span></span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="text-center">(Password must be 8-25 characters.)</div>
        <div class="apl-form-actions">
            <button class="button">Save</button>
        </div>
    </form>
</template>

<script>

    // Assuming the nonce is generated and passed from WordPress as in your previous script
    const changePasswordNonce = '<?php echo wp_create_nonce('change-password-action'); ?>';

    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentMethodChangeForm', () => ({
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            keepSignedIn: true,
            changePasswordNonce,
            errors: {},
            async handleSubmit() {
                // Validate Form
                await this.validateForm();

                if(Object.keys(this.errors).length > 0) return;

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
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
                            // Additional success actions
                        } else {
                            this.$store.toast.addToast(data.data, 'error');
                        }


                    })
                    .catch(error => {
                        console.log('Error:', error);
                        // Hide loader
                        this.$store.loader.hide();
                        this.$store.toast.addToast('AJAX request failed', 'error');
                        // Additional error handling
                    });
            },
            async validateForm() {
                const yup = window.yup;

                // Define the validation schema with password fields
                const schema = yup.object().shape({
                    currentPassword: yup.string().required('The current password is required.'),
                    newPassword: yup.string().min(8, 'The new password must be at least 8 characters long.').required('The new password is required.'),
                    confirmPassword: yup.string()
                        .oneOf([yup.ref('newPassword'), null], 'Passwords must match.')
                        .required('Confirming your new password is required.')
                });

                // Set empty errors.
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
        }));
    });

</script>
