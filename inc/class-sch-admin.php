<?php

namespace sch;

use Simply_Static\Plugin;

/**
 * Class to handle admin settings
 */
class Admin {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Admin.
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
	 * Constructor for Admin.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
		add_action( 'simply_static_settings_view_tab', array( $this, 'output_settings_tab' ), 10 );
		add_action( 'simply_static_settings_view_form', array( $this, 'output_settings_form' ), 10 );
		add_filter( 'simply_static_options', array( $this, 'add_options' ) );

		// Changing the top links.
		remove_action( 'simply_static_admin_info_links', array( Plugin::instance(), 'add_info_links' ) );
		add_action( 'simply_static_admin_info_links', array( $this, 'add_info_links' ) );

		// Only include if Simply Static Pro is not installed.
		if ( ! class_exists( '\simply_static_pro\Build_Settings' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 500 );
		}
	}

	/**
	 * Add information links in admin header.
	 *
	 * @return void
	 */
	public function add_info_links( $info_text ) {
		ob_start();
		?>
        <a href="https://simplycdn.io/documentation/"
           target="_blank"><?php esc_html_e( 'Documentation', 'simply-cdn-helper' ); ?></a>
        <a href="https://simplycdn.io/dashboard/"
           target="_blank"><?php esc_html_e( 'Dashboard', 'simply-cdn-helper' ); ?></a>
		<?php
		$info_text = apply_filters( 'simply_static_info_links', ob_get_clean() );
		echo $info_text;
	}

	/**
	 * Output a new settings tab in Simply Static Settings.
	 *
	 * @return void
	 */
	public function output_settings_tab() {
		?>
        <a class='nav-tab' id='simplycdn-tab' href='#tab-simplycdn'><?php echo esc_html( 'Simply CDN' ); ?></a>
		<?php
	}

	/**
	 * Output content for new settings tab in Simply Static Settings.
	 *
	 * @return void
	 */
	public function output_settings_form() {
		$options = get_option( 'simply-static' );

		// buffer output.
		ob_start();
		include( SCH_PATH . '/inc/views/simplycdn.php' );
		$settings = ob_get_contents();
		ob_end_clean();


		// Replacing placeholders with values from options.
		if ( ! empty( $options['security-token'] ) ) {
			$settings = str_replace( '[SECURITY_TOKEN]', $options['security-token'], $settings );
			$this->set_default_configuration();

		} else {
			$settings = str_replace( '[SECURITY_TOKEN]', '', $settings );
		}

		if ( ! empty( $options['static-url'] ) ) {
			$settings = str_replace( '[STATIC_URL]', $options['static-url'], $settings );
		} else {
			$settings = str_replace( '[STATIC_URL]', '', $settings );
		}

		if ( ! empty( $options['404-path'] ) ) {
			$settings = str_replace( '[404_PATH]', $options['404-path'], $settings );
		} else {
			$settings = str_replace( '[404_PATH]', '', $settings );
		}

		if ( $options['use-forms-hook'] ) {
			$settings = str_replace( '[USE_FORMS_WEBHOOK]', 'checked', $settings );
		} else {
			$settings = str_replace( '[USE_FORMS_WEBHOOK]', '', $settings );
		}


		echo $settings;
	}

	/**
     * Set default configuration for Simply Static on saving security token.
     *
	 * @return void
	 */
	public function set_default_configuration(): void {
		$options = get_option( 'simply-static' );

		if ( ! $options['defaults_set'] ) {
			$data = Api::get_data();

			$static_url = wp_parse_url( $data->cdn->url );

			$options['destination_url_type'] = 'absolute';
			$options['destination_scheme']   = 'https://';
			$options['destination_host']     = $static_url['host'];
			$options['static-url']           = $data->cdn->url;
			$options['delivery_method']      = 'simply-cdn';
			$options['use-forms-hook']       = 'on';
			$options['force_replace_url']    = 'on';
			$options['use_cron']             = 'on';
			$options['defaults_set']         = true;

			update_option( 'simply-static', $options );
		}
	}

	/**
	 * Filter the Simply Static options and add pro options.
	 *
	 * @param array $options array of options.
	 *
	 * @return array
	 */
	public function add_options( $options ) {
		$ss = Plugin::instance();

		$options['security-token'] = $ss->fetch_post_value( 'security-token' );
		$options['use-forms-hook'] = $ss->fetch_post_value( 'use-forms-hook' );
		$options['static-url']     = $ss->fetch_post_value( 'static-url' );
		$options['404-path']       = $ss->fetch_post_value( '404-path' );


		return $options;
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function add_admin_scripts() {
		wp_enqueue_style( 'sch-admin-style', SCH_URL . '/assets/sch-admin.css', array(), '1.0.4', 'all' );
		wp_enqueue_script( 'sch-admin', SCH_URL . '/assets/sch-admin.js', array( 'jquery' ), '1.0.4', true );

		$args = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'cache_nonce'   => wp_create_nonce( 'sch-cache-nonce' ),
			'cache_cleared' => esc_html__( 'Cache cleared successfully.', 'simply-cdn-helper' ),
		);

		wp_localize_script( 'sch-admin', 'sch_ajax', $args );

	}

	/**
	 * Add admin bar menu to visit static website.
	 *
	 * @param \WP_Admin_Bar $admin_bar current admin bar object.
	 *
	 * @return void
	 */
	public function add_admin_bar_menu( $admin_bar ) {
		global $post;

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options    = get_option( 'simply-static' );
		$static_url = $options['static-url'];

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
					'title'  => esc_html__( 'View static URL', 'simply-cdn-helper' ),
					'href'   => $static_url,
					'meta'   => array(
						'title' => esc_html__( 'View static URL', 'simply-cdn-helper' ),
					),
				)
			);
		}
	}
}
