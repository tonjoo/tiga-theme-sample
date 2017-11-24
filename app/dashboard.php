<?php

/**
 * Routes Class
 */
class HRS_Dashboard {

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
	}

	/**
	 * Register Routes
	 */
	public function register_routes() {		
		TigaRoute::get( '/dashboard', array( $this, 'dashboard_index' ) );
	}

	/**
	 * Index Controller
	 */	
	public function dashboard_index() {
		extras::check_login('login');
		
		global $wpdb;			
		set_tiga_template( 'template/dashboard-index.php', array() );
	}
}
new HRS_Dashboard();