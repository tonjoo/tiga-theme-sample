<?php

/**
 * Flash Class
 */
class Flash_Message {

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