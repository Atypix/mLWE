<?php
global $knowhere_settings;
$result = Knowhere_Widgets_Meta_Box::get_page_settings(knowhere_post_id());

if ( knowhere_is_product() || knowhere_is_product_category() || knowhere_is_product_tag() || is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') ) {
	$shop_from_page_id = $knowhere_settings['product-get-widgets-from-page'];
	if ( $shop_from_page_id ) {
		$result = Knowhere_Widgets_Meta_Box::get_page_settings($shop_from_page_id);
	}
}

if ( is_singular('knowhere_agent') || is_tax( get_object_taxonomies( 'knowhere_agent' ) ) || is_post_type_archive('knowhere_agent') ) {
	$agent_from_page_id = $knowhere_settings['agent-get-widgets-from-page'];
	if ( $agent_from_page_id ) {
		$result = Knowhere_Widgets_Meta_Box::get_page_settings($agent_from_page_id);
	}
}

if ( is_singular('knowhere_agency') || is_tax( get_object_taxonomies( 'knowhere_agency' ) ) || is_post_type_archive('knowhere_agency') ) {
	$agency_from_page_id = $knowhere_settings['agency-get-widgets-from-page'];
	if ( $agency_from_page_id ) {
		$result = Knowhere_Widgets_Meta_Box::get_page_settings($agency_from_page_id);
	}
}

if ( knowhere_is_realy_job_manager_single() || knowhere_is_realy_job_manager_tax() || knowhere_is_realy_job_manager_page() || is_tax( get_object_taxonomies( 'job_listing' ) ) || is_post_type_archive('job_listing') ) {
	$job_from_page_id = $knowhere_settings['job-get-widgets-from-page'];
	if ( $job_from_page_id ) {
		$result = Knowhere_Widgets_Meta_Box::get_page_settings($job_from_page_id);
	}
}

extract($result);
?>

<?php if ( !$footer_row_top_full_width ): ?><div class="container"><?php endif; ?>

	<div class="kw-fsection-holder">

		<?php if ( $footer_row_top_show ): ?>

			<div class="kw-fsection kw-fsection-row-top">

				<div class="kw-footer-widget-area">

					<div class="row">

						<?php if ( !empty($footer_row_top_columns_variations) ):
							$number_of_top_columns = key( json_decode( html_entity_decode ( $footer_row_top_columns_variations ), true ) );
							$columns_top_array = json_decode( html_entity_decode ( $footer_row_top_columns_variations ), true );
							?>

							<?php for ( $i = 1; $i <= $number_of_top_columns; $i++ ): ?>

								<div class="col-sm-<?php echo esc_attr($columns_top_array[$number_of_top_columns][0][$i-1]); ?>">
									<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar($get_sidebars_top_widgets[$i-1]) ) : else : knowhere_dummy_widget($i); endif; ?>
								</div>

							<?php endfor; ?>

						<?php endif; ?>

					</div>

				</div><!--/ .kw-footer-widget-area-->

			</div><!--/ .kw-fsection-->

		<?php endif; ?>

		<?php if ( $footer_row_middle_show ): ?>

			<div class="kw-fsection kw-fsection-row-middle">

				<div class="kw-footer-widget-area">

					<div class="row">

						<?php if ( !empty($footer_row_middle_columns_variations) ):
							$number_of_middle_columns = key( json_decode( html_entity_decode ( $footer_row_middle_columns_variations ), true ) );
							$columns_middle_array = json_decode( html_entity_decode ( $footer_row_middle_columns_variations ), true );
							?>

							<?php for ( $i = 1; $i <= $number_of_middle_columns; $i++ ): ?>

								<div class="col-sm-<?php echo esc_attr($columns_middle_array[$number_of_middle_columns][0][$i-1]); ?>">
									<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar($get_sidebars_middle_widgets[$i-1]) ) : else : knowhere_dummy_widget($i); endif; ?>
								</div>

						<?php endfor; ?>

						<?php endif; ?>

					</div>

				</div><!--/ .kw-footer-widget-area-->

			</div><!--/ .kw-fsection-->

		<?php endif; ?>

	</div>

<?php if ( !$footer_row_top_full_width ): ?></div><?php endif; ?>

<?php if ( !empty($knowhere_settings['footer-copyright']) ): ?>

	<!-- - - - - - - - - - - - - - Copyright Section - - - - - - - - - - - - - - - - -->

	<div class="kw-copyright">

		<div class="container">

			<?php if ( $knowhere_settings['show-footer-social-links']): ?>

				<ul class="kw-social-links">

					<?php if ( $knowhere_settings['footer-social-facebook'] ): ?>
						<li><a title="<?php echo esc_html__('Facebook', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-facebook']) ?>"><i class="fa fa-facebook"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-google-plus'] ): ?>
						<li><a title="<?php echo esc_html__('GooglePlus', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-google-plus']) ?>"><i class="fa fa-google-plus"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-twitter'] ): ?>
						<li><a title="<?php echo esc_html__('Twitter', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-twitter']) ?>"><i class="fa fa-twitter"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-linkedin'] ): ?>
						<li><a title="<?php echo esc_html__('LinkedIn', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-linkedin']) ?>"><i class="fa fa-linkedin"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-email'] ): ?>
						<li><a title="<?php echo esc_html__('Mail', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-email']) ?>"><i class="fa fa-envelope-o"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-tumblr'] ): ?>
						<li><a title="<?php echo esc_html__('Tumblr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-tumblr']) ?>"><i class="fa fa-tumblr"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-vimeo'] ): ?>
						<li><a title="<?php echo esc_html__('Vimeo', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-vimeo']) ?>"><i class="fa fa-vimeo"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-youtube'] ): ?>
						<li><a title="<?php echo esc_html__('Youtube', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-youtube']) ?>"><i class="fa fa-youtube"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-instagram'] ): ?>
						<li><a title="<?php echo esc_html__('Instagram', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-instagram']) ?>"><i class="fa fa-instagram"></i></a></li>
					<?php endif; ?>

					<?php if ( $knowhere_settings['footer-social-flickr'] ): ?>
						<li><a title="<?php echo esc_html__('Flickr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['footer-social-flickr']) ?>"><i class="fa fa-flickr"></i></a></li>
					<?php endif; ?>

				</ul>

			<?php endif; ?>

			<?php echo force_balance_tags($knowhere_settings['footer-copyright']); ?>

		</div><!--/ .container -->

	</div>

	<!-- - - - - - - - - - - - - - End of Copyright Section - - - - - - - - - - - - - - - - -->

<?php endif; ?>
