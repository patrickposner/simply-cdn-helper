<?php

namespace ssh;

use Simply_Static;

/**
 * Class to handle settings for fuse.
 */
class Helper {
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
		add_action( 'ss_finished_fetching_pages', array( $this, 'add_configs' ), 99 );
		add_action( 'wp_head', array( $this, 'add_config_meta_tag' ) );
		add_action( 'init', array( $this, 'clean_head' ) );
		add_action( 'admin_footer', array( $this, 'dev_mode_warning' ) );
		add_action( 'wp_footer', array( $this, 'insert_post_id' ) );
	}

	/**
	 * Maybe show dev mode waring.
	 *
	 * @return void
	 */
	public function dev_mode_warning() {
		$screen = get_current_screen();

		if ( 'simply-static' === $screen->parent_base ) {
			?>
			<?php if ( defined( 'SSH_DEV_MODE' ) && true === SSH_DEV_MODE ) : ?>
			<script>
			jQuery(document).ready(function( $ ) {
				$('.actions').append('<span style="float:left;width:100%;padding: 5px 0;color:red;"><?php echo esc_html_e( "Warning, you are currently running in development mode. Make sure to remove define( 'SSH_DEV_MODE', true ); from your wp-config.php before you run a new static export.", 'simply-static-hosting' ); ?></span>');
			});
			</script>
			<?php endif; ?>
			<?php
		}
	}

	/**
	 * Add config URL path as meta tag.
	 *
	 * @return void
	 */
	public function add_config_meta_tag() {
		$options    = get_option( 'simply-static' );
		$static_url = '';
		$origin_url = untrailingslashit( get_bloginfo( 'url' ) );

		if ( ! empty( get_option( 'ssh_static_url' ) ) ) {
			$static_url = get_option( 'ssh_static_url' );
		}

		$additional_path = '';

		if ( ! empty( $options['relative_path'] ) ) {
			$additional_path = $options['relative_path'];
		}

		$config_path = $additional_path . apply_filters( 'ssh_static_config_path', '/wp-content/plugins/simply-static-hosting/configs/' );
		?>
		<meta name="ssh-url" content="<?php echo esc_url( $static_url ); ?>">
		<?php if ( defined( 'SSH_DEV_MODE' ) && true === SSH_DEV_MODE ) : ?>
		<meta name="ssh-config-url" content="<?php echo esc_url( $origin_url . apply_filters( 'ssh_static_config_path', '/wp-content/plugins/simply-static-hosting/configs/' ) ); ?>">
		<?php else : ?>
			<meta name="ssh-config-url" content="<?php echo esc_url( $static_url ) . esc_html( $config_path ); ?>">
		<?php endif; ?>
		<?php
	}

	/**
	 * Add post id to each page.
	 *
	 * @return void
	 */
	public function insert_post_id() {
		?>
		<span class="ssh-id" style="display:none"><?php echo esc_html( get_the_id() ); ?></span>
		<?php
	}

	/**
	 * Add configs to static export.
	 *
	 * @return void
	 */
	public function add_configs() {
		$options    = Simply_Static\Options::instance();
		$temp_dir   = $options->get_archive_dir();
		$config_dir = SIMPLY_STATIC_HOSTING_PATH . 'configs/';

		if ( 'local' === $options->get( 'delivery_method' ) ) {
			$copy = $this->copy_directory( $config_dir, $options->get( 'local_dir' ) . apply_filters( 'ssh_configs_static_path', 'wp-content/plugins/simply-static-hosting/configs/' ) );
		} else {
			$copy = $this->copy_directory( $config_dir, $temp_dir . apply_filters( 'ssh_configs_static_path', 'wp-content/plugins/simply-static-hosting/configs/' ) );
		}
	}

	/**
	 * Copy an entire directory.
	 *
	 * @param string $source the soruce path.
	 * @param string $target the target path.
	 * @return void
	 */
	public function copy_directory( $source, $target ) {
		if ( is_dir( $source ) ) {
			wp_mkdir_p( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry;
				if ( is_dir( $Entry ) ) {
					full_copy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}
			$d->close();
		} else {
			copy( $source, $target );
		}
	}

	/**
	 * Cleans up the head area of WordPress.
	 *
	 * @return void
	 */
	public function clean_head() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}
}
