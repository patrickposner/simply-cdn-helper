<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name:       Simply Static Cloud Helper
 * Plugin URI:        https://patrickposner.dev
 * Description:       A little helper plugin to connect to simplystatic.io
 * Version:           1.0
 * Author:            Patrick Posner
 * Author URI:        https://simplystatic.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-static-cloud-helper
 * Domain Path:       /languages
 */

define( 'SIMPLY_STATIC_CLOUD_HELPER_PATH', plugin_dir_path( __FILE__ ) );
define( 'SIMPLY_STATIC_CLOUD_HELPER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// localize.
$textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
load_plugin_textdomain( 'simply-static-cloud-helper', false, $textdomain_dir );

// Bootmanager for Simply Static Cloud plugin.
if ( ! function_exists( 'ssch_run_plugin' ) ) {
	add_action( 'plugins_loaded', 'ssch_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function ssch_run_plugin() {
		// We need the task class from Simply Static to integrate our job.
		require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-task.php';
		require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-fetch-urls-task.php';
		require_once SIMPLY_STATIC_PATH . 'src/class-ss-plugin.php';
		require_once SIMPLY_STATIC_PATH . 'src/class-ss-util.php';

		// Admin settings.
		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-admin.php';
		ssch\Admin::get_instance();

		// Api.
		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-api.php';
		ssch\Api::get_instance();

		// Utils.
		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/utils/class-ssch-simply-static.php';
		ssch\Simply_Static::get_instance();

		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/utils/class-ssch-mailer.php';
		ssch\Mailer::get_instance();

		// Deployment.
		if ( ! class_exists( 'simply_static_pro\Deployment_Settings' ) ) {
			// autoload files.
			if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
				require __DIR__ . '/vendor/autoload.php';
			}

			require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/deployment/class-ssch-cdn-deploy-task.php';
			require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/deployment/class-ssch-cdn.php';
			require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/deployment/class-ssch-deployment-settings.php';
			ssch\Deployment_Settings::get_instance();
		}
	}
}
