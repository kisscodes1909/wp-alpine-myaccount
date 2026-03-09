(() => {
  // assets/src/js/alpine/components/forms/updateAccount.js
  var updateAccount_default = () => {
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || "";
    const ajaxUrl = window.ajaxurl || "/wp-admin/admin-ajax.php";
    return {
      firstName: userData.firstName || "",
      lastName: userData.lastName || "",
      email: userData.email || "",
      password: "*********",
      saveAccountDetailsNonce: nonce,
      allowSubmit: false,
      isLoading: false,
      errors: {},
      async handleSubmit() {
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
          action: "save_account_details",
          nonce: this.saveAccountDetailsNonce,
          firstName: this.firstName,
          lastName: this.lastName,
          email: this.email
        };
        try {
          const response = await fetch(ajaxUrl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(data)
          });
          const result = await response.json();
          if (result.success) {
            this.$store.toast.addToast(result.data, "success");
          } else {
            this.$store.toast.addToast("Error updating account details", "error");
          }
        } catch (error) {
          console.error("Error:", error);
          this.$store.toast.addToast("AJAX request failed", "error");
        } finally {
          this.isLoading = false;
        }
      },
      async validateForm() {
        const yup = window.yup;
        const schema = yup.object().shape({
          firstName: yup.string().required("First name is required."),
          lastName: yup.string().required("Last name is required."),
          email: yup.string().email("Invalid email address.").required("Email is required.")
        });
        this.errors = {};
        try {
          await schema.validate({
            firstName: this.firstName,
            lastName: this.lastName,
            email: this.email
          }, { abortEarly: false });
          this.allowSubmit = true;
          return true;
        } catch (err) {
          err.inner.forEach((error) => {
            this.errors[error.path] = error.message;
          });
          this.allowSubmit = false;
          return false;
        }
      }
    };
  };

  // assets/src/js/alpine/components/forms/passwordChangeForm.js
  var passwordChangeForm_default = () => {
    const nonce = window.changePasswordNonce || "";
    const ajaxUrl = window.ajaxurl || "/wp-admin/admin-ajax.php";
    return {
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
      keepSignedIn: true,
      changePasswordNonce: nonce,
      isLoading: false,
      errors: {},
      async handleSubmit() {
        await this.validateForm();
        if (Object.keys(this.errors).length > 0) return;
        this.isLoading = true;
        const data = {
          action: "change_password",
          nonce: this.changePasswordNonce,
          currentPassword: this.currentPassword,
          pass1: this.newPassword,
          pass2: this.confirmPassword,
          keepSignedIn: this.keepSignedIn
        };
        try {
          const response = await fetch(ajaxUrl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(data)
          });
          const result = await response.json();
          if (result.success) {
            this.$store.toast.addToast(result.data, "success");
            this.$store.popup.closePopup();
          } else {
            this.$store.toast.addToast(result.data, "error");
          }
        } catch (error) {
          console.error("Error:", error);
          this.$store.toast.addToast("AJAX request failed", "error");
        } finally {
          this.isLoading = false;
        }
      },
      async validateForm() {
        const yup = window.yup;
        const schema = yup.object().shape({
          currentPassword: yup.string().required("The current password is required."),
          newPassword: yup.string().min(8, "The new password must be at least 8 characters long.").required("The new password is required."),
          confirmPassword: yup.string().oneOf([yup.ref("newPassword"), null], "Passwords must match.").required("Confirming your new password is required.")
        });
        this.errors = {};
        try {
          await schema.validate({
            currentPassword: this.currentPassword,
            newPassword: this.newPassword,
            confirmPassword: this.confirmPassword
          }, { abortEarly: false });
          return true;
        } catch (err) {
          err.inner.forEach((error) => {
            this.errors[error.path] = error.message;
          });
          return false;
        }
      }
    };
  };

  // assets/src/js/alpine/components/forms/edit-account.js
  function registerEditAccountFormComponents() {
    Alpine.data("updateAccount", updateAccount_default);
    Alpine.data("passwordChangeForm", passwordChangeForm_default);
  }

  // assets/src/js/alpine/entries/endpoint-edit-account.js
  registerEditAccountFormComponents();
  window.MyAccountAlpineRuntime?.start?.();
})();
//# sourceMappingURL=alpine.edit-account.js.map
