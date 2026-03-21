<?php
/**
 * Cart abandonment email template with coupon.
 *
 * @package    Wt_Smart_Coupon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$coupon_data = Wt_Smart_Coupon_Public::get_coupon_meta_data( $coupon );
if ( ! $coupon_data || ! is_array( $coupon_data ) ) {
	$coupon_data = array(
		'coupon_code'        => __( 'coupon-code', 'decorator-woocommerce-email-customizer' ),
		'coupon_amount'      => 10,
		'coupon_description' => __( 'This is a sample coupon description', 'decorator-woocommerce-email-customizer' ),
		'coupon_type'        => __( 'Cart discount', 'decorator-woocommerce-email-customizer' ),
		'is_email_preview'   => true,
	);
	$coupon_code = $coupon_data['coupon_code'];
} else {
	$coupon_code = $coupon->get_code();
}

/**
 * Action hook to trigger before coupon email
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

/**
 * Action hook to execute the email body content
 *
 * @since 1.0.0
 */
do_action( 'wt_decorator_email_body_content', $coupon, $sent_to_admin, $plain_text, $email );
?>

<?php
if ( ! empty( $cart_data['cart'] ) ) :
	?>
	<table cellspacing="0" style="width: 100%; border: 1px solid #e5e5e5; font-size: 13px;">
		<thead>
			<tr style="background-color: #f8f8f8">
				<th style="text-align: left; border-bottom: 1px solid #e5e5e5; padding: 12px;"><?php esc_html_e( 'Image', 'decorator-woocommerce-email-customizer' ); ?></th>
				<th style="text-align: left; border-bottom: 1px solid #e5e5e5; padding: 12px;"><?php esc_html_e( 'Product', 'decorator-woocommerce-email-customizer' ); ?></th>
				<th style="text-align: left; border-bottom: 1px solid #e5e5e5; padding: 12px;"><?php esc_html_e( 'Price', 'decorator-woocommerce-email-customizer' ); ?></th>
				<th style="text-align: left; border-bottom: 1px solid #e5e5e5; padding: 12px;"><?php esc_html_e( 'Quantity', 'decorator-woocommerce-email-customizer' ); ?></th>
				<th style="text-align: right; border-bottom: 1px solid #e5e5e5; padding: 12px;"><?php esc_html_e( 'Subtotal', 'decorator-woocommerce-email-customizer' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$cart_total = 0;
			$tax_total  = 0;
			foreach ( $cart_data['cart'] as $cart_item_key => $cart_item ) :
				$_product = wc_get_product( $cart_item['product_id'] );

				/**
				 * Filter the cart item product.
				 *
				 * @since 1.0.0
				 *
				 * @param object $_product The cart item product.
				 * @param array $cart_item The cart item.
				 * @param string $cart_item_key The cart item key.
				 */
				$_product = apply_filters( 'woocommerce_cart_item_product', $_product, $cart_item, $cart_item_key );

				/**
				 * Filter the cart item product ID.
				 *
				 * @since 1.0.0
				 *
				 * @param int $product_id The cart item product ID.
				 * @param array $cart_item The cart item.
				 * @param string $cart_item_key The cart item key.
				 */
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				/**
				 * Filter the cart item name.
				 *
				 * @since 1.0.0
				 *
				 * @param string $product_name The cart item name.
				 * @param array $cart_item The cart item.
				 * @param string $cart_item_key The cart item key.
				 */
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

				/**
				 * Filter the cart item visibility.
				 *
				 * @since 1.0.0
				 *
				 * @param bool $visible Whether the cart item is visible.
				 * @param array $cart_item The cart item.
				 * @param string $cart_item_key The cart item key.
				 */
				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
					$unit_price  = $cart_item['line_subtotal'] / $cart_item['quantity'];
					$cart_total += $cart_item['line_total'];
					$tax_total  += $cart_item['line_tax'];
					?>
					<tr>
						<td style="border-bottom: 1px solid #e5e5e5; padding: 12px;">
							<?php
							/**
							 * Filter the cart item thumbnail.
							 *
							 * @since 1.0.0
							 *
							 * @param string $thumbnail The cart item thumbnail.
							 */
							echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 80, 80 ) ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
							?>
						</td>
						<td style="border-bottom: 1px solid #e5e5e5; padding: 12px;">
							<?php echo wp_kses_post( $product_name ); ?>
						</td>
						<td style="border-bottom: 1px solid #e5e5e5; padding: 12px;">
							<?php echo wp_kses_post( wc_price( $unit_price ) ); ?>
						</td>
						<td style="border-bottom: 1px solid #e5e5e5; padding: 12px;">
							<?php echo esc_html( $cart_item['quantity'] ); ?>
						</td>
						<td style="border-bottom: 1px solid #e5e5e5; padding: 12px;">
							<?php echo wp_kses_post( wc_price( $cart_item['line_subtotal'] ) ); ?>
						</td>
					</tr>
					<?php
				endif;
			endforeach;
			$grand_total = $cart_total + $tax_total;
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right; border-top: 1px solid #e5e5e5; padding: 12px;">
					<strong>
					<?php
					if ( $tax_total > 0 ) {
						esc_html_e( 'Total (including tax):', 'decorator-woocommerce-email-customizer' );
					} else {
						esc_html_e( 'Total:', 'decorator-woocommerce-email-customizer' );
					}
					?>
					</strong>
				</td>
				<td style="text-align: left; border-top: 1px solid #e5e5e5; padding: 12px 12px 12px 0;">
					<?php echo wp_kses_post( wc_price( $grand_total ) ); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php
endif;
?>

<div style="margin-top: 20px; text-align: center;">
	<div style="display: inline-block;">
		<?php
		echo Wt_Smart_Coupon_Public::get_coupon_html( $coupon, $coupon_data, 'email_coupon' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>

<p style="margin-top: 16px;">
	<?php
	esc_html_e( 'Just enter the code at checkout & SAVE BIG! This deal won\'t last—complete your order before it\'s too late!', 'decorator-woocommerce-email-customizer' );
	?>
</p>

<div style="margin: 16px 0;" class="wbte_decorator_button_container">
	<?php
	$style = 'display: inline-block; background: #3175A6; border-radius: 4px; color:#ffffff; text-decoration:none; padding: 8px 12px; text-align:center; font-weight: 500; font-size: 14px; font-family: Inter, sans-serif; border: 0px solid #3175A6';
	/**
	 * Alter the cart abandonment email button style.
	 *
	 * @since 1.0.0
	 *
	 * @param string $style The button style.
	 * @param object $coupon The coupon object.
	 */
	$style    = apply_filters( 'wt_sc_alter_abandonment_email_button_style', $style, $coupon );
	$cart_url = add_query_arg( 'wt_coupon', $coupon_code, wc_get_cart_url() );
	?>
	<span>
		<a href="<?php echo esc_url( $cart_url ); ?>">
			<button type="button" class="button" style="<?php echo esc_attr( $style ); ?>"><?php esc_html_e( 'Claim My Discount Now!', 'decorator-woocommerce-email-customizer' ); ?></button>
		</a>
	</span>
	
</div>

<p>
	<?php
	printf(
		// translators: %s: site name.
		esc_html__( 'Thank you for choosing %s! We look forward to serving you again.', 'decorator-woocommerce-email-customizer' ),
		esc_html( get_bloginfo( 'name' ) )
	);
	?>
</p>

<p>
	<?php
	esc_html_e( 'If you\'ve already checked out or no longer want these items, you can ignore this email( but we hope you don\'t!).', 'decorator-woocommerce-email-customizer' );
	?>
</p>

<?php
if ( $additional_content ) :
	?>
	<p>
		<?php
		echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
		?>
	</p>
	<?php
endif;
?>

<?php
/**
 * Action hook to trigger after coupon email
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_footer', $email );
?>