<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Tables_Meta_Box
{
	// Instance of this class
	protected static $instance = null;
	protected $table_fields;
	protected $preferences_option;
	protected $templates_option;
	protected $post_meta_key = 'rwp_tables';
	protected $post_tables;

	function __construct()
	{
		$this->plugin_slug = 'reviewer';
		$this->templates_option = RWP_Reviewer::get_option( 'rwp_templates' );
		$this->preferences_option = RWP_Reviewer::get_option( 'rwp_preferences' );
		$this->set_table_fields();
	}

	public function init()
	{
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box') );
		add_action( 'save_post', array( $this, 'save_meta_box') );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function localize_script()
	{
		$action_name = 'rwp_ajax_action_get_table_form';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'tablesMetaBoxObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public static function ajax_callback()
	{
		$rev =  RWP_Tables_Meta_Box::get_instance();
		$rev->get_table_form( $review = array( 'table_id' => $_REQUEST['rwp_table_id'] ) );
		die();
	}

	public function add_meta_box()
	{
		$this->set_table_fields();
		$this->post_tables = get_post_meta( get_the_ID(), $this->post_meta_key, true );

		$types = is_array( $this->preferences_option['preferences_post_types'] ) ? $this->preferences_option['preferences_post_types'] : array();
		foreach ( $types as $post_type) {

			add_meta_box( 'rwp-tables-meta-box', 'Reviewer | ' . __( 'Post Comparison Tables', $this->plugin_slug ), array( $this, 'render_meta_box'), $post_type );
		}
	}

	public function save_meta_box( $post_id )
	{
		// Check nonce
		if ( ! isset( $_POST['rwp_tables_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['rwp_tables_meta_box_nonce'];

		// Verufy nonce
		if ( ! wp_verify_nonce( $nonce, 'rwp_save_tables_meta_box') )
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
		//if( ! isset( $_POST[ $this->post_meta_key ] ) && ! is_array( $_POST[ $this->post_meta_key ] ) )
		if( ! isset( $_POST[ $this->post_meta_key ] ) )
			$_POST[ $this->post_meta_key ] = array();

		// Check if is a valid array
		if( ! is_array( $_POST[ $this->post_meta_key ] ) )
			return $post_id;

		// Validate reviews
		$tables = array();

		foreach ( $_POST[ $this->post_meta_key ] as $table_id => $table) {

			foreach ( $this->table_fields as $field_id => $field) {

				switch ( $field_id ) {
					case 'table_title':

						if( ! isset( $table[ $field_id ] ) ) { //if field is not set
							$tables[ $table_id ][ $field_id ] = $field['default'];
							break;
						}

						$value = trim($table[ $field_id ]);
						$tables[ $table_id ][ $field_id ] = ( empty( $value ) ) ? $field['default'] . ' ' . $table_id : wp_kses_post($value);
						break;

					case 'table_template':

						$default_v = array_values( $this->templates_option );
						$default = $default_v[0]['template_id'];


						if( ! isset( $table[ $field_id ] ) ){ //if field is not set
							$tables[ $table_id ][ $field_id ] = $default;
							break;
						}

						$value = trim($table[ $field_id ]);
						$tables[ $table_id ][ $field_id ] = ( empty( $value ) ) ? $default : esc_sql( esc_html ( $value ) );
						break;

					case 'table_theme':
					case 'table_sorting':


						if( ! isset( $table[ $field_id ] ) ){ //if field is not set
							$tables[ $table_id ][ $field_id ] = $field['default'];
							break;
						}

						$value = trim($table[ $field_id ]);
						$tables[ $table_id ][ $field_id ] = ( empty( $value ) ) ? $default : wp_kses_post( $value );
						break;

					case 'table_reviews':

						if( ! isset( $table[ $field_id ] ) ){ //if field is not set
							$tables[ $table_id ][ $field_id ] = array();
							break;
						}

						$template_id = $tables[ $table_id ][ 'table_template' ];

						if( ! isset( $table[ $field_id ][ $template_id ] ) || empty( $table[ $field_id ][ $template_id ] ) ) {
							$tables[ $table_id ][ $field_id ] = array();
							break;
						}

						foreach ($table[ $field_id ][ $template_id ] as $review) {
							$tables[ $table_id ][ $field_id ][] = esc_sql( esc_html ( $review ) );
						}

						break;

					case 'table_reviews_boxes_image':
						if( ! isset( $table[ $field_id ] ) ) { //if field is not set
							$tables[ $table_id ][ $field_id ] = $field['default'];
							break;
						}

						$tables[ $table_id ][ $field_id ] = 'yes';
						break;

					case 'table_id':
					default:
						$tables[ $table_id ][ $field_id ] = $table_id;
						break;
				}

			}
		}

		update_post_meta( $post_id, $this->post_meta_key, $tables );
	}

	public function render_meta_box()
	{
		// Add an nonce field
		wp_nonce_field( 'rwp_save_tables_meta_box', 'rwp_tables_meta_box_nonce' );

		// Check if there is at least one reviews template
		if( empty( $this->templates_option ) ) {

			echo '<p>' . __( 'Please insert a reviews template before adding a new comparison table.', $this->plugin_slug ) . '</p>';
			return;
		}
		?>

		<p class="description"><?php _e( 'In this meta box you can manage the post comparison tebles; save/update the post to save comparison tables.', $this->plugin_slug ); ?></p>

		<div class="rwp-metabox-elems">
			<a href="#" class="button button-primary" id="rwp-add-table-form-btn"><?php _e( 'Add new comparison table', $this->plugin_slug ); ?></a><img class="rwp-loader" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />
		</div>

		<ul class="rwp-tabs-wrap" data-placehoder="<?php _e( 'Table', $this->plugin_slug ); ?>">
		<?php
		if ($this->post_tables != null && ! empty( $this->post_tables ) ) {
			//$first_table_id = array_keys( $this->post_tables )[0]; // First Review ID
			$pt = array_keys( $this->post_tables );
			$first_table_id = $pt[0];

			foreach ( $this->post_tables as $table_id => $table) {
				$hide = ( $table_id != $first_table_id ) ? '' : 'rwp-tabs';
				echo '<li class="'. $hide .'" id="rwp-table-tab-'. $table_id .'"><a href="#" data-table-id="'. $table_id .'">'. $table['table_title'] .'</a></li>';
			}
		}
		?>
		</ul>

		<div id="rwp-tables-wrap">
		<?php
		if ($this->post_tables != null && ! empty( $this->post_tables ) )
			foreach ( $this->post_tables as $table_id => $table) {
				$this->get_table_form( $table );
			}
		?>
		</div><!--/rwp-tables-wrap-->
		<?php

		//RWP_Reviewer::pretty_print(  $this->post_tables );
	}

	public function get_table_form( $table = array( 'table_id' => 0 ) )
	{
		$table_id = $table['table_id']; // Get the table ID
		//$first_table_id = ($this->post_tables != null && ! empty( $this->post_tables ) ) ? array_keys( $this->post_tables )[0] : -1; // First Table ID

		if($this->post_tables != null && ! empty( $this->post_tables ) ) {
			$pt = array_keys( $this->post_tables );
			$first_table_id = $pt[0];
		} else {
			$first_table_id = -1;
		}

		$hide = ( $table_id != $first_table_id ) ? 'style="display:none;"' : '';

		?>

		<div id="rwp-table-<?php echo $table_id ?>" class="rwp-table-form rwp-tabs-panel" data-table-id="<?php echo $table_id ?>" <?php echo $hide; ?>>

			<table class="form-table">

				<tbody>
				<?php
				foreach( $this->table_fields as $field_id => $field ) {

					$default = $field['default'];

					echo '<tr valign="top">';
					echo '<th scope="row">' . $field['label'] . '</th>';
					echo '<td>';

					switch ( $field_id ) {

						case 'table_template':
							echo '<select name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" data-table-id="'. $table_id .'" class="rwp-template-selection">';
							foreach ($this->templates_option as $template_id => $template) {
								$sel = (isset($table['table_template']) && $table['table_template'] == $template_id) ? 'selected' : '';
								echo '<option value="'. $template_id .'" '. $sel.'>' . $template['template_name'] . '</option>';
							}
							echo '</select>';
							break;

						case 'table_theme':

							echo '<ul class="rwp-table-themes">';

							$table_theme = ( isset( $table['table_theme'] ) ) ? $table['table_theme'] : $default;

							$i = 0;

							foreach ($field['options'] as $theme_id => $theme_label) {

								$ck = ( $theme_id == $table_theme ) ? 'checked' : '';

								echo '<li>';

									$style = ( $i == 0) ? 'background-color: #ddd;' : 'background-image: url('. RWP_PLUGIN_URL .'admin/assets/images/themes/table-theme-preview-'. $i .'.png);';

									echo '<div class="rwp-table-theme-icon" style="'. $style .'"></div>';

									echo '<input type="radio" name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" value="'. $theme_id .'" '.$ck.'/>';

									echo '<span class="rwp-table-theme-label">'. $theme_label .'</span>';

								echo '</li>';

								$i++;
							}

							echo '</ul>';

							echo '<p class="description">'. $field['description'] .'</p>';

							break;

						case 'table_reviews':

							$reviews = $this->get_reviews_by_templates();

							echo '<div class="rwp-templates-reviews-wrap">';
							foreach ($reviews as $template_id => $template) {
								$show = (isset($table['table_template']) && $table['table_template'] == $template_id) ? 'style="display:block;"' : '';
								echo '<div id="rwp-template-reviews-'. $table_id .'-'. $template_id .'" class="rwp-template-reviews rwp-t-reviews-'. $table_id .'" '. $show .'>';
								echo '<ul>';
								foreach($template as $review_id => $review) {
									$checked = '';
									if( isset( $table[ $field_id ] ) )
										$checked = ( in_array( $review['review_post_id'] .':'. $review['review_id'], $table[ $field_id ] ) ) ? 'checked' : '';
									echo '<li>';
									echo '<input type="checkbox" '. $checked .'  name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']['. $template_id .'][]" id="rwp-t-r-'. $table_id .'-'. $template_id .'-'. $review_id .'" value="'. $review['review_post_id'] .':'. $review['review_id'] .'"/>';
									echo '<label for="rwp-t-r-'. $table_id .'-'. $template_id .'-'. $review_id .'">'. $review['review_title'] .'</label>';
									echo '</li>';
								}
								echo '</ul>';
								echo '</div><!--/template reviews-->';
							}
							echo '</div><!--/rwp-templates-reviews-wrap-->';
							break;

						case 'table_sorting':

							echo '<ul>';

							$table_sorting = ( isset( $table['table_sorting'] ) ) ? $table['table_sorting'] : $default;

							foreach ($field['options'] as $sorting_id => $sorting_label) {
								$ck = ( $sorting_id == $table_sorting ) ? 'checked' : '';
								echo '<li>';
									echo '<input type="radio" name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" value="'. $sorting_id .'" '.$ck.'/>';
									echo '<label>'. $sorting_label .'</label>';
								echo '</li>';
							}

							echo '</ul>';

							break;

						case 'table_id':
							echo '<p class="rwp-shortcode-tag">[rwp_comparison_table id="'.$table_id.'"]</p>';
							echo '<input type="hidden" name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" value="'. $table_id .'" />';

							break;

						case 'table_reviews_boxes_image':
							if( isset($table[ $field_id ]) && $table[ $field_id ] == 'yes' ) {
								$ck = 'checked';
								$value = 'yes';
							} else {
								$ck = '';
								$value = 'no';
							}

							echo '<input type="checkbox" name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" value="'. $value .'" '.$ck.'/>';
							echo '<span class="description">'. $field['description'] .'</span>';
							break;

						case 'table_title':
						default:
							$value = ( isset($table[ $field_id ]) ) ? $table[ $field_id ] : '';
							echo '<input class="rwp-table-title-input" data-table-id="'. $table_id .'" type="text" name="'. $this->post_meta_key .'['. $table_id .'][' . $field_id . ']" value="'. $value .'" placeholder="' . $default . ' ' . $table_id .'" data-default="' . $default . ' ' . $table_id .'"/>';
							break;
					}

					echo '</td>';
					echo '</tr>';
				}
				?>
				</tbody>

			</table>

			<input class="button rwp-delete-table-btn" type="button"  value="<?php _e( 'Delete table', $this->plugin_slug ); ?>" data-table-id="<?php echo $table_id ?>" />

		</div> <!--/review-<?php echo $review_id; ?>-->
		<?php
	}

	public function get_reviews_by_templates()
	{
		global $wpdb;
		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rwp_reviews';", ARRAY_A );

		foreach( $post_meta as $meta ) {

			$reviews = unserialize( $meta['meta_value'] );

			foreach( $reviews as $review ) {

				$res_rev = array(

					'review_post_id' => $meta['post_id'],
					'review_id' => $review['review_id'],
					'review_title' => $review['review_title']
				);

				foreach( $this->templates_option as $template ) {

					if( $template['template_id'] == $review['review_template'] ) {

						$result[$template['template_id']][] = $res_rev;
						break;
					}
				}
			}
		}

		//RWP_Reviewer::pretty_print(  $result );
		return $result;
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function set_table_fields()
	{
		$this->table_fields = array(

			'table_title' => array(
				'label' => __('Table Title', $this->plugin_slug ),
				'default' => 'Table',
				'description'	=> ''
			),

			'table_template' => array(
				'label' => __('Table Template', $this->plugin_slug ),
				'default' => '',
				'description'	=> ''
			),

			'table_theme' => array(
				'label' => __('Table Theme', $this->plugin_slug ),
				'default' => 'default',
				'options' => array(
					'default' 		=> __('Default', $this->plugin_slug ),
					'rwp-theme-1' 	=> __('Theme 1', $this->plugin_slug ),
					'rwp-theme-2' 	=> __('Theme 2', $this->plugin_slug ),
					'rwp-theme-3' 	=> __('Theme 3', $this->plugin_slug ),
					'rwp-theme-4' 	=> __('Theme 4', $this->plugin_slug ),
					'rwp-theme-5' 	=> __('Theme 5', $this->plugin_slug ),
				),
				'description'	=> __( 'The "Default" Theme picks one of the other themes based on the theme of reviews', $this->plugin_slug ),
			),

			'table_reviews' => array(
				'label' => __( 'Table Reviews', $this->plugin_slug ),
				'default' => array(),
				'description'	=> ''
			),

			'table_sorting' => array(
				'label' => __( 'Table Sorting', $this->plugin_slug ),
				'default' => 'latest',
				'options' => array(
					'latest' 			=> __('Sort by Latest Boxes', $this->plugin_slug ),
					'reviewer_score' 	=> __('Sort by Reviewer Score', $this->plugin_slug ),
					'users_score' 		=> __('Sort by Users Score', $this->plugin_slug ),
					'combo' 			=> __('Sort by Average of Reviewer and Users Scores', $this->plugin_slug )
				),
				'description'	=> ''
			),

			'table_reviews_boxes_image' => array(
				'label' => __( 'Include Reviews Boxes Images', $this->plugin_slug ),
				'default' => 'no',
				'description'	=> __( 'Show the related review box image - if it is set - inside tables', $this->plugin_slug ),
			),

			'table_id' => array(
				'label' => __( 'Table Shortcode', $this->plugin_slug ),
				'default' => 0,
				'description'	=> ''
			)

		);
	}
}
