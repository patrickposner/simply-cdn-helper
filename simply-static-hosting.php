<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name:       Simply Static Hosting
 * Plugin URI:        https://patrickposner.dev
 * Description:       A little helper plugin to connect to simplystatic.io
 * Version:           1.0
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

// Bootup plugin.
if ( ! function_exists( 'ssh_run_plugin' ) ) {
	add_action( 'plugins_loaded', 'ssh_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function ssh_run_plugin() {
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

		// Utils.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-simply-static.php';
		ssh\Simply_Static::get_instance();

		// SMTP.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-mailer.php';
		ssh\Mailer::get_instance();

		// Restriction.
		require_once SIMPLY_STATIC_HOSTING_PATH . 'src/utils/class-ssh-restriction.php';
		ssh\Restriction::get_instance();

		// Deployment.
		if ( ! class_exists( 'simply_static_pro\Deployment_Settings' ) ) {
			// autoload files.
			if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
				require __DIR__ . '/vendor/autoload.php';
			}

			require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-cdn-task.php';
			require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-cdn.php';
			require_once SIMPLY_STATIC_HOSTING_PATH . 'src/deployment/class-ssh-deployment-settings.php';
			ssh\Deployment_Settings::get_instance();
		}
	}
}