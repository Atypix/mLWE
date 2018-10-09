<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_IO_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	public $io_fields;
	public $option_value;
	protected $templates;

	public function __construct()
	{
		parent::__construct();

		if( isset( $_GET['rwp_file'] ) )
			$this->download( $_GET['rwp_file'] );

		$this->set_io_fields();
		$this->menu_slug = 'reviewer-io-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->option_name = 'rwp_io';
		$this->option_value = RWP_Reviewer::get_option( $this->option_name );
		if (empty($this->option_value))
			add_option( $this->option_name, array() );
		$this->add_menu_page();
		$this->register_page_fields();
		// add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page(
			$this->parent_menu_slug,
			__( 'Migration', $this->plugin_slug),
			__( 'Migration', $this->plugin_slug),
			$this->capability,
			$this->menu_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	// public function localize_script()
	// {
	// 	$action_name = 'rwp_ajax_action_restore_data';
	// 	wp_localize_script( $this->plugin_slug . '-admin-script', 'restoreDataObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	// }

	public function register_page_fields()
	{
		// Add section
		add_settings_section( 'rwp_io_section', '', array( $this, 'display_section'), $this->menu_slug );

		foreach ($this->io_fields as $field_id => $field) {

			add_settings_field( $field_id, $field['title'], array( $this, $field_id . '_cb' ), $this->menu_slug, 'rwp_io_section', array( 'field_id' => $field_id, 'options' => $field['options'], 'default' => $field['default'] ) );
		}

		register_setting( $this->option_name, $this->option_name, array( $this, 'validate_fields' ) );
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function display_section()
	{
		// Do Nothing!
	}

	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Migration', $this->plugin_slug ); ?></h2>
			<?php if($this->is_licensed()): ?>

			<p class="description"><?php _e('The page allows you to manage backups of Reviewer plugin or to migrate user reviews from an other review service to Reviewer plugin. Before go ahead make a backup of your WordPress database.', 'reviewer'); ?></p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php" enctype="multipart/form-data">
			<?php
				settings_fields( $this->option_name );
				do_settings_sections( $this->menu_slug );
				submit_button( __('Go', $this->plugin_slug), 'primary', 'rwp_io_submit', true );
			?>
			</form>

			<?php $this->backup_files(); //RWP_Reviewer::pretty_print(  $this->option_value ); ?>
			<?php else:
	            $this->license_notice();
	        endif; ?>
			<?php //RWP_Reviewer::pretty_print( $this->option_value ); ?>
		</div><!--/wrap-->
		<?php
	}

	public function backup_files()
	{
		$dir_name = RWP_PLUGIN_PATH.'backup';

		if( !file_exists( $dir_name ) )
			return;

		$files = scandir( $dir_name );

		echo '<h3>'. __('Reviewer Backups' , $this->plugin_slug) .'</h3>';

		echo '<ul class"rwp-backup-files">';

		$check = true;

		foreach ($files as $file) {

			if( substr_count( $file, '.json') < 1 )
				continue;

			$check = false;

			echo '<li><a href="'. admin_url('admin.php?page='. $this->menu_slug .'&rwp_file='.$file) .'">'. $file .'</a></li>';
		}

		echo '</ul>';

		if( $check )
			echo '<p class="description">'. __('No backup has been done yet' , $this->plugin_slug) .'</p>';
	}

	public function download( $file )
	{
		$file_path = RWP_PLUGIN_PATH . 'backup/' . $file;

		if( !file_exists( $file_path ) )
			return;

		// Set headers
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$file");
		header("Content-Type: application/json");
		header("Content-Transfer-Encoding: binary");

		// Read the file from disk
		readfile($file_path);
		exit();
	}

	public function make_backup( $method, $methods, $wp_options )
	{
		global $wpdb;

		// Backup file content
		$content = array();

		// Store plugin version
		$content['plugin_version'] = RWP_Reviewer::VERSION;

		// Scan wp options
		foreach ( $wp_options as $option_name ) {
			$option_value = RWP_Reviewer::get_option( $option_name );
			if( empty( $option_value ) ) {
				continue;
			}
			$content['options'][ $option_name ] = maybe_serialize( $option_value );
		}

		// Get wp post metas
		$post_metas = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE'rwp_%';", ARRAY_A );
		if( $post_metas ) {
			$content['post_metas'] = $post_metas;
		}

		// Create the file
		$filename = RWP_PLUGIN_PATH.'backup/Reviewer_Plugin_Backup_' . date('Y-m-d_H-i-s', current_time('timestamp')) . '.json';
		$fp = fopen( $filename, 'w' );

		if( $fp === false ) { // No privileges to open the file
			add_settings_error( $this->option_name, 'rwp-backup-error1', __( 'Unable to create the backup file. Check you have the privileges to write on reviewer/backup folder', $this->plugin_slug ), 'error' );
			return false;
		}

		fputs( $fp, json_encode($content) );
		fclose($fp);

		if(! is_file( $filename ) ) {
			add_settings_error( $this->option_name, 'rwp-backup-error2', __( 'Unable to verify the new backup file.', $this->plugin_slug ), 'error' );
			return false;
		}

		add_settings_error( $this->option_name, 'rwp-backup-ok', __( 'Success! Download Reviewer backup files from the list down below. Backup files are inside the reviewer/backup folder.', $this->plugin_slug ), 'updated' );
		return true;
	}

	public function import_backup( $method, $methods, $wp_options )
	{
		// Validation
		if( !isset( $_FILES[ $this->option_name ] ) ) {
			add_settings_error( $this->option_name, 'rwp-backup-error5', __( 'Unable upload backup file.', $this->plugin_slug ), 'error' );
			return false;
		}

		// Type check
		// if( 'application/json' != $_FILES[ $this->option_name ]['type']['io_file'] || $_FILES[ $this->option_name ]['error']['io_file'] != 0 ) {
		// 	add_settings_error( $this->option_name, 'rwp-backup-error6', __( 'Please upload a json file that contains a backup of Reviewer plugin.', $this->plugin_slug ), 'error' );
		// 	return false;
		// }

		// Get Data
		$json_file = @file_get_contents( $_FILES[ $this->option_name ]['tmp_name']['io_file'] );
		$content = json_decode( $json_file , true);

		// Check it's a valid Reviewer backup
		if ( !isset( $content['plugin_version'] ) || !version_compare( $content['plugin_version'], '3.11.0', 'ge' ) ) {
			add_settings_error( $this->option_name, 'rwp-backup-error99', __( 'Please upload a Reviewer backup file made with plugin verison >= v.3.11.0', $this->plugin_slug ), 'error' );
			return false;
		}

		// Import wp options
		foreach ( $wp_options as $option_name ) {
			if( !isset( $content['options'][ $option_name ] ) ) {
				continue;
			}
			update_option( $option_name, maybe_unserialize( $content['options'][ $option_name ] ) );
		}

		// Import post metas
		if( isset( $content['post_metas'] ) && is_array( $content['post_metas'] ) ) {
			foreach( $content['post_metas'] as $meta ) {
				add_post_meta( $meta['post_id'], $meta['meta_key'], maybe_unserialize( $meta['meta_value'] ) );
			}
		}

		add_settings_error( $this->option_name, 'rwp-backup-import-ok', __( 'The backup was successfully imported.', $this->plugin_slug ), 'updated' );
		return true;
	}

	public function migrate( $method, $methods, $wp_options )
	{
		// Validation
		if( !isset( $_FILES[ $this->option_name ] ) ) {
			add_settings_error( $this->option_name, 'rwp-backup-error97', __( 'Unable upload migration file of user reviews.', $this->plugin_slug ), 'error' );
			return false;
		}

		// Type check
		// if( 'application/json' != $_FILES[ $this->option_name ]['type']['io_file'] || $_FILES[ $this->option_name ]['error']['io_file'] != 0 ) {
		// 	add_settings_error( $this->option_name, 'rwp-backup-error96', __( 'Please upload a json file that contains user reviews.', $this->plugin_slug ), 'error' );
		// 	return false;
		// }

		// Get Data
		$json_file = @file_get_contents( $_FILES[ $this->option_name ]['tmp_name']['io_file'] );
		$content = json_decode( $json_file , true);

		// Check it's a valid migration file
		if ( !is_array( $content ) ) {
			add_settings_error( $this->option_name, 'rwp-backup-error95', __( 'Please upload a json file that contains user reviews.', $this->plugin_slug ), 'error' );
			return false;
		}

		$this->templates = RWP_Reviewer::get_option( 'rwp_templates' );

		$rating_count = 0;
		foreach ( $content as $review ) {
			$rating = $this->map_review( $review );
			if( $rating === false ){
				continue;
			}

			$meta_key = $rating['rating_review_id'];
			$post_id = $rating['rating_post_id'];
			if( $rating['rating_review_id'] == -1 ) {
				$post_type 	= get_post_type( $post_id );
				$meta_key 	= md5( 'rwp-'. $rating['rating_template'] .'-'. $post_type . '-' . $post_id . '-' . $rating['rating_review_id'] );
			}
			add_post_meta( $post_id, 'rwp_rating_' . $meta_key, $rating );
			$rating_count++;
			// RWP_Reviewer::pretty_print( $review );
			// RWP_Reviewer::pretty_print( $rating );
		}

		add_settings_error( $this->option_name, 'rwp-backup-error98', sprintf( __( 'Your user reviews were imported successfully! (%d of %d)', $this->plugin_slug ), $rating_count, count($content) ), 'updated' );
		return true;
	}

	private function map_review( $review = array() )
	{
		if( !is_array( $review ) ) {
			return false;
		}

		// Template
		$templates = $this->templates;
		$template = isset( $review['review_template'] ) ? $review['review_template'] : null;
		if( is_null( $template ) || !isset( $templates[ $template ] ) ) {
			return false;
		}

		// Post ID
		$post_id = isset( $review['review_post_id'] ) ? intval( $review['review_post_id'] ) : 1;

		// Box ID
		$box_id = isset( $review['review_box_id'] ) ? intval( $review['review_box_id'] ) : 0;

		// Status
		if( isset( $review['review_status'] ) ) {
			$status = $review['review_status'];

			if( $review['review_status'] == 'publish') {
				$status = 'published';
			} elseif($review['review_status'] == 'pending' ) {
				$status = 'pending';
			} else {
				$status = 'published';
			}
		} else {
			$status = 'published';
		}

		// Date
		$time = isset( $review['review_created_at'] ) ? strtotime( $review['review_created_at'] ) : time();
		$date = ( $time == -1 ) ? time() : $time;

		// Criteria
		$default = array();
		$minimim = $templates[ $template ]['template_minimum_score'];
		$maximum = $templates[ $template ]['template_maximum_score'];
		$criteria = array_keys( $templates[ $template ]['template_criterias'] );
		foreach( $criteria as $key ) {
			$default[ $key ] = $minimim;
		}
		$scores = ( isset( $review['review_criteria'] ) && is_array( $review['review_criteria'] ) ) ? $review['review_criteria'] :  $default;

		$review_criteria = array();
		foreach( $criteria as $key ) {
			$k = 'c' . $key;
			if( isset( $scores[ $k ] ) ) {
				$review_criteria[ $key ] = floatval( $scores[ $k ] );
			} else {
				$review_criteria[ $key ] = $minimim;
			}
		}

		// User ID
		$user_id = isset( $review['review_user_id'] ) ? intval( $review['review_user_id'] ) : 0;

		// Author name
		$name = isset( $review['review_author'] ) ? sanitize_text_field( stripslashes_deep( $review['review_author'] ) ) : '';

		// Author Email
		$email = isset( $review['review_author_email'] ) ? sanitize_text_field( stripslashes_deep( $review['review_author_email'] ) ) : '';

		// Title
		$title = isset( $review['review_title'] ) ? sanitize_text_field( stripslashes_deep( $review['review_title'] ) ) : '';

		// Comment
		$comment = isset( $review['review_comment'] ) ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes_deep( $review['review_comment'] ) ) ) ) : '';

		// Images
		$attachments = isset( $review['review_images'] ) && is_array( $review['review_images'] ) ? $review['review_images'] : array();
		$images = array();
		foreach ( $attachments as $attachment_id ) {
			$image = intval( $attachment_id );
			if( $image > 0 ){
				$images[] = $image;
			}
		}

		// Verified
		$verified = ( isset( $review['review_verified'] ) && $review['review_verified'] ) ? true : false;

		return array(
			'rating_id'				=> uniqid('rwp_rating_'),
			'rating_post_id'		=> $post_id,
			'rating_review_id'		=> $box_id,
			'rating_score'	 		=> $review_criteria,
			'rating_user_id'		=> $user_id,
			'rating_user_name'		=> $name,
			'rating_user_email'		=> $email,
			'rating_title'			=> $title,
			'rating_comment'		=> $comment,
			'rating_images'			=> $images,
			'rating_date'			=> $date,
			'rating_status'			=> $status,
			'rating_verified'		=> $verified,
			'rating_template'		=> $template,
		);
	}

	public function validate_fields( $fields )
	{
		$actions = array_keys( $this->io_fields['io_action']['options'] );
		$methods = array('append', 'replace');
		$wp_options = array('rwp_io', 'rwp_notifications', 'rwp_pending_ratings', 'rwp_preferences', 'rwp_support', 'rwp_templates');

		// Validate fields
		if( !isset( $fields['io_action'] ) || !in_array( $fields['io_action'], $actions ) || !isset( $fields['io_method'] ) || !in_array( $fields['io_method'], $methods )) {
			add_settings_error( $this->option_name, 'rwp-backup-error3', __( 'The form was not submitted correctly!', $this->plugin_slug ), 'error' );
			return $this->option_value;
		}

		$action = $fields['io_action'];
		$method = $fields['io_method'];

		// Perform the action
		$result = call_user_func( array( $this, $action ), $method, $methods, $wp_options );

		if( $result ){
			$this->option_value['last_action'] = date('Y-m-d H-i-s');
		}

		// var_dump( $result );
		// RWP_Reviewer::pretty_print( $_FILES );
		// RWP_Reviewer::pretty_print( $fields ); flush(); die();

		return $this->option_value;
	}

	public function set_io_fields()
	{
		$this->io_fields = array(

			'io_action' => array(
				'title' 	=> __( 'Type of Action', $this->plugin_slug ),
				'options' 	=> array(
					'make_backup' 	=> array(
						'label' => __( 'Make a backup', $this->plugin_slug ),
						'description' => __('It will create a JSON file with all data of Reviewer plugin.', $this->plugin_slug),
					),
					'import_backup' => array(
						'label' => __( 'Import a backup', $this->plugin_slug ),
						'description' => sprintf(__('It will import the selected JSON file that contains Reviewer plugin backup. %s The backup file have to be made with a Reviewer plugin version >= v.3.11.0 %s', $this->plugin_slug), '<br/><strong>', '</strong>'),
					),
					'migrate' 		=> array(
						'label' => __( 'Migrate', $this->plugin_slug ),
						'description' => __('It will import the selected JSON file that contains user reviews from an other review service. Specifications about migration file are described inside documentation.', $this->plugin_slug),
					),
				),
				'default'	=> 'make_backup'
			),

			// 'io_method' => array(
			// 	'title' 	=> __( 'Managing imported data', $this->plugin_slug ),
			// 	'options' 	=> array(
			// 		'append' 	=> array(
			// 			'label' => __( 'The imported data will be appended to the existing ones', $this->plugin_slug ),
			// 		),
			// 		'replace' 	=> array(
			// 			'label' => __( 'The imported data will be replaced with the existing ones', $this->plugin_slug ),
			// 		),
			// 	),
			// 	'default'	=> 'append'
			// ),

			'io_file' => array(
				'title' 	=> __( 'Backup/Migration JSON File', $this->plugin_slug ),
				'options'  	=> array(),
				'default'  	=> '',
			)
		);
	}

/*----------------------------------------------------------------------------*
 * Callbacks for form fields
 *----------------------------------------------------------------------------*/

	public function io_action_cb( $args )
	{
		extract( $args );

		echo '<ul class="rwp-migration-actions">';
		foreach ($options as $key => $option){
			echo '<li>';
				echo '<input id="rwp-migration-action-'. $key .'" type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="'. $key .'" '. ( $default == $key ? 'checked' : '' ) .' />';
				echo '<label for="rwp-migration-action-'. $key .'">'. $option['label'] .'</label>';
				echo '<p class="description" >'. $option['description'] .'</p>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<input type="hidden" name="'. $this->option_name .'[io_method]" value="append"/>';
	}

	public function io_method_cb( $args )
	{
		extract( $args );

		echo '<p class="description">'. __( 'Skip this field if you chose the "Make a backup" action', $this->plugin_slug ) .'</p>';
		echo '<ul class="rwp-migration-actions">';
		foreach ($options as $key => $option){
			echo '<li>';
				echo '<input id="rwp-migration-action-'. $key .'" type="radio" name="'. $this->option_name .'[' . $field_id . ']" value="'. $key .'" '. ( $default == $key ? 'checked' : '' ) .' />';
				echo '<label for="rwp-migration-action-'. $key .'">'. $option['label'] .'</label>';
			echo '</li>';
		}
		echo '</ul>';
	}

	public function io_file_cb( $args )
	{
		extract( $args );

		echo '<p class="description">'. __( 'Skip this field if you chose the "Make a backup" action', $this->plugin_slug ) .'</p>';
		echo '<p class="description">'. __( 'Choose a Reviewer backup file or a migration json file', $this->plugin_slug ) .'</p>';
		echo '<input type="file" id="rwp-migration-file" name="'. $this->option_name .'[' . $field_id . ']"/>';
	}
}
