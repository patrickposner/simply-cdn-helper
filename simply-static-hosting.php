<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name:       Simply Static Hosting
 * Plugin URI:        https://patrickposner.dev
 * Description:       A little helper plugin to connect to simplystatic.io
 * Version:           1.2
 * Author:            Patrick Posner
 * Author URI:        https://simplystatic.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-static-hosting
 * Domain Path:       /languages
 */

define( 'SIMPLY_STATIC_HOSTING_PATH', plugin_dir_path( __FILE__ ) );
define( 'SIMPLY_STATIC_HOSTING_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// localize.
$textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
load_plugin_textdomain( 'simply-static-hosting', false, $textdomain_dir );

// Set default options on activation.
register_activation_hook(
	__FILE__,
	function() {
		$ss_options                    = get_option( 'simply-static' );
		$ss_options['delivery_method'] = 'cdn';
		$ss_options['use_cron']        = 'on';

		update_option( 'simply-static', $ss_options );
		update_option( 'ssh_restrict_access', 'yes' );
	}
);

// Bootup plugin.
if ( ! function_exists( 'ssh_run_plugin' ) ) {
	add_action( 'plugins_loaded', 'ssh_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function ssh_run_plugin() {
		// autoload files.
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require __DIR__ . '/vendor/autoload.php';
		}

		// We need the task class from Simply Static to integrate our job.
		require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-task.php';
		require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-fetch-urls-task.php';
		require_once SIMPLY_STATIC_PATH . 'src/class-ss-plugin.php';
		require_once SIMPLY_STATIC_PATH . 'src/class-ss-util.php';

		// Admin settings.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/class-ssh-admin.php';
		ssh\Admin::get_instance();

		// Api.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/class-ssh-api.php';
		ssh\Api::get_instance();

		// Updater.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/class-ssh-updater.php';
		ssh\Updater::get_instance();

		// Utils.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-helper.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-simply-static.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-cors.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-mailer.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-restriction.php';

		ssh\Helper::get_instance();
		ssh\Simply_Static::get_instance();
		ssh\Cors_Settings::get_instance();
		ssh\Mailer::get_instance();
		ssh\Restriction::get_instance();

		// Single.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/single/class-ssh-single-meta.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/single/class-ssh-single.php';

		ssh\Single_Meta::get_instance();
		ssh\Single::get_instance();

		// Search.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/search/class-ssh-search-settings.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/search/class-ssh-search-algolia.php';

		ssh\Search_Settings::get_instance();
		ssh\Search_Algolia::get_instance();

		// CDN Deployment.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-cdn-task.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-cdn.php';
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-deployment-settings.php';
		ssh\Deployment_Settings::get_instance();
	}
}
