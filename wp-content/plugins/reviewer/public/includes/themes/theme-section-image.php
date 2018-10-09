<?php if ($has_img): ?>
<div class="rwp-review-image">
	<?php 
	$url = $this->review_field('review_image_url', true); 

	if( $this->review_field('review_use_featured_image', true) == 'no' ) {
		$img = $this->review_field('review_image', true); 
	} else {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $this->post_id ), 'full' );
		$img = $thumb[0];
	}

	$this->snippets->add('image', $img);

	if( !empty( $url ) ): ?>

		<a 
			class="rwp-image-link"
			href="<?php $this->review_field('review_image_url') ?>" 
			style="background-image: url(<?php echo $img; ?>);"
			<?php  
				$nofollow = $this->preferences_field( 'preferences_nofollow', true );
				if( in_array('box_image', $nofollow) ) {
					echo ' rel="nofollow" ';
				}
			?>
		></a>

	<?php else: ?>

		<span style="background-image: url(<?php echo $img; ?>);"></span>

	<?php endif ?>
</div> <!-- /review-image -->
<?php endif ?>

<?php if ( !$has_img && $this->template_field('template_theme', true) == 'rwp-theme-8' ): ?>
	<div class="rwp-review-image"><span></span></div> <!-- /review-image -->
<?php endif ?>