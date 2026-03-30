<?php
/**
 * Payment methods
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;

do_action( 'woocommerce_before_account_payment_methods', $has_methods );

wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading'     => 'Payment Methods',
		'page_description' => 'Manage your saved payment methods',
	)
);

$filter_add_payment_gateways = static function ( $available_gateways ) {
	foreach ( $available_gateways as $gateway_id => $gateway ) {
		if ( ! ( $gateway->supports( 'add_payment_method' ) || $gateway->supports( 'tokenization' ) ) ) {
			unset( $available_gateways[ $gateway_id ] );
		}
	}

	return $available_gateways;
};

add_filter( 'woocommerce_available_payment_gateways', $filter_add_payment_gateways, 999 );
wp_enqueue_script( 'wc-add-payment-method' );
?>

<div class="payment-methods-page ma-payment-methods">
    <section id="ma-payment-methods-add" class="payment-methods-section payment-methods-section--add">
        <h2 class="payment-methods-section__title ma-u-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="payment-methods-section__title-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M3.75 6h16.5A1.5 1.5 0 0 1 21.75 7.5v9A1.5 1.5 0 0 1 20.25 18H3.75a1.5 1.5 0 0 1-1.5-1.5v-9A1.5 1.5 0 0 1 3.75 6Z" />
            </svg>
            <span><?php esc_html_e( 'Add Payment Method', 'woocommerce' ); ?></span>
        </h2>
        <div class="payment-methods-section__body ma-u-surface-panel">
            <div class="ma-form">
                <?php
                do_action( 'before_woocommerce_add_payment_method' );
                wc_get_template( 'myaccount/form-add-payment-method.php' );
                do_action( 'after_woocommerce_add_payment_method' );
                ?>
            </div>
        </div>
    </section>

    <?php remove_filter( 'woocommerce_available_payment_gateways', $filter_add_payment_gateways, 999 ); ?>

    <section class="payment-methods-section payment-methods-section--saved">
        <h2 class="payment-methods-section__title ma-u-section-title"><?php esc_html_e( 'Saved Payment Methods', 'woocommerce' ); ?></h2>
        <div class="payment-methods-section__body">
            <?php if ( $has_methods ) : ?>
                <div class="payment-methods-list">
                    <?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                        <?php foreach ( $methods as $method ) : ?>
                            <article class="payment-methods-item ma-u-surface-panel">
                                <div class="payment-methods-item__main">
                                    <div class="payment-methods-item__brand" aria-hidden="true">
                                        <?php
                                        $brand_code = strtoupper( (string) $method['method']['brand'] );
                                        if ( 'MASTERCARD' === $brand_code ) {
                                            $brand_code = 'MC';
                                        } elseif ( 'AMERICAN EXPRESS' === $brand_code ) {
                                            $brand_code = 'AMEX';
                                        } elseif ( empty( $brand_code ) ) {
                                            $brand_code = 'CARD';
                                        }
                                        ?>
                                        <span class="payment-methods-item__brand-badge ma-u-badge ma-u-badge--brand-tile"><?php echo esc_html( $brand_code ); ?></span>
                                    </div>

                                    <div class="payment-methods-item__meta">
                                        <span class="payment-methods-item__title">
                                            <?php
                                            if ( ! empty( $method['method']['last4'] ) ) {
                                                echo sprintf( esc_html__( '%1$s ****%2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
                                            } else {
                                                echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
                                            }
                                            ?>
                                        </span>

                                        <span class="payment-methods-item__expiry">
                                            <?php echo wp_sprintf( 'Expire %s', esc_html( $method['expires'] ) ); ?>
                                        </span>

                                        <?php
                                        if ( isset( $method['actions']['default'] ) ) {
                                            echo '<a href="' . esc_url( $method['actions']['default']['url'] ) . '" class="payment-methods-item__default-link">' . esc_html( $method['actions']['default']['name'] ) . '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="payment-methods-item__actions">
                                    <?php if ( ! isset( $method['actions']['default'] ) ) : ?>
                                        <span class="payment-methods-item__default-pill ma-u-badge ma-u-badge--inverse">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="payment-methods-item__default-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span><?php esc_html_e( 'Default', 'woocommerce' ); ?></span>
                                        </span>
                                    <?php endif; ?>

                                    <?php
                                    if ( isset( $method['actions']['delete'] ) ) {
                                        echo wp_sprintf(
                                            "<a href='%s'>%s</a>",
                                            esc_url( $method['actions']['delete']['url'] ),
                                            '<svg xmlns="http://www.w3.org/2000/svg" class="payment-methods-item__delete-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6 18 18M18 6 6 18" /></svg>'
                                        );
                                    }
                                    ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
			<?php else : ?>
				<?php
				wc_get_template(
					'myaccount/partials/ma-empty-state.php',
					array(
						'title'          => esc_html__( 'No saved payment methods yet.', 'woocommerce' ),
						'description'    => esc_html__( 'Add a card now to check out faster next time, or save one during checkout.', 'myaccount-core' ),
						'primary_url'    => esc_url( wc_get_account_endpoint_url( 'payment-methods' ) . '#ma-payment-methods-add' ),
						'primary_label'  => esc_html__( 'Add payment method', 'woocommerce' ),
						'primary_icon'   => false,
						'heading_level'  => 'h3',
						'modifier_class' => 'ma-u-surface-panel ma-u-surface-panel--full',
					)
				);
				?>
			<?php endif; ?>
        </div>
    </section>
</div>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>
