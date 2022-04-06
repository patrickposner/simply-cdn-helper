<?php

namespace ssh;

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
		add_filter( 'wp_mail_from', array( $this, 'set_from_email' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'set_from_name' ) );
		add_action( 'phpmailer_init', array( $this, 'set_smtp_delivery' ) );
		add_action( 'wp_ajax_send_test_mail', array( $this, 'send_test_mail' ) );
	}

	/**
	 * Set from e-mail address.
	 *
	 * @param  string $origin_email current from email.
	 * @return string
	 */
	public function set_from_email( $origin_email ) {

		$smtp_host = get_option( 'ssh_smtp_host' );

		if ( ! empty( $smtp_host ) ) {
			return $origin_email;
		}

		$url        = wp_parse_url( untrailingslashit( get_bloginfo( 'url' ) ) );
		$mail_parts = explode( '.', $url['host'] );
		$mail       = $mail_parts[0] . '@' . $mail_parts[1] . '.io';

		return $mail;
	}

	/**
	 * Set from name
	 *
	 * @param string $origin_name current from name.
	 * @return string
	 */
	public function set_from_name( $origin_name ) {
		$smtp_host = get_option( 'ssh_smtp_host' );

		if ( ! empty( $smtp_host ) ) {
			return $origin_name;
		}

		return esc_html( get_bloginfo( 'name' ) );
	}

	/**
	 * Configure wp_mail() to use SMTP to send mails.
	 *
	 * @param  object $phpmailer object containing PHP Mailer class.
	 * @return object
	 */
	public function set_smtp_delivery( $phpmailer ) {
		$smtp_host = get_option( 'ssh_smtp_host' );

		if ( ! empty( $smtp_host ) ) {
			$phpmailer->isSMTP();
			$phpmailer->Host       = get_option( 'ssh_smtp_host' );
			$phpmailer->SMTPAuth   = true;
			$phpmailer->Port       = get_option( 'ssh_smtp_mail_port' );
			$phpmailer->Username   = get_option( 'ssh_smtp_user' );
			$phpmailer->Password   = get_option( 'ssh_smtp_password' );
			$phpmailer->SMTPSecure = 'tls';
			$phpmailer->From       = get_option( 'ssh_smtp_mail_from' );
			$phpmailer->FromName   = get_option( 'ssh_smtp_name_from' );
		}

		return $phpmailer;
	}

	/**
	 * Send a test e-mail via ajax.
	 *
	 * @return void
	 */
	public function send_test_mail() {
		$nonce = esc_html( $_POST['nonce'] );

		if ( ! wp_verify_nonce( $nonce, 'ssh-mailer-nonce' ) ) {
			die();
		}

		$to      =  esc_html( $_POST['email'] );
		$subject = __( 'Test E-Mail', 'simply-static-hosting' );
		$body    = __( 'This is the Test E-Mail you have triggered from ', 'simply-static-hosting' ) . get_bloginfo( 'url' );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$response = array( 'success' => true );

		wp_mail( $to, $subject, $body, $headers );

		$response = array( 'success' => true );

		print wp_json_encode( $response );
		exit;
	}
}
