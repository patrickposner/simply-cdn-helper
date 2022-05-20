<?php

namespace sch;

/**
 * Class to handle form webhooks.
 */
class Form_Webhook {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Cors_Settings.
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
	 * Constructor for Form_Webhook.
	 *
	 * @return void
	 */
	public function __construct() {
		$options = get_option( 'simply-static' );

		if ( 'no' === $options['use-forms'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_webhook_scripts' ) );
			add_filter( 'wpcf7_load_js', '__return_false' );
			add_filter( 'gform_form_args', array( $this, 'disable_ajax' ) );
		}
	}

	/**
	 * Enqueue scripts for webhook.
	 *
	 * @return void
	 */
	public function add_webhook_scripts() {
		wp_enqueue_script( 'sch-form-webhook-js', SCH_URL . '/assets/sch-form-webhook.js', array(), '1.0', true );
	}

	/**
	 * Disable ajax in Gravity Forms.
	 *
	 * @param array $args given list or arguments.
	 *
	 * @return mixed
	 */
	public function disable_ajax( $args ) {
		$args['ajax'] = false;
		return $args;
	}
}
