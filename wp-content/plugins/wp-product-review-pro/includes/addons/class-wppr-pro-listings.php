<?php
/**
 * The file that defines Listings Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Listings
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Listings extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Listings constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name    = __( 'Pro Listings', 'wp-product-review' );
		$this->slug    = 'wppr-pro-listings';
		$this->version = '1.0.3';
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
		$this->loader->add_filter( 'wppr_get_old_option', $this, 'get_old_option', 10, 2 );
		$this->loader->add_filter( 'the_content', $this, 'the_content' );
		$this->loader->add_filter( 'shortcode_atts_wpr_listing', $this, 'filter_attrs', 10, 3 );

		add_shortcode( 'wpr_listing', array( $this, 'shortcode' ) );
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
	 * Method to filter content.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string $content The post content.
	 *
	 * @return string
	 */
	public function the_content( $content ) {
		if ( isset( $_GET['wpprl'] ) ) {
			$num = intval( $this->model->wppr_get_option( 'cwppos_l_show_related_posts' ) ) == 1 ? 3 : 0;
			if ( $num > 0 ) {
				parse_str( str_replace( array( '|', ':' ), array( '&', '=' ), $_GET['wpprl'] ), $output );
				$content = $content . $this->get_shorcode_from_params( $output, $num );
			}
		}

		return $content;
	}

	/**
	 * Utility method to retrieve shortcode attributes.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array   $output The output array.
	 * @param   integer $num The limit.
	 *
	 * @return string
	 */
	private function get_shorcode_from_params( $output, $num ) {
		$shortcode = '[wpr_listing';
		if ( is_array( $output ) ) {
			$output['nr'] = $num;
			foreach ( $output as $key => $value ) {
				$shortcode = $shortcode . " $key=$value";
			}
		}

		return $shortcode . ']';
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
		$arguments = shortcode_atts(
			array(
				'nr'      => '10',
				'cat'     => '',
				'img'     => '',
				'orderby' => '',
				'order'   => '',
			),
			$atts,
			'wpr_listing'
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

		// Backwards compatibility.
		if ( class_exists( 'WPPR_Template' ) ) {
			$template = new WPPR_Template();

			return $template->render(
				'listing',
				array(
					'arguments' => $arguments,
					'results' => $results,
				),
				false
			);
		} else {
			$theme_template = get_template_directory() . '/wppr/listing.php';
			if ( file_exists( $theme_template ) ) {
				ob_start();
				include $theme_template;

				return ob_get_clean();
			}

			ob_start();
			include WPPR_PRO_PATH . '/includes/addons/layouts/listing.php';

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
		wp_enqueue_style( $this->slug . '-styles', WPPR_PRO_ADDONS_ASSETS . 'css/listing.css', false, $this->version );
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
		if ( in_array(
			$key, array(
				'cwppos_l_show_related_posts',
				'cwppos_l_review_text',
				'cwppos_l_button_text',
			)
		) && $value == false ) {
			$global_settings_fields = WPPR_Global_Settings::instance()->get_fields();
			$value                  = get_option( $key, $global_settings_fields['pro_listings'][ $key ]['default'] );
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
		$sections['pro_listings'] = __( 'Pro Listings', 'wp-product-review' );

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
		$fields['pro_listings'] = array(
			'cwppos_l_show_related_posts' => array(
				'id'          => 'show_related_posts',
				'name'        => __( 'Show review on related posts.', 'wp-product-review' ),
				'description' => __( 'Automatically display related reviews at the end of the post.', 'wp-product-review' ),
				'type'        => 'select',
				'options'     => array(
					'0' => __( 'No', 'wp-product-review' ),
					'1' => __( 'Yes', 'wp-product-review' ),
				),
				'default'     => '0',
			),
			'cwppos_l_review_text'        => array(
				'type'        => 'input_text',
				'name'        => __( 'Review link text', 'wp-product-review' ),
				'description' => __( 'What will be displayed on the review link.', 'wp-product-review' ),
				'id'          => 'review_text',
				'default'     => __( 'Read Review', 'wp-product-review' ),
			),
			'cwppos_l_button_text'        => array(
				'type'        => 'input_text',
				'name'        => __( 'Button text', 'wp-product-review' ),
				'description' => __( 'What will be displayed on the call to action button.', 'wp-product-review' ),
				'id'          => 'button_text',
				'default'     => __( 'Buy Now', 'wp-product-review' ),
			),
		);

		return $fields;
	}
}
