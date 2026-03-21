<?php
/**
 * StoreFrog connector page tab 2 template.
 *
 * @package Storefrog_Connector
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_warning = isset( $this->show_warning ) ? $this->show_warning : false;
?>
<div class="wrap wbte-sf-connector">
	<div class="wbte-sf-top-box">
		<div class="wbte-sf-header">
			<h1><?php esc_html_e( 'Increase Conversions with Advanced Marketing Automation', 'decorator-woocommerce-email-customizer' ); ?></h1>
			<p class="wbte-sf-subtitle"><?php esc_html_e( 'Create automated email campaigns and dynamic popups to boost conversions, drive engagement, increase revenue, and recover lost sales.', 'decorator-woocommerce-email-customizer' ); ?></p>
		</div>
		<div class="wbte-sf-auth-denied-warning">
			<img style="width: 24px;" src="<?php echo esc_url( RP_DECORATOR_PLUGIN_URL . '/includes/storefrog/assets/images/warning.svg' ); ?>" alt="<?php esc_html_e( 'Warning', 'decorator-woocommerce-email-customizer' ); ?>">
			<div class="wbte-sf-auth-denied-warning-content">
				<p class="wbte-sf-auth-denied-warning-content-title"><?php esc_html_e( 'Access to your store is pending authorization', 'decorator-woocommerce-email-customizer' ); ?></p>
				<span><?php esc_html_e( 'Some features may be limited until permission is granted.', 'decorator-woocommerce-email-customizer' ); ?> <a href="<?php echo esc_url( $auth_url ); ?>" class="wbte-sf-auth-link"><?php esc_html_e( 'Authorize access', 'decorator-woocommerce-email-customizer' ); ?></a></span>
			</div>
			<span class="wbte-sf-auth-denied-warning-close">×</span>
		</div>
       <div class="wbte-sf-banner-box-container">
		<div class="wbte-sf-banner-box">
			<div class="wbte-sf-item wbte-sf-item-left">
				<?php
                if ($is_connected) {
				?>
				<h3><?php esc_html_e( 'You’re All Set! —you’ve successfully connected your store:', 'decorator-woocommerce-email-customizer' ); ?></h3>
					<div>
						<p style="font-size: 14px;"><?php esc_html_e( 'Dive in to explore:', 'decorator-woocommerce-email-customizer' ); ?></p>
						<ul style="font-size: 14px; list-style-type: disc; margin-left: 25px;">
							<li><?php esc_html_e( 'Automated email campaigns & drip workflows', 'decorator-woocommerce-email-customizer' ); ?></li>
							<li><?php esc_html_e( 'On‑site pop‑ups and banners', 'decorator-woocommerce-email-customizer' ); ?></li>
							<li><?php esc_html_e( 'Detailed sales & engagement analytics', 'decorator-woocommerce-email-customizer' ); ?></li>
						</ul>
					</div>
					<?php
				} else {
					?>
					<h3><?php esc_html_e( 'Let’s connect your store in just a few simple steps:', 'decorator-woocommerce-email-customizer' ); ?></h3>
					<div>
						<div class="wbte-sf-tab-steps-container">
							<div class="wbte-sf-tab-step-number">1</div>
							<span>
								<p class="wbte-sf-tab-bold-text"><?php esc_html_e( 'Login or Signup.', 'decorator-woocommerce-email-customizer' ); ?></p>
								<span style="font-size: 13px; font-weight: 400; color: #0C1C37;"><?php esc_html_e( 'Create a new account or log in to your existing account.', 'decorator-woocommerce-email-customizer' ); ?></span>
							</span>
						</div>
						<div class="wbte-sf-tab-steps-container">
							<div class="wbte-sf-tab-step-number">2</div>
							<span>
								<p class="wbte-sf-tab-bold-text"><?php esc_html_e( 'Approve the authentication request.', 'decorator-woocommerce-email-customizer' ); ?></p>
								<span style="font-size: 13px; font-weight: 400; color: #0C1C37;"><?php esc_html_e( 'This grants WebToffee permission to access your store data needed for web campaigns and automation.', 'decorator-woocommerce-email-customizer' ); ?></span>
							</span>
						</div>
					</div>
					<?php
				}
				?>
				
				<div class="wbte-sf-btnbox">
					<?php
					if ( $is_connected ) { // $connected_email.
						?>
						<p style="margin-bottom:5px;"><span class="dashicons dashicons-yes-alt" style="color:#36bc77"></span> 
						<?php
						// translators: %s is the connected email address.
						echo esc_html( sprintf( __( 'Connected to %s', 'decorator-woocommerce-email-customizer' ), $connected_email ) );
						?>
						<span class="wbte-sf-disconnect">Disconnect</span> </p>
						<a href="<?php echo esc_url( $dashboard_url ); ?>" class="wbte-sf-btn" target="_blank"><?php esc_html_e( 'Go to Web app', 'decorator-woocommerce-email-customizer' ); ?> <span class="dashicons dashicons-external"></span></a>
						<?php
					} else {
						?>
						<a href="<?php echo esc_url( $auth_url ); ?>" class="wbte-sf-btn" id="wbte-sf-connect-btn" data-auth-url="<?php echo esc_url( $auth_url ); ?>"><?php esc_html_e( 'Connect now', 'decorator-woocommerce-email-customizer' ); ?></a>
						<br><span style="font-size: 12px; font-style: italic; font-weight: 400; color: #0C1C37;"><?php esc_html_e( '* Redirects you to the WebToffee Marketing app', 'decorator-woocommerce-email-customizer' ); ?></span>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="wbte-sf-banner-box-container-right">
			<div class="wbte-sf-image-slider">
				<div class="wbte-sf-slider-wrapper">
					<div class="wbte-sf-slide active">
						<div class="wbte-sf-slide-image">
							<img src="<?php echo esc_url( $asset_url . 'images/block1_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Slide 1', 'decorator-woocommerce-email-customizer' ); ?>">
						</div>
						<div class="wbte-sf-slide-content">
							<h4 class="wbte-sf-slide-title"><?php esc_html_e( 'Up to 40% More Conversion with Popups', 'decorator-woocommerce-email-customizer' ); ?></h4>
							<p class="wbte-sf-slide-description"><?php esc_html_e( 'Boost conversion by up to 40% using dynamic popups for Welcome Campaigns, Exit Intent, and Cart Abandonment Recovery.', 'decorator-woocommerce-email-customizer' ); ?></p>
						</div>
					</div>
					<div class="wbte-sf-slide">
						<div class="wbte-sf-slide-image">
							<img src="<?php echo esc_url( $asset_url . 'images/block2_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Slide 2', 'decorator-woocommerce-email-customizer' ); ?>">
						</div>
						<div class="wbte-sf-slide-content">
							<h4 class="wbte-sf-slide-title"><?php esc_html_e( 'Automated Email Campaigns', 'decorator-woocommerce-email-customizer' ); ?></h4>
							<p class="wbte-sf-slide-description"><?php esc_html_e( 'Create powerful automated email workflows to engage customers, recover abandoned carts, and drive repeat purchases.', 'decorator-woocommerce-email-customizer' ); ?></p>
						</div>
					</div>
					<div class="wbte-sf-slide">
						<div class="wbte-sf-slide-image">
							<img src="<?php echo esc_url( $asset_url . 'images/block3_bg.webp' ); ?>" alt="<?php esc_attr_e( 'Slide 3', 'decorator-woocommerce-email-customizer' ); ?>">
						</div>
						<div class="wbte-sf-slide-content">
							<h4 class="wbte-sf-slide-title"><?php esc_html_e( 'Advanced Analytics & Insights', 'decorator-woocommerce-email-customizer' ); ?></h4>
							<p class="wbte-sf-slide-description"><?php esc_html_e( 'Track performance with detailed analytics, monitor engagement rates, and optimize your marketing strategies for maximum ROI.', 'decorator-woocommerce-email-customizer' ); ?></p>
						</div>
					</div>
				</div>
				<div class="wbte-sf-slider-dots">
					<button class="wbte-sf-slider-arrow wbte-sf-slider-arrow-left" aria-label="<?php esc_attr_e( 'Previous slide', 'decorator-woocommerce-email-customizer' ); ?>">
						&lt;
					</button>
					<span class="wbte-sf-dot active" data-slide="0"></span>
					<span class="wbte-sf-dot" data-slide="1"></span>
					<span class="wbte-sf-dot" data-slide="2"></span>
					<button class="wbte-sf-slider-arrow wbte-sf-slider-arrow-right" aria-label="<?php esc_attr_e( 'Next slide', 'decorator-woocommerce-email-customizer' ); ?>">
						&gt;
					</button>
				</div>
			</div>
		</div>
	   </div>
	</div>
</div>
<style type="text/css">
body{ background:#F1F8FE; }
.wbte-sf-subtitle {font-size: 16px;color: #2d2d2d;margin: 0 auto;margin-top: 0.5rem;font-weight: 300;max-width: 700px;line-height: 1.4;text-align: center;}
.wbte-sf-banner-box-container{ display: flex; align-items: stretch; gap: 1.5rem; max-width: 95%; margin: 3rem auto; }
.wbte-sf-banner-box{ width: 68%; padding: 2rem; box-sizing: border-box; background: #ffffff; border-radius: 10px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem 0px; }
.wbte-sf-banner-box-container-right{ width: 32%; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; border-radius: 15px; }
.wbte-sf-banner-box h1{ font-size: 22px; font-weight: 700; color: #2d2d2d; }
.wbte-sf-banner-box .wbte-sf-tab-description{ font-size: 14px; font-weight: 300; }
.wbte-sf-banner-box .wbte-sf-item-left{ position: relative; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.wbte-sf-banner-box .wbte-sf-item-left h3{ font-weight: 600; font-size: 18px; }
.wbte-sf-banner-box .wbte-sf-item-right{ text-align:right; }
.wbte-sf-banner-box .wbte-sf-item-right img{ display:inline-block; max-width:352px; }
.wbte-sf-banner-box .wbte-sf-btnbox{ width:100%;}
.wbte-sf-banner-box .wbte-sf-btn{ background-color:#1763DC; color:white; display:inline-block; text-align:center; padding:.7rem 2rem; min-width: 326px; width:auto; box-sizing:border-box; border: none; border-radius:4px; font-size:.9rem; cursor: pointer; transition: background-color 0.3s; text-decoration:none; margin-top: 5px; }
.wbte-sf-banner-box .wbte-sf-disconnect{ color:#2f79b6; cursor: pointer; text-decoration:underline; }

.wbte-sf-tab-steps-container { display: flex; align-items: center; font-family: 'Poppins', sans-serif; margin-bottom: 20px; }
.wbte-sf-tab-step-number { background-color: #E0EBF5;  color: #1763DC;  width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; margin-right: 16px; }
.wbte-sf-tab-bold-text { font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; color: #0C1C37; margin: 0; }

/* Authorization Denied warning */
.wbte-sf-auth-denied-warning { align-items: flex-start; justify-content: space-between; background-color: #FCEAED; border: 0.5px solid #D63638; border-radius: 12px; padding: 16px 20px; color: #DF284C; font-family: Arial, sans-serif; max-width: 1100px; margin: 20px auto; position: relative; box-sizing: border-box; gap: 15px; display: <?php echo $show_warning ? 'flex' : 'none'; ?>; }
.wbte-sf-auth-denied-warning .wbte-sf-auth-denied-warning-content { flex: 1; font-size: 14px; line-height: 20px; font-weight: 400; }
.wbte-sf-auth-denied-warning .wbte-sf-auth-denied-warning-content-title { font-size: 16px; margin: 0; margin-bottom: 8px; }
.wbte-sf-auth-denied-warning .wbte-sf-auth-denied-warning-content span strong, .wbte-sf-auth-denied-warning .wbte-sf-auth-denied-warning-content-title { font-weight: 700; }
.wbte-sf-auth-denied-warning .wbte-sf-auth-denied-warning-close { color: #D63638; font-size: 25px; cursor: pointer; }

/* Image Slider Styles */
.wbte-sf-image-slider { position: relative; width: 100%; display: flex; flex-direction: column;background: #FFF; }
.wbte-sf-slider-wrapper { position: relative; width: 100%; overflow: hidden; border-radius: 10px; }
.wbte-sf-slide { position: absolute; top: 0; left: 0; width: 100%; opacity: 0; transition: opacity 0.5s ease-in-out; display: flex; flex-direction: column; }
.wbte-sf-slide.active { opacity: 1; position: relative; }
.wbte-sf-slide-image { width: 100%; position: relative; }
.wbte-sf-slide img { width: 100%; height: 200px; }
.wbte-sf-slide-content { padding: 20px 0 0 0; width: 100%; text-align: center; }
.wbte-sf-slide-title { font-size: 16px; font-weight: 600; color: #2d2d2d; margin: 0 0 10px 0; line-height: 1.3; }
.wbte-sf-slide-description { font-size: 13px; font-weight: 400; color: #2d2d2d; margin: 0 12px; line-height: 1.8; }
.wbte-sf-slider-dots { position: relative; margin-top: 20px; display: flex; gap: 15px; justify-content: center; align-items: center; width: 100%; margin-bottom: 20px; }
.wbte-sf-slider-arrow { position: absolute; background: transparent; border: none; color: #2d2d2d; font-size: 18px; font-weight: 300; cursor: pointer; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; transition: color 0.3s ease; line-height: 1; padding: 50px; top: 50%; transform: translateY(-50%); }
.wbte-sf-slider-arrow:hover { color: #1763DC; }
.wbte-sf-slider-arrow-left { left: 0; }
.wbte-sf-slider-arrow-right { right: 0; }
.wbte-sf-dot { width: 6px; height: 6px; border-radius: 50%; background: #9CA3AF; cursor: pointer; transition: all 0.3s ease; border: none; padding: 0; }
.wbte-sf-dot.active { background: #2d2d2d; width: 8px; height: 8px; }
.wbte-sf-dot:hover { background: #6B7280; }


@media screen and (max-width: 1100px) {
	.wbte-sf-banner-box .wbte-sf-btnbox{ position:relative; bottom:0px; margin-top:20px; }
	.wbte-sf-banner-box .wbte-sf-item img{ max-width:100%; }
}
@media screen and (max-width:800px) {
	.wbte-sf-banner-box .wbte-sf-item{
	flex:1 1 100%;
	}
	.wbte-sf-banner-box .wbte-sf-item-left{ padding-right:3.5rem; position:relative; text-align:center; }
	.wbte-sf-banner-box .wbte-sf-item-right{ text-align:center; margin-top:20px; }
	.wbte-sf-banner-box .wbte-sf-item-right img{ max-width:96%; }
}
</style>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#wbte-sf-connect-btn').on('click', function(e){
			e.preventDefault();
			var $btn = $(this);
			var authUrl = $btn.data('auth-url') || $btn.attr('href');			
			$.ajax({
				url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
				type: 'POST',
				data: { 
					action: 'wbte_sf_set_first_time_connect', 
					nonce: '<?php echo esc_html( wp_create_nonce( 'wbte_sf_set_first_time_connect' ) ); ?>' 
				},
				dataType: 'json',
				async: true
			});
			if(authUrl){
				window.location.href = authUrl;
			}
		});

		var disconnection_in_progress = false;
		$('.wbte-sf-disconnect').on('click', function(){
			if(disconnection_in_progress){
				return;
			}
			disconnection_in_progress = true;

			if(confirm('<?php esc_html_e( 'Are you sure you want to disconnect?', 'decorator-woocommerce-email-customizer' ); ?>')){
				$(this).html('<?php esc_html_e( 'Disconnecting...', 'decorator-woocommerce-email-customizer' ); ?>');
				$.ajax({
					url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
					type: 'POST',
					data: { action: 'wbte_sf_disconnect', nonce: '<?php echo esc_html( wp_create_nonce( 'wbte_sf_disconnect' ) ); ?>' },
					dataType: 'json',
					success: function(response){
						disconnection_in_progress = false;
						if(response.success){
							window.location.reload();
						}else{                   
							alert('<?php esc_html_e( 'Failed to disconnect.', 'decorator-woocommerce-email-customizer' ); ?>');
							$(this).html('<?php esc_html_e( 'Disconnect', 'decorator-woocommerce-email-customizer' ); ?>');                           
						}
					},
					error: function(xhr, status, error){
						disconnection_in_progress = false;
						alert('<?php esc_html_e( 'Failed to disconnect.', 'decorator-woocommerce-email-customizer' ); ?>');
						$(this).html('<?php esc_html_e( 'Disconnect', 'decorator-woocommerce-email-customizer' ); ?>');
					}
				});
			}
		});

		$('.wbte-sf-auth-denied-warning-close').on('click', function(){
			$('.wbte-sf-auth-denied-warning').hide();
		});

		// Image Slider Functionality
		var currentSlide = 0;
		var slides = $('.wbte-sf-slide');
		var dots = $('.wbte-sf-dot');
		var totalSlides = slides.length;
		var autoSlideInterval;

		function showSlide(index) {
			// Remove active class from all slides and dots
			slides.removeClass('active');
			dots.removeClass('active');
			
			// Add active class to current slide and dot
			slides.eq(index).addClass('active');
			dots.eq(index).addClass('active');
			
			currentSlide = index;
		}

		function nextSlide() {
			var next = (currentSlide + 1) % totalSlides;
			showSlide(next);
		}

		function prevSlide() {
			var prev = (currentSlide - 1 + totalSlides) % totalSlides;
			showSlide(prev);
		}

		function startAutoSlide() {
			autoSlideInterval = setInterval(nextSlide, 4000); // Change slide every 4 seconds
		}

		function stopAutoSlide() {
			clearInterval(autoSlideInterval);
		}

		// Arrow navigation
		$('.wbte-sf-slider-arrow-right').on('click', function() {
			stopAutoSlide();
			nextSlide();
			startAutoSlide();
		});

		$('.wbte-sf-slider-arrow-left').on('click', function() {
			stopAutoSlide();
			prevSlide();
			startAutoSlide();
		});

		// Dot navigation
		$('.wbte-sf-dot').on('click', function() {
			var slideIndex = $(this).data('slide');
			stopAutoSlide();
			showSlide(slideIndex);
			startAutoSlide();
		});

		// Pause auto-slide on hover
		$('.wbte-sf-image-slider').on('mouseenter', function() {
			stopAutoSlide();
		}).on('mouseleave', function() {
			startAutoSlide();
		});

		// Initialize slider
		if (totalSlides > 0) {
			showSlide(0);
			startAutoSlide();
		}
	});
</script>