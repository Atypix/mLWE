<?php global $post; ?>

<?php
$data_output = '';
$terms = get_the_terms(get_the_ID(), 'job_listing_type');

$termString = '';
if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) {
	$firstTerm = $terms[0];
	if ( ! $firstTerm == null ) {
		$term_id = $firstTerm->term_id;
		$data_output .= 'data-icon="' . knowhere_get_term_icon_url($term_id) .'"';
		$count = 1;
		foreach ( $terms as $term ) {
			$termString .= $term->name;
			if ( $count != count($terms) ) {
				$termString .= ', ';
			}
			$count++;
		}
	}
}

?>

<div class="single_job_listing" 
	 data-latitude="<?php echo get_post_meta($post->ID, 'geolocation_lat', true); ?>"
	 data-longitude="<?php echo get_post_meta($post->ID, 'geolocation_long', true); ?>"
	 data-categories="<?php echo esc_attr( $termString ); ?>"
	 data-img="<?php echo esc_attr( knowhere_get_post_image_src( $post->ID, 'thumbnail' ) ); ?>"
	<?php echo sprintf('%s', $data_output); ?>>

	<meta itemprop="title" content="<?php echo esc_attr( $post->post_title ); ?>" />

	<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="job-manager-info"><?php esc_html_e( 'This listing has expired.', 'knowherepro' ); ?></div>
	<?php else : ?>

		<div class="kw-grid">

			<div class="kw-grid-item kw-column-content">

				<?php knowhere_job_single_gallery( array( 'echo' => true ) ); ?>

				<?php
				/**
				 * single_job_listing_start hook
				 *
				 * @hooked job_listing_meta_display - 20
				 * @hooked job_listing_company_display - 30
				 */
				do_action( 'single_job_listing_start' );
				?>

				<div class="job_description" itemprop="description">
					<?php echo apply_filters( 'the_content', get_the_content() ); ?>
				</div>

				<?php
				/**
				 * single_job_listing_end hook
				 */
				do_action( 'single_job_listing_end' );
				?>

			</div><!--/ .kw-column-content-->

			<div class="kw-grid-item kw-column-sidebar">

				<?php if ( is_active_sidebar( 'listing_sidebar' ) ) : ?>
					<div class="kw-listing-sidebar">
						<?php dynamic_sidebar('listing_sidebar'); ?>
					</div>
				<?php endif; ?>

			</div><!--/ .kw-column-sidebar-->

		</div><!--/ .kw-grid-->

	<?php endif; ?>

</div><!--/ .single_job_listing-->
