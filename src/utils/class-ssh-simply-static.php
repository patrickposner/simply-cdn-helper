<?php

namespace ssh;

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
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 500 );
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
		#sistContainer .nav-tab {
			width: 20%;
		}
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

		$options    = get_option( 'simply-static' );
		$static_url = '';

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
					'title'  => __( 'View static URL', 'simply-static-hosting' ),
					'href'   => $static_url,
					'meta' => array(
						'title' => __( 'View static URL', 'simply-static-hosting' ),
					),
				)
			);
		}
	}
}
