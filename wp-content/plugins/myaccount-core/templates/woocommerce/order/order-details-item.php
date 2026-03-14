<?php
/**
 * Order Item Details (card layout for view-order Section 3).
 * Item meta: single inline line, comma-separated (core list markup is blocky).
 *
 * @package MyAccount_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

$is_visible        = $product && $product->is_visible();
$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
$qty               = $item->get_quantity();
$refunded_qty      = $order->get_qty_refunded_for_item( $item_id );
$qty_display       = $refunded_qty ? ( $qty - ( $refunded_qty * -1 ) ) : $qty;

$item_classes = implode( ' ', array_filter( array(
	'ma-order-details-items-summary__item',
	'ma-line-card',
	apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ),
) ) );
?>
<div class="<?php echo esc_attr( $item_classes ); ?>">
	<div class="ma-order-details-items-summary__item-image ma-line-card__media">
		<?php
		if ( $product ) {
			$image_id = $product->get_image_id();
			if ( $image_id ) {
				echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'alt' => $item->get_name() ) ) );
			} else {
				echo '<span class="ma-order-details-items-summary__item-image-placeholder" aria-hidden="true"></span>';
			}
		} else {
			echo '<span class="ma-order-details-items-summary__item-image-placeholder" aria-hidden="true"></span>';
		}
		?>
	</div>
	<div class="ma-order-details-items-summary__item-body ma-line-card__body ma-u-panel-pad">
		<h3 class="ma-order-details-items-summary__item-name">
			<?php
			echo wp_kses_post( apply_filters(
				'woocommerce_order_item_name',
				$product_permalink ? sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $item->get_name() ) : $item->get_name(),
				$item,
				$is_visible
			) );
			?>
		</h3>
		<?php do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false ); ?>
		<?php
		// Same source as wc_display_item_meta(): include_all true so variation attrs are not skipped
		// when already present in the line item name (default get_formatted_meta_data hides them).
		$meta_parts = array();
		foreach ( $item->get_all_formatted_meta_data() as $meta ) {
			$key = isset( $meta->display_key ) ? wp_strip_all_tags( (string) $meta->display_key ) : '';
			$val = isset( $meta->display_value ) ? wp_strip_all_tags( (string) $meta->display_value ) : '';
			$key = trim( $key );
			$val = trim( $val );
			if ( $key && $val ) {
				$meta_parts[] = $key . ': ' . $val;
			} elseif ( $val ) {
				$meta_parts[] = $val;
			} elseif ( $key ) {
				$meta_parts[] = $key;
			}
		}
		$meta_inline = implode( ', ', array_filter( $meta_parts ) );
		$meta_inline = apply_filters( 'myaccount_core_order_item_meta_inline', $meta_inline, $item, $order );
		if ( $meta_inline !== '' ) :
			?>
		<p class="ma-order-details-items-summary__item-meta"><?php echo esc_html( $meta_inline ); ?></p>
			<?php
		endif;
		?>
		<?php do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false ); ?>
		<p class="ma-order-details-items-summary__item-qty">
			<?php
			/* translators: %s: quantity */
			echo esc_html( sprintf( __( 'Qty %s', 'woocommerce' ), $qty_display ) );
			?>
		</p>
		<p class="ma-order-details-items-summary__item-price"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></p>
	</div>
</div>

<?php
$show_purchase_note = isset( $show_purchase_note ) ? $show_purchase_note : $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
if ( $show_purchase_note && ! empty( $purchase_note ) ) :
	?>
	<div class="ma-order-details-items-summary__item-purchase-note woocommerce-table__product-purchase-note product-purchase-note">
		<?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?>
	</div>
<?php endif; ?>
