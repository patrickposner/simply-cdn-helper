<?php

namespace ssh;

use Simply_Static;

/**
 * Class to handle settings for deployment.
 */
class Deployment_Settings {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Deployment_Settings.
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
	 * Constructor for Deployment_Settings.
	 */
	public function __construct() {
		add_filter( 'simplystatic.archive_creation_job.task_list', array( $this, 'modify_task_list' ), 99, 2 );
		add_filter( 'simply_static_class_name', array( $this, 'check_class_name' ), 10, 2 );
		add_action( 'simply_static_delivery_methods', array( $this, 'add_delivery_method' ) );
		add_action( 'simply_static_delivery_method_description', array( $this, 'add_delivery_method_description' ) );
		add_action( 'wp_ajax_clear_cache', array( $this, 'clear_cache' ) );
	}

	/**
	 * Add delivery method to Simply Static settings.
	 *
	 * @return void
	 */
	public function add_delivery_method() {
		$options = get_option( 'simply-static' );
		?>
		<option value='cdn' <?php Simply_Static\Util::selected_if( 'cdn' === $options['delivery_method'] ); ?>><?php esc_html_e( 'CDN', 'simply-static-hosting' ); ?></option>
		<?php
	}

	/**
	 * Add delivery method to Simply Static settings.
	 *
	 * @return void
	 */
	public function add_delivery_method_description() {
		?>
		<tr class="delivery-method cdn-deploy" style="display:none">
			<th></th>
			<td>
				<p><?php esc_html_e( 'When using CDN please make sure you are using relative URLs and that you have configured the necessary settings in Simply Static -> Settings -> Deployment', 'simply-static-hosting' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Add tasks to Simply Static task list.
	 *
	 * @param array  $task_list current task list.
	 * @param string $delivery_method current delivery method.
	 * @return array
	 */
	public function modify_task_list( $task_list, $delivery_method ) {
		if ( 'cdn' === $delivery_method ) {
			$task_list = array( 'setup', 'fetch_urls', 'cdn', 'wrapup' );

			return $task_list;
		}
		return $task_list;
	}

	/**
	 * Modify task class name in Simply Static.
	 *
	 * @param string $class_name current class name.
	 * @param string $task_name current task name.
	 * @return string
	 */
	public function check_class_name( $class_name, $task_name ) {
		if ( 'cdn' === $task_name ) {
			return 'ssh\\' . strtoupper( $task_name ) . '_Task';
		}
		return $class_name;
	}

	/**
	 * Clear cache via ajax.
	 *
	 * @return void
	 */
	public function clear_cache() {
		$nonce = esc_html( $_POST['nonce'] );

		if ( ! wp_verify_nonce( $nonce, 'ssh-cache-nonce' ) ) {
			die();
		}

		$cdn = CDN::get_instance();
		$cdn->purge_cache();

		$response = array( 'success' => true );

		print wp_json_encode( $response );
		exit;
	}
}
