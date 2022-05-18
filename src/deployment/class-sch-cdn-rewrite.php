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
	private static ?object $instance = null;
	private null|object $cdn;

	/**
	 * Returns instance of Cors_Settings.
	 *
	 * @return object
	 */
	public static function get_instance(): object|null {

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

		if ( get_option( 'sch_replace_html_urls' ) ) {
			add_action( 'the_content', array( $this, 'replace_image_url_in_html' ), 99 );
		}

		if ( get_option( 'sch_replace_image_urls' ) ) {
			add_filter('wp_get_attachment_url', array( $this, 'replace_image_url' ) );
			add_filter('wp_get_attachment_image',     array( $this, 'replace_image_url' ) );
			add_filter('wp_get_attachment_image_src', array( $this, 'replace_image_url' ) );
		}

		add_filter( 'wp_handle_upload', array( $this, 'upload_to_cdn' ) );
	}

	/**
	 * Upload file to CDN on upload media.
	 *
	 * @param array $upload given file.
	 *
	 * @return array
	 */
	public function upload_to_cdn( array $upload ): array {
		// Get path.
		$real_path     = $upload['file'];
		$relative_path = str_replace( get_bloginfo( 'url' ), '', $upload['url'] );

		// Subdirectory?
		if ( ! empty( $cdn->data->cdn->sub_directory ) ) {
			$relative_path = str_replace( get_bloginfo( 'url' ), $this->cdn->data->cdn->sub_directory, $upload['url'] );
		}

		$this->cdn->upload_file( $real_path, $relative_path );

		return $upload;
	}

	public function replace_image_url( $url ) {
		// Get static URL path.
		$static_url = wp_parse_url( get_option( 'sch_static_url' ) );
		$origin_url = wp_parse_url( get_bloginfo( 'url' ) );

		// Subdirectory?
		if ( ! empty( $this->cdn->data->cdn->sub_directory ) ) {
			$cdn_path   = '/' . $this->cdn->data->cdn->sub_directory;
			$static_url = $static_url['host'] . $cdn_path;
		}

		$static_image_url = str_replace( $origin_url, $static_url, $url );

		// Check if file exists.
		$handle = fopen( $static_image_url, 'r' );

		if ( $handle ) {
			return $static_image_url;
		}

		return $url;
	}

	/**
	 * Extract and replace all URLs inside of an HTML string.
	 *
	 * Note this does not factor in external images. Domain check may be required.
	 *
	 * @param string $content HTML that may contain images.
	 *
	 * @return string HTML with possibly images that have been filtered
	 */
	public function replace_image_url_in_html( string $content ): string {
		// Subdirectory?
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
