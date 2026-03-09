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
        this.notice = response.message;
        this.done(response);
        const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
        window.dispatchEvent(event);
      }).fail((error) => {
        this.notice = this.getErrorMessage(error);
        this.isFormSubmitting = false;
      });
    }
  };

  // assets/src/js/handlers/LoginHandler.js
  var LoginHandler = class extends BaseFormHandler {
    getValidationSchema() {
      return window.yup.object().shape({
        password: window.yup.string().required("This field is required.").min(8, "Your password isn't valid."),
        email: window.yup.string().email("Your email address isn't valid.").required("This field is required.")
      });
    }
    done() {
      window.location.reload();
    }
    getApiEndpoint() {
      return "handle_login";
    }
  };

  // assets/src/js/alpine/components/forms/login.js
  var login_default = () => ({
    formData: {
      email: "",
      password: "",
      rememberme: false
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    woocommerceLoginNonce: window.authenicationData?.wooLoginNonce || "",
    notice: "",
    handler: null,
    init() {
      this.handler = new LoginHandler(this.formData, {
        woocommerceLoginNonce: this.woocommerceLoginNonce
      });
      this.$watch("handler.isFormSubmitting", (value) => this.isFormSubmitting = value);
      this.$watch("handler.errors", (value) => this.errors = value);
      this.$watch("handler.touched", (value) => this.touched = value);
      this.$watch("handler.notice", (value) => this.notice = value);
    },
    async handleSubmit() {
      await this.handler.handleSubmit(["rememberme"]);
    }
  });

  // assets/src/js/alpine/components/forms/signup.js
  var signup_default = () => ({
    formData: {
      firstName: "",
      lastName: "",
      email: "",
      password: "",
      agreeTOS: false,
      receiveOfferNews: false
    },
    messages: { message: 0 },
    isFormSubmitting: false,
    notice: "",
    errors: {},
    touched: {},
    signupNonce: window.authenicationData?.signupNonce || "",
    passedRequirements: [],
    passwordRequirements: {
      minLength: { regex: /.{8,}/, message: "At least 8 characters", code: "ERR_PASSWORD_MINLENGTH" },
      uppercase: { regex: /(?=.*[A-Z])/, message: "1 uppercase letter", code: "ERR_PASSWORD_UPPERCASE" },
      number: { regex: /(?=.*[0-9])/, message: "1 number", code: "ERR_PASSWORD_NUMBER" },
      lowercase: { regex: /(?=.*[a-z])/, message: "1 lowercase letter", code: "ERR_PASSWORD_LOWERCASE" }
    },
    async handleSubmit() {
      const token = await grecaptcha.execute(window.authenicationData.captchaSiteKey, { action: "signup" });
      await this.validateForm();
      if (Object.keys(this.errors).length > 0) {
        return;
      }
      this.isFormSubmitting = true;
      window.wp.ajax.post("handle_signup", {
        firstName: this.formData.firstName,
        lastName: this.formData.lastName,
        email: this.formData.email,
        password: this.formData.password,
        agreeTOS: this.formData.agreeTOS,
        receiveOfferNews: this.formData.receiveOfferNews,
        signupNonce: this.signupNonce,
        captchaToken: token
      }).done((response) => {
        this.notice = response.message;
        this.isFormSubmitting = false;
        window.location.reload();
      }).fail((error) => {
        this.notice = error.message;
        this.isFormSubmitting = false;
        console.error("Error:", error);
      });
    },
    async validateForm() {
      const fields = Object.keys(this.formData);
      for (const field of fields) {
        if (field === "receiveOfferNews") continue;
        await this.validateField(field);
      }
    },
    async validateField(field) {
      const yup = window.yup;
      const schema = yup.object().shape({
        firstName: yup.string().required("This field is required.").matches(/^[A-Za-z]+$/, "Your name isn't valid."),
        lastName: yup.string().required("This field is required.").matches(/^[A-Za-z]+$/, "Your name isn't valid."),
        email: yup.string().email("Your email address isn't valid.").required("This field is required."),
        agreeTOS: yup.boolean().required("You must accept the Terms of Service.").oneOf([true], "You must accept the Terms of Service."),
        password: yup.string().required("This field is required.").test("password-complexity", "Your password does not meet the requirements.", (value) => {
          let passedRequirements = [];
          Object.entries(this.passwordRequirements).forEach(([, requirement]) => {
            if (requirement.regex.test(value)) {
              passedRequirements.push(requirement.code);
            }
          });
          this.passedRequirements = passedRequirements;
          return passedRequirements.length === Object.keys(this.passwordRequirements).length;
        })
      });
      try {
        await schema.validateAt(field, this.formData);
        delete this.errors[field];
      } catch (error) {
        this.errors[field] = error.message;
      }
      this.touched[field] = true;
    }
  });

  // assets/src/js/alpine/components/forms/lostPassword.js
  var LostPasswordHandler = class extends BaseFormHandler {
    getValidationSchema() {
      return window.yup.object().shape({
        email: window.yup.string().email("Your email address isn't valid.").required("This field is required.")
      });
    }
  };
  var lostPassword_default = () => ({
    formData: {
      email: ""
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    notice: "",
    handler: null,
    init() {
      this.handler = new LostPasswordHandler(this.formData, {});
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
    }
  });

  // assets/src/js/alpine/components/forms/resetPassword.js
  var ResetPasswordHandler = class extends BaseFormHandler {
    constructor(formData, additionalData = {}) {
      super(formData, additionalData);
      this.passedRequirements = [];
      this.passwordRequirements = {
        minLength: { regex: /.{8,}/, message: "At least 8 characters", code: "ERR_PASSWORD_MINLENGTH" },
        uppercase: { regex: /(?=.*[A-Z])/, message: "1 uppercase letter", code: "ERR_PASSWORD_UPPERCASE" },
        number: { regex: /(?=.*[0-9])/, message: "1 number", code: "ERR_PASSWORD_NUMBER" },
        lowercase: { regex: /(?=.*[a-z])/, message: "1 lowercase letter", code: "ERR_PASSWORD_LOWERCASE" }
      };
    }
    getValidationSchema() {
      return window.yup.object().shape({
        password: window.yup.string().required("This field is required.").test("password-complexity", "Your password does not meet the requirements.", (value) => {
          const passedRequirements = [];
          Object.values(this.passwordRequirements).forEach((requirement) => {
            if (requirement.regex.test(value || "")) {
              passedRequirements.push(requirement.code);
            }
          });
          this.passedRequirements = passedRequirements;
          return passedRequirements.length === Object.keys(this.passwordRequirements).length;
        })
      });
    }
  };
  var resetPassword_default = () => ({
    formData: {
      password: ""
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    notice: "",
    handler: null,
    passedRequirements: [],
    passwordRequirements: {},
    init() {
      this.handler = new ResetPasswordHandler(this.formData);
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
      this.$watch("handler.passedRequirements", (value) => {
        this.passedRequirements = value;
      });
      this.$watch("handler.passwordRequirements", (value) => {
        this.passwordRequirements = value;
      });
      this.passwordRequirements = this.handler.passwordRequirements;
    }
  });

  // assets/src/js/alpine/components/forms/auth.js
  function registerAuthFormComponents() {
    Alpine.data("login", login_default);
    Alpine.data("signup", signup_default);
    Alpine.data("lostPassword", lostPassword_default);
    Alpine.data("resetPassword", resetPassword_default);
  }

  // assets/src/js/alpine/entries/endpoint-auth.js
  registerAuthFormComponents();
  window.MyAccountAlpineRuntime?.start?.();
})();
//# sourceMappingURL=alpine.auth.js.map
