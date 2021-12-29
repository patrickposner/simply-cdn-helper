<?php

namespace ssch;

/**
 * Class to handle SMTP delivery
 */
class Mailer {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Admin.
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
		add_action( 'phpmailer_init', array( $this, 'set_smtp_delivery' ) );
	}

	/**
	 * Configure wp_mail() to use SMTP to send mails.
	 *
	 * @param  object $phpmailer object containing PHP Mailer class.
	 * @return object
	 */
	public function set_smtp_delivery( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host       = get_option( 'ssch_smtp_host' );
		$phpmailer->SMTPAuth   = true;
		$phpmailer->Port       = get_option( 'ssch_smtp_mail_port' );
		$phpmailer->Username   = get_option( 'ssch_smtp_user' );
		$phpmailer->Password   = get_option( 'ssch_smtp_password' );
		$phpmailer->SMTPSecure = 'tls';
		$phpmailer->From       = get_option( 'ssch_smtp_mail_from' );
		$phpmailer->FromName   = get_option( 'ssch_smtp_name_from' );

		return $phpmailer;
	}
}
