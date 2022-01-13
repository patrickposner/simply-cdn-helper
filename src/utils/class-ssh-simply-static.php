<?php

namespace ssh;

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
		} else {
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 500 );
		}

		add_action( 'plugins_loaded', array( $this, 'set_delivery_method' ) );
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
			});
		</script>
		<?php
	}

	/**
	 * Filter delivery methods.
	 *
	 * @return void
	 */
	public function set_delivery_method() {
		$options = get_option( 'simply-static' );

		$options['delivery_method'] = 'cdn';
		update_option( 'simply-static', $options );
	}

	/**
	 * Add admin bar menu to visit static website.
	 *
	 * @param \WP_Admin_Bar $admin_bar current admin bar object.
	 * @return void
	 */
	public function add_admin_bar_menu( \WP_Admin_Bar $admin_bar ) {
		global $post;

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = get_option( 'simply-static' );

		// If static URL is set.
		if ( ! empty( $options['static-search-url'] ) ) {
			$static_url = untrailingslashit( $options['static-search-url'] );
		} elseif ( ! empty( $options['static-url'] ) ) {
			$static_url = untrailingslashit( $options['static-url'] );
		} else {
			$static_url = '';
		}

		// Check if static URL is set in hosting options.
		$static_hosting_url = get_option( 'ssh_static_url' );

		if ( ! empty( $static_hosting_url ) ) {
			$static_url = $static_hosting_url;
		}

		// Additional Path set?
		if ( ! empty( $options['relative_path'] ) ) {
			$static_url = $static_url . $options['relative_path'];
		}

		// If the current page has an post id we get the permalink and replace it.
		if ( ! empty( $post ) && ! empty( $static_url ) ) {
			$permalink  = get_permalink( $post->ID );
			$static_url = str_replace( untrailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( $static_url ), $permalink );
		}

		if ( ! empty( $static_url ) ) {
			$admin_bar->add_menu(
				array(
					'id'     => 'static-site',
					'parent' => null,
					'group'  => null,
					'title'  => __( 'View static URL', 'simply-static-pro' ),
					'href'   => $static_url,
					'meta' => array(
						'title' => __( 'View static URL', 'simply-static-pro' ),
					),
				)
			);
		}
	}
}
