<template id="form-change-password" x-data>
        <form
                x-data="passwordChangeForm()"
                @submit.prevent="handleSubmit"
                class="w-full underline-form flex flex-col gap-8"
        >
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Change Password</h2>
                <button @click="$store.popup.closePopup()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="#4D4D4D"/>
                    </svg>
                </button>
            </div>
            <!-- Current Password -->
            <div>
                <label class="block" for="current-password">Current Password</label>
                <input
                        x-model="currentPassword"
                        class="border-b border-gray-500 w-full px-3 leading-tight focus:outline-none"
                        id="current-password"
                        type="password"
                        required
                        placeholder="*********">
            </div>

            <!-- New Password -->
            <div>
                <label class="block" for="new-password">New Password</label>
                <input
                        x-model="newPassword"
                        class="border-b border-gray-500 w-full px-3 leading-tight focus:outline-none"
                        id="new-password"
                        type="password"
                        minlength="8"
                        maxlength="25"
                        required
                        placeholder="*********">
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block" for="confirm-password">Confirm Password</label>
                <input
                        x-model="confirmPassword"
                        class="border-b border-gray-500 w-full px-3 leading-tight focus:outline-none"
                        id="confirm-password"
                        type="password"
                        minlength="8"
                        maxlength="25"
                        required
                        placeholder="*********">
            </div>

            <!-- Keep Me Signed In Checkbox -->
            <div class="flex items-center">
                <label class="flex items-center">
                    <input
                            x-model="keepSignedIn"
                            class="mr-2 leading-tight"
                            type="checkbox"
                            id="keep-signed-in"
                            />
                    <span class="text-sm">
                            Keep me signed in.
                        </span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="text-center">(Password must be 8-25 characters.)</div>
            <div>
                <button class="button slim w-full">Save</button>
            </div>
        </form>
</template>

<script>
    // Assuming the nonce is generated and passed from WordPress as in your previous script
    const changePasswordNonce = '<?php echo wp_create_nonce('change-password-action'); ?>';

    document.addEventListener('alpine:init', () => {
        Alpine.data('passwordChangeForm', () => ({
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            keepSignedIn: true,
            changePasswordNonce,

            handleSubmit() {
                // Perform validation
                if (this.newPassword !== this.confirmPassword) {
                    this.$store.toast.addToast("New password and confirm password do not match!", 'error');
                    return;
                }

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

                // Fetch API to send data
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
                        console.error('Error:', error);
                        // Hide loader
                        this.$store.loader.hide();
                        this.$store.toast.addToast('AJAX request failed', 'error');
                        // Additional error handling
                    });
            }
        }));
    });

</script>

