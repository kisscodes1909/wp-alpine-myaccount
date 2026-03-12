(() => {
  // assets/src/js/BaseFormHandler.js
  var BaseFormHandler = class {
    constructor(formData, additionalData = {}) {
      this.formData = formData;
      this.additionalData = additionalData;
      this.errors = {};
      this.touched = {};
      this.notice = "";
      this.isFormSubmitting = false;
    }
    async validateForm(skipFields = []) {
      const fields = Object.keys(this.formData).filter((field) => !skipFields.includes(field));
      for (const field of fields) {
        await this.validateField(field);
      }
    }
    async validateField(field) {
      const yup = window.yup;
      const schema = this.getValidationSchema();
      try {
        await schema.validateAt(field, this.formData);
        delete this.errors[field];
      } catch (error) {
        this.errors[field] = error.message;
      }
      this.touched[field] = true;
    }
    getValidationSchema() {
      throw new Error("getValidationSchema method should be implemented in the subclass");
    }
    /**
     * Get user-facing message from success response. Supports consistent shape
     * { data: { message: string } } and legacy { data: string } or { message: string }.
     */
    getResponseMessage(response) {
      if (!response) return "";
      const data = response.data;
      if (data && typeof data === "object" && typeof data.message === "string") return data.message;
      if (typeof data === "string") return data;
      if (typeof response.message === "string") return response.message;
      return "";
    }
    getErrorMessage(error) {
      const responseData = error?.responseJSON?.data;
      if (typeof responseData?.message === "string" && responseData.message) {
        return responseData.message;
      }
      if (typeof responseData === "string" && responseData) {
        return responseData;
      }
      if (typeof error?.message === "string" && error.message) {
        return error.message;
      }
      return "Something went wrong. Please try again.";
    }
    async handleSubmit(skipFields) {
      await this.validateForm(skipFields);
      if (Object.keys(this.errors).length > 0) {
        this.isFormSubmitting = false;
        return;
      }
      this.isFormSubmitting = true;
      window.wp.ajax.post(this.getApiEndpoint(), {
        ...this.formData,
        ...this.additionalData
      }).done((response) => {
        this.isFormSubmitting = false;
        this.notice = this.getResponseMessage(response);
        this.done(response);
        const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
        window.dispatchEvent(event);
      }).fail((error) => {
        this.notice = this.getErrorMessage(error);
        this.isFormSubmitting = false;
      });
    }
  };

  // assets/src/js/handlers/UpdateAccountHandler.js
  var UpdateAccountHandler = class extends BaseFormHandler {
    getValidationSchema() {
      return window.yup.object().shape({
        firstName: window.yup.string().required("First name is required."),
        lastName: window.yup.string().required("Last name is required."),
        email: window.yup.string().email("Invalid email address.").required("Email is required.")
      });
    }
    getApiEndpoint() {
      return "save_account_details";
    }
    async handleSubmit() {
      await this.validateForm();
      if (Object.keys(this.errors).length > 0) {
        this.isFormSubmitting = false;
        return;
      }
      this.isFormSubmitting = true;
      const payload = {
        ...this.formData,
        nonce: this.additionalData.nonce
      };
      window.wp.ajax.post(this.getApiEndpoint(), payload).done((response) => {
        this.isFormSubmitting = false;
        this.notice = this.getResponseMessage(response);
        this.done(response);
        const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
        window.dispatchEvent(event);
      }).fail((error) => {
        this.notice = this.getErrorMessage(error);
        this.isFormSubmitting = false;
        this.fail(error);
      });
    }
    done(response) {
      const message = this.getResponseMessage(response) || "Account details updated successfully.";
      window.Alpine?.store("toast")?.addToast(message, "success");
    }
    fail(error) {
      const message = this.getErrorMessage(error);
      window.Alpine?.store("toast")?.addToast(message, "error");
    }
  };

  // assets/src/js/alpine/components/forms/updateAccount.js
  var updateAccount_default = () => {
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || "";
    return {
      formData: {
        firstName: userData.firstName || "",
        lastName: userData.lastName || "",
        email: userData.email || ""
      },
      allowSubmit: false,
      isFormSubmitting: false,
      errors: {},
      touched: {},
      notice: "",
      handler: null,
      init() {
        this.handler = new UpdateAccountHandler(this.formData, {
          nonce
        });
        this.$watch("handler.isFormSubmitting", (value) => {
          this.isFormSubmitting = value;
        });
        this.$watch("handler.errors", (value) => {
          this.errors = value;
        });
        this.$watch("handler.touched", (value) => {
          this.touched = value;
        });
        this.$watch("handler.notice", (value) => {
          this.notice = value;
        });
      },
      setAllowSubmit() {
        this.allowSubmit = true;
      },
      async handleSubmit() {
        await this.handler.handleSubmit();
      },
      async validateForm() {
        await this.handler.validateForm();
        this.allowSubmit = Object.keys(this.handler.errors || {}).length === 0;
      },
      validateField(field) {
        return this.handler.validateField(field);
      }
    };
  };

  // assets/src/js/handlers/ChangePasswordHandler.js
  var ChangePasswordHandler = class extends BaseFormHandler {
    getValidationSchema() {
      return window.yup.object().shape({
        currentPassword: window.yup.string().required("The current password is required."),
        newPassword: window.yup.string().min(8, "The new password must be at least 8 characters long.").required("The new password is required."),
        confirmPassword: window.yup.string().oneOf([window.yup.ref("newPassword"), null], "Passwords must match.").required("Confirming your new password is required.")
      });
    }
    getApiEndpoint() {
      return "change_password";
    }
    async handleSubmit() {
      await this.validateForm();
      if (Object.keys(this.errors).length > 0) {
        this.isFormSubmitting = false;
        return;
      }
      this.isFormSubmitting = true;
      const payload = {
        ...this.additionalData,
        currentPassword: this.formData.currentPassword,
        pass1: this.formData.newPassword,
        pass2: this.formData.confirmPassword,
        keepSignedIn: this.formData.keepSignedIn
      };
      window.wp.ajax.post(this.getApiEndpoint(), payload).done((response) => {
        this.isFormSubmitting = false;
        this.notice = this.getResponseMessage(response);
        this.done(response);
        const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
        window.dispatchEvent(event);
      }).fail((error) => {
        this.notice = this.getErrorMessage(error);
        this.isFormSubmitting = false;
      });
    }
    done(response) {
      const message = this.getResponseMessage(response);
      if (message) {
        window.Alpine?.store("toast")?.addToast(message, "success");
      }
      window.Alpine?.store("popup")?.closePopup();
    }
  };

  // assets/src/js/alpine/components/forms/passwordChangeForm.js
  var passwordChangeForm_default = () => ({
    formData: {
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
      keepSignedIn: true
    },
    isFormSubmitting: false,
    errors: {},
    touched: {},
    notice: "",
    changePasswordNonce: window.changePasswordNonce || "",
    handler: null,
    init() {
      this.handler = new ChangePasswordHandler(this.formData, {
        nonce: this.changePasswordNonce
      });
      this.$watch("handler.isFormSubmitting", (value) => {
        this.isFormSubmitting = value;
      });
      this.$watch("handler.errors", (value) => {
        this.errors = value;
      });
      this.$watch("handler.touched", (value) => {
        this.touched = value;
      });
      this.$watch("handler.notice", (value) => {
        this.notice = value;
      });
    },
    async handleSubmit() {
      await this.handler.handleSubmit();
    },
    validateField(field) {
      return this.handler.validateField(field);
    }
  });

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
