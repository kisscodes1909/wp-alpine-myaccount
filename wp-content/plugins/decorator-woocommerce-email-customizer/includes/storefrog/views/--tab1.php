<?php
/**
 * StoreFrog connector page tab 1 template.
 *
 * @package Storefrog_Connector
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap wbte-sf-connector">   
	<div class="wbte-sf-top-box">
		<div class="wbte-sf-header">
			<h1><?php esc_html_e( 'Increase Conversions with Advanced Marketing Automation', 'decorator-woocommerce-email-customizer' ); ?></h1>
			<p class="wbte-sf-subtitle"><?php esc_html_e( 'Create automated email campaigns and dynamic popups to boost conversions, drive engagement, increase revenue, and recover lost sales.', 'decorator-woocommerce-email-customizer' ); ?></p>
		</div>

		<div class="wbte-sf-features-grid">
			<!-- Feature Card 1 -->
			<div class="wbte-sf-feature-card">
				<div class="wbte-sf-img">
					<img src="<?php echo esc_url( $asset_url . 'images/block1_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Up to 40% More Conversion with Popups', 'decorator-woocommerce-email-customizer' ); ?>">
				</div>
				<div class="wbte-sf-feature-card-bottom">
					<h3><?php esc_html_e( 'Up to 40% More Conversion with Popups', 'decorator-woocommerce-email-customizer' ); ?></h3>
					<p><?php esc_html_e( 'Boost conversion by up to 40% using dynamic popups for Welcome Campaigns, Exit Intent, and Cart Abandonment Recovery.', 'decorator-woocommerce-email-customizer' ); ?></p>
				</div>
			</div>

			<!-- Feature Card 2 -->
			<div class="wbte-sf-feature-card">
				<div class="wbte-sf-img">
					<img src="<?php echo esc_url( $asset_url . 'images/block2_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Advanced Email Automation and Follow-Ups', 'decorator-woocommerce-email-customizer' ); ?>">
				</div>
				<div class="wbte-sf-feature-card-bottom">
					<h3><?php esc_html_e( 'Advanced Email Automation and Follow-Ups', 'decorator-woocommerce-email-customizer' ); ?></h3>
					<p><?php esc_html_e( 'Set up automated email campaigns to welcome new users, recover abandoned carts, win back customers, and offer next-order coupons.', 'decorator-woocommerce-email-customizer' ); ?></p>
				</div>
			</div>

			<!-- Feature Card 3 -->
			<div class="wbte-sf-feature-card">
				<div class="wbte-sf-img">
					<img src="<?php echo esc_url( $asset_url . 'images/block3_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Grow Email List with Sign-up Forms', 'decorator-woocommerce-email-customizer' ); ?>">
				</div>
				<div class="wbte-sf-feature-card-bottom">
					<h3><?php esc_html_e( 'Grow Email List with Sign-up Forms', 'decorator-woocommerce-email-customizer' ); ?></h3>
					<p><?php esc_html_e( 'Create sign-up forms to capture leads and grow your email list, helping you boost engagement and conversions.', 'decorator-woocommerce-email-customizer' ); ?></p>
				</div>
			</div>
		</div>

		<div class="wbte-sf-cta">
			<a href="<?php echo esc_url( $tab2_url ); ?>" class="wbte-sf-button wbte-sf-button-primary">
				<?php esc_html_e( 'Explore WebToffee Marketing', 'decorator-woocommerce-email-customizer' ); ?> 
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</a>
			<p style="margin: 0; margin-top: 1rem; font-weight: 700; font-size: 18px;"><?php esc_html_e( 'Just want to edit Woo emails?', 'decorator-woocommerce-email-customizer' ); ?></p>
			<p style="margin: 0; font-size: 16px; font-family: sans-serif;"><?php esc_html_e( 'To edit the default WooCommerce email templates,', 'decorator-woocommerce-email-customizer' ); ?></p>
			<a style="font-weight: 500; font-size: 16px; color: #1763DC;" href="<?php echo esc_url( admin_url( 'admin.php?page=decorator-woocommerce-email-customizer' ) ); ?>" class="wbte-sf-link">
				<?php esc_html_e( 'Continue with Email Editor', 'decorator-woocommerce-email-customizer' ); ?>
			</a>
		</div>
	</div>
</div>
<style>
body{ background:#F1F8FE; }
.wbte-sf-header h1{ color:#2d2d2d; font-size:28px; font-weight:700; padding-top:0px; }
.wbte-sf-subtitle {
	font-size: .95rem;
	color:#2d2d2d; margin:0px auto;
	margin-top: 0.5rem; font-weight:300; width:100%; max-width:800px;
}

.wbte-sf-features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem auto; max-width: 1100px; }

.wbte-sf-feature-card {
	background: #fff;
	border-radius: 10px;
	text-align: center;
	box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.wbte-sf-feature-card-bottom{ padding: 20px; }

.wbte-sf-feature-card h3{ color:#233e76; font-weight:500; font-size:1.1rem; line-height:1.4rem; min-height:55px; margin:0px; display:inline-flex; align-items:center; }
.wbte-sf-feature-card p{ color:#2d2d2d; font-size:.85rem; font-weight:300; line-height:1.3rem; }
.wbte-sf-img{ width:100%; }
.wbte-sf-img img{ width:100%; border-top-left-radius:10px; border-top-right-radius:10px; }
.wbte-sf-icon {
	width:32px;
	height:32px;
	margin: 0 auto 1.5rem;
	padding: 1rem;
	border-radius:15px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.wbte-sf-icon img {
	width:90%;
	height: auto;
}

.wbte-sf-icon-campaigns { background: #FFE8E8; }
.wbte-sf-icon-automate { background: #E8F5F0; }
.wbte-sf-icon-revenue { background: #E8F0FF; }

.wbte-sf-cta{ text-align: center; margin: 1.5rem 0; display: flex; flex-direction: column; align-items: center;
}

.wbte-sf-button {
	display: inline-flex;
	align-items: center;    
	border-radius: 4px;
	text-decoration: none;
	font-weight:400;
	transition: all 0.3s ease; justify-content: center;
}
.wbte-sf-link{ color:#3157A6; text-decoration:none; font-size:.8rem; display:inline-block; padding:.7rem 2rem; text-align:center; }
.wbte-sf-cta a{ width:100%; max-width:300px; }

.wbte-sf-button-primary {
	background: #1763DC;
	color:#fff; padding:.7rem 2rem; font-size:.9rem;
}

.wbte-sf-button-secondary {
	background: #fff;
	color: #3157a6;
	border: 1px solid #3157a6; padding:.6rem 2rem; font-size:14px;
}

.wbte-sf-button:hover {
	opacity: 0.8; color:#fff;
	transform: translateY(-2px);
}

.wbte-sf-alternative h2{ font-size:22px; margin-bottom:0px; }
.wbte-sf-alternative p{ font-size:16px; font-weight:300; margin-top:5px; }

.wbte-sf-button .dashicons {
	margin-left: 0.5rem;
}

/* Responsive Styles */
@media screen and (max-width: 782px) {
	.wbte-sf-connector{ margin-left:-15px !important; }
	.wbte-sf-top-box{ width: calc( 100% + 15px) !important; }
	.wbte-sf-features-grid {
		grid-template-columns: 1fr;
	}
	
	.wbte-sf-header h1 {
		font-size: 1.8rem;
	}
	
	.wbte-sf-subtitle {
		font-size: 1rem;
	}
	
	.wbte-sf-feature-card {
		padding: 1.5rem;
	}
	.wbte-sf-cta a{ width:90%; max-width:300px; display:inline-block; margin:auto 0px; }
	.wbte-sf-link, .wbte-sf-button-primary{ padding:.7rem .8rem; }
}
</style>