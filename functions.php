<?php
/**
 * Theme Functions
 *
 * @package Tiga Demo
 * @since   1.0.0
 * @version 1.0.0
 */

require(get_stylesheet_directory() . '/inc/flash-message.php'); 
require(get_stylesheet_directory() . '/inc/extras.php'); 
require(get_stylesheet_directory() . '/app/login.php'); 
require(get_stylesheet_directory() . '/app/dashboard.php'); 
require(get_stylesheet_directory() . '/app/articles.php'); 

/**
 * Main theme init
 */
function init_tiga_cms() {
	add_theme_support( 'post-thumbnails' );
}
add_action( 'init', 'init_tiga_cms' );

/**
 * Enqueue demo scripts
 */
function demo_scripts() {
	// styles
	wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css' );
	wp_enqueue_style( 'tiga-style', get_stylesheet_uri() );
	
	// media uploader
	wp_enqueue_media();

	// js jquery
	wp_register_script('jquery-3.2.1', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js', false, '3.2.1', true);
	wp_enqueue_script('jquery-3.2.1');

	// js modernizr
  	wp_register_script('modernizr',  'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', false, '2.8.3', true);
	wp_enqueue_script('modernizr');
	
	// js popper
	wp_register_script('popper',  'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js', false, '1.12.3', true);
	wp_enqueue_script('popper');
  	
  	// js bootstarp
  	wp_register_script('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.min.js', false, '4.0.0-beta.2', true);
	wp_enqueue_script('bootstrap-js');

	// media uploader
	wp_register_script('media-uploader-js', get_template_directory_uri() . '/assets/media-uploader.js');
	wp_enqueue_script('media-uploader-js');

	// js script
	wp_register_script('script-js', get_template_directory_uri() . '/assets/script.js');
	wp_enqueue_script('script-js');
}
add_action( 'wp_enqueue_scripts', 'demo_scripts' );

/**
 * Routes Class
 */
class Demo_Routes {

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
		$this->flash = new Demo_Flash();
	}

	/**
	 * Register Routes
	 */
	public function register_routes() {
		TigaRoute::get( '/items/new', array( $this, 'item_new' ) );
		TigaRoute::post( '/items/new', array( $this, 'item_create' ) );
		TigaRoute::get( '/items/{id:num}/edit', array( $this, 'item_edit' ) );
		TigaRoute::post( '/items/{id:num}/edit', array( $this, 'item_update' ) );
		TigaRoute::get( '/items/{id:num}/delete', array( $this, 'item_delete' ) );
		TigaRoute::get( '/items', array( $this, 'item_index' ) );
		TigaRoute::get( '/sample-ajax', array( $this, 'ajax_controller' ) );
		TigaRoute::get( '/sample', array( $this, 'sample_controller' ) );
	}

	/**
	 * Item Index Controller
	 */
	public function item_index() {
		extras::check_login( 'login' );	

		$query = WP_PX::table('items')->select('*');
		$items = $query->get();

		$data = array(
			'items' => $items,
			'flash' => $this->flash,
		);
		set_tiga_template( 'template/item-index.php', $data );
	}

	/**
	 * New Item Controller
	 */
	public function item_new() {
		extras::check_login( 'login' );	

		$data = array(
			'repopulate' => $this->session->pull( 'input' ),
			'flash' => $this->flash,
		);
		set_tiga_template( 'template/item-new.php', $data );
	}

	/**
	 * New Item Process Controller
	 *
	 * @param object $request   Request object.
	 */
	public function item_create( $request ) {
		extras::check_login( 'login' );	

		if ( $request->has( 'name|price|description' ) ) {
			$data = $request->all();

			// insert to table
			$insertId = WP_PX::table('items')->insert($data);			

			// success flash message
			$this->flash->success( 'Item berhasil dibuat' );
			wp_safe_redirect( site_url() . '/items' );
		} else {
			// error flash message with return data
			$this->flash->error( 'Semua field harus diisi' );
			$this->session->set( 'input', $request->all() );
			wp_safe_redirect( site_url() . '/items/new' );
		}
	}

	/**
	 * Update Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function item_edit( $request ) {
		extras::check_login( 'login' );	

		$item = WP_PX::table( 'items' )->find( $request->input( 'id' ), 'id' );

		$data = array(
			'item' => $item,
			'repopulate' => $this->session->pull( 'input' ),
			'flash' => $this->flash,
		);

		set_tiga_template( 'template/item-edit.php', $data );
	}

	/**
	 * Update Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function item_update( $request ) {
		extras::check_login( 'login' );	

		if ( $request->has( 'name|price|description' ) ) {
			$data = $request->all();
			
			// update to table
			WP_PX::table( 'items' )->where( 'id', $request->input( 'id' ) )->update( $data );

			// success flash message
			$this->flash->success( 'Item berhasil diupdate' );
			wp_safe_redirect( site_url() . '/items' );
		} else {
			// error flash message with return data
			$this->flash->error( 'Semua field harus diisi' );
			$this->session->set( 'input', $request->all() );
			wp_safe_redirect( site_url() . '/items/' . $request->input( 'id' ) . '/edit' );
		}
	}

	/**
	 * Delete Item Controller
	 *
	 * @param object $request Request object.
	 */
	public function item_delete( $request ) {
		extras::check_login( 'login' );	

		// delete a row from table
		WP_PX::table( 'items' )->where( 'id', $request->input( 'id' ) )->delete();

		// success flash message
		$this->flash->success( 'Item berhasil dihapus' );
		wp_safe_redirect( site_url() . '/items' );
	}

	/**
	 * Sample controller
	 */
	public function sample_controller() {
		extras::check_login( 'login' );	

		set_tiga_template( 'template/sample-index.php', $data );
	}

	/**
	 * Sample ajax controller
	 */
	public function ajax_controller() {
		extras::check_login( 'login' );	
		
		echo "<p>Hello, I'm from ajax!</p>";
		die();
	}

}
new Demo_Routes();

/**
 * Flash Class
 */
class Demo_Flash {

	/**
	 * Session
	 *
	 * @var object Session Object
	 */
	private $session;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->session = new \Tiga\Session();
	}

	/**
	 * Add error notice
	 *
	 * @param string $message Message text.
	 */
	public function error( $message ) {
		$this->add( 'danger', $message );
	}

	/**
	 * Add success notice
	 *
	 * @param string $message Message text.
	 */
	public function success( $message ) {
		$this->add( 'success', $message );
	}

	/**
	 * Add info notice
	 *
	 * @param string $message Message text.
	 */
	public function info( $message ) {
		$this->add( 'info', $message );
	}

	/**
	 * Add warning notice
	 *
	 * @param string $message Message text.
	 */
	public function warning( $message ) {
		$this->add( 'warning', $message );
	}

	/**
	 * Add message to session
	 *
	 * @param string $type Notice type.
	 * @param string $message Message text.
	 */
	public function add( $type, $message ) {
		$old = $this->session->get( 'demo_flash_' . $type );
		$this->session->set( 'demo_flash_' . $type, $old . '<div>' . $message . '</div>' );
	}

	/**
	 * Displaying notice
	 */
	public function display() {
		if ( $this->session->has( 'demo_flash_danger' ) ) {
			echo '<div class="alert dismissable alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' . $this->session->pull( 'demo_flash_danger' ) . '</div>';
		}
		if ( $this->session->has( 'demo_flash_info' ) ) {
			echo '<div class="alert dismissable alert-info"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' . $this->session->pull( 'demo_flash_info' ) . '</div>';
		}
		if ( $this->session->has( 'demo_flash_success' ) ) {
			echo '<div class="alert dismissable alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' . $this->session->pull( 'demo_flash_success' ) . '</div>';
		}
		if ( $this->session->has( 'demo_flash_warning' ) ) {
			echo '<div class="alert dismissable alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' . $this->session->pull( 'demo_flash_warning' ) . '</div>';
		}
	}

}

/**
 * Create DB table for demo purpose
 */
function create_demo_db_table() {
	global $wpdb;
	$db_name = 'items';
	$charset_collate = $wpdb->get_charset_collate();

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$db_name'") !== $db_name ) {
		$sql = 'CREATE TABLE ' . $db_name . " 
				( `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
				  `name` varchar(255) NOT NULL, 
				  `price` varchar(255) NOT NULL, 
				  `description` text NOT NULL, 
				  PRIMARY KEY  (`id`) 
			  ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	TigaPixie::get('WP_PX');
}
add_action( 'init', 'create_demo_db_table', 1);