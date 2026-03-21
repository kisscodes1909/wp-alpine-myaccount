(() => {
  // assets/src/js/alpine/components/account/viewOrderReturns.js
  function getReturnsData() {
    return window.viewOrderReturnsData || {};
  }
  function getStore(name) {
    if (!window.Alpine || typeof window.Alpine.store !== "function") {
      return null;
    }
    return window.Alpine.store(name);
  }
  function createInitialQtyMap(items) {
    return items.reduce((acc, item) => {
      acc[item.item_id] = 0;
      return acc;
    }, {});
  }
  function buildSelectedItems(form, eligibleItems) {
    return eligibleItems.map((item) => ({
      item_id: item.item_id,
      qty: Number(form.itemQty[item.item_id] || 0),
      remaining_qty: Number(item.remaining_qty || 0)
    })).filter((item) => item.qty > 0);
  }
  function validateSelection(form, eligibleItems, i18n = {}) {
    const selectedItems = buildSelectedItems(form, eligibleItems);
    if (selectedItems.length === 0) {
      return i18n.selectItem || "Please select at least one item.";
    }
    const invalidQty = selectedItems.some((item) => item.qty < 1 || item.qty > item.remaining_qty);
    if (invalidQty) {
      return i18n.invalidQuantity || "One or more quantities are not valid for return.";
    }
    if (!String(form.reason || "").trim()) {
      return i18n.missingReason || "Please add a reason for the request.";
    }
    return "";
  }
  function viewOrderReturnsForm() {
    const data = getReturnsData();
    const eligibleItems = Array.isArray(data.eligibleItems) ? data.eligibleItems : [];
    const initialRequestType = Object.keys(data.requestTypes || {})[0] || "return";
    return {
      eligibleItems,
      isSubmitting: false,
      errorMessage: "",
      form: {
        requestType: initialRequestType,
        reason: "",
        note: "",
        itemQty: createInitialQtyMap(eligibleItems)
      },
      resetForm() {
        this.form = {
          requestType: initialRequestType,
          reason: "",
          note: "",
          itemQty: createInitialQtyMap(this.eligibleItems)
        };
        this.errorMessage = "";
      },
      async handleSubmit() {
        this.errorMessage = validateSelection(this.form, this.eligibleItems, data.i18n || {});
        if (this.errorMessage) {
          getStore("toast")?.addToast(this.errorMessage, "error");
          return;
        }
        this.isSubmitting = true;
        const payload = {
          orderId: data.orderId,
          nonce: data.nonce,
          requestType: this.form.requestType,
          reason: this.form.reason.trim(),
          note: this.form.note.trim(),
          items: JSON.stringify(buildSelectedItems(this.form, this.eligibleItems).map((item) => ({
            item_id: item.item_id,
            qty: item.qty
          })))
        };
        window.wp.ajax.post("submit_return_request", payload).done((response) => {
          this.isSubmitting = false;
          this.errorMessage = "";
          const request = response?.data?.request;
          if (request) {
            window.dispatchEvent(new CustomEvent("myaccount_core_return_request_created", {
              detail: { request }
            }));
          }
          const message = response?.data?.message || "Your return request has been submitted.";
          getStore("toast")?.addToast(message, "success");
          getStore("popup")?.closePopup();
          window.setTimeout(() => window.location.reload(), 150);
        }).fail((error) => {
          this.isSubmitting = false;
          this.errorMessage = error?.responseJSON?.data?.message || data.i18n?.genericError || "Something went wrong. Please try again.";
          getStore("toast")?.addToast(this.errorMessage, "error");
        });
      }
    };
  }
  function viewOrderReturns() {
    const data = getReturnsData();
    const initialRequests = Array.isArray(data.requests) ? data.requests : [];
    const initialEligibleItems = Array.isArray(data.eligibleItems) ? data.eligibleItems : [];
    return {
      requests: initialRequests,
      eligibleItems: initialEligibleItems,
      policy: data.policy || {},
      canSubmit: Boolean(data.policy?.is_eligible) && initialEligibleItems.length > 0,
      init() {
        this.onRequestCreated = (event) => {
          const request = event?.detail?.request;
          if (!request) {
            return;
          }
          this.requests = [request, ...this.requests];
          this.refreshEligibleItemsFromRequest(request);
        };
        window.addEventListener("myaccount_core_return_request_created", this.onRequestCreated);
      },
      destroy() {
        if (this.onRequestCreated) {
          window.removeEventListener("myaccount_core_return_request_created", this.onRequestCreated);
        }
      },
      get policyMessage() {
        if (!this.canSubmit) {
          return this.policy?.message || "";
        }
        if (this.eligibleItems.length === 0) {
          return "No items from this order are currently eligible for return.";
        }
        return "";
      },
      formatRequestId(id) {
        if (!id) {
          return "";
        }
        return `#${String(id).slice(0, 8).toUpperCase()}`;
      },
      openPopup() {
        if (!this.canSubmit) {
          return;
        }
        const templateId = `ma-view-order-returns-popup-template-${data.orderId}`;
        const template = document.getElementById(templateId);
        if (!template) {
          return;
        }
        getStore("popup")?.openPopup(template.innerHTML);
      },
      refreshEligibleItemsFromRequest(request) {
        if (!request || request.status === "rejected") {
          return;
        }
        const requestedItems = Array.isArray(request.items) ? request.items : [];
        this.eligibleItems = this.eligibleItems.map((item) => {
          const requested = requestedItems.find((candidate) => Number(candidate.item_id) === Number(item.item_id));
          if (!requested) {
            return item;
          }
          const remainingQty = Math.max(0, Number(item.remaining_qty || 0) - Number(requested.qty || 0));
          return {
            ...item,
            remaining_qty: remainingQty
          };
        }).filter((item) => item.remaining_qty > 0);
        data.eligibleItems = this.eligibleItems;
        this.canSubmit = Boolean(this.policy?.is_eligible) && this.eligibleItems.length > 0;
      }
    };
  }

  // assets/src/js/alpine/components/account/index.js
  function registerAccountComponents() {
    const AlpineInstance = window.Alpine;
    if (!AlpineInstance || typeof AlpineInstance.data !== "function") {
      return;
    }
    AlpineInstance.data("viewOrderReturns", viewOrderReturns);
    AlpineInstance.data("viewOrderReturnsForm", viewOrderReturnsForm);
  }

  // assets/src/js/alpine/entries/endpoint-view-order.js
  registerAccountComponents();
  window.MyAccountAlpineRuntime?.start?.();
})();
//# sourceMappingURL=alpine.view-order.js.map
