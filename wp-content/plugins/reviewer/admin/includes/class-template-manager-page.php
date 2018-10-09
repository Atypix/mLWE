<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Template_Manager_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	public $template_fields;
	public $colors_palettes;
	public $option_value;

	public function __construct()
	{
		parent::__construct();

		$this->set_colors_palettes(); 
		$this->template_fields = RWP_Template_Manager_Page::get_template_fields();
		$this->menu_slug = 'reviewer-template-manager-page';
		$this->option_name = 'rwp_templates';
		$this->option_value = RWP_Reviewer::get_option( $this->option_name );
		$this->add_menu_page();
		$this->register_page_fields();
		$this->add_fix_image_uploading();
	}
	
	public function add_fix_image_uploading()
	{
		if(isset($_GET['page']) && $_GET['page'] == 'reviewer-template-manager-page') 
			add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}
	
	public function localize_script() 
	{
		wp_localize_script( $this->plugin_slug . '-admin-script', 'isTemplateManagerObj', array( "val" => true) );
	}

	public function add_menu_page()
	{
		add_submenu_page( null, __( 'Template Manager', $this->plugin_slug), __( 'Template Manager', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	} 

	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Template Manager', $this->plugin_slug ); ?></h2>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
			<?php
				settings_fields( $this->option_name );
				do_settings_sections( $this->menu_slug );
				submit_button();
			?>
			</form>

			<?php //RWP_Reviewer::pretty_print( $this->temp); //RWP_Reviewer::pretty_print(  $this->option_value ); ?>
		</div><!--/wrap-->
		<?php
	}

	public function register_page_fields()
	{
		// Add sections
		$sections = array( 'rwp_general_section' => __( 'General Preferences', $this->plugin_slug), 'rwp_review_section' => __( 'Review Preferences', $this->plugin_slug),  'rwp_theme_section' => __( 'Theme Preferences', $this->plugin_slug), 'rwp_colors_section' => __( 'Theme Colors', $this->plugin_slug), 'rwp_customization_section' => __( 'Customization Preferences', $this->plugin_slug), 'rwp_template_users_ratings_section' => __( 'Users Ratings', $this->plugin_slug), 'rwp_template_reviews_section' => __( 'Auto Box Type', $this->plugin_slug) );

		foreach ( $sections as $section_id => $section_title )	
			add_settings_section( $section_id, $section_title, array( $this, 'display_section'), $this->menu_slug );

		if ( isset( $_GET['template'] ) && isset( $this->option_value[ $_GET['template'] ]  ) ) { // Edit mode ok!
			$template_to_edit = $this->option_value[ $_GET['template'] ];
			$this->temp = $template_to_edit;
		} else {
			$template_to_edit = array();
			$this->temp = $template_to_edit;
		}

		// Add fields
		foreach ($this->template_fields as $field_id => $field) {

			if($field_id == 'template_criteria_order') continue;

			$value = isset( $template_to_edit[ $field_id ] ) ? $template_to_edit[ $field_id ] : '';

			add_settings_field( $field_id, $field['title'], array( $this, $field_id . '_cb' ), $this->menu_slug, $field['section'], array( 'field_id' => $field_id, 'description' => $field['description'], 'default' => $field['default'], 'value' => $value ) );
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

	public function validate_fields( $fields )
	{
		$valids = array();
		$errors = array();

		//RWP_Reviewer::pretty_print($fields); //flush(); die();

		foreach ($this->template_fields as $field_id => $field) {
			
			if( !isset( $fields[ $field_id ] ) ) { // The field is not set
				
				$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
				continue;
			}
			
			$value = ( ! is_array( $fields[ $field_id ] ) ) ? trim( $fields[ $field_id ] ) : $fields[ $field_id ]; // Delete white spaces 
			$title = $this->template_fields[ $field_id ]['title'];
			
			if( $field['type'] != 'color' && empty( $value ) ) { // The field is empty

				$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
				continue;
			}
				
			// Validate field based on its type
			switch( $field['type'] )
			{
			
				case 'percentages' :
					
					$range = explode( '-', $value );
					
					if( !$range || empty( $range ) || count( $range) != 2 || intval( $range[0] ) > intval( $range[1] ) ) {
						
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						$errors[] = sprintf( __('%s are not a valid range', $this->plugin_slug), $title );
						
					} else {
						
						$valids[ $field_id ] = esc_sql( esc_html ( $value ) );
					}
					
					break;
					
				case 'min' :
				
					$number = intval( $value );
															
					if( $number >= 0 ) {
						
						$valids[ $field_id ] = esc_sql( esc_html( $number ) );
					} 
					else {
						
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						$errors[] = sprintf( __('%s must be a integer number', $this->plugin_slug), $title );
					}
					
					break;
					
				case 'max' :
				
					$number = intval( $value );
															
					if( $number > 0 && $number > $valids['template_minimum_score'] ) {
						
						$valids[ $field_id ] = esc_sql( esc_html( $number ) );
					} 
					else {
						
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						$errors[] = sprintf( __('%s must be a integer number and greater than Mimimun Score', $this->plugin_slug), $title );
					}
					
					break;
					
				case 'criterias' :

					if( is_array( $value ) && count( $value ) > 0 ) {

						$criteria 	= array();
						$order 		= array();

						foreach ($value as $key => $criterion) {

							$c = trim( $criterion );

							if( empty( $c ) )
								continue;

							$order[] 			= $key;
							$criteria[ $key ]	= esc_sql( esc_html( $c ) );
						}

						if( empty( $criteria ) ) {
							$valids[ $field_id ] 				= $this->template_fields[ $field_id ]['default'];
							$valids['template_criteria_order'] 	= array( '0' => 0 );
						} else {
							$valids[ $field_id ] 				= $criteria;
							$valids['template_criteria_order'] 	= $order;
						}

					} else {
											
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						$valids['template_criteria_order'] = array( '0' => 0 );
						$errors[] = sprintf( __('Invalid submission for %s', $this->plugin_slug), $title );
					}
					
					break;
				
				case 'color' : // color type

					$palette_id = ( isset( $valids['template_colors_palettes'] ) && in_array( $valids['template_colors_palettes'] , array_keys( $this->colors_palettes ) ) ) ? $valids['template_colors_palettes'] : 'palette_custom'; 
					
					if( $palette_id == 'palette_custom' ) {

						if( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
					
							$valids[ $field_id ] = esc_sql( esc_html( $value ) );	
						
						} else {
						
							$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
							$errors[] = sprintf( __('Invalid HEX color for %s', $this->plugin_slug), $title );
						}

					} else {
						$valids[ $field_id ] = $this->colors_palettes[$palette_id]['fields'][ $field_id ];
					}
					
					break;
					
				case 'number' : // number type
										
					$number = intval( $value );
										
					if( $number != 0 ) {
						
						$valids[ $field_id ] = esc_sql( esc_html( $number ) );
					} 
					else {
						
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						$errors[] = sprintf( __('%s must be a integer number', $this->plugin_slug), $title );
					}
					
					break;

				case 'posts_types' : // posts types

					if( is_array( $value ) ) {
						foreach ( $value as $post_type) 
							$valids[ $field_id ][] = esc_sql( esc_html( $post_type ) ); 
					} else {
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default']; 
					}

					break;

				case 'opts' :

					if( ! is_array( $value ) ) {
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						break;
					}

					$values = array();

					foreach ($field['options'] as $opt => $l) {

						if( in_array( $opt, $value ) )
							$values[] = $opt;
					}

					$valids[ $field_id ] = $values;

					break;

				case 'tabs':

					if( ! is_array( $value ) ) {
						$valids[ $field_id ] = $this->template_fields[ $field_id ]['default'];
						break;
					}

					$tabs = array();
					$form_fields   = array( 'tab_label', 'tab_color' );

					foreach ( $value as $i => $tab) {

						if( !is_array( $tab ) ) continue;

						$valid_tab = array();

						foreach ($form_fields as $key) {

							if( !isset( $tab[ $key ] ) ) {
								$valid_tab[ $key ] = $field['options'][ $key ]['default'];
								continue;
							}

							if( $key == 'tab_color' ) {

								if( preg_match( '/^#[a-f0-9]{6}$/i', $tab[ $key ] ) ) {

									$valid_tab[ $key ] = wp_kses_post( $tab[ $key ] );

								} else {
									$valid_tab[ $key ] = $field['options'][ $key ]['default'];
								}

							} else {

								$v = trim( $tab[ $key ] );

								$valid_tab[ $key ] = ( empty( $v ) ) ? $valid_tab[ $key ] = $field['options'][ $key ]['default'] : wp_kses_post( $tab[ $key ] );
							}
						}

						$tabs[ $i ] = $valid_tab;
					}

					$valids[ $field_id ] = $tabs;

					break;

				case 'checkbox':

					if ( isset( $fields[ $field_id ] ) ) {
						$valids[ $field_id ] = 'yes';
					} else {
						$valids[ $field_id ] = 'no';
					}
					break;
			
				case 'text' : // text or default type
				default:
				
					$valids[ $field_id ] = esc_sql( esc_html( $value ) );
					
					break;
			}			
		}

		//RWP_Reviewer::pretty_print($valids); flush(); die();

		if( ! empty( $errors ) ) {

			add_settings_error( $this->option_name, 'rwp-template-manager-ok', __( 'The theme was successfully saved, but fields listed below are invalid so their value is replaced with default one.', $this->plugin_slug ), 'updated' );
			add_settings_error( $this->option_name, 'rwp-template-manager-warning', implode( '<br/>', $errors ), 'update-nag' );

		} else {

			add_settings_error( $this->option_name, 'rwp-template-manager-ok', __( 'The theme was successfully saved.', $this->plugin_slug ), 'updated' );
		}

		$this->option_value[ $valids['template_id'] ] = $valids;

		return $this->option_value;
	}

	public function set_colors_palettes() 
	{
		$this->colors_palettes = array(

			'palette_forest' => array(
				'title'		=> __( 'Forest', $this->plugin_slug ),
				'colors'	=> array( '#56b258', '#3b3c3e', '#2a2a2a' ),
				'fields'	=> array(
					'template_text_color' 					=> '#2a2a2a',
					'template_total_score_box_color' 		=> '#56b258',
					'template_users_score_box_color'		=> '#3b3c3e',
					'template_high_score_color'				=> '#56b258',
					'template_medium_score_color'			=> '#56b258',
					'template_low_score_color'				=> '#56b258',
					'template_pros_label_color'				=> '#56b258',
					'template_cons_label_color'				=> '#56b258',
					'template_summary_label_color'			=> '#56b258',
					'template_users_reviews_label_color' 	=> '#56b258'
				)
			),

			'palette_robin' => array(
				'title'		=> __( 'Robin', $this->plugin_slug ),
				'colors'	=> array( '#e74343', '#3b3c3e', '#2a2a2a' ),
				'fields'	=> array(
					'template_text_color' 					=> '#2a2a2a',
					'template_total_score_box_color' 		=> '#e74343',
					'template_users_score_box_color'		=> '#3b3c3e',
					'template_high_score_color'				=> '#e74343',
					'template_medium_score_color'			=> '#e74343',
					'template_low_score_color'				=> '#e74343',
					'template_pros_label_color'				=> '#e74343',
					'template_cons_label_color'				=> '#e74343',
					'template_summary_label_color'			=> '#e74343',
					'template_users_reviews_label_color' 	=> '#e74343'
				)
			),

			'palette_sky' => array(
				'title'		=> __( 'Sky', $this->plugin_slug ),
				'colors'	=> array( '#26a4c0', '#3b3c3e', '#2a2a2a' ),
				'fields'	=> array(
					'template_text_color' 					=> '#2a2a2a',
					'template_total_score_box_color' 		=> '#26a4c0',
					'template_users_score_box_color'		=> '#3b3c3e',
					'template_high_score_color'				=> '#26a4c0',
					'template_medium_score_color'			=> '#26a4c0',
					'template_low_score_color'				=> '#26a4c0',
					'template_pros_label_color'				=> '#26a4c0',
					'template_cons_label_color'				=> '#26a4c0',
					'template_summary_label_color'			=> '#26a4c0',
					'template_users_reviews_label_color' 	=> '#26a4c0'
				)
			),

			'palette_cloud' => array(
				'title'		=> __( 'Cloud', $this->plugin_slug ),
				'colors'	=> array( '#5283b8', '#3c4650', '#566473' ),
				'fields'	=> array(
					'template_text_color' 					=> '#3b3c3e',
					'template_total_score_box_color' 		=> '#3c4650',
					'template_users_score_box_color'		=> '#566473',
					'template_high_score_color'				=> '#6666ca',
					'template_medium_score_color'			=> '#5ea7bd',
					'template_low_score_color'				=> '#5283b8',
					'template_pros_label_color'				=> '#5283b8',
					'template_cons_label_color'				=> '#5283b8',
					'template_summary_label_color'			=> '#5283b8',
					'template_users_reviews_label_color' 	=> '#5283b8'
				)
			),

			'palette_sunset' => array(
				'title'		=> __( 'Sunset', $this->plugin_slug ),
				'colors'	=> array( '#f67f3e', '#3b3c3e', '#2a2a2a' ),
				'fields'	=> array(
					'template_text_color' 					=> '#2a2a2a',
					'template_total_score_box_color' 		=> '#f67f3e',
					'template_users_score_box_color'		=> '#3b3c3e',
					'template_high_score_color'				=> '#f67f3e',
					'template_medium_score_color'			=> '#f67f3e',
					'template_low_score_color'				=> '#f67f3e',
					'template_pros_label_color'				=> '#f67f3e',
					'template_cons_label_color'				=> '#f67f3e',
					'template_summary_label_color'			=> '#f67f3e',
					'template_users_reviews_label_color' 	=> '#f67f3e'
				)
			),

			'palette_custom' => array(
				'title'		=> __( 'Custom', $this->plugin_slug ),
				'colors'	=> array( '#eaeaea', '#eaeaea', '#eaeaea' ),
			),
		);
	}

	public static function get_template_fields()
	{
		$plugin_slug = 'reviewer';

		return array(
		
			'template_id' => array(
				'title' 		=> __( 'Template ID', $plugin_slug ), 
				'section'		=> 'rwp_general_section',
				'default'		=> uniqid('rwp_template_'),
				'description'	=> __( 'An unique ID for the new template. You can leave blank (Racommended)', $plugin_slug ),
				'type'			=> 'text'
			),

			'template_name' => array(
				'title' 		=> __( 'Template Name', $plugin_slug ), 
				'section'		=> 'rwp_general_section',
				'default'		=> 'Reviewer Template',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_minimum_score' => array(
				'title' 		=> __( 'Minimum Score', $plugin_slug ), 
				'section'		=> 'rwp_review_section',
				'default'		=> 0,
				'description'	=> __( 'Criteria minimum score', $plugin_slug ),
				'type'			=> 'min'
			),

			'template_maximum_score' => array(
				'title' 		=> __( 'Maximum Score', $plugin_slug ), 
				'section'		=> 'rwp_review_section',
				'default'		=> 10,
				'description'	=> __( 'Criteria maximum score', $plugin_slug ),
				'type'			=> 'max'
			),

			'template_score_percentages' => array(
				'title' 		=> __( 'Score Percentages', $plugin_slug ), 
				'section'		=> 'rwp_review_section',
				'default'		=> '30-69',
				'description'	=> __( 'Define the percentages for high, medium, low scores', $plugin_slug ),
				'type'			=> 'percentages'
			),

			'template_criteria_order' => array(
				'default' 		=> NULL,
			),

			'template_criterias' => array(
				'title' 		=> __( 'Review Criteria', $plugin_slug ), 
				'section'		=> 'rwp_review_section',
				'default'		=> array('Criterion 1'),
				'description'	=> '',
				'type'			=> 'criterias'
			),

			'template_schema_type' => array(
				'title' 		=> __( 'Schema.org Type', $plugin_slug ), 
				'section'		=> 'rwp_review_section',
				'default'		=> 'Product',
				'options'		=> array(  
					'Product'		=> array('IndividualProduct', 'ProductModel', 'SomeProducts', 'Vehicle'),
					'Place'			=> array('AdministrativeArea', 'CivicStructure', 'Landform', 'LandmarksOrHistoricalBuildings', 'LocalBusiness', 'Residence', 'TouristAttraction'),
					'CreativeWork' 	=> array('Article', 'Blog', 'Book', 'Clip', 'Code', 'Comment', 'CreativeWorkSeason', 'CreativeWorkSeries', 'DataCatalog', 'Dataset', 'Diet', 'EmailMessage', 'Episode', 'ExercisePlan', 'Game', 'Map', 'MediaObject', 'Movie', 'MusicComposition', 'MusicPlaylist', 'MusicRecording', 'Painting', 'Photograph', 'PublicationIssue', 'PublicationVolume', 'Question', 'Recipe', 'Sculpture', 'Season', 'Series', 'SoftwareApplication', 'SoftwareSourceCode', 'TVSeason', 'TVSeries', 'VisualArtwork', 'WebPage', 'WebPageElement', 'WebSite'),
					'Organization' 	=> array('Airline', 'Airline', 'Corporation', 'EducationalOrganization', 'GovernmentOrganization', 'LocalBusiness', 'NGO', 'PerformingGroup', 'SportsOrganization'),
					'Services'		=> array('BroadcastService', 'CableOrSatelliteService', 'GovernmentService', 'Taxi', 'TaxiService'),
					'Event'			=> array(),
				),
				'description'	=> sprintf(__( 'The plugin uses Review type (for Reviewer Box) and AggregateRating type (for Users and Auto Boxes) for implementing %s. To define the item reviewed type, choose one option of the most common %s types.', $plugin_slug ),'<a href="https://developers.google.com/structured-data/rich-snippets/reviews" target="_blanck">Google Rich Snippets</a>', '<a href="http://schema.org/" target="_blanck">Schema.org</a>'),
				'type'			=> 'text'
			),

			'template_theme' => array(
				'title' 		=> __( 'Theme Layout', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'number'		=> 9,
				'default'		=> 'rwp-theme-1',
				'description'	=> __( 'Hover on theme name to view a preview', $plugin_slug ),
				'type'			=> 'text'
			),

			'template_colors_palettes' => array(
				'title' 		=> __( 'Theme Colors Palette', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> 'palette_custom',
				'description'	=> __( 'Choose a colors palette or define a custom one', $plugin_slug ),
				'type'			=> 'text'
			),


			'template_text_color' => array(
				'title' 		=> __( 'Text Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3b3c3e',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_total_score_box_color' => array(
				'title' 		=> __( 'Total Score Box Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3c4650',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_users_score_box_color' => array(
				'title' 		=> __( 'Users Score Box Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#566473',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_high_score_color' => array(
				'title' 		=> __( 'High Score Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#5283b8',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_medium_score_color' => array(
				'title' 		=> __( 'Medium Score Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#5283b8',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_low_score_color' => array(
				'title' 		=> __( 'Low Score Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#5283b8',
				'description'	=> '',
				'type'			=> 'color'
			),

			'template_box_font_size' => array(
				'title' 		=> __( 'Review Box Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 14,
				'description'	=> '',
				'type'			=> 'number'
			),
			
			'template_pros_label_color' => array(
				'title' 		=> __( 'Pros Label Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3b3c3e',
				'description'	=> '',
				'type'			=> 'color'
			),
			
			'template_pros_label_font_size' => array(
				'title' 		=> __( 'Pros Label Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 18,
				'description'	=> '',
				'type'			=> 'number'
			),
			
			'template_pros_text_font_size' => array(
				'title' 		=> __( 'Pros Text Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 12,
				'description'	=> '',
				'type'			=> 'number'
			),
			
			'template_cons_label_color' => array(
				'title' 		=> __( 'Cons Label Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3b3c3e',
				'description'	=> '',
				'type'			=> 'color'
			),
			
			'template_cons_label_font_size' => array(
				'title' 		=> __( 'Cons Label Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 18,
				'description'	=> '',
				'type'			=> 'number'
			),
			
			'template_cons_text_font_size' => array(
				'title' 		=> __( 'Cons Text Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 12,
				'description'	=> '',
				'type'			=> 'number'
			),

			'template_summary_label_color' => array(
				'title' 		=> __( 'Summary Label Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3b3c3e',
				'description'	=> '',
				'type'			=> 'color'
			),
			
			'template_summary_label_font_size' => array(
				'title' 		=> __( 'Summary Label Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 18,
				'description'	=> '',
				'type'			=> 'number'
			),

			'template_users_reviews_label_color' => array(
				'title' 		=> __( 'Users Reviews Label Color', $plugin_slug ), 
				'section'		=> 'rwp_colors_section',
				'default'		=> '#3b3c3e',
				'description'	=> '',
				'type'			=> 'color'
			),
			
			'template_users_reviews_label_font_size' => array(
				'title' 		=> __( 'Users Reviews Label Font Size', $plugin_slug ), 
				'section'		=> 'rwp_theme_section',
				'default'		=> 18,
				'description'	=> '',
				'type'			=> 'number'
			),
			
			'template_total_score_label' => array(
				'title' 		=> __( 'Reviewer Score Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Reviewer',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_users_score_label' => array(
				'title' 		=> __( 'Users Score Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Users',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_users_count_label_s' => array(
				'title' 		=> __( 'Users Ratings Count Label (Singular)', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> __( 'vote', $plugin_slug ),
				'description'	=> '',
				'type'			=> 'text',
			),
			'template_users_count_label_p' => array(
				'title' 		=> __( 'Users Ratings Count Label (Plural)', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> __( 'votes', $plugin_slug ),
				'description'	=> '',
				'type'			=> 'text',
			),
			
			'template_pros_label' => array(
				'title' 		=> __( 'Pros Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Pros',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_cons_label' => array(
				'title' 		=> __( 'Cons Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Cons',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_summary_label' => array(
				'title' 		=> __( 'Summary Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Summary',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_users_reviews_label' => array(
				'title' 		=> __( 'Users Reviews Label', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'What people say...',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_message_to_rate' => array(
				'title' 		=> __( 'Message to rate', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Leave your rating',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_message_to_rate_login' => array(
				'title' 		=> __( 'Message to rate (Login required)', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Login to rate',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_success_message' => array(
				'title' 		=> __( 'Success Message after rating', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Thank you for your rating',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_moderation_message' => array(
				'title' 		=> __( 'Moderation Message after rating', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Thank you, your rating is under moderation. It will appear soon',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_failure_message' => array(
				'title' 		=> __( 'Failure Message after rating', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=> 'Error during rate process',
				'description'	=> '',
				'type'			=> 'text'
			),
			
			'template_rate_image' => array(
				'title' 		=> __( 'Replace Star Image', $plugin_slug ), 
				'section'		=> 'rwp_customization_section',
				'default'		=>  RWP_PLUGIN_URL . 'public/assets/images/rating-star.png',
				'description'	=> '',
				'type'			=> 'text'
			),

			'template_auto_reviews' => array(
				'title' 		=> __( 'Enable Auto Boxes type', $plugin_slug ), 
				'section'		=> 'rwp_template_reviews_section',
				'default'		=>  array(),
				'description'	=> 'Skip this section if you are going to use Users Box type. Checking the post types in which you want to add an reviews box. Please read the documentation for more informations.',
				'type'			=> 'posts_types'
			),

			'template_auto_reviews_featured_image' => array(
				'title' 		=> __( 'Enable Featured Image', $plugin_slug ), 
				'section' 		=> 'rwp_template_reviews_section',
				'default'		=> 'no',
				'description' 	=> __( "Add Post's Featured Image (if it is set) to Auto Boxes", $plugin_slug ),
				'type'			=> 'checkbox',
			),

			'template_exclude_terms' => array(
				'title' 		=> __( 'Exclude auto reviews from...', $plugin_slug ), 
				'section'		=> 'rwp_template_reviews_section',
				'default'		=>  array(),
				'description'	=> 'Skip this setting if you are going to add reviews box manually. Checking the terms in which you want to exclude the auto review box.',
				'type'			=> 'posts_types'
			),

			'template_user_rating_options' => array(
				'title' 		=> __( 'User Rating Options', $plugin_slug ),
				'default' 		=> array('rating_option_avatar', 'rating_option_name', 'rating_option_email', 'rating_option_title', 'rating_option_comment', 'rating_option_score', 'rating_option_like', 'rating_option_share', 'rating_option_captcha'),
				'section'		=> 'rwp_template_users_ratings_section',
				'options' 		=>array(
					'rating_option_avatar' 	=> __( 'User Avatar', $plugin_slug ),
					'rating_option_name' 	=> __( 'User Name', $plugin_slug ),
					'rating_option_email' 	=> __( 'User Email', $plugin_slug ),
					'rating_option_title' 	=> __( 'User Review Title', $plugin_slug ), 
					'rating_option_comment' => __( 'User Review Comment', $plugin_slug ), 
					'rating_option_score'	=> __( 'User Review Score ( Five Stars or Full Rating )', $plugin_slug ), 
					'rating_option_like' 	=> __( 'User Review Like/Dislike Counter', $plugin_slug ),
					'rating_option_share' 	=> __( 'User Review Sharing', $plugin_slug ),
					//'rating_option_captcha' => __( 'User Review Secure Code', $plugin_slug)
				),
				'description'	=> __( 'Choose the options you want to include in Users Rating', $plugin_slug ),
				'type'			=> 'opts'
			),

			'template_custom_tabs' => array(
				'title' 		=> __( 'Custom Tabs', $plugin_slug ),
				'default' 		=> array(),
				'section'		=> 'rwp_theme_section',
				'options' 		=> array(

					'tab_color' => array(
						'label' => __( 'Tab Color', $plugin_slug ),
						'default' => '#3b3c3e'
					),

					'tab_label' => array(
						'label' => __( 'Tab Label', $plugin_slug ),
						'default' => __( 'Custom Tab', $plugin_slug ),
					),

					'tab_link' => array(
						'label' => __( 'Tab link', $plugin_slug ),
						'default' => ''
					),

				),
				'description'	=> __( 'Custom tabs will be displayed near to overall scores. For example you are reviewing some products and you need to add a price tab or any additional information. ', $plugin_slug ),
				'type'			=> 'tabs'
			),
			


		);
	}

/*----------------------------------------------------------------------------*
 * Callbacks for form fields
 *----------------------------------------------------------------------------*/

	public function template_id_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_name_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_minimum_score_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_maximum_score_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_score_percentages_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="' . $default . '" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_criterias_cb( $args )
	{
		extract( $args );
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
		echo '<input type="button" id="rwp-add-criteria-btn" class="button" value="'. __( 'Add Criterion', $this->plugin_slug ) .'" />';
		echo '<ul id="rwp-criterias" data-placeholder="' . __( 'Criterion Label', $this->plugin_slug ) . '" data-remove-label="' . __( 'Remove', $this->plugin_slug ) . '" data-id-label="' . __( 'ID', $this->plugin_slug ) . '">';

		$value = ( is_array( $value ) ) ? $value : array('');
		$order = isset( $this->temp['template_criteria_order'] ) ? $this->temp['template_criteria_order'] : array_keys( $value );

		for ( $i=0; $i < count( $value ); $i++) {

			$key = $order[ $i ];

			echo '<li><span class="dashicons dashicons-menu"></span><input type="text" name="'. $this->option_name .'[' . $field_id . ']['.$key.']" value="'. $value[ $key ] .'" placeholder="' . __( 'Criterion Label', $this->plugin_slug ) . '" />';
				echo ' <a class="rwp-remove-criteria-btn" href="#">'. __( 'Remove', $this->plugin_slug ) .'</a>';
				echo ' <em>'. __( 'ID', $this->plugin_slug ) .': c'. $key .'</em>';
			echo '</li>';
		}

		echo '</ul>';
	}

	public function template_schema_type_cb( $args ) 
	{	
		extract( $args );
		$options = $this->template_fields[ $field_id ]['options'];
		$value 	 = empty( $value ) ? $default : $value;

		echo '<p class="description">'. $description .'</p>';
		echo '<ul class="rwp-schema-types">';
		foreach ($options as $type => $type_data) {
			$ck = ($value == $type ) ? 'checked' : '';
			echo '<li>'; 
				echo '<input type="radio" id="rwp-radio-scheme-type-'. $type .'" name="'. $this->option_name .'[' . $field_id . ']" value="'. $type .'" '. $ck .' />';
				echo '<label title="" for="rwp-radio-scheme-type-'. $type .'" class="rwp-schema-types__main">'. $type .'</label>'; 
				if( !empty( $type_data ) ) {
					echo '<ul>';
					foreach ($type_data as $subtype) {
						echo '<li>'; 
							$ck2 = ($value == $subtype ) ? 'checked' : '';
							echo '<input type="radio" id="rwp-radio-scheme-type-'. $subtype .'" name="'. $this->option_name .'[' . $field_id . ']" value="'. $subtype .'" '. $ck2 .' />';
							echo '<label title="" for="rwp-radio-scheme-type-'. $subtype .'">'. $subtype .'</label>'; 
						echo'</li>';
					}
					echo '</ul>';
				}
			echo'</li>';
		}
		echo '</ul>';
	}

	public function template_theme_cb( $args )
	{
		extract( $args );

		$value = ( empty( $value ) || $value == 'rwp-theme-7' ) ? 'rwp-theme-1': $value;
		
		echo '<ul id="rwp-themes">';
		for ($i=1; $i <= 9; $i++) {
			if( $i == 7 ) continue;
			$ck = ($value == 'rwp-theme-'. $i ) ? 'checked' : '';

			echo '<li><input type="radio" id="rwp-radio-theme-'. $i .'" name="'. $this->option_name .'[' . $field_id . ']" value="rwp-theme-'. $i .'" '. $ck .' /><label title="" for="rwp-radio-theme-'. $i .'" class="rwp-theme-name" data-preview="'. RWP_PLUGIN_URL .'admin/assets/images/themes/theme-preview-'. $i .'.png">'. __( 'Theme', $this->plugin_slug ) .' '. $i .'</label></li>';
		}
		echo '</ul>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_colors_palettes_cb( $args )
	{
		extract( $args );
		// echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<p class="description" style="margin-bottom: 20px;">'. $description .'</p>';
		
		$selected = (empty( $value )) ? 'palette_custom' : $value;

		echo '<fieldset  class="scheme-list">';
		
		foreach ($this->colors_palettes as $palette_id => $palette) {

			$ck  = ($selected == $palette_id ) ? 'checked' : '';
			$sel = ($selected == $palette_id ) ? 'selected' : '';

				echo '<div class="color-option '.$sel.'">';
					echo '<input id="rwp-palette-input-'. $palette_id .'" name="'. $this->option_name .'[' . $field_id . ']" type="radio" value="'. $palette_id .'" class="tog" '.$ck.'>';
					echo '<label for="rwp-palette-input-'. $palette_id .'">'.$palette['title'].'</label>';
					echo '<table class="color-palette">';
						echo '<tbody>';
							echo '<tr>';

							foreach ($palette['colors'] as $color) {
								echo '<td style="background-color: '. $color .'">&nbsp;</td>';
							}
							echo '</tr>';
						echo '</tbody>';
					echo '</table>';
				echo '</div>';	
		}

		echo '</fieldset>';
	}

	public function template_text_color_cb( $args )
	{
		extract( $args );
		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_total_score_box_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_users_score_box_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_high_score_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_medium_score_color_cb( $args )
	{
		extract( $args );
		
		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_low_score_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}	
	
	public function template_box_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_pros_label_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_pros_label_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_pros_text_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_cons_label_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_cons_label_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_cons_text_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_summary_label_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_summary_label_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_users_reviews_label_color_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" class="rwp-color-picker"/>';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_users_reviews_label_font_size_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_total_score_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_users_score_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_users_count_label_s_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_users_count_label_p_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
			
	public function template_pros_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_cons_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_summary_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_users_reviews_label_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_message_to_rate_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_message_to_rate_login_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_success_message_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}

	public function template_moderation_message_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_failure_message_cb( $args )
	{
		extract( $args );

		echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<span class="rwp-field-desc description">'. $description .'</span>';
	}
	
	public function template_rate_image_cb( $args )
	{
		extract( $args );

		$src = (empty( $value )) ? $default : $value;
		echo '<div id="rwp-rating-image-wrap"><img src="'. $src .'" alt="Rating Image" id="rwp-rating-image" /></div>';
		echo '<input id="rwp-rating-image-url" type="hidden" name="'. $this->option_name .'[' . $field_id . ']" value="'. $value .'" placeholder="'. $default .'" />';
		echo '<input id="rwp-rating-image-btn" type="button" class="button" value="'. __( 'Choose Image', $this->plugin_slug ) .'" data-tb-title=" Reviewer | '. __( 'Upload Custom Image', $this->plugin_slug ) .'" data-tb-button="'.__('Use as rating image', $this->plugin_slug ).'" />';
		echo '<input id="rwp-rating-image-btn-default" type="button" class="button" value="'. __( 'Use Default', $this->plugin_slug ) .'" data-default="'. $default .'" />';
 
	}

	public function template_auto_reviews_cb( $args )
	{
		extract( $args );

		$post_types = get_post_types();
		$value 		= ( empty( $value ) ) ? array() : $value;

		echo '<p class="description" style="margin-bottom: 20px;">'. $description .'</p>';

		echo '<ul class="rwp-post-type-ul">';

		foreach ($post_types as $type) {
			
			$ck = ( in_array( $type, $value ) ) ? 'checked' : '';
			$post_type = get_post_type_object( $type );
            $label   = $post_type->labels->name;

			echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="' . $type . '" '. $ck .' /> <label>'. $label . ' - <em style="color:#666">' . $type . '</em></label></li>';
		}		

		echo '</ul>';

	}

	public function template_auto_reviews_featured_image_cb( $args ) 
	{
		extract( $args );

		if( $value == 'yes' ) {
			$ck = 'checked';
			$value = 'yes';
		} else {
			$ck = '';
			$value = 'no';
		}

		echo '<input type="checkbox" name="'. $this->option_name .'['. $field_id .']" value="'. $value .'" '.$ck.'/>';
		echo '<span class="description">'. $description.'</span>';
	}

	public function template_exclude_terms_cb( $args )
	{
		extract( $args );

		$taxonomies = get_taxonomies();
		$value 		= ( empty( $value ) ) ? array() : $value;

		echo '<p class="description" style="margin-bottom: 20px;">'. $description .'</p>';

		echo '<ul class="rwp-post-type-ul rwp-exclude-terms" data-msg="'. __( 'You can not exclude auto reviews from all terms', $this->plugin_slug ) .'">';

		$taxonomies_to_exclude = array('post_tag', 'nav_menu', 'link_category', 'post_format');

		foreach ( $taxonomies as $taxonomy => $v ) {

			if( in_array( $taxonomy, $taxonomies_to_exclude ) )
				continue;
			
			$terms = get_terms( $taxonomy, 'hide_empty=0' );

			if( !is_array( $terms ) ) {
				break;
			}

			foreach ( $terms as $term ) {

				$id = $taxonomy . '-' . $term->term_id;

				$ck = ( in_array( $id, $value ) ) ? 'checked' : '';

				echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="' . $id . '" '. $ck .' /> <label>' . $term->name . '</label></li>';
			}

		}	

		echo '</ul>';
		echo '<p class="description rwp-terms-msg"></p>';
	}

	
	public function template_user_rating_options_cb( $args ) {

		extract( $args );

		$options = $this->template_fields[ $field_id ]['options'];

		$ur_options = ( empty( $value ) || !is_array( $value ) ) ? $default : $value;

		echo '<p class="description" style="margin-bottom: 20px;">'. $description .'</p>';

		echo '<ul class="rwp-post-type-ul">';

		foreach ($options as $key => $label) {
			
			$ck = in_array($key, $ur_options) ? 'checked' : '';

			echo '<li><input type="checkbox" name="'. $this->option_name .'[' . $field_id . '][]" value="'. $key .'" '.$ck.'/> <label>' . $label . '</label></li>';
		}

		echo '</ul>';
	}

	public function template_custom_tabs_cb( $args ) {

		extract( $args );

		$options 		= $this->template_fields[ $field_id ]['options'];
		$options_keys 	= array_keys( $options );
 		$value 			= ( is_array( $value ) ) ? $value : array();

		$form_fields = array( 'tab_label', 'tab_color' );

		$ajax = array(
			'formFields' => $form_fields,
			'options'	 => $options,
			'optionsKeys'=> $options_keys,
			'removeLabel'=> __( 'Remove', $this->plugin_slug ),
			'fieldId'  	 => $field_id,
			'optionName' => $this->option_name
		);

		echo '<p class="description" style="margin-bottom: 20px;">'. $description .'</p>';

		echo '<input type="button" id="rwp-add-custom-tab-btn" class="button" value="'. __( 'Add Custom Tab', $this->plugin_slug ) .'" />';
		echo "<ul id=\"rwp-custom-tabs\" data-ajax='". json_encode( $ajax ) ."' >";

		foreach ($value as $i => $v) {
		
			echo '<li data-i="'. $i .'">';

				foreach ( $form_fields as $key ) {

					if( !in_array( $key, $options_keys) ) continue;

					$c = ( $key == 'tab_color' ) ? 'class="rwp-color-picker"' : '';

					echo '<span>';
						echo '<label>'. $options[ $key ]['label'] .'</label>';
						echo '<input type="text" name="'. $this->option_name .'[' . $field_id . ']['. $i .']['.$key.']" value="'. $value[ $i ][ $key ] .'" placeholder="'. $options[ $key ]['default'] .'" '. $c .' />';
					echo '</span>';
				}

				echo '<span><a class="rwp-remove-tab-btn" href="#">'.__('Remove', $this->plugin_slug).'</a></span>';

			echo '</li>';
		}

		echo '</ul>';
	}

}