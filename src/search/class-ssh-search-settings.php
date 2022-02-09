<?php

namespace ssh;

use Simply_Static;

/**
 * Class to handle settings for deployment.
 */
class Search_Settings {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Search_Settings.
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
	 * Constructor for Search_Settings.
	 */
	public function __construct() {
		add_action( 'simply_static_settings_view_tab', array( $this, 'output_settings_tab' ), 30 );
		add_action( 'simply_static_settings_view_form', array( $this, 'output_settings_form' ), 30 );
		add_filter( 'simply_static_options', array( $this, 'add_options' ) );
		add_filter( 'simply_static_class_name', array( $this, 'check_class_name' ), 20, 2 );
	}

	/**
	 * Output a new settings tab in Simply Static Settings.
	 *
	 * @return void
	 */
	public function output_settings_tab() {
		?>
		<a class='nav-tab' id='search-tab' href='#tab-search'><?php esc_html_e( 'Search', 'simply-static-hosting' ); ?></a>
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
		include( SIMPLY_STATIC_HOSTING_PATH . '/src/search/views/search.php' );
		$settings = ob_get_contents();
		ob_end_clean();

		// Replacing placeholders with values from options.
		if ( ! empty( $options['use-search'] ) ) {
			if ( 'no' === $options['use-search'] ) {
				$select_options = '<option selected value="no">' . __( 'no', 'simply-static-hosting' ) . '</option><option value="yes">' . __( 'yes', 'simply-static-hosting' ) . '</option>';
			} else {
				$select_options = '<option selected value="yes">' . __( 'yes', 'simply-static-hosting' ) . '</option><option value="no">' . __( 'no', 'simply-static-hosting' ) . '</option>';
			}
			$settings = str_replace( '[USE_SEARCH]', $select_options, $settings );
		} else {
			$select_options = '<option value="no">' . __( 'no', 'simply-static-hosting' ) . '</option><option value="yes">' . __( 'yes', 'simply-static-hosting' ) . '</option>';
			$settings       = str_replace( '[USE_SEARCH]', $select_options, $settings );
		}

		if ( ! empty( $options['search-index-title'] ) ) {
			$settings = str_replace( '[SEARCH_INDEX_TITLE]', $options['search-index-title'], $settings );
		} else {
			$settings = str_replace( '[SEARCH_INDEX_TITLE]', 'title', $settings );
		}

		if ( ! empty( $options['search-index-content'] ) ) {
			$settings = str_replace( '[SEARCH_INDEX_CONTENT]', $options['search-index-content'], $settings );
		} else {
			$settings = str_replace( '[SEARCH_INDEX_CONTENT]', 'body', $settings );
		}

		if ( ! empty( $options['search-index-excerpt'] ) ) {
			$settings = str_replace( '[SEARCH_INDEX_EXCERPT]', $options['search-index-excerpt'], $settings );
		} else {
			$settings = str_replace( '[SEARCH_INDEX_EXCERPT]', '.entry-content', $settings );
		}

		if ( ! empty( $options['algolia-app-id'] ) ) {
			$settings = str_replace( '[ALGOLIA_APP_ID]', $options['algolia-app-id'], $settings );
		} else {
			$settings = str_replace( '[ALGOLIA_APP_ID]', '', $settings );
		}

		if ( ! empty( $options['algolia-admin-api-key'] ) ) {
			$settings = str_replace( '[ALGOLIA_ADMIN_API_KEY]', $options['algolia-admin-api-key'], $settings );
		} else {
			$settings = str_replace( '[ALGOLIA_ADMIN_API_KEY]', '', $settings );
		}

		if ( ! empty( $options['algolia-search-api-key'] ) ) {
			$settings = str_replace( '[ALGOLIA_SEARCH_API_KEY]', $options['algolia-search-api-key'], $settings );
		} else {
			$settings = str_replace( '[ALGOLIA_SEARCH_API_KEY]', '', $settings );
		}

		if ( ! empty( $options['algolia-index'] ) ) {
			$settings = str_replace( '[ALGOLIA_INDEX]', $options['algolia-index'], $settings );
		} else {
			$settings = str_replace( '[ALGOLIA_INDEX]', 'simply_static', $settings );
		}

		if ( ! empty( $options['algolia-selector'] ) ) {
			$settings = str_replace( '[ALGOLIA_SELECTOR]', $options['algolia-selector'], $settings );
		} else {
			$settings = str_replace( '[ALGOLIA_SELECTOR]', '.search-field', $settings );
		}

		echo $settings;
	}

	/**
	 * Filter the Simply Static options and add pro options.
	 *
	 * @param array $options array of options.
	 * @return array
	 */
	public function add_options( $options ) {
		$ss = Simply_Static\Plugin::instance();

		$options['use-search']             = $ss->fetch_post_value( 'use-search' );
		$options['search-index-title']     = $ss->fetch_post_value( 'search-index-title' );
		$options['search-index-content']   = $ss->fetch_post_value( 'search-index-content' );
		$options['search-index-excerpt']   = $ss->fetch_post_value( 'search-index-excerpt' );
		$options['search-excludable']      = $ss->fetch_post_array_value( 'search-excludable' );
		$options['algolia-app-id']         = $ss->fetch_post_value( 'algolia-app-id' );
		$options['algolia-admin-api-key']  = $ss->fetch_post_value( 'algolia-admin-api-key' );
		$options['algolia-search-api-key'] = $ss->fetch_post_value( 'algolia-search-api-key' );
		$options['algolia-index']          = $ss->fetch_post_value( 'algolia-index' );
		$options['algolia-selector']       = $ss->fetch_post_value( 'algolia-selector' );

		return $options;
	}

	/**
	 * Modify task class name in Simply Static.
	 *
	 * @param string $class_name current class name.
	 * @param string $task_name current task name.
	 * @return string
	 */
	public function check_class_name( $class_name, $task_name ) {
		if ( 'search_index' === $task_name ) {
			return 'ssh\\' . ucwords( $task_name ) . '_Task';
		}
		return $class_name;
	}
}
