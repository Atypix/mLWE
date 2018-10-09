<?php
/**
 * Layout for the Listings Addon
 *
 * @since   2.0.0
 * @package WPPR_Pro
 */
if ( ! $results || count( $results ) == 0 ) {
	return;
}
$options = new WPPR_Options_Model();
?>
<div class="wppr_listing">
	<?php
	foreach ( $results as $review ) {

		$review_object = new WPPR_Review_Model( $review['ID'] );
		?>
		<div class="wppr_product">
			<div class="wppr_title">
				<h3 class="wppr_product_title">
					<?php echo $review_object->get_name(); ?>
				</h3>
			</div>
					<?php if ( isset( $arguments['img'] ) && $arguments['img'] == 'yes' ) : ?>

			<div class="wppr_product_image_wrap">
				<div class="wppr_product_image">
						<img src='<?php echo $review_object->get_small_thumbnail(); ?>'
							 alt="<?php echo strtolower( $review_object->get_name() ); ?>">

				</div>
			</div>
					<?php endif; ?>
			<div class="wppr_price"><?php echo $review_object->get_price_raw(); ?></div>
			<div class="wppr_button_wrap">
				<div class="wppr_button wppr_rating">
					<a href='<?php echo get_permalink( $review['ID'] ); ?>'
					   title='<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_review_text' ) ); ?>'
					   class='wppr_review'>
						<button class="wppr_btn wppr_review_btn">
							<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_review_text' ) ); ?>
						</button>
					</a>
				</div>
				<?php
				$links = $review_object->get_links();
				if ( ! empty( $review['wppr_links'] ) ) {
					foreach ( $links as $name => $link ) {
						?>
						<div class="wppr_button wppr_buynow">
							<a href='<?php echo $link; ?>'
							   title='<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_button_text' ) ); ?>'
							   rel='nofollow' target='_blank' class='wppr_cwppose_affiliate_button'>
								<button class="wppr_btn wppr_cwppose_affiliate_button_btn">
									<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_button_text' ) ); ?>
								</button>
							</a>
						</div>
						<?php
					}
				} else {
					?>
					<div class="wppr_button wppr_buynow">
						<a href='#'
						   title='<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_button_text' ) ); ?>'
						   rel='nofollow' target='_blank' class='wppr_cwppose_affiliate_button'>
							<button class="wppr_btn wppr_cwppose_affiliate_button_btn">
								<?php echo esc_attr( $options->wppr_get_option( 'cwppos_l_button_text' ) ); ?>
							</button>
						</a>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}// End foreach().
	?>
</div>
