(() => {
  // assets/src/js/alpine/stores/userAddress.js
  var MAX_ADDRESSES = 9;
  var DEFAULT_COUNTRY = "US";
  var AJAX_ACTION = "save-address";
  var userAddress_default = {
    // State
    addresses: [],
    countries: {},
    ajaxUrl: "",
    nonce: "",
    preferredCountry: "",
    stopAdd: false,
    editAddress: null,
    form: {
      title: "",
      buttonSaveLabel: "",
      action: ""
    },
    saving: false,
    removing: false,
    activeAction: "",
    activeActionId: "",
    _inited: false,
    // ==================== Initialization ====================
    init() {
      if (this._inited) return;
      const data = window.scriptData || {};
      this.addresses = Array.isArray(data.addresses) ? data.addresses : [];
      this.countries = data.countries || {};
      this.ajaxUrl = data.ajaxUrl || window.ajaxurl || "/wp-admin/admin-ajax.php";
      this.nonce = data.nonce || "";
      this.preferredCountry = data.defaultCountry || "";
      this._inited = true;
      this.normalizeCountryValues();
      this.ensureOneDefault();
      this.checkMaxAddress();
    },
    normalizeCountryValues() {
      if (!Object.keys(this.countries).length) return;
      const fallbackCountry = this.getDefaultCountryCode();
      this.addresses = this.addresses.map((address) => {
        const countryCode = this._resolveCountryCode(address.country);
        return { ...address, country: countryCode || fallbackCountry };
      });
    },
    ensureOneDefault() {
      if (this.addresses.length === 0) return;
      const hasDefault = this.addresses.some((addr) => this._isDefault(addr));
      if (!hasDefault) {
        this.addresses[0].default = true;
      }
    },
    checkMaxAddress() {
      this.stopAdd = this.addresses.length >= MAX_ADDRESSES;
    },
    // ==================== Helpers ====================
    _normalizeId(id) {
      return String(id);
    },
    _setActiveAction(action = "", id = "") {
      this.activeAction = action;
      this.activeActionId = id ? this._normalizeId(id) : "";
    },
    isActionLoading(action, id) {
      return this.activeAction === action && this.activeActionId === this._normalizeId(id);
    },
    _isDefault(address) {
      return address.default === true || address.default === 1 || address.default === "1";
    },
    _getEmptyAddress(id = null) {
      return {
        id: id || this.generateUniqueKey(),
        fname: "",
        lname: "",
        phone: "",
        address: "",
        address2: "",
        city: "",
        region: "",
        postalCode: "",
        country: this.getDefaultCountryCode(),
        default: false
      };
    },
    getDefaultCountryCode() {
      const preferred = this._resolveCountryCode(this.preferredCountry);
      if (preferred) return preferred;
      if (this.countries[DEFAULT_COUNTRY]) return DEFAULT_COUNTRY;
      const firstCode = Object.keys(this.countries)[0];
      return firstCode || DEFAULT_COUNTRY;
    },
    getCountryLabel(countryCode) {
      return this.countries[countryCode] || countryCode || "";
    },
    _setFormMode(action, title, buttonLabel) {
      this.form.action = action;
      this.form.title = title;
      this.form.buttonSaveLabel = buttonLabel;
    },
    _findAddressIndex(id) {
      const idStr = this._normalizeId(id);
      return this.addresses.findIndex((addr) => this._normalizeId(addr.id) === idStr);
    },
    _updateDefaultFlags(addresses, defaultId) {
      const defaultIdStr = this._normalizeId(defaultId);
      addresses.forEach((addr) => {
        addr.default = this._normalizeId(addr.id) === defaultIdStr;
      });
    },
    generateUniqueKey() {
      const now = Date.now().toString();
      const random = Math.random().toString(36).substring(2, 15);
      return now + random;
    },
    // ==================== Public Methods ====================
    startAdd() {
      this._setFormMode("add", "Add Address", "Add Address");
      this.editAddress = this._getEmptyAddress();
    },
    startEdit(id) {
      this._setFormMode("edit", "Edit Address", "Update Address");
      const index = this._findAddressIndex(id);
      const address = index !== -1 ? this.addresses[index] : null;
      if (address) {
        const countryCode = this._resolveCountryCode(address.country);
        this.editAddress = {
          ...address,
          country: countryCode || this.getDefaultCountryCode()
        };
        return;
      }
      this.editAddress = this._getEmptyAddress();
    },
    _resolveCountryCode(value) {
      const raw = String(value || "").trim();
      if (!raw) return "";
      if (this.countries[raw]) return raw;
      const lower = raw.toLowerCase();
      for (const [code, label] of Object.entries(this.countries)) {
        if (String(label).toLowerCase() === lower) return code;
      }
      return "";
    },
    initEditAddressForm() {
      const normalizeCountry = () => {
        if (!this.editAddress) {
          this.editAddress = this._getEmptyAddress();
          return;
        }
        const resolved = this._resolveCountryCode(this.editAddress.country);
        this.editAddress.country = resolved || this.getDefaultCountryCode();
      };
      normalizeCountry();
      requestAnimationFrame(normalizeCountry);
      return !!this.editAddress?.address2;
    },
    async save() {
      const addresses = [...this.addresses];
      const editId = this._normalizeId(this.editAddress.id);
      if (this.form.action === "edit") {
        const index = this._findAddressIndex(this.editAddress.id);
        if (index !== -1) {
          addresses[index] = { ...this.editAddress };
        }
      } else if (this.form.action === "add") {
        addresses.push({ ...this.editAddress });
      }
      if (this.editAddress.default) {
        this._updateDefaultFlags(addresses, editId);
      }
      await this.ajaxRequest(AJAX_ACTION, addresses);
      this.addresses = addresses;
      this.checkMaxAddress();
    },
    async setDefault(id, syncToServer = false) {
      const idStr = this._normalizeId(id);
      this._setActiveAction("set-default", idStr);
      try {
        this._updateDefaultFlags(this.addresses, idStr);
        if (syncToServer) {
          await this.ajaxRequest(AJAX_ACTION, this.addresses);
        }
      } finally {
        this._setActiveAction();
      }
    },
    async remove(id) {
      const idStr = this._normalizeId(id);
      this._setActiveAction("delete", idStr);
      const addresses = this.addresses.filter((addr) => this._normalizeId(addr.id) !== idStr);
      this.removing = true;
      try {
        await this.ajaxRequest(AJAX_ACTION, addresses);
        this.addresses = addresses;
        Alpine.store("popup").closePopup();
        this.checkMaxAddress();
      } finally {
        this.removing = false;
        this._setActiveAction();
      }
    },
    // ==================== AJAX ====================
    async ajaxRequest(action, data, options = {}) {
      const {
        showToast = true,
        closePopup = true
      } = options;
      this.saving = true;
      try {
        const response = await fetch(this.ajaxUrl, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action,
            nonce: this.nonce,
            data: JSON.stringify(data)
          })
        });
        const result = await response.json();
        if (result.success) {
          const message = result.data && result.data.message || result.data;
          if (showToast) Alpine.store("toast").addToast(message, "success");
          if (closePopup) {
            this.editAddress = this._getEmptyAddress();
            Alpine.store("popup").closePopup();
          }
          return result;
        } else {
          const message = result.data && result.data.message || result.data;
          if (showToast) Alpine.store("toast").addToast(message, "error");
          throw new Error(typeof message === "string" ? message : "An error occurred");
        }
      } catch (error) {
        if (showToast) Alpine.store("toast").addToast("An error occurred", "error");
        console.error("AJAX error:", error);
        throw error;
      } finally {
        this.saving = false;
      }
    },
    // ==================== Phone Formatting ====================
    formatUSPhoneNumber() {
      let phone = this.editAddress?.phone || "";
      phone = phone.replace(/\D/g, "");
      if (phone.length > 10) phone = phone.slice(0, 10);
      if (phone.length >= 6) {
        this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3, 6)}-${phone.slice(6)}`;
      } else if (phone.length >= 3) {
        this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3)}`;
      } else {
        this.editAddress.phone = phone;
      }
    }
  };

  // assets/src/js/alpine/stores/address.js
  function registerAddressStore() {
    Alpine.store("userAddress", userAddress_default);
  }

  // assets/src/js/alpine/entries/endpoint-address.js
  registerAddressStore();
  window.MyAccountAlpineRuntime?.start?.();
})();
//# sourceMappingURL=alpine.address.js.map
