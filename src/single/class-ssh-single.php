<?php

namespace ssh;

use Simply_Static;
use voku\helper\HtmlDomParser;
use voku\Httpful\Client;

/**
 * Class to handle settings for single.
 */
class Single {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Single.
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
	 * Constructor for Single.
	 */
	public function __construct() {
		add_filter( 'ss_static_pages', array( $this, 'filter_static_pages' ), 10, 2 );
		add_filter( 'ss_remaining_pages', array( $this, 'filter_remaining_pages' ), 10, 2 );
		add_filter( 'ss_total_pages', array( $this, 'filter_total_pages' ) );
		add_filter( 'ss_total_pages_log', array( $this, 'filter_total_pages_log' ) );
		add_action( 'wp_ajax_apply_single', array( $this, 'apply_single' ) );
		add_action( 'wp_ajax_nopriv_apply_single', array( $this, 'apply_single' ) );
		add_action( 'ss_after_cleanup', array( $this, 'clear_single' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
		add_action( 'before_delete_post', array( $this, 'delete_single' ) );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function add_admin_scripts() {
		wp_enqueue_script( 'ssh-single-admin', SIMPLY_STATIC_HOSTING_URL . '/assets/ssh-single-admin.js', array( 'jquery' ), '1.1.1', true );

		wp_localize_script(
			'ssh-single-admin',
			'ssh_ajax',
			array(
				'ajax_url'         => admin_url() . 'admin-ajax.php',
				'run_single_nonce' => wp_create_nonce( 'ssh-run-single' ),
				'redirect_url'     => admin_url() . 'admin.php?page=simply-static',
			)
		);
	}

	/**
	 * Generate single for static export.
	 *
	 * @return void
	 */
	public function apply_single() {
		// check nonce.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ssh-run-single' ) ) {
			$response = array( 'message' => 'Security check failed.' );
			print wp_json_encode( $response );
			exit;
		}

		$single_id       = intval( $_POST['single_id'] );
		$options         = get_option( 'simply-static' );
		$additional_urls = apply_filters( 'ssh_single_export_additional_urls', array_merge( $this->get_related_urls( $single_id ), $this->get_related_attachements( $single_id ) ) );

		// Update option for using a single post.
		update_option( 'simply-static-use-single', $single_id );

		// Add URls for static export.
		$this->add_url( $single_id );
		$this->add_additional_urls( $additional_urls, $single_id );

		do_action( 'ssh_before_run_single' );

		// Start static export.
		$ss = Simply_Static\Plugin::instance();
		$ss->run_static_export();

		// Exit now.
		$response = array( 'success' => true );
		print wp_json_encode( $response );
		exit;
	}

	/**
	 * Get related URls to include in single export.
	 *
	 * @param  int $single_id single post id.
	 * @return array
	 */
	public function get_related_urls( $single_id ) {
		$related_urls = array();

		// Get category URLs.
		$categories = get_the_terms( $single_id, 'category' );

		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$related_urls[] = get_term_link( $category );
			}
		}

		// Get tag URLs.
		$tags = get_the_terms( $single_id, 'post_tag' );

		if ( ! empty( $tags ) ) {
			foreach ( $tags as $tag ) {
				$related_urls[] = get_term_link( $tag );
			}
		}

		// Get archive URL.
		$post_type      = get_post_type( $single_id );
		$related_urls[] = get_post_type_archive_link( $post_type );

		return $related_urls;
	}

	/**
	 * Get related URls to include in single export.
	 *
	 * @param  int $single_id single post id.
	 * @return array
	 */
	public function get_related_attachements( $single_id ) {
		$options       = get_option( 'simply-static' );
		$related_files = array();

		// Get all images from that post.

		// Basic Auth enabled?
		if ( isset( $options['http_basic_auth_digest'] ) && ! empty( $options['http_basic_auth_digest'] ) ) {
			$auth_params = explode( ':', base64_decode( $options['http_basic_auth_digest'] ) );
			$response    = \Httpful\Client::get_request( get_permalink( $single_id ) )
			->expectsHtml()
			->disableStrictSSL()
			->withBasicAuth( $auth_params[0], $auth_params[1] )
			->send();

		} else {
			$response = \Httpful\Client::get_request( get_permalink( $single_id ) )
			->expectsHtml()
			->disableStrictSSL()
			->send();
		}

		$dom = $response->getRawBody();

		foreach ( $dom->find( 'img' ) as $img ) {
			$related_files[] = $img->src;
		}
		return $related_files;
	}

	/**
	 * Add single URL.
	 *
	 * @param  int $single_id current single id.
	 * @return void
	 */
	public function add_url( $single_id ) {
		// Add URL.
		$url = get_permalink( $single_id );

		if ( Simply_Static\Util::is_local_url( $url ) ) {
			Simply_Static\Util::debug_log( 'Adding additional URL to queue: ' . $url );
			$static_page = Simply_Static\Page::query()->find_or_initialize_by( 'url', $url );
			$static_page->set_status_message( __( "Additional URL", 'simply-static' ) );
			$static_page->post_id     = $single_id;
			$static_page->found_on_id = 0;
			$static_page->save();
		}
	}

	/**
	 * Ensure the user-specified Additional URLs are in the DB.
	 *
	 * @param  array $additional_urls array of additional urls.
	 * @return void
	 */
	public function add_additional_urls( $additional_urls, $single_id ) {
		foreach ( $additional_urls as $url ) {
			if ( Simply_Static\Util::is_local_url( $url ) ) {
				Simply_Static\Util::debug_log( 'Adding additional URL to queue: ' . $url );
				$static_page = Simply_Static\Page::query()->find_or_initialize_by( 'url', $url );
				$static_page->set_status_message( __( "Additional URL", 'simply-static' ) );
				$static_page->post_id     = $single_id;
				$static_page->found_on_id = 0;
				$static_page->save();
			}
		}
	}

	/**
	 * Clear selected single after export.
	 *
	 * @return void
	 */
	public function clear_single() {
		delete_option( 'simply-static-use-single' );
	}

	/**
	 * Filter static pages.
	 *
	 * @param  array $results results from database.
	 * @param  array $archive_start_time timestamp.
	 * @return array
	 */
	public function filter_static_pages( $results, $archive_start_time ) {
		$batch_size = apply_filters( 'simply_static_fetch_urls_batch_size', 10 );
		$use_single = get_option( 'simply-static-use-single' );

		if ( empty( $use_single ) ) {
			return $results;
		}

		$post_id = intval( $use_single );

		return Simply_Static\Page::query()
		->where( 'last_checked_at < ? AND post_id = ?', $archive_start_time, $post_id )
		->limit( $batch_size )
		->find();
	}

	/**
	 * Filter remaining pages.
	 *
	 * @param  array $results results from database.
	 * @param  array $archive_start_time timestamp.
	 * @return array
	 */
	public function filter_remaining_pages( $results, $archive_start_time ) {
		$use_single = get_option( 'simply-static-use-single' );

		if ( empty( $use_single ) ) {
			return $results;
		}

		$post_id = intval( $use_single );

		return Simply_Static\Page::query()
		->where( 'last_checked_at < ? AND post_id = ?', $archive_start_time, $post_id )
		->count();
	}

	/**
	 * Filter total pages.
	 *
	 * @param  array $results results from database.
	 * @return array
	 */
	public function filter_total_pages( $results ) {
		$use_single = get_option( 'simply-static-use-single' );

		if ( empty( $use_single ) ) {
			return $results;
		}

		$post_id = intval( $use_single );

		return Simply_Static\Page::query()
		->where( 'post_id = ?', $post_id )
		->count();
	}

	/**
	 * Filter total pages for log.
	 *
	 * @param  array $results results from database.
	 * @return array
	 */
	public function filter_total_pages_log( $results ) {
		$per_page     = $_POST['per_page'];
		$current_page = $_POST['page'];
		$offset       = ( intval( $current_page ) - 1 ) * intval( $per_page );
		$use_single   = get_option( 'simply-static-use-single' );

		if ( empty( $use_single ) ) {
			return $results;
		}

		$post_id = intval( $use_single );

		return Simply_Static\Page::query()
			->where( 'post_id = ?', $post_id )
			->limit( $per_page )
			->offset( $offset )
			->order( 'http_status_code' )
			->find();
	}

	/**
	 * Delete single post/page.
	 *
	 * @param  int $post_id given post id.
	 * @return void
	 */
	public function delete_single( $post_id ) {
		$options      = get_option( 'simply-static' );
		$trashed_post = get_post( $post_id );

		$additional_path = '';

		if ( ! empty( $options['relative_path'] ) ) {
			$additional_path = $options['relative_path'];
		}

		// Build path.
		$path = $additional_path . str_replace( '__trashed', '', $trashed_post->post_name );

		// Delete item from Algolia.

		// Setup Algolia.
		$client = \Algolia\AlgoliaSearch\SearchClient::create( $options['algolia-app-id'], $options['algolia-admin-api-key'] );
		$index  = $client->initIndex( $options['algolia-index'] );

		// Get row by post_id and grab ID.
		global $wpdb;
		$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}simply_static_pages WHERE post_id = %d", $post_id ) );

		// Delete index item.
		if ( ! is_null( $item ) ) {
			$index->deleteObject( $item->id );
		}
	}
}
