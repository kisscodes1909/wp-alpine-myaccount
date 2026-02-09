/**
 * DEPRECATED: Wishlist store has been moved to:
 * - assets/js/alpine/stores/wishlist.js
 * 
 * This file is kept for backward compatibility only.
 * The store is now registered via alpine/init.js
 * 
 * TODO: Remove this file after confirming all references are updated.
 */

// Store is now registered in alpine/stores/wishlist.js
// Keeping commented code for reference:
/*
const {addItemNonce, removeItemNonce, isLoggedIn} = wishlistData;

document.addEventListener('alpine:init', () => {
    Alpine.store('wishlist', {
        items: [],
        init() {

            // Remove wishlist and refresh wishlist after login success.
            window.addEventListener('handle_login_success', () => {
                sessionStorage.removeItem('wishlist');
            });

            if (!this.isUserLoggedIn()) return;

            // Kiểm tra session storage và load state
            if (sessionStorage.getItem('wishlist')) {
                this.items = JSON.parse(sessionStorage.getItem('wishlist'));
            } else {
                this.fetchWishlist();
            }

            // Watch for changes in items array and save to session storage
            // Sử dụng Alpine.effect để theo dõi và cập nhật session storage
            Alpine.effect(() => {
                sessionStorage.setItem('wishlist', JSON.stringify(this.items));
            });
        },

        // TODO: Refactory duplicate code
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
                nonce:addItemNonce
            } : {
                remove_from_wishlist: id,
                nonce:removeItemNonce
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
                // Back state
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
    });
})
*/;