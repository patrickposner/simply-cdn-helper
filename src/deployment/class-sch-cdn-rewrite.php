<?php

namespace sch;

/**
 * Class to handle CDN URL rewrites.
 */
class Simply_CDN_Rewrite {
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
	 * Constructor for Cors_Settings.
	 */
	public function __construct() {
		$this->cdn = Simply_CDN::get_instance();

		add_action( 'the_content', array( $this, 'replace_image_url_in_html' ), 99 );
		add_filter( 'wp_handle_upload', array( $this, 'upload_to_cdn' ), 10, 2 );
	}

	/**
	 * Upload file to CDN on upload media.
	 *
	 * @param  array $upload given file.
	 * @param  array $context up or download.
	 * @return array
	 */
	public function upload_to_cdn( $upload, $context ) {
		

		// Get path.
		$real_path     = $upload['file'];
		$relative_path = str_replace( get_bloginfo( 'url' ), '', $upload['url'] );

		// Sub directory?
		if ( ! empty( $cdn->data->cdn->sub_directory ) ) {
			$relative_path = str_replace( get_bloginfo( 'url' ), $this->cdn->data->cdn->sub_directory, $upload['url'] );
		}

		$this->cdn->upload_file( $real_path, $relative_path );

		return $upload;
	}

	/**
	 * Extract and replace all URLs inside of an HTML string.
	 *
	 * Note this does not factor in external images. Domain check may be required.
	 *
	 * @param string $content HTML that may contain images.
	 * @return string HTML with possibly images that have been filtered
	 */
	public function replace_image_url_in_html( $content ) {
		// Sub directory?
		$cdn_path = '';

		if ( ! empty( $this->cdn->data->cdn->sub_directory ) ) {
			$cdn_path = '/' . $this->cdn->data->cdn->sub_directory;
		}

		// Get static URL path.
		$static_url      = wp_parse_url( get_option( 'sch_static_url' ) );
		$origin_url      = wp_parse_url( get_bloginfo( 'url' ) );
		$static_url_path = $static_url['host'] . $cdn_path;

		// Replace in HTML.
		preg_match_all( '/(\/\/\S+\.(?:jpg|png|gif|webp))/', $content, $images );

		foreach ( $images[0] as $image ) {
			$new_path  = str_replace( $origin_url['host'], $static_url_path, $image );
			$image_url = $static_url['scheme'] . ':' . $new_path;

			// Check if file exists.
			$handle = fopen( $image_url, 'r' );

			if ( $handle ) {
				$content = str_replace( $image, $new_path, $content );
			}
		}
		return $content;
	}
}
