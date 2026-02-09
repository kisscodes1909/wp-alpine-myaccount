<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'We have received your return request. Here are the details:', 'woocommerce' ); ?></p>

<?php if (!empty($return_data)): ?>
    <h2><?php esc_html_e('Return Details', 'woocommerce'); ?></h2>
    <ul>
        <li><strong><?php esc_html_e('Return ID:', 'woocommerce'); ?></strong> <?php echo esc_html($return_data['id']); ?></li>
        <li><strong><?php esc_html_e('Status:', 'woocommerce'); ?></strong> <?php echo esc_html($return_data['status']['label']); ?></li>
        <li><strong><?php esc_html_e('Created At:', 'woocommerce'); ?></strong> <?php echo esc_html($return_data['createAt']); ?></li>
    </ul>
<?php endif; ?>


<?php if (!empty($return_data) && !empty($return_data['order_items'])): ?>
    <h2><?php esc_html_e('Returned Items', 'woocommerce'); ?></h2>
    <table cellspacing="0" cellpadding="6" border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th><?php esc_html_e('Thumbnail', 'woocommerce'); ?></th>
            <th><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            <th><?php esc_html_e('Reason', 'woocommerce'); ?></th>
            <th><?php esc_html_e('Feedback', 'woocommerce'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($return_data['order_items'] as $item): ?>
            <?php
            $order_item = $order->get_item( $item['id'] );
            if ( $order_item ) {
                $product = $order_item->get_product();
                $thumbnail = $product ? $product->get_image('order-product-item') : '';
                $product_name = $order_item->get_name();
            } else {
                $thumbnail = '';
                $product_name = esc_html__('Product not found', 'woocommerce');
            }
            ?>
            <tr>
                <td><?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $thumbnail, $order_item ) ); ?></td>
                <td><?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_name, $order_item, false ) ); ?></td>
                <td><?php echo esc_html($item['qty']); ?></td>
                <td><?php echo esc_html($item['reason']); ?></td>
                <td><?php echo esc_html($item['feed_back']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?php esc_html_e('No items found for return.', 'woocommerce'); ?></p>
<?php endif; ?>
    <!-- Footer Content -->
<div style="text-align: center; padding: 20px 0; font-family: Arial, sans-serif; color: #6c757d; font-size: 14px;">
    <p>Thank you for choosing our products and services. We are committed to providing you with the best quality and service.</p>
</div>

<?php
do_action( 'woocommerce_email_footer', $email );
?>