<?php

namespace sch;

/**
 * Class to handle CDN updates.
 */
class Simply_CDN {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Contains data array for the site.
	 *
	 * @var object|bool
	 */
	public $data;

	/**
	 * Returns instance of CDN.
	 *
	 * @return object|null
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for CDN.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->data = Api::get_data();
	}

	/**
	 * Upload file to BunnyCDN storage.
	 *
	 * @param string $current_file_path current local file path.
	 * @param string $cdn_path file path in storage.
	 *
	 * @return void
	 */
	public function upload_file( string $current_file_path, string $cdn_path ) {
		$ftp_connection = ftp_connect( 'storage.bunnycdn.com' );

		ftp_pasv( $ftp_connection, true );

		if ( $ftp_connection ) {
			ftp_login( $ftp_connection, $this->data->cdn->storage_zone->name, $this->data->cdn->access_key );

			// Set execution time for transfer.
			set_time_limit( 0 );

			// Upload files.
			$ftp_upload = ftp_put( $ftp_connection, $cdn_path, $current_file_path, FTP_BINARY );

			if ( ! $ftp_upload ) {
				error_log( sprintf( esc_html__( 'The file located at %s could not be uploaded via FTP.', 'simply-static-hosting' ), $current_file_path ) );
			}

			// Close connection.
			ftp_close( $ftp_connection );
		}
	}
}
