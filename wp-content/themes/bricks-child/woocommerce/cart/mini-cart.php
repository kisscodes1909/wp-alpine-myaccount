<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
    <div class="pb-4 border-b flex flex-row justify-between" x-data>
<!--		--><?php
//		/**
//		 * Hook: woocommerce_widget_shopping_cart_total.
//		 *
//		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
//		 */
//		do_action( 'woocommerce_widget_shopping_cart_total' );
//		?>
        <div class="flex flex-row items-center gap-5">
            <button class="lg:w-[23px] lg:h-[23px]" @click="jQuery('.brxe-woocommerce-mini-cart').removeClass(['show-cart-details']); jQuery('.cart-detail').removeClass('active')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="lg:`w-[23px]` lg:h-[23px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="font-bold text-xl">Cart</span>
        </div>
        <div class="flex flex-row gap-6 font-bold">
            <span id="mini-cart-total-items"><?php echo WC()->cart->get_cart_contents_count(); ?> Items</span>
            |
            <span><?php echo WC()->cart->get_cart_subtotal() ?></span>
        </div>

    </div>

	<?php
	wc_get_template('cart/free-shipping-bar.php');
	?>

	<ul class="flex-1 divide-y space-y-7 woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

//			var_dump($_product->get_id());

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

                ?>
				<li x-data="{productId:<?php echo $_product->get_id() ?>}" class="woocommerce-mini-cart-item pt-7 <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <div class="font-bold leading-8 mb-7"><?php echo wp_kses_post( $product_name ) . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-5">
	                        <?php if ( empty( $product_permalink ) ) : ?>
		                        <?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	                        <?php else : ?>
                            <a href="<?php echo esc_url( $product_permalink ); ?>">
		                        <?php echo $thumbnail // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
	                        <?php endif; ?>
                        </div>
                        <div class="col-span-7">
	                        <?php echo get_variation_attributes_html($cart_item['data']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <div class="font-bold"><?php echo $product_price; ?></div>

                            <div class="flex flex-row gap-5 items-center mt-6">
                               <div class="lg:h-[23px]">
	                               <?php wc_get_template('ui/apl-wishlist-button.php'); ?>
                               </div>

                                <?php

                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove_from_cart_button lg:h-[23px]" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">%s</a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        /* translators: %s is the product name */
                                        esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                                        esc_attr( $product_id ),
                                        esc_attr( $cart_item_key ),
                                        esc_attr( $_product->get_sku() ),
                                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        '

                                    ),
                                    $cart_item_key
                                );
                                ?>
                                <div class="flex flex-row font-bold items-center relative">
<!--                                    <label class="mb-0 text-charcoal font-bold">Qty: </label>-->
                                    <select class="cartQty block w-full h-full bg-transparent appearance-none border-none opacity-0 absolute left-0 top-0 text-sm" data-key="<?php echo $cart_item_key; ?>" value="<?php echo $cart_item['quantity']; ?>">
			                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php selected($cart_item['quantity'], $i); ?>><?php echo $i; ?></option>
			                            <?php endfor; ?>
                                    </select>
                                    <span class="flex flex-row items-center">
                                        Qty: <?php echo $cart_item['quantity']; ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="buttons flex justify-between"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>

<script>
    jQuery(document).ready(function($) {
        $('.cartQty').on('change', function() {

            const newQty = $(this).val();
            const cartItemKey = $(this).data('key');
            $row        = $( this ).closest( '.woocommerce-mini-cart-item' );

            $row.block({
                message: null,
                overlayCSS: {
                    opacity: 0.6
                }
            });

            $.ajax({
                url: '/?wc-ajax=update_cart_item_quantity',
                type: 'POST',
                data: {
                    // action: 'update_cart_item_quantity',
                    cart_item_key: cartItemKey,
                    quantity: newQty
                },
                success: function(response) {
                    $.each(response.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });

                    $row.unblock();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ', error);
                }
            });
        });
    });
</script>
