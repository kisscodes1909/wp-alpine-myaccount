/**
 * Wishlist Store - Manage user wishlist items
 * Usage: Alpine.store('wishlist').addItem(id) / removeItem(id)
 * Note: Requires wishlistData to be localized (addItemNonce, removeItemNonce)
 */
export default {
    items: [],
    
    init() {
        // Get nonce data from localized script
        const { addItemNonce, removeItemNonce, isLoggedIn } = window.wishlistData || {};
        this._addItemNonce = addItemNonce;
        this._removeItemNonce = removeItemNonce;

        // Remove wishlist and refresh wishlist after login success
        window.addEventListener('handle_login_success', () => {
            sessionStorage.removeItem('wishlist');
        });

        if (!this.isUserLoggedIn()) return;

        // Check session storage and load state
        if (sessionStorage.getItem('wishlist')) {
            this.items = JSON.parse(sessionStorage.getItem('wishlist'));
        } else {
            this.fetchWishlist();
        }

        // Watch for changes in items array and save to session storage
        Alpine.effect(() => {
            sessionStorage.setItem('wishlist', JSON.stringify(this.items));
        });
    },

    addItem(id) {
        if(!this.isUserLoggedIn()) {
            Alpine.store('popup').openPopup(document.getElementById('login-form').innerHTML);
            return;
        }

        if (!this.items.includes(id)) {
            this.items.push(id);
            this.syncWithServer(id, 'add');
        }
    },

    removeItem(id) {
        if(!this.isUserLoggedIn()) {
            Alpine.store('popup').openPopup(document.getElementById('login-form').innerHTML);
            return;
        }

        this.items = this.items.filter(itemId => itemId !== id);
        this.syncWithServer(id, 'remove');
    },

    fetchWishlist() {
        wp.ajax.send('get_wishlist', {
            success: (data) =>  {
                this.items = Object.values(data);
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    },

    syncWithServer(id, action) {
        const wp_action = action === 'add' ? 'wishlist_add_item' : 'wishlist_remove_item';

        const params = action === 'add' ?  {
            add_to_wishlist: id,
            nonce: this._addItemNonce
        } : {
            remove_from_wishlist: id,
            nonce: this._removeItemNonce
        };

        wp.ajax.post(wp_action, params)
        .done((data) => {
            this.items = Object.values(data);
            if (action === 'add') {
                Alpine.store('toast').addToast('Item added to your wishlist');
            } else if (action === 'remove') {
                Alpine.store('toast').addToast('Item removed from your wishlist');
            }
        })
        .fail((error) => {
            console.error('Error:', error);
            // Rollback state
            if (action === 'add') {
                this.items = this.items.filter(i => i.id !== id);
            } else if (action === 'remove') {
                this.items.push(id);
            }
        });
    },

    isUserLoggedIn() {
        return document.body.classList.contains('logged-in');
    },
};
