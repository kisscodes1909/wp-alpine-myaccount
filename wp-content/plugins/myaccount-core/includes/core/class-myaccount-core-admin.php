<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MyAccount_Core_Admin {
	private const PAGE_SLUG = 'myaccount-core-settings';
	private static ?MyAccount_Core_Admin $instance = null;

	public static function instance(): MyAccount_Core_Admin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'render_mode_notice' ) );
	}

	public function register_settings_page(): void {
		add_options_page(
			__( 'My Account Core', 'myaccount-core' ),
			__( 'My Account Core', 'myaccount-core' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings(): void {
		register_setting(
			'myaccount_core_settings',
			MyAccount_Core_Plugin::OPTION_OWNER_MODE,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_owner_mode' ),
				'default'           => 'plugin',
			)
		);

		register_setting(
			'myaccount_core_settings',
			'myaccount_layout',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_layout' ),
				'default'           => '',
			)
		);

	}

	public function sanitize_owner_mode( $value ): string {
		$mode = is_string( $value ) ? $value : '';

		return in_array( $mode, array( 'plugin', 'theme' ), true ) ? $mode : 'plugin';
	}

	public function sanitize_layout( $value ): string {
		$layout = is_string( $value ) ? $value : '';

		return $layout === 'stacked' ? 'stacked' : '';
	}

	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$mode   = MyAccount_Core_Plugin::get_owner_mode();
		$layout = get_option( 'myaccount_layout', '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'My Account Core', 'myaccount-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'myaccount_core_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Ownership mode', 'myaccount-core' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input
										type="radio"
										name="<?php echo esc_attr( MyAccount_Core_Plugin::OPTION_OWNER_MODE ); ?>"
										value="plugin"
										<?php checked( $mode, 'plugin' ); ?>
									/>
									<?php esc_html_e( 'Plugin owns My Account templates, hooks, assets, and AJAX.', 'myaccount-core' ); ?>
								</label>
								<br />
								<label>
									<input
										type="radio"
										name="<?php echo esc_attr( MyAccount_Core_Plugin::OPTION_OWNER_MODE ); ?>"
										value="theme"
										<?php checked( $mode, 'theme' ); ?>
									/>
									<?php esc_html_e( 'Theme fallback mode (quick rollback for QA).', 'myaccount-core' ); ?>
								</label>
							</fieldset>
							<p class="description">
								<?php esc_html_e( 'Switch to Theme mode to disable plugin ownership without deactivating the plugin.', 'myaccount-core' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'My Account layout', 'myaccount-core' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input
										type="radio"
										name="myaccount_layout"
										value=""
										<?php checked( $layout, '' ); ?>
									/>
									<?php esc_html_e( 'Theme default (column)', 'myaccount-core' ); ?>
								</label>
								<p class="description" style="margin-left: 1.5em; margin-top: 0.25em;">
									<?php esc_html_e( 'Navigation left, content right; theme controls column layout.', 'myaccount-core' ); ?>
								</p>
								<br />
								<label>
									<input
										type="radio"
										name="myaccount_layout"
										value="stacked"
										<?php checked( $layout, 'stacked' ); ?>
									/>
									<?php esc_html_e( 'Stacked', 'myaccount-core' ); ?>
								</label>
								<p class="description" style="margin-left: 1.5em; margin-top: 0.25em;">
									<?php esc_html_e( 'Navigation on top (horizontal), content below.', 'myaccount-core' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function render_mode_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'settings_page_' . self::PAGE_SLUG === $screen->id ) {
			return;
		}

		if ( MyAccount_Core_Plugin::is_plugin_owner() ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( 'My Account Core is in Theme fallback mode. Plugin ownership is currently disabled.', 'myaccount-core' ); ?>
			</p>
		</div>
		<?php
	}
}
