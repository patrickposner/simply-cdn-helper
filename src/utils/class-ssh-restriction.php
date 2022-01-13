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
		add_filter( 'allow_password_reset', array( $this, 'disable_password_reset' ) );
		add_filter( 'gettext', array( $this, 'remove_lostpassword_text' ) );
		add_action( 'login_head', array( $this, 'add_login_logo' ) );
		add_action( 'ss_before_static_export', array( $this, 'allow_access_static' ) );
		add_action( 'ss_after_cleanup', array( $this, 'restrict_access_static' ) );
	}

	/**
	 * Enable website access for static export.
	 *
	 * @return void
	 */
	public function allow_access_static() {
		update_option( 'ssc_restrict_access', 'no' );
	}

	/**
	 * Disable website access after static export.
	 *
	 * @return void
	 */
	public function restrict_access_static() {
		update_option( 'ssc_restrict_access', 'yes' );
	}


	/**
	 * Only accessable as logged in user.
	 *
	 * @return void
	 */
	public function restrict_access() {
		global $pagenow;

		$restrict_access = get_option( 'ssc_restrict_access' );

		if ( 'yes' === $restrict_access ) {
			if ( ! is_user_logged_in() && $pagenow != 'wp-login.php' ) {
				auth_redirect();
			}
		}
	}

	/**
	 * Disable password reset.
	 *
	 * @return bool
	 */
	public function disable_password_reset() {
		return false;
	}

	/**
	 * Remove password reset text.
	 *
	 * @param  string $text given password reset text.
	 * @return string
	 */
	public function remove_lostpassword_text( $text ) {
		if ( 'Lost your password?' == $text ) {
			$text = '';
		}

		if ( 'Passwort vergessen?' == $text ) {
			$text = '';
		}
		return $text;
	}

	/**
	 * Set custom admin logo for Simply Static.
	 *
	 * @return void
	 */
	public function add_login_logo() {
		?>
		<style type="text/css">
			h1 a {
			background-image: url('https://manage.simplystatic.io/wp-content/uploads/2021/12/simply-static-logo.svg') !important;
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
