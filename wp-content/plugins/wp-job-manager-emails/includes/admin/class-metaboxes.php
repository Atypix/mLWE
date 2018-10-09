<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Admin_MetaBoxes
 *
 * @since 2.0.0
 *
 */
class WP_Job_Manager_Emails_Admin_MetaBoxes extends WP_Job_Manager_Emails_Admin_Views {

	/**
	 * WP_Job_Manager_Emails_Admin_MetaBoxes constructor.
	 */
	public function __construct( $cpt, $post ) {

		$this->cpt = $cpt;
		$this->post = $post;

		$this->get_meta_boxes();
		// Run add_meta_box based on config
		$this->add_meta_boxes();

		// Call any extended class specific filters/actions
		$this->factions();

		//add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 999 );
		add_action( 'edit_form_after_title', array($this, 'output_headers') );
		add_action( 'edit_form_after_title', array($this, 'open_pusher'), 11);
		add_action( 'edit_form_after_editor', array($this, 'close_pusher') );

		// Remove other plugin/theme media buttons from showing on our CPT
		add_action( 'admin_enqueue_scripts', array($this, 'media_buttons') );
	}

	/**
	 * Add shortcode button above TinyMCE
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $editor_id
	 */
	function shortcode_button( $editor_id ){

		$screen = get_current_screen();

		if( ! $this->cpt()->page() ) return;

		echo '<button id="templates-media-btn" class="button"><i class="icon wizard"></i>' . __( 'Email Templates', 'wp-job-manager-emails' ) . '</button>';
	}

	/**
	 * Only allow our media buttons to show on CPT page
	 *
 	 * This method removes all media buttons from showing above the editor besides the standard
 	 * Add Media button, and the email templates button (or any other configured in this plugin).
	 *
	 * @since 2.4.0
	 *
	 */
	function media_buttons(){

		if( ! $this->cpt()->page() ) {
			return;
		}

		// Remove all media buttons
		remove_all_actions( 'media_buttons' );
		// Add back standard media button (to "Add Media")
		add_action( 'media_buttons', 'media_buttons' );
		// Add our Email Templates media button
		add_action( 'media_buttons', array($this, 'shortcode_button') );

		do_action( 'job_manager_emails_after_remove_media_buttons' );
	}

	/**
	 * Open Pusher element for Shortcode sidebar
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	function open_pusher( $post ){

		if( ! $this->cpt()->page() ) return;
		?>
		<div id="sc_pushable" class="pushable">
			<div id="sc_sidebar" class="ui inverted right small vertical sidebar menu sidebar_haspopover">
				<?php
					$group_scs = $this->cpt()->integration()->get_sidebar_grouped_fields();
					foreach( $group_scs as $group_name => $shortcodes ){
						$this->output_shortcode_group( $shortcodes, $group_name );
					}
 				?>
			</div>
			<div class="pusher">
		<?php
	}

	/**
	 * Output Group Shortcodes for Sidebar
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $shortcodes
	 * @param $group
	 */
	function output_shortcode_group( $shortcodes, $group ){

		$this_slug = $this->cpt()->get_slug();
		$hidden_group = strpos( $group, $this_slug ) !== FALSE ? '' : 'hidden_sc_group';

		// Used to output custom header for hook groups
		$hook_group = strpos( $group, '_hooks' ) !== FALSE;
		if( $hook_group ){
			$group_header = __( 'Hook Shortcodes', 'wp-job-manager-emails' );
		} else {
			if( $group === 'application' ){
				$group_header = __( 'App Shortcodes', 'wp-job-manager-emails' );
			} else {
				$group_header = ucfirst( $group ) . ' ' . __( 'Shortcodes', 'wp-job-manager-emails');
			}
		}

		$group_hook_header = $hook_group ? "{$group}_sc_header shortcode_sb_head_dynamic" : '';
		echo "<div class=\"item {$group_hook_header} {$group}_shortcodes_sb_group {$hidden_group}\" style=\"background-color: #e8e8e8; color: #72777c;\"><div class=\"header\" style=\"margin: 0;\"><i class=\"code icon\"></i>{$group_header}</div></div>";

		foreach( (array) $shortcodes as $shortcode => $config ){
			$placeholder   = array_key_exists( 'placeholder', $config ) ? $config[ 'placeholder' ] : '';
			$desc          = htmlentities( array_key_exists('description', $config) ? $config[ 'description' ] : $placeholder );
			$popover_class = ! empty( $desc ) ? 'popover' : '';
			$hidden_sc = array_key_exists( 'visible', $config ) && empty( $config['visible'] ) ? ' shortcode_sb_dynamic' : '';

			echo "<div class=\"item link shortcode_sb_item {$popover_class}{$hidden_sc} {$group}_shortcodes_sb_group shortcode_{$shortcode} {$hidden_group}\" data-content=\"{$desc}\" data-title=\"[{$shortcode}]\" data-shortcode=\"[{$shortcode}]\">";
			echo "	[{$shortcode}]";
			echo '</div>';
		}

	}

	/**
	 * Close Pusher Sidebar Element
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	function close_pusher( $post ){

		if ( ! $this->cpt()->page() ) return;

		echo '</div>'; // #pusher
		echo '</div>'; // #sc_pushable
		?>
		<div id="hook_form_wrap" style="display: none; margin-top: 1em;">
			<div class="ui top attached segment secondary small" style="width: auto;">
				<div class="icon"><i class="icon settings"></i><?php _e( 'Hook Configuration', 'wp-job-manager-emails' ); ?></div>
			</div>
			<div class="ui equal width form bottom attached fluid segment small" id="hook_form">
				<div class="fields" id="hook_form_fields">
					<?php // Auto populated by jQuery ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Default and Custom Metaboxes
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function get_meta_boxes(){

		global $post;

		$this->init_meta_boxes();

		$publish_callback_args = NULL;
		if ( 'auto-draft' != $post->post_status ) {
			$revisions = wp_get_post_revisions( $post->ID );
			$publish_callback_args = array('revisions_count' => count( $revisions ), 'revision_id' => key( $revisions ));
		}

		$default_mb = apply_filters(
			'job_manager_emails_default_metaboxes',
			array(
				'format' => array(
					'id'            => 'format_mb',
					'title'         => __( 'Email Format', 'wp-job-manager-emails' ),
					'callback'      => array($this, 'output_format'),
					'screen'        => $this->cpt()->get_post_type(),
					'context'       => 'side',
					'priority'      => 'default',
					'callback_args' => null
				),
				'attachments' => array(
					'id'            => 'attachments_mb',
					'title'         => __( 'Attachments', 'wp-job-manager-emails' ),
					'callback'      => array($this, 'output_attachments'),
					'screen'        => $this->cpt()->get_post_type(),
					'context'       => 'normal',
					'priority'      => 'low',
					'callback_args' => $this->cpt()->get_fields()
				),
			    'hook' => array(
				    'id'            => 'hook_mb',
				    'title'         => __( 'Send Email Action ...', 'wp-job-manager-emails' ),
				    'callback'      => array($this, 'output_hook'),
				    'screen'        => $this->cpt()->get_post_type(),
				    'context'       => 'normal',
				    'priority'      => 'high',
				    'callback_args' => NULL
			    ),
				'conditionals' => array(
					'id'            => 'conditionals',
					'title'         => __( 'Conditionals', 'wp-job-manager-emails' ),
					'callback'      => array( $this, 'output_conditionals' ),
					'screen'        => $this->cpt()->get_post_type(),
					'context'       => 'side',
					'priority'      => 'low',
					'callback_args' => array('fields' => $this->cpt()->shortcodes()->get_conditionals())
				),
				'send_logic' => array(
					'id'            => 'send_logic',
					'title'         => __( 'Send Logic', 'wp-job-manager-emails' ),
					'callback'      => array( $this, 'output_logic' ),
					'screen'        => $this->cpt()->get_post_type(),
					'context'       => 'side',
					'priority'      => 'low',
					'callback_args' => null
				),
				'submitdiv' => array(
					'id'            => 'submitdiv',
					'title'         => __( 'Manage Email Template', 'wp-job-manager-emails' ),
					'callback'      => array( $this, 'output_submitdiv' ),
					'screen'        => $this->cpt()->get_post_type(),
					'context'       => 'side',
					'priority'      => 'high',
					'callback_args' => array( 'publish' => $publish_callback_args )
				),
			)
		);

		$slug = $this->cpt()->get_slug();
		$this->meta_boxes = array_merge( $default_mb, $this->meta_boxes );
		return $this->meta_boxes;
	}

	/**
	 * Add Metaboxes From Configuration
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function add_meta_boxes(){

		//$this->add_debug_meta_box();

		if( ! $this->meta_boxes ) $this->get_meta_boxes();

		foreach ( $this->meta_boxes as $mb => $conf ) {
			// Go to next if callback method doesn't exist
			//if( ! method_exists( $conf['callback'][0], $conf['callback'][1] ) ) continue;
			add_meta_box( $conf[ 'id' ], $conf[ 'title' ], $conf[ 'callback' ], $conf[ 'screen' ], $conf[ 'context' ], $conf[ 'priority' ], $conf[ 'callback_args' ] );
		}

	}

	/**
	 * Construct Placeholder
	 *
	 * This is just a construct placeholder that should be overriden by
	 * any classes that extend this class.  All this does is eliminate
	 * the need to call parent::__construct() in class that extends
	 * this one.
	 *
	 *
	 * @since 1.1.0
	 *
	 */
	function construct() {
		// Not used, only a placeholder, see method doc for details
	}

	/**
	 *
	 *
	 *
	 * @since 2.0.0
	 *
	 */
	function factions(){}
}