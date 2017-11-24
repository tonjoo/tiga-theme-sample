<?php

/**
 * Routes Class
 */
class HRS_Articles {

	/**
	 * Session
	 *
	 * @var object Session Object
	 */
	private $session;

	/**
	 * Flash
	 *
	 * @var object Flash Object
	 */
	private $flash;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'tiga_route', array( $this, 'register_routes' ) );
		$this->session = new \Tiga\Session();
		$this->flash = new Flash_Message();
	}

	/**
	 * Register Routes
	 */
	public function register_routes() {
		TigaRoute::get( '/articles/status/{status:any}/page/{page:num}', array( $this, 'articles_index' ) );
		TigaRoute::get( '/articles/status/{status:any}', array( $this, 'articles_index' ) );
		TigaRoute::get( '/articles/page/{page:num}', array( $this, 'articles_index' ) );
		TigaRoute::get( '/articles/{id:num}/edit', array( $this, 'articles_edit' ) );
		TigaRoute::post( '/articles/{id:num}/edit', array( $this, 'articles_update' ) );
		TigaRoute::get( '/articles/{id:num}/trash', array( $this, 'articles_trash' ) );
		TigaRoute::get( '/articles/{id:num}/restore', array( $this, 'articles_untrash' ) );
		TigaRoute::get( '/articles/{id:num}/delete', array( $this, 'articles_delete' ) );		
		TigaRoute::get( '/articles/new', array( $this, 'articles_new' ) );
		TigaRoute::post( '/articles/new', array( $this, 'articles_create' ) );	
		TigaRoute::get( '/articles', array( $this, 'articles_index' ) );
	}

	/**
	 * Index Controller
	 */	
	public function articles_index($request) {
		extras::check_login( 'login' );

		global $wpdb;

		// data table
		$args = array(
    		'post_type' => 'post',
    		'posts_per_page' => 10,
    		'post_status' => $request->input( 'status', 'publish' ),
    		'paged' => $request->input( 'page', 1 ),
    		'ignore_sticky_posts' => true
    	);

    	// active status
		$status = $request->input( 'status', 'publish' );
		if($status == 'publish') {
			$active_status = array('active', '', '', '');
		}
		else if($status == 'trash') {
			$active_status = array('', 'active', '', '');
		}
		else {
			$active_status = array('', '', 'active', '');
		}

		// paginate route
		if( $request->input( 'status', false ) ) {
			$paginate_route = 'articles/status/' . $status;
		}
		else {
			$paginate_route = 'articles';
		}

		// search
		if( isset( $_REQUEST['search'] ) && $_REQUEST['search'] != '' ) {
			$args['s'] = sanitize_text_field( $_REQUEST['search'] );
		}

		// filter category
		if( isset( $_REQUEST['category'] ) && intval($_REQUEST['category']) > 0 ) {
			$term_id = intval($_REQUEST['category']);			
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			);
		}

		// data
    	$data = array(
    		'args' => $args,
    		'count_posts' =>  wp_count_posts(),
    		'status' => $status,
    		'active_status' => $active_status,
    		'paginate_route' => $paginate_route,
    		'flash' => $this->flash,
    	);
			
		set_tiga_template( 'template/articles-index.php', $data );
	}

	/**
	 * New Item Controller
	 */
	public function articles_new() {
		extras::check_login( 'login' );
		
		// required for wp_category_checklist
		require_once( ABSPATH . '/wp-admin/includes/template.php' );

		$statuses = array_reverse( get_post_statuses() ); 

		$data = array(
			'repopulate' => $this->session->pull( 'input' ),
			'statuses' => $statuses,
			'flash' => $this->flash,
		);
		
		set_tiga_template( 'template/articles-new.php', $data );
	}

	/**
	 * New Item Process Controller
	 *
	 * @param object $request   Request object.
	 */
	public function articles_create( $request ) {
		extras::check_login( 'login' );
		extras::verify_nonce_field( 'article_actions_nonce' );

		if ( $request->has( 'title' ) ) {
			$data = $request->all();

			/**
			 * The data will be:
			 * title|content|post_category|featured_image
			 */

			$featured_id = intval($request->input( 'featured_image' ));
			$categories = $request->input( 'post_category' );

			// wp_insert_post
			$post_id = wp_insert_post( array(
			    'post_title' => $request->input( 'title' ),
			    'post_content' => $request->input( 'content' ),
			    'post_status' => $request->input( 'status', 'publish' ),
			    'post_type' => 'post',
			) );

			// set category
			if( is_array( $categories ) ) {
				wp_set_post_terms( $post_id, $categories, 'category', false );
			}

			// set featured image
			if( $featured_id > 0 ) {
				set_post_thumbnail( $post_id, $featured_id );
			}

			// success flash message
			$this->flash->success( 'Article created' );
			wp_safe_redirect( site_url() . '/articles/' . $post_id . '/edit/' );
		} 
		else {
			// error flash message with return data
			$this->flash->error( 'The title still empty!' );
			$this->session->set( 'input', $request->all() );
			wp_safe_redirect( site_url() . '/articles/new/' );
		}
	}

	/**
	 * Update Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function articles_edit( $request ) {
		extras::check_login( 'login' );		

		$post = get_post( $request->input( 'id' ) );
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		$thumbnail_src = false;

		if(! empty($thumbnail_id)) {
			$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, 'medium' );	
		}		

		// required for wp_category_checklist
		require_once( ABSPATH . '/wp-admin/includes/template.php' );

		$statuses = array_reverse( get_post_statuses() ); 

		$data = array(
			'post' => $post,
			'statuses' => $statuses,
			'thumbnail_id' => $thumbnail_id,
			'thumbnail_src' => $thumbnail_src,
			'repopulate' => $this->session->pull( 'input' ),
			'flash' => $this->flash,
		);

		set_tiga_template( 'template/articles-edit.php', $data );
	}

	/**
	 * Update Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function articles_update( $request ) {
		extras::check_login( 'login' );
		extras::verify_nonce_field( 'article_actions_nonce' );

		if ( $request->has( 'title' ) ) {
			$data = $request->all();

			/**
			 * The data will be:
			 * title|content|post_category|featured_image
			 */

			$post_id = intval($request->input( 'id' ));
			$featured_id = intval($request->input( 'featured_image' ));
			$categories = $request->input( 'post_category' );

			// wp_insert_post
			wp_update_post( array(
			    'ID' => $post_id,
			    'post_title' => $request->input( 'title' ),
			    'post_content' => $request->input( 'content' ),
			    'post_status' => $request->input( 'status', 'publish' )
			) );

			// set category
			if( is_array( $categories ) ) {
				wp_set_post_terms( $post_id, $categories, 'category', false );
			}

			// set featured image
			if( $featured_id > 0 ) {
				set_post_thumbnail( $post_id, $featured_id );
			}

			// success flash message
			$this->flash->success( 'Article updated' );
			wp_safe_redirect( site_url() . '/articles/' . $post_id . '/edit/' );
		} 
		else {
			// error flash message with return data
			$this->flash->error( 'The title still empty' );
			$this->session->set( 'input', $request->all() );
			wp_safe_redirect( site_url() . '/articles/new/' );
		}
	}

	/**
	 * Delete Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function articles_trash( $request ) {
		extras::check_login( 'login' );
		extras::verify_nonce_request( 'article_actions_nonce' );

		// delete a post
		wp_delete_post( $request->input( 'id' ), false );

		// success flash message
		$this->flash->success( 'Article trashed' );
		wp_safe_redirect( site_url() . '/articles' );
	}

	/**
	 * Untrash Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function articles_untrash( $request ) {
		extras::check_login( 'login' );
		extras::verify_nonce_request( 'article_actions_nonce' );

		// delete a post
		wp_untrash_post( $request->input( 'id' ) );

		// success flash message
		$this->flash->success( 'Article trashed' );
		wp_safe_redirect( site_url() . '/articles' );
	}

	/**
	 * Delete Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function articles_delete( $request ) {
		extras::check_login('login');
		extras::verify_nonce_request( 'article_actions_nonce' );

		// delete a post
		wp_delete_post( $request->input( 'id' ), true );

		// success flash message
		$this->flash->success( 'Article deleted permanenly' );
		wp_safe_redirect( site_url() . '/articles' );
	}
}
new HRS_Articles();