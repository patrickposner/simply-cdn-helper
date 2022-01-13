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
		$username = get_option( 'ssh_username' );
		$password = get_option( 'ssh_app_password' );

		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ),
			),
		);

		$response = wp_remote_get( 'https://manage.simplystatic.io/wp-json/ssc/v1/cdn', $args );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$result = json_decode( $response['body'] );

				return $result->data->key;
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
		$username = get_option( 'ssh_username' );
		$password = get_option( 'ssh_app_password' );
		$site_id  = get_option( 'ssh_app_site_id' );

		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ),
			),
		);

		$response = wp_remote_get( 'https://manage.simplystatic.io/wp-json/ssc/v1/site/' . $site_id, $args );

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$result = json_decode( $response['body'] );

				return $result->data;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
