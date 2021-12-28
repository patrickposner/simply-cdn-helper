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

	// autoload files.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	add_action( 'plugins_loaded', 'ssch_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function ssch_run_plugin() {
		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-admin.php';
		ssch\Admin::get_instance();

		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-mailer.php';
		ssch\Mailer::get_instance();

		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-api.php';
		ssch\Api::get_instance();

		require_once SIMPLY_STATIC_CLOUD_HELPER_PATH . 'src/class-ssch-simply-static.php';
		ssch\Simply_Static::get_instance();
	}
}
