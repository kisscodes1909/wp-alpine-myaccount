/**
 * User Address Store - Manage user addresses
 * Usage: Alpine.store('userAddress').startAdd() / startEdit(id) / save() / remove(id)
 * Requires: scriptData (addresses, countries, ajaxUrl) to be localized - call init() from Address Book page (x-init)
 */

// Constants
const MAX_ADDRESSES = 9;
const DEFAULT_COUNTRY = 'United States';
const AJAX_ACTION = 'save-address';

// Google Places Autocomplete constants
const AUTOCOMPLETE_INIT_DELAY = 150; // Delay for popup content to render in DOM
const GOOGLE_API_RETRY_DELAY = 500; // Delay before retrying Google API load
const GOOGLE_API_MAX_RETRIES = 10; // Max retries (5 seconds total)
const POPUP_SELECTOR = '#popup-container';
const AUTOCOMPLETE_WRAPPER_SELECTOR = '.address-autocomplete-wrapper';

// Address component type mapping for Google Places API (new)
const ADDRESS_COMPONENT_MAP = {
    street_number: { field: 'address', prefix: true },
    route: { field: 'address', append: true },
    locality: { field: 'city' },
    administrative_area_level_1: { field: 'region', shortName: true },
    postal_code: { field: 'postalCode' }
};

export default {
    // State
    addresses: [],
    countries: {},
    ajaxUrl: '',
    nonce: '',
    stopAdd: false,
    editAddress: null,
    isEditing: false,
    form: {
        title: '',
        buttonSaveLabel: '',
        action: ''
    },
    _inited: false,
    _autocompleteInstance: null,
    _googleApiRetries: 0,

    // ==================== Initialization ====================

    init() {
        if (this._inited) return;

        const data = window.scriptData || {};
        this.addresses = Array.isArray(data.addresses) ? data.addresses : [];
        this.countries = data.countries || {};
        this.ajaxUrl = data.ajaxUrl || window.ajaxurl || '/wp-admin/admin-ajax.php';
        this.nonce = data.nonce || '';

        this._inited = true;
        this.ensureOneDefault();
        this.checkMaxAddress();
    },

    ensureOneDefault() {
        if (this.addresses.length === 0) return;

        const hasDefault = this.addresses.some(addr => this._isDefault(addr));
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

    _isDefault(address) {
        return address.default === true || address.default === 1 || address.default === '1';
    },

    _getEmptyAddress(id = null) {
        return {
            id: id || this.generateUniqueKey(),
            fname: '',
            lname: '',
            phone: '',
            address: '',
            address2: '',
            city: '',
            region: '',
            postalCode: '',
            country: DEFAULT_COUNTRY,
            default: false
        };
    },

    _setFormMode(action, title, buttonLabel) {
        this.form.action = action;
        this.form.title = title;
        this.form.buttonSaveLabel = buttonLabel;
    },

    _findAddressById(id) {
        const idStr = this._normalizeId(id);
        return this.addresses.find(addr => this._normalizeId(addr.id) === idStr);
    },

    _findAddressIndex(id) {
        const idStr = this._normalizeId(id);
        return this.addresses.findIndex(addr => this._normalizeId(addr.id) === idStr);
    },

    _updateDefaultFlags(addresses, defaultId) {
        const defaultIdStr = this._normalizeId(defaultId);
        addresses.forEach(addr => {
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
        this._setFormMode('add', 'Add Address', 'Add Address');
        this.editAddress = this._getEmptyAddress();
        this.isEditing = true;
        this._cleanupAutocomplete();
        this._googleApiRetries = 0;
        // Init autocomplete after popup content is in DOM (x-html doesn't run x-init)
        setTimeout(() => this.initAutocomplete(), AUTOCOMPLETE_INIT_DELAY);
    },

    startEdit(id) {
        this._setFormMode('edit', 'Edit Address', 'Update Address');
        const address = this._findAddressById(id);

        if (address) {
            this.editAddress = { ...address };
            this.isEditing = true;
            this._cleanupAutocomplete();
            this._googleApiRetries = 0;
            setTimeout(() => this.initAutocomplete(), AUTOCOMPLETE_INIT_DELAY);
        }
    },

    async save() {
        const addresses = [...this.addresses];
        const editId = this._normalizeId(this.editAddress.id);

        // Update existing or add new
        if (this.form.action === 'edit') {
            const index = this._findAddressIndex(this.editAddress.id);
            if (index !== -1) {
                addresses[index] = { ...this.editAddress };
            }
        } else if (this.form.action === 'add') {
            addresses.push({ ...this.editAddress });
        }

        // Update default flags if this address is set as default
        if (this.editAddress.default) {
            this._updateDefaultFlags(addresses, editId);
        }

        await this.ajaxRequest(AJAX_ACTION, addresses);

        this.addresses = addresses;
        this.isEditing = false;
        this.checkMaxAddress();
    },

    async setDefault(id, syncToServer = false) {
        this._updateDefaultFlags(this.addresses, id);

        if (syncToServer) {
            await this.ajaxRequest(AJAX_ACTION, this.addresses);
        }
    },

    async remove(id) {
        const idStr = this._normalizeId(id);
        const addresses = this.addresses.filter(addr => this._normalizeId(addr.id) !== idStr);

        await this.ajaxRequest(AJAX_ACTION, addresses);

        this.addresses = addresses;
        this.isEditing = false;
        Alpine.store('popup').closePopup();
        this.checkMaxAddress();
    },

    async delete(id) {
        return this.remove(id);
    },

    // ==================== AJAX ====================

    async _sendRequest(action, data) {
        const response = await fetch(this.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action,
                nonce: this.nonce,
                data: JSON.stringify(data)
            })
        });
        return await response.json();
    },

    async ajaxRequest(action, data, options = {}) {
        const {
            showLoader = true,
            showToast = true,
            closePopup = true
        } = options;

        if (showLoader) Alpine.store('loader').show();

        try {
            const result = await this._sendRequest(action, data);

            if (showLoader) Alpine.store('loader').hide();

            if (result.success) {
                if (showToast) Alpine.store('toast').addToast(result.data, 'success');
                if (closePopup) {
                    this.editAddress = this._getEmptyAddress();
                    Alpine.store('popup').closePopup();
                }
                return result;
            } else {
                if (showToast) Alpine.store('toast').addToast(result.data, 'error');
                throw new Error(result.data);
            }
        } catch (error) {
            if (showLoader) Alpine.store('loader').hide();
            if (showToast) Alpine.store('toast').addToast('An error occurred', 'error');
            console.error('AJAX error:', error);
            throw error;
        }
    },

    // ==================== Phone Formatting ====================

    formatUSPhoneNumber() {
        let phone = this.editAddress?.phone || '';
        phone = phone.replace(/\D/g, '');

        if (phone.length > 10) phone = phone.slice(0, 10);

        if (phone.length >= 6) {
            this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3, 6)}-${phone.slice(6)}`;
        } else if (phone.length >= 3) {
            this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3)}`;
        } else {
            this.editAddress.phone = phone;
        }
    },

    // ==================== Places API (New) – PlaceAutocompleteElement (see place-autocomplete-new doc) ====================

    async initAutocomplete() {
        this._cleanupAutocomplete();

        // Check if Google Maps API is loaded
        if (typeof google === 'undefined' || !google.maps) {
            if (this._googleApiRetries < GOOGLE_API_MAX_RETRIES) {
                this._googleApiRetries++;
                setTimeout(() => this.initAutocomplete(), GOOGLE_API_RETRY_DELAY);
            } else {
                console.error('Google Maps API failed to load after max retries');
            }
            return;
        }

        const popup = document.querySelector(POPUP_SELECTOR);
        const wrapper = popup?.querySelector(AUTOCOMPLETE_WRAPPER_SELECTOR);
        if (!wrapper) {
            console.warn('Autocomplete wrapper not found in popup');
            return;
        }

        try {
            const { PlaceAutocompleteElement } = await google.maps.importLibrary('places');
            const placeAutocomplete = new PlaceAutocompleteElement({
                placeholder: 'Start typing your address...'
            });
            placeAutocomplete.includedRegionCodes = ['us'];

            placeAutocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await this._handlePlaceSelectNew(place);
            });

            wrapper.innerHTML = '';
            wrapper.appendChild(placeAutocomplete);
            this._autocompleteInstance = placeAutocomplete;
            this._googleApiRetries = 0; // Reset on success
        } catch (err) {
            console.error('Places API autocomplete init error:', err);
        }
    },

    async _handlePlaceSelectNew(place) {
        if (!place || !this.editAddress) {
            console.warn('Invalid place or editAddress is null');
            return;
        }

        this._resetAddressFields();

        try {
            await place.fetchFields({ fields: ['addressComponents', 'formattedAddress'] });
        } catch (e) {
            console.error('Failed to fetch place fields:', e);
            Alpine.store('toast')?.addToast('Failed to load address details', 'error');
            return;
        }

        // Parse address components
        if (place.addressComponents?.length) {
            this._parseAddressComponentsNew(place.addressComponents);
        } else if (place.formattedAddress) {
            // Fallback: use formatted address if components not available
            this.editAddress.address = place.formattedAddress;
        }
    },

    _parseAddressComponentsNew(components) {
        if (!this.editAddress) return;
        components.forEach((component) => {
            if (!component.types?.length) return;
            component.types.forEach((type) => {
                const mapping = ADDRESS_COMPONENT_MAP[type];
                if (!mapping) return;
                const value = mapping.shortName ? (component.shortText || '') : (component.longText || '');
                if (!value) return;
                if (mapping.append) {
                    this.editAddress[mapping.field] += value;
                } else if (mapping.prefix) {
                    this.editAddress[mapping.field] = value + ' ';
                } else {
                    this.editAddress[mapping.field] = value;
                }
            });
        });
        if (this.editAddress.address) {
            this.editAddress.address = this.editAddress.address.trim();
        }
    },

    _cleanupAutocomplete() {
        if (!this._autocompleteInstance) return;

        try {
            const popup = document.querySelector(POPUP_SELECTOR);
            const wrapper = popup?.querySelector(AUTOCOMPLETE_WRAPPER_SELECTOR);
            if (wrapper && this._autocompleteInstance.parentNode === wrapper) {
                wrapper.removeChild(this._autocompleteInstance);
            }
        } catch (e) {
            console.warn('Autocomplete cleanup error:', e);
        }

        this._autocompleteInstance = null;
    },

    _resetAddressFields() {
        if (!this.editAddress) return;

        this.editAddress.address = '';
        this.editAddress.city = '';
        this.editAddress.region = '';
        this.editAddress.postalCode = '';
    }
};
