<?php

namespace sch;

/**
 * Class to handle Api settings
 */
class Api {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Api.
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
	 * Get site data
	 *
	 * @return object|bool
	 */
	public static function get_data() {
		$token    = get_option( 'sch_token' );
		$response = wp_remote_get( 'https://simplycdn.io?ssecurity-token=' . $token, array() );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$result = json_decode( $response['body'] );
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}