<?php

namespace ssh;

use Simply_Static;

/**
 * Class which handles GitHub commits.
 */
class CDN_Task extends Simply_Static\Task {
	/**
	 * The task name.
	 *
	 * @var string
	 */
	protected static $task_name = 'cdn';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$options = Simply_Static\Options::instance();

		$this->data       = Api::get_site_data();
		$this->options    = $options;
		$this->temp_dir   = $options->get_archive_dir();
		$this->start_time = $options->get( 'archive_start_time' );
	}

	/**
	 * Perform action to run on commit task.
	 *
	 * @return bool
	 */
	public function perform() {
		// Setup BunnyCDN client.
		$bunny_updater = CDN::get_instance();

		// Sub directory?
		$cdn_path = '';

		if ( ! empty( $bunny_updater->data->cdn->sub_directory ) ) {
			$cdn_path = $bunny_updater->data->cdn->sub_directory . '/';
		}

		$message = __( 'Starts to transfer of pages/files to CDN', 'simply-static-hosting' );
		$this->save_status_message( $message );

		// Upload directory.
		$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $this->temp_dir, \RecursiveDirectoryIterator::SKIP_DOTS ) );
		$counter  = 0;

		// Open FTP connection.
		$storage_zone   = $bunny_updater->get_storage_zone();
		$ftp_connection = ftp_connect( 'storage.bunnycdn.com' );

		ftp_pasv( $ftp_connection, true );

		if ( $ftp_connection ) {
			ftp_login( $ftp_connection, $storage_zone['name'], $this->data->cdn->access_key );

			// Set execution time for transfer.
			set_time_limit( 0 );

			// Upload files.
			foreach ( $iterator as $file_name => $file_object ) {
				if ( ! realpath( $file_name ) ) {
					continue;
				}
	
				$relative_path = str_replace( $this->temp_dir, $cdn_path, realpath( $file_name ) );
				$ftp_upload    = ftp_put( $ftp_connection, $relative_path, realpath( $file_name ), FTP_BINARY );

				if ( ! $ftp_upload ) {
					error_log( sprintf( esc_html__( 'The file located at %s could not be uploaded via FTP.', 'simply-static-hosting' ), $current_file_path ) );
				}

				$counter++;
			}
		}

		// Close connection.
		ftp_close( $ftp_connection );

		$message = sprintf( __( 'Pushed %d pages/files to CDN', 'simply-static-hosting' ), $counter );
		$this->save_status_message( $message );

		// Maybe add 404.
		$cdn_404_path = get_option( 'ssh_404_path' );

		if ( ! empty( $cdn_404_path ) && realpath( $this->temp_dir . untrailingslashit( $cdn_404_path ) . '/index.html' ) ) {

			// Rename and copy file.
			$src_error_file  = $this->temp_dir . untrailingslashit( $cdn_404_path ) . '/index.html';
			$dst_error_file  = $this->temp_dir . 'bunnycdn_errors/404.html';
			$error_directory = dirname( $dst_error_file );

			if ( ! is_dir( $error_directory ) ) {
				wp_mkdir_p( $error_directory );
				chmod( $error_directory, 0777 );
			}

			copy( $src_error_file, $dst_error_file );

			// Upload 404 template file.
			$error_file_path     = realpath( $this->temp_dir . 'bunnycdn_errors/404.html' );
			$error_relative_path = str_replace( $this->temp_dir, '', $error_file_path );

			if ( $error_file_path ) {
				$bunny_updater->upload_file( $error_file_path, $error_relative_path );
			}
		}

		// Clear Pull zone cache.
		$bunny_updater->purge_cache();
		return true;
	}
}
