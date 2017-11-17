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
		set_tiga_template( 'template/sample-index.php', $data );
	}

	/**
	 * Sample ajax controller
	 */
	public function ajax_controller() {
		echo "<p>Hello, I'm from ajax!</p>";
		wp_die();
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

	TigaPixie::get('WP_PX');
}
add_action( 'init', 'create_demo_db_table', 1);