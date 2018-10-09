
<!-- - - - - - - - - - - - - - Header Top Bar - - - - - - - - - - - - - - - - -->

<?php global $knowhere_settings, $knowhere_config; ?>

<?php

$show_top_bar = $knowhere_settings['header-type-1-show-top-bar'];
$meta_show_top_bar = mad_meta('knowhere_page_show_topbar');

if ( $meta_show_top_bar == 1 ) {
	$show_top_bar = false;
}

?>

<?php if ( $show_top_bar ): ?>

	<div class="kw-top-bar">

		<ul class="kw-hr-list">

			<?php if ( defined('Knowhere_Woo_Config') ): ?>
				<?php if ( $knowhere_settings['header-type-1-show-location'] ): ?>

					<?php if ( function_exists('wc_get_customer_default_location')): ?>

						<?php $default_location = wc_get_customer_default_location(); ?>

						<?php if ( $default_location ): ?>

							<li>
								<div class="kw-current-location">
									<span class="lnr icon-map-marker"></span><?php esc_html_e('Your location', 'knowherepro') ?>: <b><?php echo sprintf('%s', $default_location['country']) ?></b>
								</div><!--/ .kw-current-location -->
							</li>

						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $knowhere_settings['show-header-social-links']): ?>

				<li>

					<ul class="kw-social-links">

						<?php if ( $knowhere_settings['header-social-linkedin']): ?>
							<li><a title="<?php echo esc_html__('LinkedIn', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-linkedin']) ?>"><i class="fa fa-linkedin"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-tumblr']): ?>
							<li><a title="<?php echo esc_html__('Tumblr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-tumblr']) ?>"><i class="fa fa-tumblr"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-vimeo']): ?>
							<li><a title="<?php echo esc_html__('Vimeo', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-vimeo']) ?>"><i class="fa fa-vimeo"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-youtube']): ?>
							<li><a title="<?php echo esc_html__('Youtube', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-youtube']) ?>"><i class="fa fa-youtube"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-facebook']): ?>
							<li><a title="<?php echo esc_html__('Facebook', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-facebook']) ?>"><i class="fa fa-facebook"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-twitter']): ?>
							<li><a title="<?php echo esc_html__('Twitter', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-twitter']) ?>"><i class="fa fa-twitter"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-instagram']): ?>
							<li><a title="<?php echo esc_html__('Instagram', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-instagram']) ?>"><i class="fa fa-instagram"></i></a></li>
						<?php endif; ?>

						<?php if ( $knowhere_settings['header-social-flickr']): ?>
							<li><a title="<?php echo esc_html__('Flickr', 'knowherepro') ?>" href="<?php echo esc_url($knowhere_settings['header-social-flickr']) ?>"><i class="fa fa-flickr"></i></a></li>
						<?php endif; ?>

					</ul>

				</li>

			<?php endif; ?>

		</ul><!--/ .kw-hr-list -->

		<ul class="kw-hr-list">
			<li style="color:white;"><strong>Service client 7/7 - de 9h à 18h : 06 32 16 42 24 </strong></li>
			<?php if ( $knowhere_settings['header-type-1-show-login'] ): ?>
				<?php knowhere_get_login_links(); ?>
			<?php endif; ?>

			<?php if ( defined('ICL_LANGUAGE_CODE') ): ?>
				<?php if ( $knowhere_settings['header-type-1-show-language'] ): ?>
					<?php echo Knowhere_WC_WPML_Config::wpml_header_languages_list(); ?>
				<?php endif; ?>
			<?php endif; ?>

		</ul><!--/ .kw-hr-list -->

	</div><!--/ .kw-top-bar-->

	<!-- - - - - - - - - - - - - - End of Header Top Bar - - - - - - - - - - - - - - - - -->

<?php endif; ?>

<!-- - - - - - - - - - - - - - Header Section - - - - - - - - - - - - - - - - -->

<?php
$sticky_class = 'kw-sticky';
if ( knowhere_is_realy_job_manager_tax() || is_post_type_archive('job_listing') || knowhere_job_listing_has_shortcode_jobs() ) {

	if ( knowhere_is_realy_job_manager_tax() ) {

		if ( $knowhere_config['sidebar_position'] !== 'kw-no-sidebar' ) {

		} else {
			$sticky_class = '';
		}

	} else {
		$sticky_class = '';
	}

}
?>

<div class="kw-hsection <?php echo sanitize_html_class($sticky_class) ?>">

	<!-- - - - - - - - - - - - - - Logo - - - - - - - - - - - - - - - - -->

	<?php echo knowhere_logo(); ?>

	<!-- - - - - - - - - - - - - - End of Logo - - - - - - - - - - - - - - - - -->

	<div class="kw-hcontent">

		<!-- - - - - - - - - - - - - - Navigation - - - - - - - - - - - - - - - - -->

		<nav class="kw-nav-wrap"><?php echo Knowhere_Helper::main_navigation(); ?></nav>

		<!-- - - - - - - - - - - - - - End of Navigation - - - - - - - - - - - - - - - - -->

		<?php  if ( knowhere_is_shop_installed() ) : ?>

			<?php if ( knowhere_is_realy_woocommerce_page(false) || knowhere_is_shop() || knowhere_is_product_category() || knowhere_is_product() || knowhere_is_product_tax() ) : ?>

				<?php if ( $knowhere_settings['header-type-1-show-link-to-cart'] ): ?>

					<?php
					global $woocommerce;
					$cart_url = wc_get_cart_url();
					?>

					<?php if ( $cart_url ): ?>
						<a class="kw-cart-btn" href="<?php echo esc_url($cart_url) ?>"><span class="lnr icon-cart"></span></a>
					<?php endif; ?>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

		<?php
		$show_search_and_login = $knowhere_settings['header-type-1-show-search-and-login'];
		$meta_show_search_and_login = mad_meta('knowhere_header_show_search_and_login');

		if ( !empty($meta_show_search_and_login) && is_array($meta_show_search_and_login) ) {
			$show_search_and_login = array_unique(array_merge($show_search_and_login, $meta_show_search_and_login));
		}

		?>

		<?php if ( in_array('search', $show_search_and_login) ): ?>

			<!-- - - - - - - - - - - - - - Search Form - - - - - - - - - - - - - - - - -->

			<button class="kw-search-btn kw-search-box-opener"><span class="lnr icon-magnifier"></span></button>

			<!-- - - - - - - - - - - - - - End of Search Form - - - - - - - - - - - - - - - - -->

		<?php endif; ?>

		<?php if ( in_array('login', $show_search_and_login) ): ?>

			<?php knowhere_get_login_links(); ?>

		<?php endif; ?>

		<?php if ( class_exists('WP_Job_Manager') ): ?>

			<?php if ( $knowhere_settings['header-type-1-show-button-add-listing'] ):

				$job_form_page_id_url = '';
				$job_form_page_id = get_option( 'job_manager_submit_job_form_page_id', false ) ;

				if ( !empty($job_form_page_id ) ) {
					$job_form_page_id_url = get_permalink($job_form_page_id);
				}
				?>

				<?php if ( !empty($job_form_page_id_url) ): ?>

					<a href="<?php echo esc_url($job_form_page_id_url) ?>" class="kw-btn-medium kw-border-button">
						<?php echo knowhere_name_of_listing() ?>
					</a>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

	</div><!--/ .kw-hcontent -->

</div><!--/ .kw-hsection-->

<!-- - - - - - - - - - - - - - End of Header Section - - - - - - - - - - - - - - - - -->