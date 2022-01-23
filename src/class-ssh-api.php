<?php

namespace ssh;

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
	 * Get CDN API key
	 *
	 * @return string|bool
	 */
	public static function get_cdn_key() {
		$response = wp_remote_get( 'https://manage.simplystatic.io?cdn-key=get', array() );

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

	/**
	 * Get server data
	 *
	 * @return object|bool
	 */
	public static function get_site_data() {
		$site_id  = get_option( 'ssh_app_site_id' );
		$response = wp_remote_get( 'https://manage.simplystatic.io?site-id=' . $site_id, array() );

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
