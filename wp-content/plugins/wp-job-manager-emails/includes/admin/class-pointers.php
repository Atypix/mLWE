<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Pointers {

	/**
	 * @type
	 */
	public $screen_id;
	/**
	 * @type
	 */
	public $valid;
	/**
	 * @type
	 */
	public $pointers;

	/**
	 * WP_Job_Manager_Emails_Admin_Pointers constructor.
	 *
	 * @param $pointers
	 */
	public function __construct( $pointers = array() ) {

		$screen = get_current_screen();
		$this->screen_id = $screen->id;

		$pointers = apply_filters( 'job_manager_emails_admin_pointers', $pointers, $this->screen_id, $screen );

		$this->register_pointers( $pointers );

		add_action( 'admin_enqueue_scripts', array($this, 'add_pointers'), 1000 );
		add_action( 'admin_print_footer_scripts', array($this, 'add_scripts') );

	}

	/**
	 * Register Pointers
	 *
	 * Register the available pointers for the current screen
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $pointers
	 */
	public function register_pointers( $pointers ) {

		$screen_pointers = NULL;
		foreach( $pointers as $ptr ) {
			if( $ptr['screen'] == $this->screen_id || $ptr['screen'] == 'all' ) {
				$options                       = array(
					'content'  => sprintf(
						'<h3> %s </h3> <p> %s </p>',
						__( $ptr['title'], 'wp-job-manager-emails' ),
						__( $ptr['content'], 'wp-job-manager-emails' )
					),
					'position' => $ptr['position']
				);
				$screen_pointers[ $ptr['id'] ] = array(
					'screen'  => $ptr['screen'],
					'target'  => $ptr['target'],
					'options' => $options
				);
			}
		}

		$this->pointers = $screen_pointers;
	}

	/**
	 * Add Pointers
	 *
	 * Add pointers to the current screen if they were not dismissed
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_pointers() {

		if( ! $this->pointers || ! is_array( $this->pointers ) ) return;

		// Get dismissed pointers
		$get_dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', TRUE );
		$dismissed     = explode( ',', (string) $get_dismissed );

		// Check pointers and remove dismissed ones.
		$valid_pointers = array();
		foreach( $this->pointers as $pointer_id => $pointer ) {
			if(
				in_array( $pointer_id, $dismissed )
				|| empty($pointer)
				|| empty($pointer_id)
				|| empty($pointer['target'])
				|| empty($pointer['options'])
			)
				continue;
			$pointer['pointer_id']        = $pointer_id;
			$valid_pointers['pointers'][] = $pointer;
		}
		if( empty($valid_pointers) ) return;

		$this->valid = $valid_pointers;

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Print JS
	 *
	 * Print JavaScript if pointers are available
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_scripts() {

		if( empty($this->valid) ) return;

		$pointers = json_encode( $this->valid );

		echo "<style>.jme-wp-help-pointer .wp-pointer-arrow { right: 10px; left: auto; }</style>";

		echo <<<HTML
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready( function($) {
		var job_manager_pointer = {$pointers};
		$.each(job_manager_pointer.pointers, function(i) {
			job_manager_emails_help_pointer_open(i);
		});
		function job_manager_emails_help_pointer_open(i) 
		{
			pointer = job_manager_pointer.pointers[i];
			var pointer_class = 'jme-wp-pointer';
			
			if ( pointer.target === '#contextual-help-link-wrap' ) {
				pointer_class += ' jme-wp-help-pointer';
				$('#contextual-help-link-wrap').click( function () {
					setTimeout( function () {
						$('#contextual-help-link-wrap').pointer('close');
					}, 0);
				});
			}
			
			$( pointer.target ).pointer( 
			{
				content: pointer.options.content,
				position: 
				{
					edge: pointer.options.position.edge,
					align: pointer.options.position.align
				},
				pointerClass: pointer_class,
				close: function() 
				{
					$.post( ajaxurl, 
					{
						pointer: pointer.pointer_id,
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');
		}
		
		$(window).resize(function() {
			if ( $('.jme-wp-pointer').is(":visible") ) $( pointer.target ).pointer('reposition');
		});
		
	});
	
//]]>
</script>
HTML;
	}
}