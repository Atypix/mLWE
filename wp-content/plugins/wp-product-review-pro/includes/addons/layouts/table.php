<?php
/**
 * Layout for the Comparison Table Addon
 *
 * @since   2.0.0
 * @package WPPR_Pro
 */
$options = new WPPR_Options_Model();
global $content_width;
// @codingStandardsIgnoreStart
?>
<table class='cwppose_reviews_table' id='tablesorter'>
	<thead>
	<tr>
		<th>#</th>
		<th><?php echo __( $options->wppr_get_option( 'cwppose_lang_name' ), 'wp-product-review' ); ?></th>
		<th><?php echo __( $options->wppr_get_option( 'cwppose_lang_rating' ), 'wp-product-review' ); ?></th>
		<?php if ( $options->wppr_get_option( 'cwppose_view_description' ) == 'yes' && $content_width > $min_width ) : ?>
			<th><?php echo __( $options->wppr_get_option( 'cwppose_lang_description' ), 'wp-product-review' ); ?></th>
		<?php endif;
		if ( $options->wppr_get_option( 'cwppose_view_price' ) == 'yes' ) : ?>
			<th><?php echo __( $options->wppr_get_option( 'cwppose_lang_price' ), 'wp-product-review' ); ?></th>
		<?php endif;
		if ( $options->wppr_get_option( 'cwppose_view_options' ) == 'yes' ) : ?>
			<th class="option_thead"><?php echo __( $options->wppr_get_option( 'cwppose_lang_statistics' ), 'wp-product-review' ); ?></th>
		<?php endif; ?>
		<th><?php echo __( $options->wppr_get_option( 'cwppose_lang_link' ), 'wp-product-review' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( $results ) :
		$n = 1;
		foreach ( $results as $review ) :

			$review_object = new WPPR_Review_Model( $review['ID'] );
			?>
			<tr>
				<td><?php echo $n ++; ?></td>
				<td>
					<?php if ( isset( $arguments['img'] ) && $arguments['img'] == 'yes' ) : ?>
						<h2 class="cwppose_hide wppr-comparison-title-with-image"><?php echo $review_object->get_name(); ?></h2>
						<img class="wppr-comparison-image" src='<?php echo $review_object->get_small_thumbnail(); ?>'>
					<?php else : ?>
						<h2 class="wppr-comparison-title-without-image"><?php echo $review_object->get_name(); ?></h2>
					<?php endif; ?>
				</td>
				<td>
					<?php
					$rating = $review_object->get_rating();
					if ( $rating >= 20 ) : ?>
						<div class='rating wppr-comparison-rating'>
							<div style='width:<?php echo $rating; ?>%;'><?php echo number_format( $rating, 2 ); ?></div>
						</div>
					<?php endif; ?>
					<a href='<?php echo get_permalink( $review_object->get_ID() ); ?>'
					   title='<?php echo $options->wppr_get_option( 'cwppose_lang_read_review' ); ?>' class='review'
					   style='color:<?php echo $options->wppr_get_option( 'cwppose_read_review_color' ); ?>;'><?php echo $options->wppr_get_option( 'cwppose_lang_read_review' ); ?></a>
				</td>
				<?php if ( $options->wppr_get_option( 'cwppose_view_description' ) == 'yes' && $content_width > $min_width ) : ?>
					<td><p><?php echo $review_object->get_excerpt(); ?></p></td>
				<?php endif;

				// Added by Ash/Upwork
				if ( $options->wppr_get_option( 'cwppose_view_price' ) == 'yes' ) : ?>
					<td><p><?php
							echo $review_object->get_price_raw();
							?></p></td>
				<?php endif;
				// Added by Ash/Upwork
				if ( $options->wppr_get_option( 'cwppose_view_options' ) == 'yes' ) : ?>
					<td>
						<?php
						$options_rates = $review_object->get_options();
						if ( ! empty( $options_rates ) ) {
							foreach ( $options_rates as $option ) {
								/* Sett color for option bar by value */
								if ( $option['value'] > 0 && $option['value'] <= 25 ) {
									$option_color = $options->wppr_get_option( 'cwppos_rating_weak' );
								} elseif ( $option['value'] > 25 && $option['value'] <= 50 ) {
									$option_color = $options->wppr_get_option( 'cwppos_rating_notbad' );
								} elseif ( $option['value'] > 50 && $option['value'] <= 75 ) {
									$option_color = $options->wppr_get_option( 'cwppos_rating_good' );
								} elseif ( $option['value'] > 75 ) {
									$option_color = $options->wppr_get_option( 'cwppos_rating_very_good' );
								}
								?>
								<div class='option_group cwppose_clearfix'>
									<div class='option'><?php echo $option['name']; ?></div>
									<?php if ( $option['value'] > 49 ) { ?>
										<div class='bar'>
											<div class='grade'
											     style='width:<?php echo $option['value']; ?>%; background:<?php echo $option_color; ?>;'>
												<span><?php echo $option['value']; ?></span></div>
										</div>
									<?php } else { ?>
										<div class='bar'>
											<div class='grade'
											     style='width:<?php echo $option['value']; ?>%; background:<?php echo $option_color; ?>;'></div>
											<span><?php echo $option['value']; ?></span></div>
									<?php } ?>
								</div><!--/div.option_group .cwppose_clearfix-->
								<?php
							}
						}
						?>
					</td>
				<?php endif; ?>
				<td>
					<?php
					$links = $review_object->get_links();
					if ( ! empty( $links ) ) {
						$name = key( $links );
						$link = reset( $links );
						?>
						<a href='<?php echo $link; ?>' title='<?php echo $name; ?>' rel='nofollow' target='_blank'
						   class='cwppose_affiliate_button'>
							<?php
							if ( isset( $arguments['button'] ) ) {
								echo $arguments['button'];
							} elseif ( $options->wppr_get_option( 'cwppose_lang_button' ) ) {
								echo $options->wppr_get_option( 'cwppose_lang_button' );
							} elseif ( $name ) {
								echo $name;
							} else {
								echo __( 'Buy Now!', 'wp-product-review' );
							}
							?>
						</a>
						<?php
					} else {
						?>
						<a href='' title='<?php echo __( 'Affiliate URL', 'wp-product-review' ); ?>' rel='nofollow'
						   target='_blank' class='cwppose_affiliate_button'>
							<?php
							if ( isset( $arguments['button'] ) ) {
								echo $arguments['button'];
							} elseif ( $options->wppr_get_option( 'cwppose_lang_button' ) ) {
								echo $options->wppr_get_option( 'cwppose_lang_button' );
							} elseif ( $name ) {
								echo $name;
							} else {
								echo __( 'Buy Now!', 'wp-product-review' );
							}
							?>
						</a>
						<?php
					}
					?>
				</td>
			</tr>
		<?php endforeach;
	endif; ?>
	</tbody>
</table><!--/table.cwppose_reviews_table-->
<?php
// @codingStandardsIgnoreEnd
?>
