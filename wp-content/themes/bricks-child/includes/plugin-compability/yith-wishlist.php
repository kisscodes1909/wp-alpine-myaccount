<?php

class Yith_Wishlist {
	public $yith_frontend;
    public function __construct()
    {
	    $this->yith_frontend = YITH_WCWL_Frontend();

	    add_filter('yith_wcwl_wishlist_page_url', [$this, 'change_base_remove_url'], 10, 2);
	    add_action('woocommerce_after_add_to_cart_button', [$this, 'wishlist_btn']);
	    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);


	    remove_action( 'init', array( $this->yith_frontend, 'add_button' ));
	    remove_action( 'wp_enqueue_scripts', array( $this->yith_frontend, 'enqueue_styles_and_stuffs' ) );
	    remove_action( 'init', array( $this->yith_frontend, 'init' ), 0 );

    }

	function enqueue_scripts(): void
	{
		wp_register_script('wishlist', CHILD_URL . '/assets/js/wishlist.js', [], filemtime(CHILD_DIR . '/assets/js/wishlist.js'));

		wp_enqueue_script('wishlist');

		wp_localize_script('wishlist', 'wishlistData', [
			'addItemNonce' => wp_create_nonce('wishlist-add-item'),
			'removeItemNonce' => wp_create_nonce('wishlist-remove-item'),
			'isLoggedIn' => is_user_logged_in(),
		]);
	}

    function change_base_remove_url($base_url, $action): string
    {

        if(is_wc_endpoint_url('wishlist')) {
            $base_url = wc_get_endpoint_url('wishlist');
        }

        return $base_url;

    }

	function wishlist_btn() {
		global $product;

		?>
		<script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('singleProduct', () => ({
                    productId: null,
                    init() {
                        jQuery('form.variations_form').on('found_variation', (event, variation) => {
                            this.productId = variation.variation_id
                        });
                    }
                }))
            })
		</script>

		<div x-data="singleProduct">
			<?php wc_get_template('ui/apl-wishlist-button.php'); ?>
		</div>
		<?php
	}
}

new Yith_Wishlist();

