<?php

namespace ssh;

use Simply_Static;

/**
 * Class to handle settings for fuse.
 */
class Multilingual {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Multilingual.
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
	 * Constructor for Multilingual.
	 */
	public function __construct() {
		add_action( 'ss_match_tags', array( $this, 'find_translated_pages' ) );
		add_filter( 'ss_get_options', array( $this, 'get_multilingual_options' ) );
	}

	/**
	 * Add translations from meta tags.
	 *
	 * @param  array $match_tags list of matching tags for extraction.
	 * @return array
	 */
	public function find_translated_pages( $match_tags ) {
		$match_tags['link'] = array( 'href' );
		return $match_tags;
	}

	/**
	 * Return options in selected language with WPML.
	 *
	 * @param  array $options array of options.
	 * @return array
	 */
	public function get_multilingual_options( $options ) {
		do_action( 'wpml_multilingual_options', 'simply-static' );

		$options = get_option( 'simply-static' );
		return $options;
	}
}
