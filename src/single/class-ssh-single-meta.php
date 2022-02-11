<?php

namespace ssh;

/**
 * Class to handle meta for single.
 */
class Single_Meta {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Single_Meta.
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
	 * Constructor for Single_Meta.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_toolbar_action' ) );
	}


	/**
	 * Add static export action to Block editor toolbar.
	 *
	 * @return void
	 */
	public function add_toolbar_action() {
		wp_enqueue_script( 'ssh-toolbar', SIMPLY_STATIC_HOSTING_URL . '/assets/ssh-toolbar.js', array(), '1.0', true );
		wp_localize_script(
			'ssh-toolbar',
			'ssht',
			array(
				'button_label'         => __( 'Generate Static', 'simply-static-hosting' ),
				'publish_button_label' => __( 'Publish', 'simply-static-hosting' ),
			)
		);
	}


	/**
	 * Adds the meta box container.
	 *
	 * @param array $post_type array of post types.
	 * @return void
	 */
	public function add_metaboxes( $post_type ) {
		$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );

		add_meta_box( 'single-export', __( 'Simply Static', 'simply-static-hosting' ), array( $this, 'render_simply_static' ), apply_filters( 'ssh_single_export_post_types', $post_types ), 'side', 'high' );
	}

	/**
	 * Add static export button.
	 *
	 * @param  object $post current post object.
	 * @return void
	 */
	public function render_simply_static( $post ) {
		?>
		<?php if ( 'publish' === $post->post_status ) : ?>
		<p>
			<a href="#" id="generate-single" class="button button-primary" data-id="<?php echo esc_html( $post->ID ); ?>"><?php esc_html_e( 'Generate static', 'simply-static-hosting' ); ?></a><br>
			<small><?php esc_html_e( 'Use this to generate a static version of the current page you are editing.', 'simply-static-hosting' ); ?></small>
		</p>
		<?php else : ?>
		<p>
			<small><?php esc_html_e( 'You have to publish your post before you can create a static version of it.', 'simply-static-hosting' ); ?></small>
		</p>
		<?php endif; ?>
		<?php
	}
}
