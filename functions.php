<?php
/**
 * Theme Functions
 *
 * @package Tiga Demo
 * @since   1.0.0
 * @version 1.0.0
 */



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
		global $wpdb;
		$items = $wpdb->get_results( 'SELECT * FROM items' );
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
		$data = array(
			'repopulate'    => $this->session->pull( 'input' ),
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
		global $wpdb;
		if ( $request->has( 'name|price|description' ) ) {
			$data = $request->all();
			$wpdb->insert( 'items', $data );
			$this->flash->success( 'Item berhasil dibuat' );
			wp_safe_redirect( site_url() . '/items' );
		} else {
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
		global $wpdb;
		$item = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM items WHERE id = %d', $request->input( 'id' ) ) );
		$data = array(
			'item'          => $item,
			'repopulate'    => $this->session->pull( 'input' ),
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
		global $wpdb;
		if ( $request->has( 'name|price|description' ) ) {
			$data = $request->all();
			$wpdb->update( 'items', $data, array( 'id' => $request->input( 'id' ) ) );
			$this->flash->success( 'Item berhasil diupdate' );
			wp_safe_redirect( site_url() . '/items' );
		} else {
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
		global $wpdb;
		$wpdb->delete( 'items', array( 'id' => $request->input( 'id' ) ) );
		$this->flash->success( 'Item berhasil dihapus' );
		wp_safe_redirect( site_url() . '/items' );
	}

	/**
	 * Sample controller
	 */
	public function sample_controller() {
		set_tiga_template( 'template/sample-index.php', $data );
	}

	/**
	 * Sample ajax controller
	 */
	public function ajax_controller() {
		echo "<p>Hello, I'm from ajax!</p>";
		die;
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
 * Enqueue demo scripts
 */
function demo_scripts() {
	wp_enqueue_style( 'demo', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css' );
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'demo_scripts' );

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
}
add_action( 'init', 'create_demo_db_table' );