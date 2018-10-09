<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class WP_Job_Manager_Emails_Shortcodes
 *
 * @since 1.0.
 *
 */
class WP_Job_Manager_Emails_Shortcodes {

	/**
	 * WP_Job_Manager_Emails_CPT
	 *
	 * @var WP_Job_Manager_Emails_CPT
	 * @since  1.0.0
	 */
	public $cpt = NULL;
	/**
	 * @type array
	 */
	protected $shortcodes;
	/**
	 * @type array
	 */
	protected $conditionals;
	/**
	 * @type array
	 */
	protected $default;
	/**
	 * @type $GLOBAL['shortcode_tags']
	 */
	protected $shortcode_tags;
	/**
	 * @type integer
	 */
	protected $job_id;
	/**
	 * @type integer
	 */
	protected $alert_id;
	/**
	 * @type integer
	 */
	protected $resume_id;
	/**
	 * @type integer
	 */
	protected $listing_id;
	/**
	 * @type integer
	 */
	protected $app_id;
	/**
	 * @type object
	 */
	protected $template;
	/**
	 * @type integer
	 */
	protected $meta_key;
	/**
	 * @type integer
	 */
	protected $args;
	/**
	 * @var bool Whether email is being processed or not
	 */
	protected $processing = FALSE;

	/**
	 * WP_Job_Manager_Emails_Shortcodes constructor.
	 */
	public function __construct( $cpt ) {
		$this->cpt = $cpt;
		add_filter( 'job_manager_visibility_is_admin_or_author', array( $this, 'visibility_bypass' ) );
	}

	/**
	 * Attempt to Get Value for Shortcode
	 *
	 * This method will attempt to get the value of a shortcode, starting with checking for a filter, if a filter
	 * does not exist it will check for a method (in this or extending class), and if that method does not exist,
	 * will attempt to get value from the listing's metadata.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param         $args
	 * @param string  $content
	 * @param string  $meta_key     Should be the meta key to get value from, also can be $tag which would be meta key
	 * @param boolean $each         Return array instead of imploded value (foreach loop will handle array)
	 *
	 * @return mixed|null
	 */
	function handler_get_value( $args, $content = '', $meta_key = '', $each = FALSE ){

		$value = '';

		$this->meta_key = $meta_key;
		$this->args = $args;

		/**
		 * Get only shortcode defined configuration
		 *
		 * $shortcodes should only contain the shortcode configuration set in class configuration (or through filters) and should not include or be merged with field config.
		 */
		$shortcodes = $this->get_shortcodes( TRUE );

		// Check if meta key needs to be changed based on Application field rules
		if( $this->application() && method_exists( $this, 'check_rules' ) ){
			$meta_key = $this->check_rules( $meta_key );
		}

		/**
		 * Check for filter first
		 */
		if( has_filter( "job_manager_emails_shortcode_{$meta_key}" ) ) {
			return apply_filters( "job_manager_emails_shortcode_{$meta_key}", '', $args, $content, $meta_key );
		}

		/**
		 * Check if method exists to return the value
		 */
		if( method_exists( $this, $meta_key ) ) {

			/**
			 * Because there is a chance that the meta key is a method not meant for returning a value,
			 * we need to check what is returned before actually using that value.
			 * Example is the `application` meta key, which would actually return the Application class object
			 */
			$method_return = call_user_func( array($this, $meta_key), $args, $content, $meta_key );

			if( ! is_object( $method_return ) && $method_return !== null ){
				return $method_return;
			}
		}

		/**
		 * If that didn't work, let's see if the shortcode config has a callback defined
		 */
		if ( isset( $shortcodes[ $meta_key ], $shortcodes[ $meta_key ][ 'callback' ] ) ){

			$callback = $shortcodes[ $meta_key ][ 'callback' ];

			// First check if an array was passed, maybed it's another object outside ours
			if( is_array( $callback ) && is_object( $callback[ 0 ] ) && is_callable( $callback ) ){

				return call_user_func( $callback, $args, $content, $meta_key );

			// Check if the callback is for our class object
			} elseif( ! is_array( $callback ) && method_exists( $this, $callback ) ){

				return $this->{$callback}( $args, $content, $meta_key );

			// Last resort, check if it's just a standard function to call
			} elseif( ! is_array( $callback ) && function_exists( $callback ) ){

				return call_user_func( $callback, $args, $content, $meta_key );
			}

		}

		/**
		 * Check if this is a template meta value, and should be pulled from the template meta
		 *
		 * Example would be days before expiration, etc.
		 */
		if ( isset( $shortcodes[ $meta_key ], $shortcodes[ $meta_key ][ 'templatemeta' ] ) ){
			// See if meta_key is defined, otherwise use the shortcode as the metakey
			$meta_to_check = isset( $shortcodes[ $meta_key ]['meta_key'] ) && ! empty( $shortcodes[ $meta_key ][ 'meta_key' ] ) ? $shortcodes[ $meta_key ][ 'meta_key' ] : $meta_key;
			$default_val = isset( $shortcodes[ $meta_key ]['default'] ) ? $shortcodes[ $meta_key ][ 'default' ] : '';
			$template_val = $this->hooks()->email_template( $meta_to_check );

			if( empty( $template_val ) ) {
				$template_val = $default_val;
			}

			// No need to further process as this is template meta
			return $template_val;
		}

		$job_id    = $this->get_job_id();
		$resume_id = $this->get_resume_id();
		$app_id    = $this->get_app_id();

		/**
		 * Check field taxonomy first
		 *
		 * Sometimes meta will still exist under a listing for a taxonomy field, so we need to check if it's a taxonomy first,
		 * before checking from meta, to make sure the correct values are returned.
		 */
		if( $taxonomy_slug = $this->job()->get_taxonomy_slug( $meta_key ) ){
			$tax_post_id = $job_id;
		} elseif( $this->resume() && $taxonomy_slug = $this->resume()->get_taxonomy_slug( $meta_key ) ){
			$tax_post_id = $resume_id;
		}

		/**
		 * Process and return as Taxonomy value
		 */
		if( ! empty($taxonomy_slug) && ! empty($tax_post_id) ){

			$field_terms = get_the_terms( $tax_post_id, $taxonomy_slug );
			if( ! $field_terms || is_wp_error( $field_terms ) ) {
				return '';
			}

			$tax_values = array();
			foreach( (array) $field_terms as $field_term ) {
				$tax_values[] = $field_term->name;
			}

			// Filter the separator (default is CSV set in args already)
			$separator = apply_filters( 'job_manager_emails_shortcodes_handler_taxonomy_separator', $args['separator'], $taxonomy_slug, $args, $content, $meta_key );
			$value     = $each ? $tax_values : implode( $separator, $tax_values );

		/**
		 * Process and return as standard Post Meta
		 */
		} else {

			/**
			 * If shortcode config has 'meta_key' defined, check that for value, otherwise check known meta key with prepended underscore
			 *
			 * The $shortcodes array should ONLY contain shortcode configuration, and should not be merged with field config.  If so, this would cause an error
			 * as field configuration has meta key stored in `meta_key` key but without prepended underscore.
			 */
			$chk_meta_key = $this->check_meta_key( $meta_key, $shortcodes );

			if( in_array( $chk_meta_key, array( 'post_title', 'post_content' ) ) ){
				$post = get_post( $this->get_the_id() );
				if( $post ) {
					$value = $post->$meta_key;
				}
			}

			// Attempt to pull from current post in loop's meta
			if( ! $value ) {
				$value = maybe_unserialize( get_post_meta( get_the_ID(), $chk_meta_key, TRUE ) );
			}

			// If that didn't work, attempt to get value from application first
			if( ! $value && $app_id ) {
				$value = maybe_unserialize( get_post_meta( $app_id, $chk_meta_key, TRUE ) );
			}

			// If that didn't work, attempt to get value from resume first
			if( ! $value && $resume_id ) {
				$value = maybe_unserialize( get_post_meta( $resume_id, $chk_meta_key, TRUE ) );
			}

			// And then job as last resort
			if( ! $value  && $job_id ) {
				$value = maybe_unserialize( get_post_meta( $job_id, $chk_meta_key, TRUE ) );
			}

		}

		// Process array values if value isn't for each loop
		$value = $each || ! is_array( $value ) ? $value : $this->process_array_values( $value );

		return $value;
	}

	/**
	 * Return meta key to check post meta with
	 *
	 * Looks in shortcode configuration for specific meta key defined (for pulling post meta), if that's not found, returns
	 * the passed meta key, with a prepended underscore, as the standard for WP Job Manager is to save all meta that way.
	 *
	 * @since 2.0.2
	 *
	 * @param $meta_key
	 * @param $shortcodes
	 *
	 * @return string
	 */
	function check_meta_key( $meta_key, $shortcodes ){

		$chk_meta_key = isset( $shortcodes[$meta_key], $shortcodes[$meta_key]['meta_key'] ) ? $shortcodes[$meta_key]['meta_key'] : "_{$meta_key}";

		return $chk_meta_key;
	}

	/**
	 * Process Array and Multidimensional Array into Human Readable
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $values
	 *
	 * @return string
	 */
	function process_array_values( $values ){

		if( ! is_array( $values ) || empty( $values ) ) return $values;

		/*
		 * Check if array is multi-dimensional
		 * array( array( 'something' ) )
		 *
		 */
		$values = WP_Job_Manager_Emails_Utils::is_multi_array( $values ) ? array_map( array( $this, 'process_array_values' ), $values ) : $values;

		$processed_value = "\n";

		/*
		 * Loop through array and values.  Should only be a single
		 * array at this point, with either ( 0 => value ) or ( 'key' => 'value' )
		 */
		foreach( $values as $label => $value ){

			// Filter label and set to $label if string (array key is string), otherwise set empty string
			$label = apply_filters( "job_manager_emails_shortcode_array_values_label_{$this->meta_key}", (! is_numeric( $label ) && ! empty($label) ? $label : ''), $value, $this );

			// Filter colon and set to ": " if string (array key is string), otherwise set empty string
			$colon = apply_filters( "job_manager_emails_shortcode_array_values_colon_{$this->meta_key}", (! is_numeric( $label ) && ! empty($label) ? ': ' : ''), $label, $value, $this );

			$value = apply_filters( "job_manager_emails_shortcode_array_values_value_{$this->meta_key}", $value, $label, $this );

			$processed_value .= "{$label}{$colon}{$value}\n";

		}

		return $processed_value;
	}

	/**
	 * Default Shortcode Handler
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param        $args
	 * @param string $content
	 * @param string $tag
	 *
	 * @return string
	 */
	function handler( $args = array(), $content = '', $tag = '', $skip_value = FALSE ) {

		$values = array('value' => '', 'top_divider' => '', 'bottom_divider' => '', 'before' => '', 'after' => '', 'content' => $content, 'label' => '' );
		$job_id = $this->get_job_id();
		$resume_id = $this->get_resume_id();

		// Trim quote entities to prevent issues in messy editors.
		$args = WP_Job_Manager_Emails_Utils::trim_qts_deep( $args );

		$atts = shortcode_atts(
			array(
				'before'    => '',
				'after'     => '',
				'skip_keys' => '',
				'divider'   => '-',
				'repeat'    => 12,
				'order'     => 'top_divider,before,content,label,value,after,bottom_divider',
				'separator' => ', '
			), $args, $tag );

		$args = array_merge( $atts, (array) $args );

		// Get value for shortcode, and apply filter, uses $this->handler_get_value
		if( ! $skip_value ) $values['value'] = apply_filters( 'job_manager_emails_shortcode_handler_value', $this->handler_get_value( $args, $values['content'], $tag ), $args, $values['content'], $tag, $job_id, $resume_id );

		$returned_value = $values['value'];

		// Return empty string if there is no value
		if( ! $skip_value && empty( $values['value'] ) ) return '';

		// Handle setting divider if specified in shortcode
		if( in_array( 'divider', $args ) ){

			$divider = $this->get_divider( $args['divider'], $args['repeat'] );

			// If divider specified, but without top/bottom, set both
			if( ! in_array( 'top', $args ) && ! in_array( 'bottom', $args ) ){
				$values['top_divider'] = $divider;
				$values['bottom_divider'] = $divider;
			}

			// If specific (top/bottom) specified, set that value to the divider
			if( in_array( 'top', $args ) ) $values['top_divider'] = $divider;
			if( in_array( 'bottom', $args ) ) $values['bottom_divider'] = $divider;

		}

		// Set before and after values
		$values['before'] = apply_filters( 'job_manager_emails_shortcode_handler_before', $args['before'], $args, $values, $tag, $job_id, $resume_id );
		$values['after'] = apply_filters( 'job_manager_emails_shortcode_handler_post', $args['after'], $args, $values, $tag, $job_id, $resume_id );

		// Set content value and replace any shortcodes nested inside [shortcode][/shortcode]
		// No need to run through $this->replace if there is no value
		if( ! empty( $values['content'] ) ) $values['content'] = $this->replace( $values[ 'content' ] );
		$values['content'] = apply_filters( 'job_manager_emails_shortcode_handler_content', $values['content'], $args, $values, $tag, $job_id, $resume_id );

		// Set label value
		if( in_array( 'label', $args )  && ! in_array( $tag, apply_filters( 'job_manager_emails_shortcode_handler_no_label', array('resume_fields', 'job_fields'), $args, $values, $tag, $job_id, $resume_id ) ) ){
			$fields = $this->get_all();
			if( is_array( $fields ) && isset($fields[ $tag ], $fields[ $tag ]['label']) ) {
				// General filter
				$colon = apply_filters( 'job_manager_emails_shortcode_handler_label_colon', ':', $args, $values, $tag, $job_id, $resume_id );
				// Tag specific filter
				$colon = apply_filters( "job_manager_emails_shortcode_handler_label_colon_{$tag}", ':', $args, $values, $tag, $job_id, $resume_id );
				$values['label'] = $fields[ $tag ]['label'] . "{$colon} ";
			}
		}

		// Format HTML/Plain text in values, and pass through filter
		$values = apply_filters( 'job_manager_emails_shortcode_handler_values', $this->handler_html_format( $values ), $args, $tag, $job_id, $resume_id );

		// Form output value based on order
		$output = '';
		$output_order_key = array_map( 'trim', explode( ',', $args[ 'order'] ) );

		// Loop through key order array building output string
		foreach( $output_order_key as $values_key ) {

			if( ! isset( $values[ $values_key ] ) ) continue;

			// If for some reason the value is an array, implode it so the email doesn't show "Array", otherwise just set variable to the value
			$the_value = ! is_array( $values[$values_key] ) ? $values[$values_key] : implode( apply_filters( 'job_manager_emails_shortcode_handler_array_separator', $args['separator'], $args, $content, $tag ) );
			$output .= wp_kses_post( $the_value );

		}

		return $output;
	}

	/**
	 * Format Handler Values for HTML or Plain Text Email
	 *
	 * This method will check if email is HTML email, and replace all Windows (\r\n),
	 * Mac (\r), and Linux (\n) linebreaks with HTML <br />.  If email is plain text
	 * will pass each value through wp_strip_all_tags()
	 *
	 * @since 1.0.0
	 *
	 * @param $values
	 *
	 * @return array
	 */
	function handler_html_format( $values ){

		// HTML or Plain Text handling/formatting
		$is_html = $this->cpt()->hooks()->is_html_email();

		foreach( (array) $values as $value_key => $actual_value ) {
			// Decode HTML entities that may be mangled by visual editor
			$decoded_value = html_entity_decode( $actual_value );

			// If HTML email, replace all Windows (\r\n), Mac (\r), and Linux (\n) linebreaks with HTML <br />
			// otherwise if plain text, strip all HTML tags
			$values[ $value_key ] = $is_html ? str_replace( array("\r\n", "\r", "\n"), "<br />", $decoded_value ) : wp_strip_all_tags( $decoded_value );
		}

		return $values;
	}

	/**
	 * [divider]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	function divider_handler( $args = array(), $content = '' ) {

		$values = array('top_divider' => '', 'bottom_divider' => '', 'content' => $content);

		// Trim quote entities to prevent issues in messy editors.
		$args = WP_Job_Manager_Emails_Utils::trim_qts_deep( $args );

		$atts = shortcode_atts(
			array(
				'use' => '-',
				'repeat'  => 12
			), $args, 'divider' );

		$args = array_merge( $atts, (array) $args );

		// If no content, means only single [divider] and not [divider][/divider]
		// so no need to worry about top or bottom
		$divider = $this->get_divider( $args['use'], $args['repeat'] );
		if( empty($content) ) return $divider;

		// If divider specified, but without top/bottom, set both
		if( ! in_array( 'top', $args ) && ! in_array( 'bottom', $args ) ) {
			$values['top_divider'] = $values['bottom_divider'] = $divider;
		}

		// If specific (top/bottom) specified, set that value to the divider
		if( in_array( 'top', $args ) ) $values['top_divider'] = $divider;
		if( in_array( 'bottom', $args ) ) $values['bottom_divider'] = $divider;

		// Replace any shortcodes inside if/else block
		$values['content'] = apply_filters( 'job_manager_emails_shortcode_divider_content', $this->replace( $values['content'] ), $args, $values );
		// Format all values for HTML or Plain Text email type
		$values            = apply_filters( 'job_manager_shortcode_divider_values', $this->handler_html_format( $values ), $args );

		$output = $values['top_divider'] . $values['content'] . $values['bottom_divider'];

		return $output;
	}

	/**
	 * [date]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	function date_handler( $args = array(), $content = '' ) {

		$atts = shortcode_atts( array(
				'format' => get_option('date_format'),
			), $args, 'date' );

		$output = apply_filters( 'job_manager_emails_date_handler_output', date( $atts['format'] ), $this->get_the_id(), $this->template, $this );

		return $output;
	}

	/**
	 * [time]
	 *
	 *
	 * @since @@since
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	function time_handler( $args = array(), $content = '' ) {

		$atts = shortcode_atts( array(
				'format' => get_option('time_format'),
			), $args, 'time' );

		$output = apply_filters( 'job_manager_emails_time_handler_output', date( $atts['format'] ), $atts, $this->get_the_id(), $this->template, $this );

		return $output;
	}

	/**
	 * Get All Shortcodes Merged with Job Manager Fields
	 *
	 *
	 * @since    1.0.0
	 *
	 * @param bool $force_update
	 *
	 * @return array
	 */
	function get_all( $force_update = false ){

		$fields = $this->get_fields();

		$shortcodes = $this->get_shortcodes( $force_update );
		return array_merge( $fields, $shortcodes );
	}

	/**
	 * Return Conditional Shortcodes
	 *
	 * Will return array of only conditional shortcodes.  This is used for using shortcodes in things like [if][else][/if]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_update
	 *
	 * @return array
	 */
	function get_conditionals( $force_update = false ){

		if( $force_update || empty($this->conditionals) ) {
			$this->init_conditionals();
			$default_conditionals = apply_filters( 'job_manager_emails_default_conditionals_shortcodes',
				array(
					'if' => array(
						'label'       =>  __( 'If/Else Statement', 'wp-job-manager-emails' ),
						'description' => __( 'The IF conditional shortcode can be used to form your own if/else shortcode blocks.  See help tab in top right corner for more details.', 'wp-job-manager-emails' ),
						'handler' => 'if_handler',
						'title'   => '[if][else][/if]',
						'usage' => '[if candidate_phone]<br>Candidate Phone Number: [candidate_phone]<br>[else]<br>Candidate phone not provided<br>[/if]',
 						'args' => array(
							'field' => array(
								'desc' => __( 'The meta key to use when the if/else statement is processed.', 'wp-job-manager-emails' ),
								'value' => __( 'Any meta key', 'wp-job-manager-emails' ),
								'required' => TRUE
							)

						)
					),
					'each' => array(
						'label'       =>  __( 'For Each Loop', 'wp-job-manager-emails' ),
						'description' => __( 'The EACH conditional shortcode can be used to form your own each loop to customize array fields output.  See help tab in top right corner for more details.', 'wp-job-manager-emails' ),
						'handler' => 'each_handler',
						'title'   => '[each][value][/each]',
						'usage' => '[each job_download]<br>Download: [value]<br>[/each]',
 						'args' => array(
							'field' => array(
								'desc' => __( 'The meta key to use when the each loop is processed.', 'wp-job-manager-emails' ),
								'value' => __( 'Any meta key', 'wp-job-manager-emails' ),
								'required' => TRUE
							)
						)
					)
				)
			);

			$this->conditionals = wp_parse_args( $this->conditionals, $default_conditionals );
		}

		return apply_filters( 'job_manager_emails_shortcodes_conditionals', $this->conditionals );
	}

	/**
	 * Return Default Shortcodes
	 *
	 * Will return array of only default core shortcodes
	 *
	 *
	 * @since @@since
	 *
	 * @param bool $force_update
	 *
	 * @return array
	 */
	function get_default( $force_update = false ){

		if( $force_update || empty($this->default) ) {
			$this->init_default();
			$default_default = apply_filters( 'job_manager_emails_default_core_shortcodes',
				array(
					'divider' => array(
						'label'       => __( 'Divider', 'wp-job-manager-emails' ),
						'description' => __( 'Will output a divider to separate content sections', 'wp-job-manager-emails' ),
						'handler'     => 'divider_handler',
						'nonmeta'     => TRUE
					),
					'admin_email' => array(
						'label'       => __( 'Admin Email', 'wp-job-manager-emails' ),
						'description' => __( 'Administrator email (admin_email option value)', 'wp-job-manager-emails' ),
						'callback'    => 'admin_email',
						'nonmeta'     => TRUE
					),
//					'user_emails' => array(
//						'label'       => __( 'User Emails' ),
//						'description' => __( 'User emails based on passed attributes' ),
//						'callback'    => 'user_emails',
//						'nonmeta'     => TRUE,
//						'args' => array(
//							'roles' => array(
//								'desc' => __( 'CSV of user roles to build emails from' ),
//								'required' => true,
//								'example' => 'employers,administrators'
//							),
//							'exclude' => array(
//								'desc' => __( 'CSV of user IDs to exclude'),
//								'required' => false,
//								'example' => '1,2,3'
//							)
//						)
//					),
					'date' => array(
						'label'       => __( 'Date', 'wp-job-manager-emails' ),
						'description' => __( 'Will output the date the email is sent', 'wp-job-manager-emails' ),
						'callback'    => 'date_handler',
						'nonmeta'     => TRUE,
						'args' => array(
							'format' => array(
								'desc'     => __( 'PHP date format characters', 'wp-job-manager-emails' ),
								'required' => FALSE,
								'default' => __( 'WordPress Date Format (in Settings)', 'wp-job-manager-emails' ),
								'example'  => 'F j, Y'
							),
						)
					),
					'time' => array(
						'label'       => __( 'Time', 'wp-job-manager-emails' ),
						'description' => __( 'Will output the time the email is sent', 'wp-job-manager-emails' ),
						'callback'    => 'time_handler',
						'nonmeta'     => TRUE,
						'args' => array(
							'format' => array(
								'desc'     => __( 'PHP time format characters', 'wp-job-manager-emails' ),
								'required' => FALSE,
								'default'  => __( 'WordPress Time Format (in Settings)', 'wp-job-manager-emails' ),
								'example'  => 'g:i a'
							),
						)
					),
				)
			);

			$this->default = wp_parse_args( $this->default, $default_default );
		}

		return apply_filters( 'job_manager_emails_default_shortcodes', $this->default );
	}

	/**
	 * Return Action/Hook Specific Shortcodes
	 *
	 * Will return array of only action/hook specific shortcodes
	 *
	 *
	 * @since @@since
	 *
	 * @param bool $force_update
	 *
	 * @return array
	 */
	function get_actions( $force_update = false ){
		return $this->cpt()->hooks()->hook_shortcodes( $force_update );
	}

	/**
	 * Get Extending Class and Default Shortcodes
	 *
	 * This method returns extending class configured [$this->init_shortcodes()], action specific, and default (standard) shortcodes.
	 * It does not return shortcodes that include all fields @see get_all()
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_update    Set to TRUE to force update the shortcodes (default: false)
	 *
	 * @return array
	 */
	function get_shortcodes( $force_update = false ){

		if( $force_update ||  empty( $this->shortcodes ) ) {
			$this->init_shortcodes();

			$default_shortcodes = $this->get_default( $force_update );
			$action_shortcodes = $this->get_actions( $force_update );

			$this->shortcodes = array_merge( (array) $this->shortcodes, (array) $default_shortcodes,(array) $action_shortcodes );
		}

		return apply_filters( 'job_manager_emails_shortcodes', $this->shortcodes );
	}

	/**
	 * [each][value][/each] Handler
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function each_handler( $args = array(), $content = '', $tag = 'each' ) {
		// No content (content to use in loop), arguments (meta key), or [value] to replace in content, return empty string
		if( empty($content) || empty( $args ) || strpos( $content, "[value]" ) === FALSE ) return '';

		// Trim quote entities to prevent issues in messy editors.
		$args = WP_Job_Manager_Emails_Utils::trim_qts_deep( $args );

		// Meta key should be first argument in array
		$meta_key = $args[0];

		// Set defaults and merge with passed arguments
		$atts = shortcode_atts(
			array(
				'before'    => '',
				'after'     => '',
				'skip_keys' => '',
				'divider'   => '-',
				'repeat'    => 12,
				'order'     => 'top_divider,before,content,label,value,after,bottom_divider',
				'separator' => ', '
			), $args, $tag );

		$args = array_merge( $atts, (array) $args );

		// Try to get value for meta key
		$values = $this->handler_get_value( $args, $content, $meta_key, TRUE );
		if( empty( $values ) ) return '';

		// If a string returned instead of array for some reason, set it in an array
		$values = is_array( $values ) ? $values : array( $values );

		$output = '';

		foreach( $values as $value ){
			$output .= str_replace( '[value]', $value, $content );
		}

		// Process through default handler to support additional arguments, and nested shortcodes
		$completed_output = $this->handler( $args, $output, $meta_key, TRUE );

		return $completed_output;
	}

	/**
	 * [if][else][/if] Handler
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return null|string
	 */
	function if_handler( array $args = array(), $content = '' ){

		if( empty( $content ) ) return $content;
		if( empty( $args ) ) return '';
		
		// Trim quote entities to prevent issues in messy editors.
		$args = WP_Job_Manager_Emails_Utils::trim_qts_deep( $args );

		// TODO: support false if statements
		// Check for exclamation as first arg, if so that means user probably
		// put a space between first argument, [if ! some_argument] instead of [if !some_argument]
		//if( $args[0] === '!' ) {
		//	$exclamation = array_shift( $args );
		//	$args[0] = $exclamation . $args[0];
		//}

		// TODO: add support for functions in if statements
		$safe_functions = apply_filters( 'job_manager_emails_if_shortcode_safe_functions', array() );

		$field = $args[0];

		$content_if = $content_else = '';

		if( strpos( $content, '[else]' ) !== FALSE ) {
			list($content_if, $content_else) = explode( '[else]', $content, 2 );
		} else {
			$content_if = $content;
		}

		// Start check for field value
		$if_else_ids = apply_filters( 'job_manager_emails_if_else_meta_check_ids', array( $this->get_the_id(), $this->get_resume_id(), $this->get_job_id() ) );

		foreach( $if_else_ids as $post_id ){
			// TODO: support standard meta in if statements
			$value = get_post_meta( $post_id, "_{$field}", TRUE );
			$if_else = empty( $value ) ? false : true;
			if( ! empty( $if_else ) ) break;
		}
		// End check for field value

		$content_to_output = empty($if_else) ? $content_else : $content_if;

		// Replace any shortcodes inside if/else block
		$content_to_output = $this->replace( $content_to_output );

		return $content_to_output;
	}

	/**
	 * Add Shortcodes to WordPress
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function add_shortcodes(){

		// Add fields and additional shortcodes
		$shortcodes = $this->get_all( true );
		if( ! empty( $shortcodes ) ){
			foreach( $shortcodes as $shortcode => $config ) {
				// Check for custom handler
				$handler = isset( $config[ 'handler' ] ) ? $config[ 'handler' ] : 'handler';
				add_shortcode( $shortcode, array($this, $handler) );

			}
		}

		// Add conditionals shortcodes (if, etc)
		$conditionals = $this->get_conditionals( true );
		if( ! empty( $conditionals ) ){
			foreach( $conditionals as $shortcode => $config ) {
				// Check for custom handler
				$handler = isset( $config[ 'handler' ] ) ? $config[ 'handler' ] : 'handler';
				add_shortcode( $shortcode, array($this, $handler) );

			}
		}
	}

	/**
	 * Single Shortcode Replacement
	 *
	 * This method is for filters or other times where you need to replace a shortcode
	 * in a string with the value, without having to setup the shortcodes, then reset
	 * them, etc.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param      $template_val
	 * @param      $job_id
	 * @param      $resume_id
	 * @param      $app_id
	 *
	 * @return string
	 */
	function single( $template_val, $job_id, $resume_id, $app_id = false ) {

		$this->clear_ids();

		// Start Shortcode Replacement
		$this->start( false );

		$this->set_job_id( $job_id );
		$this->set_resume_id( $resume_id );

		if ( $app_id ) {
			$this->job->shortcodes()->set_app_id( $app_id );
		}

		$this->job()->shortcodes()->add_shortcodes();

		if ( $this->application() ) {
			$this->application()->shortcodes()->add_shortcodes();
		}

		if ( $this->resume() ) {
			$this->resume()->shortcodes()->set_listing_id( $resume_id );
			$this->resume()->shortcodes()->set_resume_id( $resume_id );
			$this->resume()->shortcodes()->add_shortcodes();
		}

		$value = $this->replace( $template_val );

		// Adds <p> in subject and others
		//if( $this->cpt()->hooks()->is_html_email() ) $value = wpautop( $value );

		// Reset Shortcodes
		$this->stop();

		return $value;
	}

	/**
	 * Replace Shortcodes in Content with Values
	 *
	 * Because shortcodes are used as template variables for content, we need to first set a var equal to
	 * the existing shortcodes, then remove all shortcodes, add our custom shortcodes, execute shortcode
	 * replace, and then restore shortcodes back.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param      $content
	 *
	 * @param bool $remove_and_add  Whether to remove existing shortcodes and then add (default false)
	 *
	 * @return string
	 */
	function replace( $content, $remove_and_add = false ){

		if( $remove_and_add ) $this->start();
		$content = do_shortcode( $content );
		if( $remove_and_add ) $this->stop();

		return $content;
	}

	/**
	 * Loop through all fields and output with label
	 *
	 * This method is specifically for shortcodes like [resume_fields] and [job_fields] to
	 * output all of the fields.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return bool|string
	 */
	function output_all_fields( array $args = array(), $content = '', $tag = '' ) {

		$fields = $this->get_fields();
		if( empty($fields) ) return FALSE;

		$skip_in_all_output = apply_filters( 'job_manager_emails_skip_output_in_all_fields', array( 'candidate_education', 'candidate_experience', 'links' ), $args, $content, $tag );
		// Add any meta keys to skip that were passed in shortcode argument `skip_keys`
		if( isset( $args['skip_keys'] ) && ! empty( $args['skip_keys'] ) ) {
			$skip_keys = array_map( 'trim', explode( ",", $args['skip_keys']) );
			$skip_in_all_output = array_merge( $skip_in_all_output, $skip_keys );
		}

		$output  = '';
		$post_id = $this->get_the_id();
		if( empty( $post_id ) ) return '';

		foreach( $fields as $meta_key => $config ) {

			if( in_array( $meta_key, $skip_in_all_output ) ) continue;

			/*
			 * Pass value to method to handle arrays, strings, formatting, etc.
			 */
			$value = $this->handler_get_value( $args, $content, $meta_key );

			if( ! empty( $value ) ){

				$output .= __( $config['label'], 'wp-job-manager-emails' ) . ': ';

				if( is_array( $value ) ){

					$output .= "\n\n";

					foreach( (array) $value as $index => $multiple_value ) {
						// If array key is non-numeric, use as a label before the value
						$index_label = ! is_numeric( $index ) ? "{$index}: " : '';
						$output .= $index_label . $multiple_value . "\n\n";
					}

				} else {

					$output .= $value . "\n\n";

				}
			}
		}

		return $output;
	}

	/**
	 * Get Field Configuration Array
	 *
	 *
	 * @since 2.0.2
	 *
	 * @return array    field configuration data, without any shortcode arguments or setings.
	 */
	function get_fields(){
		return $this->cpt()->get_fields();
	}

	/**
	 * Get Job ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function get_job_id(){

		if( isset($_REQUEST['job_id'] ) && ! empty( $_REQUEST['job_id'] ) ){

			if( empty( $this->job_id ) ){
				$this->job_id = absint( $_REQUEST['job_id'] );
			} else {
				if( $this->job_id !== $_REQUEST['job_id'] ) {
					return absint( $_REQUEST['job_id'] );
				}
			}

		}

		if( empty( $this->job_id ) ){
			$this->job_id = wp_job_manager_emails()->tmp_job_id;
		}

		return $this->job_id;
	}

	/**
	 * Clear out temporary IDs stored in core singleton object
	 *
	 *
	 * @since 2.0.3
	 *
	 */
	function clear_ids(){

		$core = wp_job_manager_emails();
		$core->tmp_job_id = null;
		$core->tmp_app_id = null;
		$core->tmp_resume_id = null;
		$core->tmp_alert_id = null;
	}

	/**
	 * Get Resume ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function get_resume_id(){

		if( isset( $_REQUEST['resume_id'] ) && ! empty( $_REQUEST['resume_id'] ) ){

			if( empty( $this->resume_id ) ){

				$this->resume_id = absint( $_REQUEST['resume_id'] );

			} else {

				if( $this->resume_id !== $_REQUEST['resume_id'] ) {
					return absint( $_REQUEST['resume_id'] );
				}

			}

		}

		if( empty( $this->resume_id ) ){
			$this->resume_id = wp_job_manager_emails()->tmp_resume_id;
		}

		return $this->resume_id;
	}

	/**
	 * Get Listing ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function get_listing_id(){
		return $this->listing_id;
	}

	/**
	 * Get Application ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	function get_app_id(){

		if( empty( $this->app_id ) ){
			$this->app_id = wp_job_manager_emails()->tmp_app_id;
		}

		return $this->app_id;
	}

	/**
	 * Get Alert ID
	 *
	 *
	 * @since @@since
	 *
	 * @return int
	 */
	function get_alert_id(){

		if( empty( $this->alert_id ) ){
			$this->alert_id = wp_job_manager_emails()->tmp_alert_id;
		}

		return $this->alert_id;
	}

	/**
	 * Set Application ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @param $app_id
	 */
	function set_app_id( $app_id ){
		wp_job_manager_emails()->tmp_app_id = $app_id;
		$this->app_id = $app_id;
	}

	/**
	 * Set Resume ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $resume_id
	 */
	function set_resume_id( $resume_id ){
		wp_job_manager_emails()->tmp_resume_id = $resume_id;
		$this->resume_id = $resume_id;
	}

	/**
	 * Set Listing ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $listing_id
	 */
	function set_listing_id( $listing_id ){
		$this->listing_id = $listing_id;
	}

	/**
	 * Return ID
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	function get_the_id(){
		if( $this->listing_id ){
			return $this->listing_id;
		} else {
			$slug = $this->cpt()->get_slug();
			if( $slug === 'application' ) $slug = 'app';
			$slug_method = "get_{$slug}_id";
			if( method_exists( $this, $slug_method ) ){
				return $this->$slug_method;
			}
		}

		return 0;
	}

	/**
	 * Set Template
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $template
	 */
	function set_template( $template ){
		$this->template = $template;
	}

	/**
	 * Set Job ID
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $job_id
	 */
	function set_job_id( $job_id ){
		wp_job_manager_emails()->tmp_job_id = $job_id;
		$this->job_id = $job_id;
	}

	/**
	 * Set Alert ID
	 *
	 *
	 * @since 2.0.5
	 *
	 * @param $alert_id
	 */
	function set_alert_id( $alert_id ){
		wp_job_manager_emails()->tmp_alert_id = $alert_id;
		$this->alert_id                       = $alert_id;
	}

	/**
	 * Start Shortcode Replacement
	 *
	 * This method will temporarily store $GLOBALS['shortcode_tags'] in a variable,
	 * remove all shortcodes, and then add our shortcodes.
	 *
	 * You MUST call CLASS->stop() to reset original shortcodes!
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param bool $add_shortcodes
	 */
	function start( $add_shortcodes = TRUE ) {
		$this->shortcode_tags = $GLOBALS['shortcode_tags'];
		$this->processing = TRUE;

		remove_all_shortcodes();

		if( $add_shortcodes ) {

			/**
			 * Alert & Claim shortcodes handled through Job Shortcodes
			 *
			 * Claims are also handled through job shortcodes, but the job ID should be set already for claim listing,
			 * as a single listing is associated with a claim, whereas alert is not associated with a single job listing.
			 */
			if( $this->get_job_id() || $this->get_alert_id() ){
				$this->job()->shortcodes()->add_shortcodes();
			}

			if( $this->get_resume_id() ){
				$this->resume()->shortcodes()->add_shortcodes();
			}

			if( $this->get_app_id() ){
				$this->application()->shortcodes()->add_shortcodes();
			}

		}
	}

	/**
	 * Reset Shortcode Tags
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function stop() {
		$GLOBALS['shortcode_tags'] = $this->shortcode_tags;
		$this->processing = FALSE;
	}

	/**
	 * [admin_email]
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	function admin_email( array $args = array(), $content = '' ) {
		return get_option('admin_email');
	}

	/**
	 * Returns Filtered Divider
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param string $divider
	 * @param int    $repeat
	 *
	 * @return mixed|void
	 */
	function get_divider( $divider = '-', $repeat = 12){
		return apply_filters( 'job_manager_emails_shortcode_divider', "\n" . "\n" . str_repeat( $divider, $repeat ) . "\n" . "\n" );
	}

	/**
	 * WP_Job_Manager_Emails_CPT
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_CPT
	 */
	function cpt() {
		return $this->cpt;
	}

	/**
	 * WP_Job_Manager_Emails_Hooks
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Hooks
	 */
	function hooks() {

		return $this->cpt()->hooks();
	}

	/**
	 * WP_Job_Manager_Emails_Job
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Job
	 */
	function job() {

		return $this->cpt()->integration()->job();
	}

	/**
	 * WP_Job_Manager_Emails_Resume
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Resume
	 */
	function resume() {

		return $this->cpt()->integration()->resume();
	}

	/**
	 * WP_Job_Manager_Emails_Application
	 *
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Job_Manager_Emails_Application
	 */
	function application() {

		return $this->cpt()->integration()->application();
	}

	/**
	 * Initialize Conditionals Placeholder
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function init_conditionals() { }

	/**
	 * Initialize Default Shortcodes Placeholder
	 *
	 *
	 * @since @@since
	 *
	 */
	function init_default() { }

	/**
	 * Retrieves the edit post link for post.
	 *
	 * This is a duplicate of the core WordPress function, with capability
	 * check removed so we can return the value even when non-admin or guest
	 * causes an email to be sent.
	 *
	 * @since @@since
	 *
	 * @param int    $id      Optional. Post ID. Default is the ID of the global `$post`.
	 * @param string $context Optional. How to output the '&' character. Default '&amp;'.
	 *
	 * @return string|null The edit post link for the given post. null if the post type is invalid or does
	 *                     not allow an editing UI.
	 */
	function get_edit_post_link( $id = 0, $context = 'display' ) {

		if ( ! $post = get_post( $id ) ) {
			return '';
		}

		$action = '&action=edit';

		if ( 'revision' === $post->post_type ) {
			$action = '';
		} elseif ( 'display' == $context ) {
			$action = '&amp;action=edit';
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) return '';

		if ( $post_type_object->_edit_link ) {
			$link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
		} else {
			$link = '';
		}

		/**
		 * Filters the post edit link.
		 *
		 * @since 2.3.0
		 *
		 * @param string $link    The edit link.
		 * @param int    $post_id Post ID.
		 * @param string $context The link context. If set to 'display' then ampersands
		 *                        are encoded.
		 */
		return apply_filters( 'get_edit_post_link', $link, $post->ID, $context );
	}

	/**
	 * Visibility Plugin Integration
	 *
	 * This filter is called by the visibility plugin when meta or other data is pulled,
	 * before filtering based on visibility configuration.  If we're processing an email
	 * we return TRUE to bypass visibility configuration and allow all meta values to
	 * be pulled so they can output in the email
	 *
	 * @since 2.0.1
	 *
	 * @param $is_admin_or_author
	 *
	 * @return bool
	 */
	function visibility_bypass( $is_admin_or_author ){

		// We're processing an email, so return TRUE to prevent any filtering
		if( $this->processing ) return TRUE;

		return $is_admin_or_author;
	}
}