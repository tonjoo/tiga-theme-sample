<?php

/**
 * Routes Class
 */
class HRS_Login {

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
		TigaRoute::get( '/login', array( $this, 'login_index' ) );
		TigaRoute::post( '/login', array( $this, 'login_request' ) );
		TigaRoute::get( '/logout', array( $this, 'logout' ) );
	}

	/**
	 * Index Controller
	 */	
	public function login_index() {
		global $wpdb, $user_ID;
		
		if ($user_ID) {
			wp_safe_redirect( site_url() . '/dashboard' ); 
		} 
		else {		 
			$data = array(
				'flash' => $this->flash,
			);
			set_tiga_template( 'template/login-index.php', $data );
		}
	}

	/**
	 * Request Controller
	 */
	public function login_request( $request ) {
		if ( $request->has( 'username|password' ) ) {
			$data = $request->all();

			// source: https://haveposts.com/blog/2013/01/25/watch-movie-online-silence-2016-subtitle-english/

			global $wpdb;
 
			//We shall SQL escape all inputs
			$username = $wpdb->escape($data['username']);
			$password = $wpdb->escape($data['password']);
			// $remember = $wpdb->escape($_REQUEST['rememberme']);
		 
			// if($remember) $remember = "true";
			// else $remember = "false";
		 
			$login_data = array();
			$login_data['user_login'] = $username;
			$login_data['user_password'] = $password;
			// $login_data['remember'] = $remember;
		 
			$user_verify = wp_signon( $login_data, false ); 
		 
			if ( is_wp_error($user_verify) ) {
				$this->flash->error( 'Username atau password salah' );
				$this->session->set( 'input', $request->all() );
			 	wp_safe_redirect( site_url() . '/login' );
			} 
			else {	
				wp_safe_redirect( site_url() . '/dashboard' );
			}
		}
		else {
			$this->flash->error( 'Username dan password harus diisi' );
			$this->session->set( 'input', $request->all() );
			wp_safe_redirect( site_url() . '/login' );
		}
	}

	/**
	 * Logout then redirect to login
	 */	
	public function logout() {
		wp_logout();

		$this->flash->success( 'You has logged out successfully.' );
		wp_safe_redirect( site_url() . '/login' );
	}

}
new HRS_Login();