<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name:       Simply CDN helper
 * Plugin URI:        https://patrickposner.dev
 * Description:       A little helper plugin to connect to simplycdn.io
 * Version:           1.0.4
 * Author:            Patrick Posner
 * Author URI:        https://simplycdn.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-cdn-helper
 * Domain Path:       /languages
 */

define( 'SCH_PATH', plugin_dir_path( __FILE__ ) );
define( 'SCH_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// Bootup plugin.
if ( ! function_exists( 'sch_run_plugin' ) ) {
	add_action( 'plugins_loaded', 'sch_run_plugin' );

	/**
	 * Run plugin
	 *
	 * @return void
	 */
	function sch_run_plugin() {

		// localize.
		$textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
		load_plugin_textdomain( 'simply-cdn-helper', false, $textdomain_dir );

		if ( function_exists( 'simply_static_run_plugin' ) ) {
			// Maybe upgrade options.
			sch_maybe_upgrade_options();

			// Includes from Simply Static.
			require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-task.php';
			require_once SIMPLY_STATIC_PATH . 'src/tasks/class-ss-fetch-urls-task.php';
			require_once SIMPLY_STATIC_PATH . 'src/class-ss-plugin.php';
			require_once SIMPLY_STATIC_PATH . 'src/class-ss-util.php';

			// Add autoupdater.
			require SCH_PATH . 'inc/plugin-update-checker/plugin-update-checker.php';
			$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/patrickposner/simply-cdn-helper/', __FILE__, 'simply-cdn-helper' );
			$updater->setBranch( 'master' );

			// Admin settings.
			require_once SCH_PATH . 'inc/class-sch-admin.php';
			sch\Admin::get_instance();

			// Api.
			require_once SCH_PATH . 'inc/class-sch-api.php';
			sch\Api::get_instance();

			// Cors.
			require_once SCH_PATH . 'inc/class-sch-cors.php';
			sch\Cors::get_instance();

			// Form webhook.
			require_once SCH_PATH . 'inc/class-sch-form-webhook.php';
			sch\Form_Webhook::get_instance();

			// Single exports.
			require_once SCH_PATH . 'inc/class-sch-auto-export.php';
			sch\Auto_Export::get_instance();

			// CDN.
			require_once SCH_PATH . 'inc/deployment/class-sch-simplycdn-task.php';
			require_once SCH_PATH . 'inc/deployment/class-sch-cdn.php';
			require_once SCH_PATH . 'inc/deployment/class-sch-deployment-settings.php';

			sch\Deployment_Settings::get_instance();
		} else {
			add_action( 'admin_notices', 'sch_show_requirements' );
		}
	}
}

function sch_maybe_upgrade_options() {
	$options = get_option( 'simply-static' );

	// Getting old options.
	$old_token      = get_option( 'sch_token' );
	$old_static_url = get_option( 'sch_static_url' );
	$old_404_path   = get_option( 'sch_404_path' );


	if ( ! empty( $options ) && ! empty( $old_token ) ) {
		if ( ! empty( $old_token ) ) {
			$options['security-token'] = $old_token;
		}

		if ( ! empty( $old_static_url ) ) {
			$options['static-url'] = $old_static_url;
		}

		if ( ! empty( $old_404_path ) ) {
			$options['404-path'] = $old_404_path;
		}

		// Update and delete old options.
		update_option('simply-static', $options );

		delete_option('sch_token');
		delete_option('sch_static_url');
		delete_option('sch_404_path');
	}

	// Activate forms integration

	if( ! isset( $options['use-forms-hook'] ) ) {
		$options['use-forms-hook'] = true;
		update_option('simply-static', $options );
	}
}


/**
 * Show conditional message for requirements.
 *
 * @return void
 */
function sch_show_requirements() {
	$message = sprintf( esc_html__( 'The free version of Simply Static is required to use Simply CDN Helper. You can get it %s.', 'simply-cdn-helper' ), '<a target="_blank" href="https://wordpress.org/plugins/simply-static/">here</a>' );
	echo wp_kses_post( '<div class="notice notice-error"><p>' . $message . '</p></div>' );
}