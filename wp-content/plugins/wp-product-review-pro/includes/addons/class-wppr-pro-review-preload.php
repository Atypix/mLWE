<?php
/**
 * The file that defines Review Preload Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Review_Preload
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Review_Preload extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Custom_Icon constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name = __( 'Pro Review Preload' , 'wp-product-review' );
		$this->slug = 'wppr-pro-review-preload';
		$this->version = '1.1.1';
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'wppr_review_preload_scripts' );
		$this->loader->add_action( 'wp_ajax_review_preload_ajax', $this, 'review_preload_ajax' );
	}

	/**
	 * Register scripts and styles for this addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function wppr_review_preload_scripts() {
		wp_register_script( $this->slug . '-main-script', WPPR_PRO_ADDONS_ASSETS . 'js/wppr-pro-review-preload.js', false, $this->version, 'all' );
		$data_array = array(
			'cwpThemeUrl' => WPPR_PRO_ADDONS_ASSETS . 'img/',
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_action' => 'review_preload_ajax',
		);
		wp_localize_script( $this->slug . '-main-script', 'passed_data', $data_array );
		wp_enqueue_script( $this->slug . '-main-script' );
	}

	/**
	 * Method to process ajax request.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function review_preload_ajax() {
		$args = array(
			'offset'           => 0,
			'post_type'        => array( 'post' ),
			'meta_query'             => array(
				array(
					'key'       => 'cwp_meta_box_check',
					'value'     => 'Yes',
				),
			),
		);

		$cwp_query = new WP_Query( $args );
		while ( $cwp_query->have_posts() ) {
			$cwp_query->the_post();
			$post_id = get_the_ID();
			$review = new WPPR_Review_Model( $post_id );
			$preloaded_info = array();
			$preloaded_info[ $post_id ] = array();

			?>
			<li class="cwp_preloaded_item cwpr_clearfix">
				<header>
					<h3 class="cwp_p_title"><?php the_title(); ?></h3>
					<button class="preload" title="Preload all details">&curarr;</button>
				</header>
				<?php
				$options = $review->get_options();
				$pros = $review->get_pros();
				$cons = $review->get_cons();
				?>

				<div class="cwp_pitem_info post_<?php echo $post_id; ?>">
					<ul class="cwp_pitem_options_content">
						<h4><?php _e( 'Options', 'wp-product-review' ); ?></h4>
						<?php
						for ( $i = 0; $i < 5; $i++ ) {
							if ( isset( $options[ $i ]['name'] ) && ! empty( $options[ $i ] ) ) {
								echo '<li>' . $options[ $i ]['name'] . '</li>';
							} else {
								echo '<li>-</li>';
							}
						}
						?>
					</ul><!-- end .cwp_pitem_options_content -->

					<ul class="cwp_pitem_options_pros">
						<h4><?php _e( 'Pros', 'wp-product-review' ); ?></h4>
						<?php
						for ( $i = 0; $i < 5; $i++ ) {
							if ( isset( $pros[ $i ] ) ) {
								echo '<li>' . $pros[ $i ] . '</li>';
							} else {
								echo '<li>-</li>';
							}
						}
						?>
					</ul><!-- end .cwp_pitem_options_pros -->

					<ul class="cwp_pitem_options_cons">
						<h4><?php _e( 'Cons', 'wp-product-review' ); ?></h4>
						<?php
						for ( $i = 0; $i < 5; $i++ ) {
							if ( isset( $cons[ $i ] ) ) {
								echo '<li>' . $cons[ $i ] . '</li>';
							} else {
								echo '<li>-</li>';
							}
						}
						?>
					</ul><!-- end .cwp_pitem_options_cons -->
				</div><!-- end .cwp_pitem_info -->
			</li><!-- end .cwp_preloaded_item -->
		<?php
		}// End while().
		wp_reset_postdata();
		die();

	}

}
