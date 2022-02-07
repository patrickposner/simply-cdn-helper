<?php

namespace ssh;

/**
 * Class to handle settings for cors.
 */
class Cors_Settings {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Cors_Settings.
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
	 * Constructor for Cors_Settings.
	 */
	public function __construct() {
		add_filter( 'allowed_http_origins', array( $this, 'add_allowed_origins' ) );
		add_action( 'init', array( $this, 'set_cors_headers' ) );
	}

	/**
	 * Add static URL to allowed origins.
	 *
	 * @param  array $origins list of allowed origins.
	 * @return array
	 */
	public function add_allowed_origins( $origins ) {
		$static_url = get_option( 'ssh_static_url' );

		if ( ! empty( $static_url ) ) {
			$origins[] = $static_url;
		}
		return $origins;
	}

	/**
	 * Handle CORS on init.
	 *
	 * @return void
	 */
	public function set_cors_headers() {
		$origin     = get_http_origin();
		$static_url = untrailingslashit( get_option( 'ssh_static_url' ) );

		if ( ! empty( $static_url ) ) {
			if ( $origin === $static_url ) {
				header( 'Access-Control-Allow-Origin: ' . $static_url );
				header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
				header( 'Access-Control-Allow-Credentials: true' );
				header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, X-WP-Nonce, Content-Type, Accept, Authorization ' );

				if ( 'OPTIONS' == $_SERVER['REQUEST_METHOD'] ) {
					status_header( 200 );
					exit();
				}
			}
		}
	}
}