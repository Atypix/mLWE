<?php 
if( isset( $review['review_use_featured_image'] ) ) {
	if ( $review['review_use_featured_image'] == 'no' ) {
		$image_url = ( isset( $review['review_image'] ) && !empty( $review['review_image'] ) ) ? $review['review_image'] : '';
	} else {
		if( has_post_thumbnail( $review['review_post_id'] ) ) {
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $review['review_post_id'] ), 'full' );
			$image_url = $thumb[0];
		} else {
			$image_url =  '';
		}
	}
} else {
	$image_url = ( isset( $review['review_image'] ) && !empty( $review['review_image'] ) ) ? $review['review_image'] : '';
}

// Image link
if( isset( $review['review_image_url'] ) && !empty( $review['review_image_url'] ) ) {
	$image_link = esc_url( $review['review_image_url'] );
} else {
	$image_link = '';
}
?>
<?php if ( !empty( $image_url ) ): ?>
	<?php if( !empty( $image_link ) ): ?>
	<a class="rwp-image-link-url" href="<?php echo $image_link ?>">
	<?php endif; ?>
		<div class="rwp-image" style="background-image: url(<?php echo $image_url ?>);"></div><!-- /table image --> 
	<?php if( !empty( $image_link ) ): ?>
	</a>
	<?php endif; ?>
<?php endif ?>
