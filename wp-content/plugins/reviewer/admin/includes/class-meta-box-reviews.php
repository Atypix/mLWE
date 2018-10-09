<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Reviews_Meta_Box
{
	// Instance of this class
	protected static $instance = null;
	protected $review_fields;
	protected $review_types;
	//protected $template_fields;
	protected $preferences_option;
	protected $templates_option;
	protected $post_meta_key = 'rwp_reviews';
	protected $post_reviews;

	function __construct()
	{
		$this->plugin_slug 			= 'reviewer';
		$this->templates_option 	= RWP_Reviewer::get_option( 'rwp_templates' );
		$this->preferences_option 	= RWP_Reviewer::get_option( 'rwp_preferences' );
		$this->review_fields 		= RWP_Reviews_Meta_Box::get_review_fields();
		//$this->template_fields 		= RWP_Template_Manager_Page::get_template_fields();
		$this->set_review_types();
	}

	public function init()
	{
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box') );
		add_action( 'save_post', array( $this, 'save_meta_box') );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function localize_script()
	{
		$action_name = 'rwp_ajax_action_get_review_form';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'reviewsMetaBoxObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public static function ajax_callback()
	{
		$rev =  RWP_Reviews_Meta_Box::get_instance();

		//RWP_Reviewer::pretty_print( $_POST );

		$rev->get_review_form( $review = array( 'review_id' => $_POST['rwp_review_id'], 'review_type' => $_POST['rwp_review_type'] ) );
		die();
	}

	public function add_meta_box()
	{
		$this->review_fields = RWP_Reviews_Meta_Box::get_review_fields();
		$this->post_reviews = get_post_meta( get_the_ID(), $this->post_meta_key, true );

		$types = is_array( $this->preferences_option['preferences_post_types'] ) ? $this->preferences_option['preferences_post_types'] : array();
		foreach ( $types as $post_type) {

			add_meta_box( 'rwp-reviews-meta-box', 'Reviewer | ' . __( 'Reviews Boxes', $this->plugin_slug ), array( $this, 'render_meta_box'), $post_type );
		}
	}

	public function save_meta_box( $post_id )
	{
		// Check nonce
		if ( ! isset( $_POST['rwp_reviews_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['rwp_reviews_meta_box_nonce'];

		// Verufy nonce
		if ( ! wp_verify_nonce( $nonce, 'rwp_save_reviews_meta_box') )
			return $post_id;

		// Skip if it is autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check user permission
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// Check if there are reviews
		if( ! isset( $_POST[ $this->post_meta_key ] ) )
			$_POST[ $this->post_meta_key ] = array();

		// Check if is a valid array
		if( ! is_array( $_POST[ $this->post_meta_key ] ) )
			return $post_id;

		// Validate reviews
		$reviews = array();

		//RWP_Reviewer::pretty_print($_POST[ $this->post_meta_key ]); //flush(); die();

		foreach ( $_POST[ $this->post_meta_key ] as $review_id => $review) {

			// Get the fields for review type
			$review_type = ( isset( $review['review_type'] ) ) ? $review['review_type'] : 'PAR+UR';
			$review_type_fields = $this->review_type_fields[ $review_type ];

			foreach( $review_type_fields as $field_id ) {

				$field = $this->review_fields[ $field_id ];

				switch ( $field_id ) {
					case 'review_title':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$value = trim($review[ $field_id ]);
						$reviews[ $review_id ][ $field_id ] = ( empty( $value ) ) ? $field['default'] . ' ' . $review_id : wp_kses_post($value);
						break;

					case 'review_title_options':
					case 'review_criteria_source':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$options = array_keys( $field['options'] );

						$value = trim( $review[ $field_id ] );
						$reviews[ $review_id ][ $field_id ] = ( !in_array( $value, $options ) ) ? $field['default'] : $value;
						break;

					case 'review_disable_user_rating':
					case 'review_use_featured_image':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$reviews[ $review_id ][ $field_id ] = 'yes';
						break;

					case 'review_status':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$value = trim($review[ $field_id ]);
						$reviews[ $review_id ][ $field_id ] = ( empty( $value ) ) ? $field['default'] : wp_kses_post($value);

						break;

					case 'review_template':

						$default_v = array_values( $this->templates_option );
						$default = $default_v[0]['template_id'];


						if( ! isset( $review[ $field_id ] ) ){ //if field is not set
							$reviews[ $review_id ][ $field_id ] = $default;
							break;
						}

						$value = trim($review[ $field_id ]);
						$reviews[ $review_id ][ $field_id ] = ( empty( $value ) ) ? $default : esc_sql( esc_html ( $value ) );
						break;

					case 'review_custom_tabs':

						$template 		= $this->templates_option[ $reviews[ $review_id ]['review_template'] ];
						$template_id 	= $template['template_id'];

						if( ! isset( $review[ $field_id ][ $template_id ] ) ) { // Field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$template_tabs 		= $template['template_custom_tabs'];
						$reviews_tabs 		= $review[ $field_id ][ $template_id ];
						$reviews_tabs_opts	= $field['options'];
						$valid_tabs 		= array();

						//print_r( $template_tabs);

						foreach ($template_tabs as $key => $t_tab) {

							if( isset( $reviews_tabs[ $key ] ) && is_array( $reviews_tabs[ $key ] )  ) {

								foreach ($reviews_tabs_opts as $k => $opt) {

									if ( isset( $reviews_tabs[ $key ] ) ) {

										$value = trim( $reviews_tabs[ $key ][ $k ] );
										$valid_tabs[ $key ][ $k ] = wp_kses_post( $value );

									} else {

										$valid_tabs[ $key ][ $k ] = $opt['default'];
									}
								}

							} else { // Set tab default values

								foreach ($reviews_tabs_opts as $k => $o)
									$valid_tabs[ $key ][ $k ] = $o['default'];
							}
						}

						$reviews[ $review_id ][ $field_id ] = $valid_tabs;
						break;

					case 'review_scores':

						$default = array();
						$template = $this->templates_option[ $reviews[ $review_id ]['review_template'] ];
						$count = count( $template['template_criterias'] );
						$min = $template['template_minimum_score'];
						$max = $template['template_maximum_score'];

						for ($i=0; $i < $count; $i++)
							$default[] = $min;

						if( ! isset( $review[ $field_id ] ) || ! isset( $review[ $field_id ][ $reviews[ $review_id ]['review_template'] ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $default;
							break;
						}

						$scores = array();

						foreach ($template['template_criterias'] as $criteria_id => $criteria) {

							if( ! isset( $review[ $field_id ][ $template['template_id'] ][ $criteria_id ] ) ) {
								$scores[ $criteria_id ] = $min;
								continue;
							}

							$value = floatval( $review[ $field_id ][ $template['template_id'] ][ $criteria_id ] );
							$scores[ $criteria_id ] = ( $value <= 0 || ( $value < $min || $value > $max ) ) ? $min : $value;
						}

						$reviews[ $review_id ][ $field_id ] = $scores;
						break;

					case 'review_image_url':
					case 'review_custom_overall_score' :
					case 'review_sameas_attr':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$value = trim($review[ $field_id ]);
						$reviews[ $review_id ][ $field_id ] = ( empty( $value ) ) ? $field['default'] : esc_sql( esc_html ( $value ) );
						break;

					case 'review_pros':
					case 'review_cons':
					case 'review_summary':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = '';
							break;
						}

						$value = wp_kses_post( $review[ $field_id ] );
						$reviews[ $review_id ][ $field_id ] = $value;
						break;

					case 'review_image':
						$reviews[ $review_id ][ $field_id ] = esc_sql( $review['review_image'] );
						break;

					case 'review_custom_links':
						// Open custom links in new window or tab
						$field_idd = 'review_open_custom_links_out';
						$reviews[ $review_id ][ $field_idd ] = ( isset( $review[ $field_idd ] ) ) ? 'yes' : 'no';

						// Custom links;
						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						if( ! is_array( $review[ $field_id ] ) ) {
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$values = array();

						foreach ($review[ $field_id ] as $link) {

							if( isset( $link['label'] ) && isset( $link['url'] ) ) {
								$label = trim( $link['label'] );
								$url = trim( $link['url'] );
							} else {
								continue;
							}

							if( empty( $label ) && empty( $url ) )
								continue;

							$label = ( ! empty( $label ) ) 	? wp_kses_post( $link['label'] ) 	: 'Link';
							$url   = ( ! empty( $url ) ) 	? wp_kses_post( $link['url'] ) 		: '#';

							$values[] = array( 'label' => $label, 'url' => $url );
						}

						$reviews[ $review_id ][ $field_id ] = $values;
						break;

					case 'review_user_rating_options':

						if( ! isset( $review[ $field_id ] ) ) { //if field is not set
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						if( ! is_array( $review[ $field_id ] ) ) {
							$reviews[ $review_id ][ $field_id ] = $field['default'];
							break;
						}

						$values = array();

						foreach ($field['default'] as $value) {

							if( in_array( $value, $review[ $field_id ] ) )
								$values[] = $value;
						}

						$reviews[ $review_id ][ $field_id ] = $values;

						//RWP_Reviewer::pretty_print($field['default']); flush(); die();

						break;

					case 'review_id':
					default:
						$reviews[ $review_id ][ $field_id ] = $review_id;

						// Review Type
						$reviews[ $review_id ]['review_type'] = $review_type;
						break;
				}

			}
		}

		//RWP_Reviewer::pretty_print($reviews); flush(); die();

		update_post_meta( $post_id, $this->post_meta_key, $reviews );
	}

	public function render_meta_box()
	{
		// Add an nonce field
		wp_nonce_field( 'rwp_save_reviews_meta_box', 'rwp_reviews_meta_box_nonce' );

		// Check if there is at least one reviews template
		if( empty( $this->templates_option ) ) {

			echo '<p>' . __( 'It is necessary to create a Template before adding a new review.', $this->plugin_slug ) . '</p>';
			return;
		}
		?>

		<p class="description"><?php _e( 'In this meta box you can manage the reviews boxes for the post. You can find more informations about Reviewer Box or Users Box types inside documentation. Save/update the post to save reviews boxes.', $this->plugin_slug ); ?></p>

		<div class="rwp-metabox-elems">

			<select name="rwp-review-type">
				<?php foreach ($this->review_types as $key => $label): ?>
					<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php endforeach ?>
			</select>

			<a href="#" class="button button-primary" id="rwp-add-review-form-btn"><?php _e( 'Add new reviews box', $this->plugin_slug ); ?></a><img class="rwp-loader" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />
		</div>


		<ul class="rwp-tabs-wrap" data-placehoder="<?php _e( 'Review', $this->plugin_slug ); ?>">
		<?php
		if ($this->post_reviews != null && ! empty( $this->post_reviews ) ) {
			//$first_review_id = array_keys( $this->post_reviews )[0]; // First Review ID
			$pr = array_keys( $this->post_reviews );
			$first_review_id = $pr[0];

			foreach ( $this->post_reviews as $review_id => $review) {
				$hide = ( $review_id != $first_review_id ) ? '' : 'rwp-tabs';
				echo '<li class="'. $hide .'" id="rwp-review-tab-'. $review_id .'"><a href="#" data-review-id="'. $review_id .'">'. $review['review_title'] .'</a></li>';
			}
		}
		?>
		</ul>

		<div id="rwp-reviews-wrap">
		<?php
		if ($this->post_reviews != null && ! empty( $this->post_reviews ) )
			foreach ( $this->post_reviews as $review_id => $review) {
				$this->get_review_form( $review );
			}
		?>
		</div><!--/rwp-reviews-wrap-->

		<?php
		// RWP_Reviewer::pretty_print(  $this->post_reviews ); //flush();
	}

	public function get_review_form( $review = array( 'review_id' => 0, 'review_type' => 'PAR+UR' ) )
	{
		$review_id = $review['review_id']; // Get the review ID

		if($this->post_reviews != null && ! empty( $this->post_reviews ) ) {
			$pr = array_keys( $this->post_reviews );
			$first_review_id = $pr[0];
		} else {
			$first_review_id = -1;
		}

		//$first_review_id = ($this->post_reviews != null && ! empty( $this->post_reviews ) ) ? array_keys( $this->post_reviews )[0] : -1; // First Review ID
		$hide = ( $review_id != $first_review_id ) ? 'style="display:none;"' : '';
		$type_color = ( $review['review_type'] == 'PAR+UR' ) ? '--rwp-reviewer-box' : '--rwp-users-box';
		?>

		<div id="rwp-review-<?php echo $review_id ?>" class="rwp-review-form rwp-tabs-panel" data-review-id="<?php echo $review_id ?>" <?php echo $hide; ?>>

			<div class="rwp-reviews-box-type" >
				<span class="rwp-reviews-box-type__label <?php echo $type_color ?>"><?php echo $this->review_types[ $review['review_type'] ]; ?></span>
				<span class="rwp-reviews-box-type__label <?php echo $type_color ?>"><?php _e('Post id', $this->plugin_slug) ?>: <strong><?php echo get_the_ID(); ?></strong></span>
				<span class="rwp-reviews-box-type__label <?php echo $type_color ?>"><?php _e('Box id', $this->plugin_slug) ?>: <strong><?php echo $review_id; ?></strong></span>
			</div><!-- /reviews box type -->

			<table class="form-table">

				<tbody>
				<?php

				// Get the fields for review type
				$review_type = ( isset( $review['review_type'] ) ) ? $review['review_type'] : 'PAR+UR';
				$review_type_fields = $this->review_type_fields[ $review_type ];

				foreach( $review_type_fields as $field_id ) {

					// if($field_id == 'review_type') continue;

					$field 		= $this->review_fields[ $field_id ];
					$default 	= $field['default'];

					echo '<tr valign="top">';
					echo '<th scope="row">' . $field['label'] . '</th>';
					echo '<td>';

					switch ( $field_id ) {

						case 'review_template':
							echo '<select name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" data-review-id="'. $review_id .'" class="rwp-template-selection">';
							foreach ($this->templates_option as $template_id => $template) {
								$sel = (isset($review['review_template']) && $review['review_template'] == $template_id) ? 'selected' : '';
								echo '<option value="'. $template_id .'" '. $sel.'>' . $template['template_name'] . '</option>';
							}
							echo '</select>';
							break;

						case 'review_custom_tabs':
							//$tab_opts = $this->template_fields['template_custom_tabs']['options'];
							echo '<div class="rwp-custom-tabs-wrap">';
							foreach ($this->templates_option as $template_id => $template) {
								$show = (isset($review['review_template']) && $review['review_template'] == $template_id) ? 'style="display:block;"' : '';
								echo '<div id="rwp-custom-tabs-'. $review_id .'-'. $template_id .'" class="rwp-custom-tabs rwp-t-custom-tabs-'. $review_id .'" '. $show .'>';
								echo '<ul class="rwp-review-custom-tabs">';

								if( isset( $template['template_custom_tabs'] ) && !empty( $template['template_custom_tabs'] ) ) {

									foreach ($template['template_custom_tabs'] as $tab_id => $tab) {

										echo '<li>';
										echo '<span>'. $tab['tab_label'] .'</span>';

										foreach ( $field['options'] as $key => $opt) {

											$value = ( isset($review['review_template']) && $review['review_template'] == $template_id && isset( $review['review_custom_tabs'][ $tab_id ][$key] ) ) ? $review['review_custom_tabs'][ $tab_id ][$key] : $opt['default'];

											echo '<span>';
												echo '<label for="">'. $opt['label'] .'</label>';
												echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']['. $template_id .']['. $tab_id .']['. $key .']" value="'. $value .'" />';
											echo '</span>';
										}

										echo '</li>';
									}

								} else {
									echo '<p class="description">'. sprintf( __( 'No custom tabs were defined in "%s" template',  $this->plugin_slug ), $template['template_name'] ) .'</p>';
								}

								echo '</ul>';
								echo '</div><!--/custom tabs-->';
							}
							echo '</div><!--/rwp-custom-tabs-wrap-->';
							break;

						case 'review_scores':
							echo '<div class="rwp-templates-scores-wrap">';
							foreach ($this->templates_option as $template_id => $template) {
								$show = (isset($review['review_template']) && $review['review_template'] == $template_id) ? 'style="display:block;"' : '';
								echo '<div id="rwp-template-scores-'. $review_id .'-'. $template_id .'" class="rwp-template-scores rwp-t-scores-'. $review_id .'" '. $show .'>';
								echo '<ul>';

								$order = isset( $template['template_criteria_order'] ) ? $template['template_criteria_order'] : array_keys( $template['template_criterias'] );

								foreach( $order as $key => $criteria_id) {

									$criteria = $template['template_criterias'][ $criteria_id ];
									$value = ( isset($review['review_template']) && $review['review_template'] == $template_id && isset( $review['review_scores'][ $criteria_id ] ) ) ? $review['review_scores'][ $criteria_id ] : '0';
									echo '<li>';
									echo '<label>'. $criteria .'</label>';
									echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']['. $template_id .']['. $criteria_id .']" value="'. $value .'" placeholder="'. $template['template_minimum_score'] .'"/>';
									echo '<div class="rwp-slider" data-val="'. $value .'" data-min="'. $template['template_minimum_score'] .'" data-max="'. $template['template_maximum_score'] .'"></div>';
									echo '</li>';
								}
								echo '</ul>';
								echo '</div><!--/template scores-->';
							}
							echo '</div><!--/rwp-templates-scores-wrap-->';
							break;

						case 'review_image_url':
						case 'review_custom_overall_score':

							$value = ( isset($review[ $field_id ]) ) ? $review[ $field_id ] : '';
							echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $value .'" />';
							echo '<p class="description">'. $field['description'] .'</p>';
							break;

						case 'review_pros':
						case 'review_cons':
						case 'review_summary':
							$value = ( isset($review[ $field_id ]) ) ? $review[ $field_id ] : '';
							//echo '<textarea id="rwp_editor_'.$this->post_meta_key .'_'. $review_id .'_'. $field_id .'" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" placeholder="">'. $value .'</textarea>';

							wp_editor( $value, 'rwp_editor_'.$this->post_meta_key .'_'. $review_id .'_'. $field_id , $settings = array(

								'wpautop'		=> false,
								'media_buttons' => false,
								'textarea_name'	=> $this->post_meta_key .'['. $review_id .'][' . $field_id . ']',
								'textarea_rows'	=> 5,
								'tinymce' => false,
        						'quicktags' => true,
							) );

							break;

						case 'review_image':
							$value = ( !isset($review[ $field_id ]) || empty($review[ $field_id ]) ) ? '' : $review[ $field_id ];
							echo '<input id="rwp-review-add-image-input-' . $review_id . '" type="hidden" name="'. $this->post_meta_key .'[' . $review_id . '][' . $field_id . ']" value="'. $value.'" placeholder="" />';
							echo '<input class="rwp-review-add-image-btn button"type="button" value="'. __( 'Choose Image', $this->plugin_slug ) .'" data-review-id="'. $review_id.'" data-tb-title="Reviewer | '. __( 'Review Image', $this->plugin_slug ) .'" /> ';
							echo '<input class="rwp-review-remove-image-btn button" type="button" class="button" value="'. __( 'Remove Image', $this->plugin_slug ) .'" data-review-id="'. $review_id.'"  data-default="'. $default .'"/>';

							$src = ( !isset($review[ $field_id ]) || empty($review[ $field_id ]) ) ? $default : $review[ $field_id ];
							echo '<div class="rwp-review-image-wrap"><img src="'. $src .'" alt="Review Image" id="rwp-review-image-img-' . $review_id . '" /></div>';
							echo '<span class="rwp-review-image-desc description">'. $field['description'] .'</span>';
							break;

						case 'review_id':
							echo '<p class="description" style="margin-bottom:20px;">'. $field['description'] .'</p>';

							foreach ($field['shortcodes'] as $tag => $tag_data) {
								if( $tag == 'rwp-reviewer-rating-stars' &&  $review_type != 'PAR+UR' ) continue;
								echo '<p class="rwp-shortcode-tag"><span class="description">'. $tag_data['description'] .'</span>['. $tag .' id="'.$review_id.'"]</p>';
							}
							// echo '<p class="rwp-shortcode-tag"><span class="description">'. __( 'Display the full review', $this->plugin_slug ) .'</span>[rwp-review id="'.$review_id.'"]</p><br/>';
							// echo '<p class="rwp-shortcode-tag"><span class="description">'. __( 'Display the review recap only', $this->plugin_slug ) .'</span>[rwp-review-recap id="'.$review_id.'"]</p>';
							// echo '<p class="rwp-shortcode-tag"><span class="description">'. __( 'Display the review scores only', $this->plugin_slug ) .'</span>[rwp-review-scores id="'.$review_id.'"]</p>';
							// echo '<p class="rwp-shortcode-tag"><span class="description">'. __( 'Display the review ratings only', $this->plugin_slug ) .'</span>[rwp-review-ratings id="'.$review_id.'"]</p>';
							// echo '<p class="rwp-shortcode-tag"><span class="description">'. __( 'Display the review form only', $this->plugin_slug ) .'</span>[rwp-review-form id="'.$review_id.'"]</p>';
							echo '<input type="hidden" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $review_id .'" />';

							break;

						case 'review_status':
							echo '<select name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']">';

							foreach ( array('publish' => __( 'Published', $this->plugin_slug ), "draft" => __( 'Draft', $this->plugin_slug )) as $key => $value) {

								$ck = (isset($review[ $field_id ]) && $review[ $field_id ] == $key) ? 'selected' : '';

								echo '<option value="'. $key.'" '.$ck.'>'.$value.'</option>';
							}

							echo '</select>';

							echo '<p class="description">'. $field['description'] .'</p>';
							break;

						case 'review_custom_links':

							$links = isset($review[ $field_id ]) ? $review[ $field_id ] : $default;

							echo '<span class="description" style="margin-bottom: 10px; display: block;">'. $field['description'] .'</span>';

							echo '<input class="button rwp-add-custom-link-btn" type="button"  value="'. __( 'Add Custom Link', $this->plugin_slug ).'" data-review-id="'. $review_id .'" />';

							echo '<ul class="rwp-custom-links" data-label-placeholder="'.__( 'Link Label', $this->plugin_slug ).'" data-url-placeholder="'.__( 'Link URL', $this->plugin_slug ).'" data-remove-placeholder="'.__( 'Remove', $this->plugin_slug ).'">';

							foreach ($links as $key => $value) {

								echo '<li data-id="'.$key.'">';
									echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']['.$key.'][label]" 	value="'. $value['label'] .'"	placeholder="' . __( 'Link Label', $this->plugin_slug ) . '" />';
									echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']['.$key.'][url]" 		value="'. $value['url'] .'" 	placeholder="' . __( 'Link URL', $this->plugin_slug ) . '" />';
									echo '<a href="#" class="rwp-delete-custom-link-btn">'.__( 'Remove', $this->plugin_slug ).'</a>';
								echo '</li>';
							}

							echo '</ul>';

							$open_out = ( isset( $review['review_open_custom_links_out'] ) && $review['review_open_custom_links_out'] == 'yes' ) ? 'checked' : '';
							echo '<input type="checkbox" name="'. $this->post_meta_key .'['. $review_id .'][review_open_custom_links_out]" value="yes" '. $open_out .'/> <label>' . __( 'Open custom links in a new window or tab', $this->plugin_slug ) . '</label>';
							break;

						case 'review_user_rating_options':

							$ur_options = isset($review[ $field_id ]) ? $review[ $field_id ] : $default;

							foreach ($field['options'] as $key => $value) {

								$ck = in_array($key, $ur_options) ? 'checked' : '';

								echo '<input type="checkbox" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . '][]" value="'. $key .'" '.$ck.'/>' . ' ' . $value . '<br/>';

							}

							echo '<span class="description rwp-ck-list">'. $field['description'] .'</span>';
							break;

						case 'review_disable_user_rating':
						case 'review_use_featured_image':

							if( isset($review[ $field_id ]) && $review[ $field_id ] == 'yes' ) {
								$ck = 'checked';
								$value = 'yes';
							} else {
								$ck = '';
								$value = 'no';
							}

							echo '<input type="checkbox" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $value .'" '.$ck.'/>';
							echo '<span class="description">'. $field['description'] .'</span>';
							break;

						case 'review_title':
							$value = ( isset($review[ $field_id ]) ) ? $review[ $field_id ] : '';
							echo '<input class="rwp-review-title-input" data-review-id="'. $review_id .'" type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $value .'" placeholder="' . $default . ' ' . $review_id .'" data-default="' . $default . ' ' . $review_id .'" />';
							// echo '<input type="hidden" name="'. $this->post_meta_key .'['. $review_id .'][review_type]" value="'. $review_type .'" />';
							break;

						case 'review_title_options':
						case 'review_criteria_source':
						case 'review_type':
							$value = ( isset($review[ $field_id ]) ) ? $review[ $field_id ] : $field['default'];

							foreach ( $field['options'] as $key => $v) {

								$ck = ( $value == $key ) ? 'checked' : '';
								echo '<input type="radio" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $key .'" '.$ck.'/>' . ' ' . $v . '<br/>';

							}
							echo '<p class="description">'. $field['description'] .'</p>';

							break;

						case 'review_sameas_attr':
							$value = ( isset($review[ $field_id ]) ) ? esc_url( $review[ $field_id ] ) : '';
							echo '<input type="text" class="regular-text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $value .'" />';
							echo '<p class="description">'. $field['description'] .'</p>';
							break;

						default:
							$value = ( isset($review[ $field_id ]) ) ? $review[ $field_id ] : '';
							echo '<input type="text" name="'. $this->post_meta_key .'['. $review_id .'][' . $field_id . ']" value="'. $value .'" placeholder="' . $default . ' ' . $review_id .'" />';
							break;
					}

					echo '</td>';
					echo '</tr>';
				}
				?>
				</tbody>

			</table>

			<input class="button rwp-delete-review-btn" type="button"  value="<?php _e( 'Delete review', $this->plugin_slug ); ?>" data-review-id="<?php echo $review_id ?>" />

		</div> <!--/review-<?php echo $review_id; ?>-->
		<?php
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function set_review_types()
	{
		$this->review_types = array(

			'PAR+UR' 	=> __('Reviewer Box', $this->plugin_slug ),
			'UR' 		=> __('Users Box', $this->plugin_slug ),
		);

		$this->review_type_fields = array(

			'PAR+UR' 	=> array_keys( $this->review_fields ),

			'UR' 		=> array(

				// 'review_status',
				'review_type',
				'review_title',
				'review_title_options',
				'review_template',
				'review_custom_tabs',
				'review_use_featured_image',
				'review_image',
				'review_image_url',
				'review_custom_links',
				'review_sameas_attr',
				//'review_user_rating_options',
				'review_id'
			)
		);

	}

	public static function get_review_fields()
	{
		$plugin_slug = 'reviewer';

		 return array(

			// 'review_status' => array(

			// 	'label' => __('Reviews Box Status', $plugin_slug ),
			// 	'default' => 'publish',
			// 	'description'	=> __('If the status is set to "Draft", the review will not appear inside Reviews Lists, Reviewer Widget and Comparison Tables', $plugin_slug )
			// ),

			'review_type' => array(
				'label' => __('Review Box Type', $plugin_slug ),
				'default' => 'PAR+UR',
				'options' => array(
					'PAR+UR' 	=> __('Reviewer Box', $plugin_slug ),
					'UR' 	=> __('Users Box', $plugin_slug ),
				),
				'description' => __('If you change the box type you need to save the post before editing the review box settings.', $plugin_slug ),
			),

			'review_title' => array(

				'label' => __('Reviews Box Title', $plugin_slug ),
				'default' => __( 'Review Box', $plugin_slug ),
				'description'	=> ''
			),

			'review_title_options' => array(

				'label' => __('Reviews Box Title Options', $plugin_slug ),
				'default' => 'custom_title',
				'description'	=> '',
				'options' => array(
					'custom_title' 	=> __('Show Custom Title', $plugin_slug ),
					'post_title' 	=> __('Show Post Title', $plugin_slug ),
					'hidden' 		=> __('Hide Title', $plugin_slug ),
				),
			),

			'review_template' => array(

				'label' => __('Reviews Box Template', $plugin_slug ),
				'default' => '',
				'description'	=> ''
			),

			'review_scores' => array(

				'label' => __('Reviewer Scores', $plugin_slug ),
				'default' => array(),
				'description'	=> ''
			),

			'review_custom_overall_score' => array(

				'label' => __('Reviewer Custom Overall Score', $plugin_slug ),
				'default' => '',
				'description'	=> __( 'Leave blank for default overall score', $plugin_slug )
			),

			'review_criteria_source' => array(

				'label' => __('Reviews Box Criteria', $plugin_slug ),
				'default' => 'reviewer',
				'description'	=> __( 'Decide if you want to show the single criteria of Reviewer or Users inside the reviews box', $plugin_slug ),
				'options' => array(
					'reviewer' 	=> __('Show Reviewer single criteria ', $plugin_slug ),
					'users' 	=> __('Show Users single criteria', $plugin_slug ),
				),
			),

			'review_custom_tabs' => array(

				'label' => __('Reviews Box Custom Tabs', $plugin_slug ),
				'default' => array(),
				'description'	=> __( 'Custom tabs will be shown near overall scores', $plugin_slug ),
				'options' => array(

					'tab_value' => array(
						'label' => __('Tab Value', $plugin_slug ),
						'default' => '',
					),

					'tab_link' => array(
						'label' => __('Tab Link', $plugin_slug ),
						'default' => '',
					),
				),
			),

			'review_pros' => array(

				'label' => __( 'Reviews Box Pros', $plugin_slug ),
				'default' => '',
				'description'	=> ''
			),

			'review_cons' => array(

				'label' => __( 'Reviews Box Cons', $plugin_slug ),
				'default' => '',
				'description'	=> ''
			),

			'review_summary' => array(

				'label' => __( 'Reviews Box Summary', $plugin_slug ),
				'default' => '',
				'description'	=> ''
			),

			'review_use_featured_image' => array(
				'label' => __( 'Use "Featured Image" as Reviews Box Image', $plugin_slug ),
				'default' => 'no',
				'description'	=> __( 'Check the checkbox for using the post "Featured Image" as review image', $plugin_slug )
			),

			'review_image' => array(

				'label' => __( 'Reviews Box Custom Image', $plugin_slug ),
				'default' => RWP_PLUGIN_URL . 'admin/assets/images/review-image-preview.png',
				'description'	=> sprintf( __( 'Best size %s, or any size with the same aspect ratio', $plugin_slug ), '400 x 272 (px)'),
			),

			'review_image_url' => array(

				'label' => __( 'Reviews Box Image URL', $plugin_slug ),
				'default' => '',
				'description'	=> __( 'You can add a custom url for review image', $plugin_slug )
			),

			'review_custom_links' => array(

				'label' => __( 'Reviews Box Custom Links', $plugin_slug ),
				'default' => array(),
				'description'	=> __( 'You can add custom links that will appear under the overall score box', $plugin_slug )
			),

			'review_sameas_attr' => array(

				'label' => __('Define "sameAs" URL', $plugin_slug ),
				'default' => '',
				'description'	=> __( 'Define "sameAs" attribute url for Google Rich Snippets', $plugin_slug ),
			),

			'review_disable_user_rating' => array(
				'label' => __( 'Disable User reviews', $plugin_slug ),
				'default' => 'no',
				'description'	=> __( 'Check the checkbox for disabling the users rating for this review', $plugin_slug )
			),

			'review_id' => array(
				'label' => __( 'Reviews Box Shortcodes', $plugin_slug ),
				'default' => 0,
				'description'	=> sprintf( __( 'You can insert the following shortcodes inside your post content. If you add the %s parameter to one of those shortcodes you can display the reviews box in a different post/page. More info inside documentation.', $plugin_slug ), '<span>post="THE_POST_ID"</span>' ),
				'shortcodes' => array(
					'rwp_box' => array(
						'description' => __('Display the review box', $plugin_slug),
					),
					'rwp_reviewer_rating_stars' => array(
						'description' => __('Display rating stars about the reviewer score', $plugin_slug),
					),
					'rwp_users_rating_stars' => array(
						'description' => __('Display rating stars about the users score', $plugin_slug),
					),
					'rwp_box_recap' => array(
						'description' => __('Display some recap sections of review box. The user reviews and form sections are not shown', $plugin_slug),
					),
					'rwp_box_criteria' => array(
						'description' => __('Display the scores of single criteria', $plugin_slug),
					),
					'rwp_box_reviews' => array(
						'description' => __('Display the user reviews only', $plugin_slug),
					),
					'rwp_box_form' => array(
						'description' => __('Display the form for user review', $plugin_slug),
					),
				),
			)
		);
	}
}
