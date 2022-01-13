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
	 * Constructor
	 */
	public function __construct() {
		$options = get_option( 'simply-static' );
		$client  = new \Corbpie\BunnyCdn\BunnyAPI( 0 );

		// Authenticate.
		$api_key = Api::get_cdn_key();
		$client->apiKey( $api_key );

		$this->client = $client;
	}

	/**
	 * Configure BunnyCDN before adding files to it.
	 *
	 * @return array
	 */
	public function configure_zones() {
		$zone_config = array();
		$options     = get_option( 'simply-static' );
		$data        = Api::get_site_data();


		// Handling Pull zone.
		$pull_zones = json_decode( $this->client->listPullZones() );

		// Get data from API.
		$api_pull_zone    = 'sshm-' . $data->cdn->pull_zone;
		$api_storage_zone = 'sshm-' . $data->cdn->storage_zone;

		foreach ( $pull_zones as $pull_zone ) {
			if ( $pull_zone->Name === $api_pull_zone ) {
				$zone_config['pull_zone'] = array(
					'name'       => $pull_zone->Name,
					'zone_id'    => $pull_zone->Id,
					'storage_id' => $pull_zone->StorageZoneId,
				);
			}
		}

		// Handling Storage Zone.
		$storage_zones = json_decode( $this->client->listStorageZones() );

		foreach ( $storage_zones as $storage_zone ) {
			if ( $storage_zone->Name === $api_storage_zone ) {
				$zone_config['storage_zone'] = array(
					'name'       => $storage_zone->Name,
					'storage_id' => $storage_zone->Id,
					'password'   => $storage_zone->Password
				);
			}
		}

		// If there was no storage zone we create one and configure it.
		if ( empty( $zone_config['storage_zone'] ) ) {
			$storage_zone = $this->client->addStorageZone( $api_storage_zone );
		}

		return $zone_config;
	}

	/**
	 * Upload file to BunnyCDN storage.
	 *
	 * @param string $current_file_path current local file path.
	 * @param string $cdn_path file path in storage.
	 * @return string
	 */
	public function upload_file( $current_file_path, $cdn_path ) {
		if ( ! empty( $current_file_path ) ) {
			try {
				$this->client->uploadFile( $current_file_path, $cdn_path );
			} catch ( Exception $e ) {
				return $e->getMessage();
			} catch ( Error $e ) {
				return $e->getMessage();
			}
		}
	}

	/**
	 * Purge Zone Cache in BunnyCDN pull zone.
	 *
	 * @return void
	 */
	public function purge_cache() {
		$zones = $this->configure_zones();

		$this->client->purgeCache( 'https://' . $zones['pull_zone']['name'] . '.b-cdn.net' );
	}
}
