<?php
/**
 * The file that defines Comparison Table Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Comparison_Table
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Comparison_Table extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Listings constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name    = __( 'Pro Comparison Table', 'wp-product-review' );
		$this->slug    = 'wppr-pro-comparison-table';
		$this->version = '1.2.2';
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {

		$this->model = new WPPR_Query_Model();

		$this->loader->add_filter( 'wppr_settings_sections', $this, 'add_sections', 10, 1 );
		$this->loader->add_filter( 'wppr_settings_fields', $this, 'add_fields', 10, 1 );

		$this->loader->add_filter( 'wppr_get_default_option', $this, 'get_old_option', 20, 2 );
		$this->loader->add_filter( 'shortcode_atts_wpr_landing', $this, 'filter_attrs', 10, 3 );

		add_shortcode( 'wpr_landing', array( $this, 'shortcode' ) );
	}

	/**
	 * Fixed shortcode attribute img, when present, show the images.
	 *
	 * @param array $out The old attributes values.
	 * @param array $pairs The defaults.
	 * @param array $atts The attributes used.
	 *
	 * @return mixed The shortcode attributes.
	 */
	public function filter_attrs( $out, $pairs, $atts ) {
		if ( ! is_array( $atts ) ) {
			return $out;
		}
		if ( in_array( 'img', $atts ) ) {
			$out['img'] = 'yes';
		}

		return $out;
	}

	/**
	 * Method to process the shortcode.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   array      $atts The attributes array.
	 * @param   mixed|null $content The post content.
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		global $content_width;

		$arguments = shortcode_atts(
			array(
				'nr'      => '10',
				'cat'     => '',
				'img'     => 'no',
				'orderby' => 'rating',
				'order'   => 'desc',
			),
			$atts,
			'wpr_landing'
		);

		$post = array();
		if ( isset( $arguments['cat'] ) && is_numeric( $arguments['cat'] ) ) {
			$post['category_id'] = $arguments['cat'];
		}

		if ( isset( $arguments['cat'] ) && trim( $arguments['cat'] ) != '' ) {
			$post['category_name'] = $arguments['cat'];
		}

		$order[ $arguments['orderby'] ] = strtoupper( $arguments['order'] );

		$this->enqueue();
		$results = $this->model->find( $post, $arguments['nr'], array(), $order );

		$content_width = filter_var( $content_width, FILTER_SANITIZE_NUMBER_INT );
		$min_width     = 800;
		// Backwards compatibility check.
		if ( class_exists( 'WPPR_Template' ) ) {
			$template = new WPPR_Template();

			return $template->render(
				'table', array(
					'results'   => $results,
					'arguments'   => $arguments,
					'min_width' => $min_width,
				),
				false
			);
		} else {
			$theme_template = get_template_directory() . '/wppr/table.php';
			if ( file_exists( $theme_template ) ) {
				ob_start();
				include $theme_template;

				return ob_get_clean();
			}

			ob_start();
			include WPPR_PRO_PATH . '/includes/addons/layouts/table.php';

			return ob_get_clean();
		}
	}

	/**
	 * Method to enqueue scripts and styles.
	 *
	 * @since   2.0.0
	 * @access  private
	 */
	private function enqueue() {
		wp_enqueue_style( $this->slug . '-styles', WPPR_PRO_ADDONS_ASSETS . 'css/table_styles.css', false, $this->version );

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		if ( wp_script_is( 'stacktable.js', 'enqueued' ) ) {
			wp_enqueue_script( $this->slug . '-scripts', WPPR_PRO_ADDONS_ASSETS . 'js/cwppose_scripts.js', false, $this->version );
		} else {
			wp_enqueue_script( $this->slug . '-scripts', WPPR_PRO_ADDONS_ASSETS . 'js/cwppose_scripts_stacktable.js', false, $this->version );
		}
	}

	/**
	 * Method to filter old value from DB.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string $value The value passed by the filter.
	 * @param   string $key The key passed by the filter.
	 *
	 * @return mixed
	 */
	public function get_old_option( $value, $key ) {
		$allowed_options = array(
			'cwppose_lang_name',
			'cwppose_lang_rating',
			'cwppose_lang_description',
			'cwppose_lang_statistics',
			'cwppose_lang_link',
			'cwppose_lang_read_review',
			'cwppose_lang_button',
			'cwppose_lang_price',
			'cwppose_read_review_color',
			'cwppose_view_options',
			'cwppose_view_description',
			'cwppose_view_price',
		);
		if ( in_array( $key, $allowed_options ) ) {

			$old_settings = get_option( 'cwppose_settings', false );

			if ( $old_settings != false ) {
				if ( isset( $old_settings[ $key ] ) ) {

					$value = $old_settings[ $key ];
				}
			}
		}

		return $value;
	}

	/**
	 * Registers a new section in the global settings.
	 *
	 * @since   2.0.0
	 * @acccess public
	 *
	 * @param   array $sections The sections array.
	 *
	 * @return mixed
	 */
	public function add_sections( $sections ) {
		$sections['pro_comparison_table'] = __( 'Pro Comparison Table', 'wp-product-review' );

		return $sections;
	}

	/**
	 * Registers a new fields list for the section defined in add_section().
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   array $fields The fields array.
	 *
	 * @return mixed
	 */
	public function add_fields( $fields ) {
		$fields['pro_comparison_table'] = array(
			'cwppose_lang_name'         => array(
				'type'        => 'input_text',
				'name'        => __( 'Name column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the NAME column.', 'wp-product-review' ),
				'id'          => 'lang_name',
				'default'     => __( 'NAME', 'wp-product-review' ),
			),
			'cwppose_lang_rating'       => array(
				'type'        => 'input_text',
				'name'        => __( 'Rating column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the RATING column.', 'wp-product-review' ),
				'id'          => 'lang_rating',
				'default'     => __( 'RATING', 'wp-product-review' ),
			),
			'cwppose_lang_description'  => array(
				'type'        => 'input_text',
				'name'        => __( 'Description column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the DESCRIPTION column.', 'wp-product-review' ),
				'id'          => 'lang_description',
				'default'     => __( 'DESCRIPTION', 'wp-product-review' ),
			),
			'cwppose_lang_statistics'   => array(
				'type'        => 'input_text',
				'name'        => __( 'Statistics column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the Statistics column.', 'wp-product-review' ),
				'id'          => 'lang_statistics',
				'default'     => __( 'Statistics', 'wp-product-review' ),
			),
			'cwppose_lang_link'         => array(
				'type'        => 'input_text',
				'name'        => __( 'Link column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the LINK column.', 'wp-product-review' ),
				'id'          => 'lang_link',
				'default'     => __( 'LINK', 'wp-product-review' ),
			),
			'cwppose_lang_read_review'  => array(
				'type'        => 'input_text',
				'name'        => __( 'The text for the review link', 'wp-product-review' ),
				'description' => __( 'Change text for "Read review"', 'wp-product-review' ),
				'id'          => 'lang_read_review',
				'default'     => __( 'Read review', 'wp-product-review' ),
			),
			'cwppose_lang_button'       => array(
				'type'        => 'input_text',
				'name'        => __( 'The default button text', 'wp-product-review' ),
				'description' => __( 'Set default text for button', 'wp-product-review' ),
				'id'          => 'lang_button',
				'default'     => '',
			),
			'cwppose_lang_price'        => array(
				'type'        => 'input_text',
				'name'        => __( 'Price column title', 'wp-product-review' ),
				'description' => __( 'The text to be displayed on the PRICE column.', 'wp-product-review' ),
				'id'          => 'lang_price',
				'default'     => __( 'PRICE', 'wp-product-review' ),
			),
			'cwppose_read_review_color' => array(
				'type'        => 'color',
				'name'        => __( 'Read Review Color', 'wp-product-review' ),
				'description' => __( 'Change color for "Read Review".', 'wp-product-review' ),
				'id'          => 'read_review_color',
				'default'     => '#E1E2E0',
			),
			'cwppose_view_options'      => array(
				'type'        => 'select',
				'name'        => __( 'Show options in table', 'wp-product-review' ),
				'description' => __( 'Show the product options inside the table.', 'wp-product-review' ),
				'id'          => 'view_options',
				'options'     => array(
					'yes' => __( 'Yes', 'wp-product-review' ),
					'no'  => __( 'No', 'wp-product-review' ),
				),
				'default'     => 'yes',
			),
			'cwppose_view_description'  => array(
				'type'        => 'select',
				'name'        => __( 'Show descriptions in table', 'wp-product-review' ),
				'description' => __( 'Show the product descriptions inside the table.', 'wp-product-review' ),
				'id'          => 'view_description',
				'options'     => array(
					'yes' => __( 'Yes', 'wp-product-review' ),
					'no'  => __( 'No', 'wp-product-review' ),
				),
				'default'     => 'yes',
			),
			'cwppose_view_price'        => array(
				'type'        => 'select',
				'name'        => __( 'Show price in table', 'wp-product-review' ),
				'description' => __( 'Show the product price inside the table.', 'wp-product-review' ),
				'id'          => 'view_price',
				'options'     => array(
					'yes' => __( 'Yes', 'wp-product-review' ),
					'no'  => __( 'No', 'wp-product-review' ),
				),
				'default'     => 'no',
			),
		);

		return $fields;
	}
}
