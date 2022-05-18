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
	private static ?object $instance = null;

	/**
	 * Returns instance of Api.
	 *
	 * @return object|null
	 */
	public static function get_instance(): object|null {

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
	public static function get_data(): object|bool {
		$token    = get_option( 'sch_token' );
		$response = wp_remote_get( 'https://simplycdn.io?security-token=' . $token, array() );

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
