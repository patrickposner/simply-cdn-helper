<?php

namespace ssch;

use simply_static_pro;

/**
 * Class to handle Simply_Static settings
 */
class Simply_Static {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Simply_Static.
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
	 * Constructor for Simply_Static.
	 */
	public function __construct() {
		if ( class_exists( 'simply_static_pro\Deployment_Settings' ) ) {
			// Remove deployment settings.
			$deploy_settings = simply_static_pro\Deployment_Settings::get_instance();

			remove_action( 'simply_static_settings_view_tab', array( $deploy_settings, 'output_settings_tab' ), 10 );
			remove_action( 'simply_static_settings_view_form', array( $deploy_settings, 'output_settings_form' ), 10 );

			// Set CDN credentials.
			add_action( 'plugins_loaded', array( $this, 'set_deployment_filters' ) );
		}
		add_action( 'admin_footer', array( $this, 'cleanup_ui' ) );
	}



	/**
	 * Clean up Simply Static UI.
	 *
	 * @return void
	 */
	public function cleanup_ui() {
		?>
		<style>
		.url-dest-option {
			display: none !important;
		}
		.url-dest-option.active {
			display: block !important;
		}
		<?php if ( class_exists( 'simply_static_pro\Deployment_Settings' ) ) : ?>
		#sistContainer .nav-tab {
			width: 12.5% !important;
		}
		<?php endif; ?>
		</style>
		<script>
			jQuery(document).ready(function( $ ) {
				$( "#deliveryMethod" ).val("cdn").change();
				$( "#deliveryMethod" ).prop( "disabled", true );
			});
		</script>
		<?php
	}

	/**
	 * Set filter to deploy to simplystatic.io CDN.
	 *
	 * @return void
	 */
	public function set_deployment_filters() {
		$options = get_option( 'simply-static' );

		$options['delivery_method'] = 'cdn';
		update_option( 'simply-static', $options );


		add_filter( 'ssp_cdn_key', function() {
			$api_key = Api::get_cdn_key();

			if ( ! empty( $api_key ) ) {
				return $api_key;
			}
		});

		add_filter( 'ssp_cdn_pull_zone', function() {
			$data = Api::get_site_data();

			if ( ! empty( $data->cdn->pull_zone ) ) {
				return 'ssc-' . $data->cdn->pull_zone;
			}
		});

		add_filter( 'ssp_cdn_storage_zone', function() {
			$data = Api::get_site_data();

			if ( ! empty( $data->cdn->storage_zone ) ) {
				return 'ssc-' . $data->cdn->storage_zone;
			}
		});

		add_filter( 'ssp_cdn_path', function() {
			$data = Api::get_site_data();

			if ( ! empty( $data->cdn->sub_directory ) ) {
				return $data->cdn->sub_directory;
			}
		});
	}
}
