<div class="rwp-rl-wrapper <?php echo ($layout == 'inline') ? 'rwp-rl-inline' : ''; ?>">

	<?php if(!empty( $title )): ?><span class="rwp-rl-title"><?php echo $title; ?></span><?php endif; ?>
	
	<?php for ( $i = 0; $i < $count; $i++ ): ?>
	
	<div class="rwp-rl-review <?php echo ( ( ! isset( $reviews[ $i ]['review_image'] ) || empty( $reviews[ $i ]['review_image'] ) ) && $has_image) ? 'rwp-rl-no-image' : ''; ?>">
		
		<span class="rwp-rl-ranking"><?php echo $i + 1; ?></span>
		
		<?php 
		$img = $reviews[ $i ]['review_image'];
		if( $reviews[ $i ]['review_use_featured_image'] == 'no' ) {
			$has_img = (!empty( $img ) ) ? true : false;
		} else {
			$has_img =  has_post_thumbnail( $reviews[ $i ]['review_post_id'] );
			$thumb 	 = wp_get_attachment_image_src( get_post_thumbnail_id( $reviews[ $i ]['review_post_id']), 'full' );
			$img 	 = $thumb[0];
		}

		if( $has_img ): ?>

		<a class="rwp-rl-img-link" href="<?php echo $reviews[ $i ]['review_permalink']; ?>">
			<div class="rwp-rl-review-image" style="background-image: url(<?php echo $img ?>);"></div>
		</a>

		<?php endif; ?>

		<div class="rwp-rl-review-info">

			<?php 
				$review_title = '';
				$review_title_option = isset( $reviews[ $i ]['review_title_options'] ) ? $reviews[ $i ]['review_title_options'] : '';

	                switch ( $review_title_option ) {
	                    case 'hidden':
	                        break;

	                    case 'post_title':
	                    	$post_id = $reviews[ $i ]['review_post_id'];
	                       	$review_title = get_the_title( $post_id );
	                        break;
	                    
	                    default:
	                        $review_title = $reviews[ $i ]['review_title'];
	                        break;
	                } 

			?>
			
			<a href="<?php echo $reviews[ $i ]['review_permalink']; ?>" class="rwp-rl-review-title"><?php echo $review_title ?></a>

			<div class="rwp-rl-scores-wrap">

			    <div class="rwp-rl-total-score-wrap">			        
			    	<span class="rwp-rl-total-score-label"><?php echo $reviews[ $i ][ 'review_score' ]['label'] ?></span> 
			        <span class="rwp-rl-total-score" style="color:<?php echo $reviews[ $i ][ 'review_color' ] ?>"><?php echo ( $stars == 'yes') ? RWP_Reviewer::get_stars( $reviews[ $i ][ 'review_score' ]['overall'], $templates[ $reviews[ $i ][ 'review_template' ] ] ) :  $reviews[ $i ][ 'review_score' ]['overall'] ?></span>
			    </div><!--/total-score-->

			    <?php if ( isset( $reviews[ $i ][ 'review_custom_tabs' ] ) && !empty( $reviews[ $i ][ 'review_custom_tabs' ] ) ): ?>

				    <?php foreach ($reviews[ $i ][ 'review_custom_tabs' ] as $tab):  if( empty($tab['tab_value']) ) continue; ?>
				    	
				    	<div class="rwp-rl-total-score-wrap rwp-rl-tab">			        
					    	<span class="rwp-rl-total-score-label"><?php echo $tab['tab_label'] ?></span> 
					       
							<?php if ( empty( $tab['tab_link'] ) ): ?>
								<span class="rwp-rl-total-score"><?php echo $tab['tab_value'] ?></span>
							<?php else: ?>
								<a href="<?php echo $tab['tab_link'] ?>" class="rwp-rl-total-score"><?php echo $tab['tab_value'] ?></a>
								
							<?php endif ?>
					    </div><!--/tab-->

				    <?php endforeach ?>
			    	
			    <?php endif ?>

			</div><!--/scores-wrap-->

		</div> <!-- rwp-rl-review-info -->

	</div> <!-- rwp-rl-review -->

	<?php endfor; ?>

</div> <!-- rwp-reviews-list-wrapper -->