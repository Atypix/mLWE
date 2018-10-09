<?php $links = $this->review_field( 'review_custom_links' , true ); if ( !empty( $links ) ): ?>
<div class="rwp-box__links">
<?php
	$color 			= $this->template_field('template_total_score_box_color', true);
	$nofollow 		= $this->preferences_field( 'preferences_nofollow', true );
	$has_nofollow 	=  in_array('box_custom_links', $nofollow);
?>
<?php foreach ($this->review_field( 'review_custom_links' , true ) as $link): ?>

	<a href="<?php echo $link['url'] ?>" class="rwp-box__link" style="border-color: <?php echo $color ?>" <?php if( $has_nofollow ) { echo ' rel="nofollow" '; }?> <?php echo (isset( $this->review['review_open_custom_links_out'] ) &&  $this->review['review_open_custom_links_out'] == 'yes') ? 'target="_blank"' : ''; ?>>
		<span class="rwp-box__link-label"><?php echo $link['label'] ?></span>
		<i class="dashicons dashicons-star-filled rwp-box__link-icon" style="background-color: <?php echo $color ?>"></i>
	</a>

<?php endforeach ?>

</div> <!-- /custom-links -->
<?php endif ?>
