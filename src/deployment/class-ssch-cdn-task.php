<?php

namespace ssch;

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
		$zones         = $bunny_updater->configure_zones();

		$bunny_updater->client->zoneConnect( $zones['storage_zone']['name'], $zones['storage_zone']['password'] );

		// Sub directory?
		$options  = get_option( 'simply-static' );
		$cdn_path = apply_filters( 'ssp_cdn_path', '' );

		// Upload directory.
		$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $this->temp_dir, \RecursiveDirectoryIterator::SKIP_DOTS ) );
		$counter  = 0;

		foreach ( $iterator as $file_name => $file_object ) {
			if ( ! realpath( $file_name ) ) {
				continue;
			}

			$relative_path = str_replace( $this->temp_dir, $cdn_path, realpath( $file_name ) );

			$bunny_updater->upload_file( realpath( $file_name ), $relative_path );
			$counter++;
		}

		$message = sprintf( __( 'Pushed %d pages/files to CDN', 'simply-static-pro' ), $counter );
		$this->save_status_message( $message );

		// Clear Pull zone cache.
		$bunny_updater->purge_cache();
		return true;
	}
}
