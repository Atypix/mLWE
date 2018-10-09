<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Log {

	public $type;

	public $resume = null;
	public $job = null;
	public $application = null;

	protected static $job_instance = null;
	protected static $resume_instance = null;
	protected static $application_instance = null;

	public function __construct( $type ) {
		$this->type = $type;
	}

	public function enabled(){

		$type = $this->type;

		if( $this->$type === null ){
			$this->$type = get_option( "job_manager_emails_{$type}_enable_debug", FALSE );
		}

		return $this->$type;
	}

	public function log( $type, $message, $data, $doing = NULL ){

		if( ! $this->enabled() ){
			return;
		}

		$backtrace       = debug_backtrace();
		$caller_function = $backtrace[2]['function'];
		$caller_line     = $backtrace[2]['line'];
		$caller_file     = $backtrace[2]['file'];

		$plugin_title = '[emails][' . strtoupper( $type ) . ']';

		if( in_array( $type, array( 'warning', 'error', 'critical', 'emergency' ), false ) ){
			$plugin_title .= "[ {$caller_function} ][ {$caller_file}:{$caller_line} ]";
		}

		if( is_array( $data ) || is_object( $data ) ){
			error_log( $plugin_title . $doing . "[ {$message} ] - " . 'Array/Object:' );
			error_log( print_r( $data, TRUE ) );
		} else {
			error_log( $plugin_title . $doing . "[ {$message} ] - " . strtolower( $data ) );
		}

	}

	public function debug( $message, $data = null, $doing = null ){

		$this->log( 'debug', $message, $data, $doing );

	}

	public function info( $message, $data = null, $doing = null ){

		$this->log( 'info', $message, $data, $doing );

	}

	public function notice( $message, $data = null, $doing = null ){

		$this->log( 'notice', $message, $data, $doing );

	}

	public function warning( $message, $data = null, $doing = null ){

		$this->log( 'warning', $message, $data, $doing );

	}

	public function error( $message, $data = null, $doing = null ){

		$this->log( 'error', $message, $data, $doing );

	}

	public function critical( $message, $data = null, $doing = null ){

		$this->log( 'critical', $message, $data, $doing );

	}

	public function alert( $message, $data = null, $doing = null ){

		$this->log( 'alert', $message, $data, $doing );

	}

	public function emergency( $message, $data = null, $doing = null ){

		$this->log( 'emergency', $message, $data, $doing );

	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  @@since
	 */
	public static function get_instance( $type ){

		$instance = "{$type}_instance";

		if( NULL === self::$$instance ){
			self::$$instance = new self( $type );
		}

		return self::$$instance;
	}
}

