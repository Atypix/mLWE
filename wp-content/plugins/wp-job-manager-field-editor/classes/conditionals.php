<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Job_Manager_Field_Editor_Conditionals
 *
 * @since 1.7.10
 *
 */
class WP_Job_Manager_Field_Editor_Conditionals {

	/**
	 * @var \WP_Job_Manager_Field_Editor
	 */
	private $core;

	/**
	 * @var array|boolean Logic configuration
	 */
	public $logic = null;

	/**
	 * @var array|boolean Listing fields
	 */
	public $fields;

	/**
	 * WP_Job_Manager_Field_Editor_Conditionals constructor.
	 *
	 * @param $core \WP_Job_Manager_Field_Editor
	 */
	public function __construct( $core ) {

		$this->core = $core;
		$this->hooks();

		add_action( 'wp', array( $this, 'add_fields_filter' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

	}

	/**
	 *  Form Output
	 *
	 *
	 * @since 1.7.10
	 *
	 */
	public function form(){

		if( ! $logic = $this->get_logic() ){
			return;
		}

		$this->localize( $logic, $this->get_fields() );
		wp_enqueue_script( 'jmfe-conditionals' );

	}

	/**
	 * Check if logic should set field as not required
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param $meta_key
	 * @param $config
	 * @param $logic
	 *
	 * @return bool
	 */
	public function logic_not_required( $meta_key, $config, $logic ){

		$not_required = false;
		$js_config = $this->get_js_config( $logic );
		$default_hidden = $js_config['default_hidden'];

		foreach( (array) $logic as $slug => $lcfg ){

			if( ! in_array( $meta_key, $lcfg['fields'] ) ){
				continue;
			}

			// Nothing will be set in POST if field is ACTUALLY hidden (whereas it would be empty string if nothing was entered)
			if( ! array_key_exists( $meta_key, $_POST ) ){

				// If meta key is found in a group with an action/type that would hide the field, set required false in listing field config
				if ( ( $default_hidden && $lcfg['type'] === 'show' ) || ( ! $default_hidden && $lcfg['type'] === 'hide' ) ) {
					$not_required = true;
					break;
				}


			}

		}

		return $not_required;

	}

	/**
	 * Set Required Fields False
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param $listing_fields
	 *
	 * @return mixed
	 */
	public function set_required_false( $listing_fields ){

		if( ! $logic = $this->get_logic() ){
			return $listing_fields;
		}

		$active_fields = $this->get_group_fields();

		// Loop through groups (job, company, resume_fields)
		foreach( (array) $listing_fields as $group => $fields ){

			// Loop through meta keys
			foreach( (array) $fields as $meta_key => $config ){

				$required = array_key_exists( 'required', $config ) && $config['required'] === true;

				// Set required false if one of our meta keys is in active logic configuration (to prevent core from handling validation)
				if( $required && in_array( $meta_key, $active_fields ) && $this->logic_not_required( $meta_key, $config, $logic ) ){
					$listing_fields[ $group ][ $meta_key ][ 'required' ] = false;
				}

			}


		}

		return $listing_fields;
	}

	/**
	 * Get Conditional Group Fields
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param bool $logic
	 * @param bool $type_only
	 *
	 * @return array
	 */
	public function get_group_fields( $logic = false, $type_only = false ){

		if( ! $logic ){
			$logic = $this->get_logic();
		}

		$group_fields = array();

		foreach ( (array) $logic as $group => $gcfg ) {

			if( ! $type_only || ( $type_only && $gcfg['type'] === $type_only ) ){
				$group_fields = array_merge( $group_fields, $gcfg['fields'] );
			}

		}

		return $group_fields;
	}

	/**
	 * Get Fields to Hide by Default
	 *
	 * By default, if a field has "show" configuration, it will be added to the list of
	 * default hidden fields.  When using conditional logic, the majority of the time it will be
	 * to "show" fields under certain situations, that is why by default fields are hidden if they
	 * have logic configuration.  You can return false to the filter to fields as shown by default.
	 *
	 * @since 1.7.10
	 *
	 * @param $logic
	 *
	 * @return array|bool      An array of fields to hide by default, or false to show fields by default
	 */
	public function default_hidden( $logic ){

		$hidden_fields = $this->get_group_fields( $logic, 'show' );

		return apply_filters( 'field_editor_conditionals_default_hidden_fields', $hidden_fields, $this );
	}

	/**
	 * Get JS Conditional Config
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param bool|array $logic
	 * @param bool|array $meta_keys
	 *
	 * @return array
	 */
	public function get_js_config( $logic = false, $meta_keys = false ){

		if( $logic ){
			$logic = $this->get_logic();
		}

		$default_hidden = $this->default_hidden( $logic );
		$case_sensitive = get_option( 'jmfe_logic_case_sensitive', false ) == 1 ? true : false;

		$js_config = array(
			'delay'          => get_option( 'jmfe_logic_debounce_delay', 250 ), // debounce delay on input (amount of time to wait on each input change before checking logic) -- should be in milliseconds (1000ms = 1s)
			'group_types'    => self::get_group_types( $default_hidden ),
			'case_sensitive' => $case_sensitive,
			'chosen_fields'  => $this->get_chosen_fields( $meta_keys ),
			'default_hidden' => $default_hidden,
		);

		return apply_filters( 'field_editor_conditionals_front_js_config', $js_config, $this );
	}

	/**
	 * Return All Meta Keys that are Chosen Field Types
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param bool $meta_keys
	 *
	 * @return array|bool
	 */
	public function get_chosen_fields( $meta_keys = false ){

		$chosen_enabled = apply_filters( 'job_manager_chosen_enabled', true );

		if( ! $chosen_enabled ){
			return false;
		}

		if( ! $meta_keys ){
			$meta_keys = $this->get_fields();
		}

		$chosen_field_types = array( 'term-multiselect', 'multiselect' );
		$chosen_fields = array();

		foreach( (array) $meta_keys as $meta_key => $config ){

			if( in_array( $config['type'], $chosen_field_types ) ){
				$chosen_fields[] = $meta_key;
			}

		}

		return $chosen_fields;
	}

	/**
	 * Localize JS
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param $logic
	 * @param $meta_keys
	 */
	public function localize( $logic, $meta_keys ){

		wp_localize_script( 'jmfe-conditionals', 'jmfe_js_logic_config', $this->get_js_config( $logic, $meta_keys ) );

		wp_localize_script( 'jmfe-conditionals', 'jmfe_conditional_logic', $logic );
		wp_localize_script( 'jmfe-conditionals', 'jmfe_logic_meta_keys', $this->build_meta_keys_js( $logic, $meta_keys ) );

	}

	/**
	 * Build Meta Key JS Configurations
	 *
	 * This method will loop through all group/logic configuration, and build an array of data using the
	 * structure below, which will be converted to JSON for use in the javascript on the frontend.
	 *
	 * This is used for handling jQuery callbacks on input changes for meta keys, which is built using
	 * the configuration returned from this method.
	 *
	 * Example:
	 *
	 * 'meta_key' => array(
	 *     'type' => 'text',
	 *     'logic' => array(
	 *          array(
	 *              'group' => 'logic_group',
	 *              'section' => section_array_index,
	 *              'row' => row_array_index
	 *          ),
	 *          array(
	 *              'group' => 'logic_group_2',
	 *              'section' => section_array_index_2,
	 *              'row' => row_array_index_2
	 *          ),
	 *      )
	 * )
	 *
	 * @since 1.7.10
	 *
	 * @param array $config             Group logic configuration array
	 * @param array $meta_keys_config   Meta key configuration array (should be array with meta keys as array keys)
	 *
	 * @return array    Will return array with meta key as array key, and logic under logic key in array (see doc for example)
	 */
	public function build_meta_keys_js( $config , $meta_keys_config ){

		$mk_js = array();

		// Loop through each group configuration
		foreach( (array) $config as $group => $gcfg ){

			// Group doesn't have any logic configuration
			if( ! array_key_exists( 'logic', $gcfg ) || empty( $gcfg['logic'] ) ){
				continue;
			}

			// Loop through each logic section
			foreach( (array) $gcfg['logic'] as $section_id => $rows ){


				// Loop through each logic row
				foreach( (array) $rows as $row_id => $logic ){

					$meta_key = $logic['check'];

					// May not be a meta key logic, or meta key may no longer exist (removed, etc)
					if( ! array_key_exists( $meta_key, $meta_keys_config ) ){
						continue;
					}

					// If meta key not already setup by previous logic config, set defaults now
					if( ! array_key_exists( $meta_key, $mk_js ) ){

						$mk_js[ $meta_key ] = array(
							'type' => str_replace( '-', '_', $meta_keys_config[ $meta_key ]['type'] ),
							'logic' => array(),
						);

					}

					// Add logic config for meta key to logic array of arrays
					$mk_js[ $meta_key ][ 'logic' ][] = array(
						'group' => $group,
						'section' => $section_id,
						'row' => $row_id
					);

				} // close each logic row


			} // close each logic section


		} // close each group config

		return apply_filters( 'field_editor_conditionals_front_meta_keys_js', $mk_js, $this );
	}

	/**
	 * Register Scripts and Styles
	 *
	 *
	 * @since 1.7.10
	 *
	 */
	public function register_assets(){

		if ( defined( 'WPJMFE_DEBUG' ) && WPJMFE_DEBUG == true ) {

			$cjs = 'build/conditionals.js';

		} else {

			$cjs = 'conditionals.min.js';

		}

		wp_register_script( 'jmfe-conditionals', WPJM_FIELD_EDITOR_PLUGIN_URL . "/assets/js/{$cjs}", array( 'jquery' ), WPJM_FIELD_EDITOR_VERSION, true );
	}

	/**
	 * Get Group Types
	 *
	 *
	 * @since 1.7.10
	 *
	 * @param bool $default_hidden
	 *
	 * @return mixed|void
	 */
	public static function get_group_types( $default_hidden = false ){

		$types = apply_filters( 'field_editor_conditionals_group_types', array(
			'show'    => array(
				'label' => __( 'Show', 'wp-job-manager-field-editor' ),
				'icon'  => 'unhide',
				'opposite' => 'hide',
			),
			'hide'    => array(
				'label'   => __( 'Hide', 'wp-job-manager-field-editor' ),
				'icon'    => 'hide',
				'opposite' => 'show',
			),
			'disable' => array(
				'label'   => __( 'Disable', 'wp-job-manager-field-editor' ),
				'icon'    => 'lock',
				'default' => 'enable',
				'opposite' => 'enable'
			),
			'enable'  => array(
				'label' => __( 'Enable', 'wp-job-manager-field-editor' ),
				'icon'  => 'unlock',
				'opposite' => 'disable'
			),
		));

		if( $default_hidden ){
			$types[ 'show' ][ 'default' ] = 'hide';
		} else {
			$types[ 'hide' ][ 'default' ] = 'show';
		}

		return $types;
	}

	/**
	 * Get Fields Placeholder
	 *
	 *
	 * @since 1.7.10
	 *
	 * @return bool
	 */
	public function get_fields(){ return false; }

	/**
	 * Get Logic Placeholder
	 *
	 *
	 * @since 1.7.10
	 *
	 * @return null
	 */
	public function get_logic(){ return null; }
}