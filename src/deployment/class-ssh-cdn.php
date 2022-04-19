<?php

namespace ssh;

/**
 * Class to handle CDN updates.
 */
class CDN {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Contains new BunnyAPI client.
	 *
	 * @var object
	 */
	public $client;


	/**
	 * Returns instance of CDN.
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
	 * Get current pull zone.
	 *
	 * @return bool|array
	 */
	public function get_pull_zone() {
		$data          = Api::get_site_data();
		$api_key       = Api::get_cdn_key();
		$api_pull_zone = 'sshm-' . $data->cdn->pull_zone;

		// Get pullzones.
		$response = wp_remote_get(
			'https://api.bunny.net/pullzone',
			array(
				'headers' => array(
					'AccessKey'    => $api_key,
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json; charset=utf-8',
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$body       = wp_remote_retrieve_body( $response );
				$pull_zones = json_decode( $body );

				foreach ( $pull_zones as $pull_zone ) {
					if ( $pull_zone->Name === $api_pull_zone ) {
						return array(
							'name'       => $pull_zone->Name,
							'zone_id'    => $pull_zone->Id,
							'storage_id' => $pull_zone->StorageZoneId,
						);
					}
				}
			} else {
				$error_message = wp_remote_retrieve_response_message( $response );
				error_log( $error_message );
				return false;
			}
		} else {
			$error_message = $response->get_error_message();
			error_log( $error_message );
			return false;
		}
	}

	/**
	 * Get current storage zone.
	 *
	 * @return bool|array
	 */
	public function get_storage_zone() {
		$data             = Api::get_site_data();
		$api_key          = Api::get_cdn_key();
		$api_storage_zone = 'sshm-' . $data->cdn->storage_zone;

		// Get storage zones.
		$response = wp_remote_get(
			'https://api.bunny.net/storagezone',
			array(
				'headers' => array(
					'AccessKey'    => $api_key,
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json; charset=utf-8',
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$body          = wp_remote_retrieve_body( $response );
				$storage_zones = json_decode( $body );

				foreach ( $storage_zones as $storage_zone ) {
					if ( $storage_zone->Name === $api_storage_zone ) {
						return array(
							'name'       => $storage_zone->Name,
							'storage_id' => $storage_zone->Id,
							'password'   => $storage_zone->Password
						);
					}
				}
			} else {
				$error_message = wp_remote_retrieve_response_message( $response );
				error_log( $error_message );
				return false;
			}
		} else {
			$error_message = $response->get_error_message();
			error_log( $error_message );
			return false;
		}
	}


	/**
	 * Upload file to BunnyCDN storage.
	 *
	 * @param string $current_file_path current local file path.
	 * @param string $cdn_path file path in storage.
	 * @return void
	 */
	public function upload_file( $current_file_path, $cdn_path ) {
		$data         = Api::get_site_data();
		$storage_zone = $this->get_storage_zone();

		$ftp_connection = ftp_connect( 'storage.bunnycdn.com' );
		ftp_pasv( $ftp_connection, true );

		if ( $ftp_connection ) {
			ftp_login( $ftp_connection, $storage_zone['name'], $data->cdn->access_key );

			// Upload files.
			$ftp_upload = ftp_put( $ftp_connection, $cdn_path, $current_file_path, FTP_ASCII );

			if ( ! $ftp_upload ) {
				error_log( sprintf( esc_html__( 'The file located at %s could not be uploaded via FTP.', 'simply-static-hosting' ), $current_file_path ) );
			}

			// Close connection.
			ftp_close( $ftp_connection );
		}
	}

	/**
	 * Delete file from BunnyCDN storage.
	 *
	 * @return string
	 */
	public function delete_file( $path ) {
		$zones = $this->configure_zones();
		$data  = Api::get_site_data();

		$response = wp_remote_request(
			'https://storage.bunnycdn.com/' . $zones['storage_zone']['name'] . '/' . $path . '/',
			array(
				'method' => 'DELETE',
				'headers' => array( 'AccessKey' => $data->cdn->access_key ),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				return true;
			} else {
				$error_message = wp_remote_retrieve_response_message( $response );
				error_log( $error_message );
				return false;
			}
		} else {
			$error_message = $response->get_error_message();
			error_log( $error_message );
			return false;
		}
	}

	/**
	 * Purge Zone Cache in BunnyCDN pull zone.
	 *
	 * @return bool
	 */
	public function purge_cache() {
		$zones   = $this->configure_zones();
		$api_key = Api::get_cdn_key();

		$response = wp_remote_post(
			'https://api.bunny.net/pullzone/' . $zones['pull_zone']['zone_id'] . '/purgeCache',
			array(
				'headers' => array(
					'AccessKey' => $api_key,
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
