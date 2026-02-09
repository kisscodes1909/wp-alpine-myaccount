/**
 * User Address Store - Manage user addresses
 * Usage: Alpine.store('userAddress').startAdd() / startEdit(id) / save() / remove(id)
 * Requires: scriptData (addresses, countries, ajaxUrl) to be localized - call init() from Address Book page (x-init)
 */
export default {
    addresses: [],
    countries: {},
    ajaxUrl: '',
    stopAdd: false,
    editAddress: {
        fname: '',
        lname: '',
        phone: '',
        address: '',
        address2: '',
        city: '',
        region: '',
        postalCode: '',
        country: 'United States',
        default: false,
    },
    isEditing: false,
    form: {
        title: 'Add Address',
        buttonSaveLabel: 'Add',
        action: ''
    },
    _inited: false,

    init() {
        if (this._inited) return;
        const data = window.scriptData || {};
        this.addresses = data.addresses || [];
        this.countries = data.countries || {};
        this.ajaxUrl = data.ajaxUrl || window.ajaxurl || '/wp-admin/admin-ajax.php';
        window.countries = this.countries;
        this._inited = true;
        this.checkMaxAddress();
    },

    checkMaxAddress() {
        this.stopAdd = this.addresses.length >= 9;
    },

    generateUniqueKey() {
        const now = Date.now().toString();
        const random = Math.random().toString(36).substring(2, 15);
        return now + random;
    },

    startAdd() {
        this.form.title = 'Add Address';
        this.form.buttonSaveLabel = 'Add Address';
        this.form.action = 'add';

        this.editAddress = {
            id: this.generateUniqueKey(),
            fname: '',
            lname: '',
            phone: '',
            address: '',
            address2: '',
            city: '',
            region: '',
            postalCode: '',
            country: 'United States',
            default: false
        };
        this.isEditing = true;
    },

    startEdit(id) {
        this.form.title = 'Edit Address';
        this.form.buttonSaveLabel = 'Update Address';
        this.form.action = 'edit';

        const address = this.addresses.find(addr => addr.id === id);

        if (address) {
            this.editAddress = { ...address };
            this.isEditing = true;
        }
    },

    async save() {
        let addresses = [...this.addresses];

        if (this.form.action === 'edit') {
            const index = addresses.findIndex(addr => addr.id === this.editAddress.id);
            if (index !== -1) {
                addresses[index] = { ...this.editAddress };
            }
        } else if (this.form.action === 'add') {
            addresses.push({ ...this.editAddress });
        }

        if (this.editAddress.default) {
            addresses.forEach(addr => {
                addr.default = (addr.id === this.editAddress.id);
            });
        }

        await this.ajaxRequest('save-address', addresses);

        this.addresses = addresses;
        this.isEditing = false;
        this.checkMaxAddress();
    },

    async setDefault(id, syncToServer = false) {
        let addresses = [...this.addresses];
        const defaultIndex = addresses.findIndex(addr => addr.id === id);
        if (defaultIndex !== -1 && defaultIndex !== 0) {
            const temp = addresses[defaultIndex];
            addresses[defaultIndex] = addresses[0];
            addresses[0] = temp;
        }
        addresses.forEach((address) => {
            address.default = (address.id === id);
        });
        this.addresses = addresses;
        if (syncToServer) {
            await this.ajaxRequest('save-address', this.addresses);
        }
    },

    async remove(id) {
        const addresses = this.addresses.filter(addr => addr.id !== id);
        await this.ajaxRequest('save-address', addresses);

        this.addresses = addresses;
        this.isEditing = false;
        Alpine.store('popup').closePopup();
        this.checkMaxAddress();
    },

    async delete(id) {
        return this.remove(id);
    },

    async ajaxRequest(action, data) {
        Alpine.store('loader').show();

        try {
            const response = await fetch(this.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: action,
                    data: JSON.stringify(data)
                })
            });

            const responseData = await response.json();

            if (responseData.success) {
                Alpine.store('toast').addToast(responseData.data, 'success');
                this.editAddress = {};
                Alpine.store('popup').closePopup();
            } else {
                Alpine.store('toast').addToast(responseData.data, 'error');
            }
        } catch (error) {
            console.error('AJAX error:', error);
            Alpine.store('toast').addToast('An error occurred', 'error');
        }

        Alpine.store('loader').hide();
    },

    formatUSPhoneNumber() {
        let phone = this.editAddress.phone || '';
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

    transformToGoogleAutoComplete(el) {
        const addressEl = document.getElementById('address');

        if (!addressEl) return;

        const addressAutoComplete = new google.maps.places.Autocomplete(addressEl, {});
        addressAutoComplete.setComponentRestrictions({ country: 'us' });

        addressAutoComplete.addListener('place_changed', () => {
            const place = addressAutoComplete.getPlace();
            this.editAddress.address = '';
            this.editAddress.city = '';
            this.editAddress.region = '';
            this.editAddress.postalCode = '';

            if (place.address_components) {
                place.address_components.forEach(component => {
                    const types = component.types;

                    if (types.includes('street_number')) {
                        this.editAddress.address = component.long_name + ' ';
                    }
                    if (types.includes('route')) {
                        this.editAddress.address += component.short_name;
                    }
                    if (types.includes('locality')) {
                        this.editAddress.city = component.long_name;
                    }
                    if (types.includes('administrative_area_level_1')) {
                        this.editAddress.region = component.short_name;
                    }
                    if (types.includes('postal_code')) {
                        this.editAddress.postalCode = component.long_name;
                    }
                });
            }
        });
    },
};
