<?php

namespace ssh;

/**
 * Class to handle the restriction.
 */
class Restriction {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Restriction.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for Admin.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'restrict_access' ) );
		add_action( 'login_head', array( $this, 'add_login_logo' ) );
		//add_action( 'ss_before_static_export', array( $this, 'allow_access_static' ) );
		//add_action( 'ss_after_cleanup', array( $this, 'restrict_access_static' ) );
	}

	/**
	 * Enable website access for static export.
	 *
	 * @return void
	 */
	public function allow_access_static() {
		update_option( 'ssh_restrict_access', 'no' );
	}

	/**
	 * Disable website access after static export.
	 *
	 * @return void
	 */
	public function restrict_access_static() {
		update_option( 'ssh_restrict_access', 'yes' );
	}


	/**
	 * Only accessable as logged in user.
	 *
	 * @return void
	 */
	public function restrict_access() {
		global $pagenow;

		$restrict_access = get_option( 'ssh_restrict_access' );

		$ip_address = getHostByName( getHostName() );
		$whitelist  = array( '127.0.0.1', '::1', $ip_address );

		if ( 'yes' === $restrict_access ) {
			if ( ! is_user_logged_in() && $pagenow != 'wp-login.php' && ! in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {
				auth_redirect();
			}
		}
	}

	/**
	 * Set custom admin logo for Simply Static.
	 *
	 * @return void
	 */
	public function add_login_logo() {
		$admin_logo = get_option( 'ssh_admin_logo' );

		if ( empty( $admin_logo ) ) {
			$admin_logo = 'https://manage.simplystatic.io/wp-content/uploads/2021/12/simply-static-logo.svg';
		}

		?>
		<style type="text/css">
			h1 a {
			background-image: url('<?php echo esc_url( $admin_logo ); ?>') !important;
			min-width: 250px !important;
			background-size: 100% !important;
			}
			#wp-submit {
				background-color: #7200e5;
				color: #fff;
				border: 2px solid transparent;
				box-shadow: 0 0 20px 0 rgba(114,0,229,.2);
			}
			#wp-submit:hover {
				background-color: #6500cc;
				color: #fff;
				border-color: transparent;
				box-shadow: 0 0 30px 0 rgba(114,0,229,.4);
			}
		</style>
		<?php
	}
}
