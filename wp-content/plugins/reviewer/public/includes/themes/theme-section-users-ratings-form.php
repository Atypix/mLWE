<?php if (! $this->is_users_rating_disabled() && $this->enable_rating() ): ?>
<?php

        if (goach_review_accepted() == true) {
        	
       
        
?>
<div class="rwp-ratings-form-wrap" id="rwp-ratings-form-<?php echo $this->post_id ?>-<?php $this->review_field('review_id') ?>">
	
	<span 
        class="rwp-ratings-form-label" 
        style="color: <?php $this->template_field('template_users_reviews_label_color') ?>; 
        	   font-size: <?php $this->template_field('template_users_reviews_label_font_size') ?>px; 
        	   line-height: <?php $this->template_field('template_users_reviews_label_font_size') ?>px;"><?php $this->template_field('template_message_to_rate') ?></span>

	<div class="rwp-ratings-form">

		<?php  
			$user   		= wp_get_current_user();
			//$rating_options = $this->review_field('review_user_rating_options', true);
			$rating_options = $this->template_field('template_user_rating_options', true);
			$text_color		= $this->template_field('template_text_color', true);
			$font_size 		= $this->template_field('template_box_font_size', true);
		?>

		
		<?php if( in_array( 'rating_option_avatar', $rating_options ) ): ?>
		<div class="rwp-rating-form-user-info">
			
			<?php echo get_avatar( $user->ID, 50 ); ?>

			<?php if( in_array( 'rating_option_name', $rating_options ) ): ?>
				<span style="line-height: <?php echo $font_size ?>px; font-size: <?php echo $font_size ?>px;"><?php echo $user->display_name ?></span>
			<?php endif; ?>

		</div> <!-- /user-info -->
		<?php endif; ?>
	
		<div class="rwp-rating-form-content <?php if( !in_array( 'rating_option_avatar', $rating_options ) ) echo 'rwp-no-avatar'; ?>">

			<input type="hidden" name="rwp-ur[post-id]" value="<?php echo $this->post_id ?>" />
			<input type="hidden" name="rwp-ur[user-id]" value="<?php echo $user->ID; ?>" />
			<input type="hidden" name="rwp-ur[review-id]" value="<?php $this->review_field('review_id') ?>" />
			<input type="hidden" name="rwp-ur[mode]" value="<?php $this->preferences_field('preferences_rating_mode') ?>" />
			<input type="hidden" name="rwp-ur[template]" value="<?php $this->review_field('review_template') ?>" />
			
			<?php if ( !empty( $this->branch ) && $this->branch == 'form' ): ?>
				<input type="hidden" name="rwp-ur[branch]" value="on" />
			<?php endif ?>
			
			<?php if( ($user instanceof WP_User) && $user->ID == 0 && in_array( 'rating_option_name', $rating_options )  ): ?>
			<p>
				<input type="text" name="rwp-ur[name]" value="" placeholder="<?php _e( 'Write your name', $this->plugin_slug); ?>" style="font-size: <?php echo $font_size ?>px; color: <?php echo $text_color ?>;">
			</p>
			<?php endif; ?>

			<?php if( ($user instanceof WP_User) && $user->ID == 0 && in_array( 'rating_option_email', $rating_options )  ): ?>
			<p>
				<input type="text" name="rwp-ur[email]" value="" placeholder="<?php _e( 'Write your email', $this->plugin_slug); ?>" style="font-size: <?php echo $font_size ?>px; color: <?php echo $text_color ?>;">
			</p>
			<?php endif; ?>

			<?php if( in_array( 'rating_option_title', $rating_options ) ): ?>
			<p>
				<input type="text" name="rwp-ur[review_title]" value="" placeholder="<?php _e( 'Write your review title', $this->plugin_slug); ?>" style="font-size: <?php echo $font_size ?>px; color: <?php echo $text_color ?>;">
			</p>
			<?php endif; ?>

			<?php if( in_array( 'rating_option_comment', $rating_options ) ): ?>
			<p>
				<textarea name="rwp-ur[comment]" placeholder="<?php _e( 'Write your review', $this->plugin_slug); ?>" style="font-size: <?php echo $font_size ?>px; color: <?php echo $text_color ?>;"></textarea>
			</p>
			<?php endif; ?>
			
			<?php 
				$attachments = $this->preferences_field('preferences_user_review_images', true);
				if( $attachments['field_enabled'] ):
			?>
			<div class="rwp-form-upload-images">
	            <div class="rwp-dropzone dropzone" id="<?php echo $this->vueID ?>-dropzone">
	                <div class="fallback"><p><?php _e('Your browser does not support images upload. Please choose a modern one', $this->plugin_slug) ?></p></div><!-- dropzone fallback -->
	            </div>
	        </div><!-- /form-field -->
		    <?php endif; ?>

			<?php if( in_array( 'rating_option_score', $rating_options ) ) {

				$mode = $this->preferences_field('preferences_rating_mode', true);

				switch ( $mode ) {
					case 'five_stars':
						$step		= $this->preferences_field('preferences_step', true);
						if( floatval( $step ) == 1 ){
							echo $this->get_stars_form2( $this->review_field('review_id', true) );
						} else {
							echo $this->get_stars_form( $this->review_field('review_id', true) );
						}
						break;

					case 'full_five_stars':
						
						$order 		= $this->template_field('template_criteria_order', true);
						$criteria 	= $this->template_field('template_criterias', true);
						$order		= ( $order == null ) ? array_keys( $criteria) : $order;
						$min 		= $this->template_field('template_minimum_score', true);
						$max 		= $this->template_field('template_maximum_score', true);
						$step		= $this->preferences_field('preferences_step', true);

						echo '<ul class="rwp-scores-sliders rwp-with-stars">';
						
						foreach ($order as $i) {
							echo '<li>';
								echo '<label class="rwp-lab">'. $criteria[$i] .'</label>';
								if( floatval( $step ) == 1 ){
									echo $this->get_stars_form2( $this->review_field('review_id', true), 5, true, $i );
								} else {
									echo $this->get_stars_form( $this->review_field('review_id', true), 5, true, $i );
								}
							echo '</li>';
						}

						echo '</ul>';
						break;

					default:

						$order 		= $this->template_field('template_criteria_order', true);
						$criteria 	= $this->template_field('template_criterias', true);
						$order		= ( $order == null ) ? array_keys( $criteria) : $order;
						$min 		= $this->template_field('template_minimum_score', true);
						$max 		= $this->template_field('template_maximum_score', true);
						$step		= $this->preferences_field('preferences_step', true);

						echo '<ul class="rwp-scores-sliders">';
						
						foreach ($order as $i) {
							echo '<li>';
								echo '<label>'. $criteria[$i] .'</label>';
								echo '<input type="text" name="rwp-ur[rating][]" placeholder="'. $min .'" style="font-size: '. $font_size .'px; color: '.$text_color .';"/>';
								echo '<div class="rwp-slider" data-step="'. $step .'" data-val="'.$min .'" data-min="'. $min .'" data-max="'. $max .'" data-index="'. $i .'"></div>';
							echo '</li>';
						}

						echo '</ul>';
						break;
				}


			} // if rating option ?>

			<?php  $captcha = $this->preferences_field('preferences_users_reviews_captcha', true );
				   if( isset( $captcha['enabled'] ) && isset( $captcha['site_key'] ) && isset( $captcha['secret_key'] ) && $captcha['enabled'] && !empty( $captcha['site_key'] ) && !empty( $captcha['secret_key'] ) ): ?>
			<p>
				<div class="rwp-recaptcha" data-sitekey="<?php echo $captcha['site_key'] ?>" data-grcid="" id="<?php echo 'rwp-recaptcha-' . $this->post_id . '-' . $this->review_field('review_id', true) ?>"></div>
			</p>
			<?php endif; ?>

			<p class="rwp-submit-wrap">
				<input v-on:click.prevent="submitUserReview"  type="button" value="<?php _e('Submit', $this->plugin_slug) ?>" style="background-color: <?php $this->template_field('template_users_score_box_color') ?>; " />
				<span class="rwp-loader"></span><!-- /loader-->	
			</p>

		</div> <!-- /rating-form-content -->
		
	</div><!-- /rating-form -->

	<div class="rwp-notification --rwp-notice-with-error"></div>

</div> <!-- /ratings-form-wrap -->

<?php 	} else {
	echo 'Vous devez avoir participé à l\'activité pour voter !';
} ?>
<?php endif ?>