<?php
/**
 * The file that defines Single Review Shortcode Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Single_Review_Shortcode
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Single_Review_Shortcode extends WPPR_Pro_Addon_Abstract {

	/**
	 * The instance of WPPR_Review_Model.
	 *
	 * @since   2.0.0
	 * @access  private
	 * @var     WPPR_Review_Model $review The instance of WPPR_Review_Model.
	 */
	private $review;

	/**
	 * WPPR_Pro_Listings constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name    = __( 'Single Review Shortcode', 'wp-product-review' );
		$this->slug    = 'wppr-pro-single-review-shortcode';
		$this->version = '1.0.6';
	}

	/**
	 * Register default shortcodes attributes.
	 *
	 * @param array $defaults Default attrbutes.
	 *
	 * @return array Shortcode attributes.
	 */
	public function register_default_attributes( $defaults ) {
		return array(
			'post_id' => '',
			'visual'  => 'no',
		);
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {
		if ( ! shortcode_exists( 'P_REVIEW' ) ) {
			add_shortcode( 'P_REVIEW', array( $this, 'single_post_review' ) );
		}
		$this->loader->add_filter( 'wppr_shortcode_attributes', $this, 'register_default_attributes' );
		$this->loader->add_filter( 'wppr_templates_dir', $this, 'register_templates_dirs' );
	}

	/**
	 * Register template dirs.
	 *
	 * @param array $dirs The old dirs.
	 *
	 * @return array The template dirs.
	 */
	public function register_templates_dirs( $dirs ) {
		$dirs[] = trailingslashit( WPPR_PRO_PATH ) . 'includes/addons/layouts';
		$dirs[] = trailingslashit( WPPR_PRO_PATH ) . 'includes/addons/wppr_comparison_table';
		$dirs[] = trailingslashit( WPPR_PRO_PATH ) . 'includes/addons/wppr_listings';

		return $dirs;
	}

	/**
	 * Utility method to do shortcode.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   array $atts Attributes array.
	 *
	 * @return string
	 */
	public function single_post_review( $atts ) {
		$default_attributes = apply_filters( 'wppr_shortcode_attributes', array() );
		$a                  = shortcode_atts(
			$default_attributes, $atts
		);

		$this->review = new WPPR_Review_Model( $a['post_id'] );
		$visual       = $a['visual'];

		$output = '';

		if ( $this->review->is_active() ) {

			if ( ! class_exists( 'WPPR' ) ) {
				return $output;
			}
			$plugin        = new WPPR();
			$public        = new Wppr_Public( $plugin->get_plugin_name(), $plugin->get_version() );
			$review_object = $this->review;

			$public->load_review_assets( $review_object );

			if ( class_exists( 'WPPR_Template' ) ) {
				$template = new WPPR_Template();
				if ( $visual == 'full' ) {
					$output .= $template->render(
						'default', array(
							'review_object' => $review_object,
						), false
					);

				}
				if ( $visual == 'yes' ) {
					$output .= $template->render(
						'rating', array(
							'review_object' => $review_object,
						), false
					);
				}

				if ( $visual == 'no' ) {
					$output .= $template->render(
						'score', array(
							'review_object' => $review_object,
						), false
					);
				}

				$output .= $template->render(
					'rich-json-ld', array(
						'review_object' => $review_object,
					), false
				);
			} else {

				/**
				 * Deprecated code for loading template in lite.
				 */
				if ( $visual == 'full' ) {
					$theme_template = get_template_directory() . '/wppr/default.php';
					if ( file_exists( $theme_template ) ) {
						include( $theme_template );
					} else {
						include( WPPR_PATH . '/includes/public/layouts/default-tpl.php' );
					}
				}

				if ( $visual == 'yes' ) {
					$theme_template = get_template_directory() . '/wppr/rating.php';
					if ( file_exists( $theme_template ) ) {
						include( $theme_template );
					} else {
						include( WPPR_PATH . '/includes/public/layouts/rating-tpl.php' );
					}
				}

				if ( $visual == 'no' ) {
					$theme_template = get_template_directory() . '/wppr/score.php';
					if ( file_exists( $theme_template ) ) {
						include( $theme_template );
					} else {
						include( WPPR_PATH . '/includes/public/layouts/score-tpl.php' );
					}
				}

				include( WPPR_PATH . '/includes/public/layouts/rich-json-ld.php' );
			}
		}

		return $output;
	}
}
