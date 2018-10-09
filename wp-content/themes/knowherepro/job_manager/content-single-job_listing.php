<?php global $post, $knowhere_config, $knowhere_settings; ?>

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

if ( $knowhere_settings['job-type-fields'] == 'property' ) : ?>

	<?php $video = get_post_meta( get_the_ID(), '_company_video', true ); ?>

	<?php if ( !empty($knowhere_config['job-single-style']) && $knowhere_config['job-single-style'] == 'kw-style-4' ): ?>

		<div class="kw-listing-item kw-single kw-type-4">

			<div class="kw-tabs kw-default">

				<ul class="kw-tabs-nav">

					<?php if ( knowhere_job_single_gallery() ): ?>
						<li><a class="kw-active" href="#tab-photos"><?php esc_html_e('Photos', 'knowherepro') ?></a></li>
					<?php endif; ?>

					<?php if ( !empty( $video ) ): ?>
						<li><a href="#tab-video"><?php esc_html_e('Video', 'knowherepro') ?></a></li>
					<?php endif; ?>

					<li><a href="#tab-map"><?php esc_html_e('Map', 'knowherepro') ?></a></li>

				</ul><!--/ .kw-tabs-nav-->

				<div class="kw-tabs-container">

					<?php if ( knowhere_job_single_gallery() ): ?>
						<div id="tab-photos" class="kw-tab kw-active">

							<?php knowhere_job_single_gallery( array( 'echo' => true ) ); ?>

							<?php knowhere_property_data_features_output(); ?>

						</div><!--/ .kw-tab-->
					<?php endif; ?>

					<?php if ( !empty( $video ) ): ?>
						<div id="tab-video" class="kw-tab">

							<div class="kw-iframe-wrap">
								<?php the_company_video() ?>
							</div>

							<?php knowhere_property_data_features_output(); ?>

						</div><!--/ .kw-tab-->
					<?php endif; ?>

					<div id="tab-map" class="kw-tab">

						<div id="kw-listings-gmap" class="kw-listing-widget-gmap"></div>

						<?php knowhere_property_data_features_output(); ?>

					</div><!--/ .kw-tab-->

				</div><!--/ .kw-tabs-container-->

			</div><!--/ .kw-tabs-->

		</div><!--/ .kw-listing-item-->

	<?php endif; ?>

<?php endif; ?>

<?php if ( !empty($knowhere_config['job-single-style']) && $knowhere_config['job-single-style'] == 'kw-style-5' ): ?>

	<div class="kw-listing-item kw-single kw-type-5">

		<div class="kw-tabs kw-default">

			<ul class="kw-tabs-nav">

				<?php if ( knowhere_job_single_gallery() ): ?>
					<li><a class="kw-active" href="#tab-images"><?php esc_html_e('Images', 'knowherepro') ?></a></li>
				<?php endif; ?>

				<?php if ( get_the_job_location($post) ): ?>
					<li><a href="#tab-location"><?php esc_html_e('Location', 'knowherepro') ?></a></li>
				<?php endif; ?>

			</ul><!--/ .kw-tabs-nav-->

			<div class="kw-tabs-container">

				<?php if ( knowhere_job_single_gallery() ): ?>

					<div id="tab-images" class="kw-tab kw-active">
						<?php knowhere_job_single_gallery( array( 'echo' => true ) ); ?>
					</div><!--/ .kw-tab-->

				<?php endif; ?>

				<?php if ( get_the_job_location($post) ): ?>

					<div id="tab-location" class="kw-tab">

						<div id="kw-listings-gmap" class="kw-listing-widget-gmap"></div>

						<div class="kw-listing-item-meta kw-sm-table-row kw-xs-small-offset">

							<div class="col-sm-8">
								<ul class="kw-listing-item-data kw-icons-list">

									<?php if ( get_the_job_location($post) ): ?>
										<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
									<?php endif; ?>

								</ul>
							</div>

							<div class="col-sm-4 kw-right-edge">

								<?php
								$geolocation_lat  = get_post_meta( $post->ID, 'geolocation_lat', true );
								$geolocation_long = get_post_meta( $post->ID, 'geolocation_long', true );
								$get_directions_link = '';
								if ( ! empty( $geolocation_lat ) && ! empty( $geolocation_long ) && is_numeric( $geolocation_lat ) && is_numeric( $geolocation_long ) ) {
									$get_directions_link = '//maps.google.com/maps?daddr=' . $geolocation_lat . ',' . $geolocation_long;
								}
								?>

								<?php if ( !empty($get_directions_link) ): ?>
									<a href="<?php echo esc_url($get_directions_link); ?>" class="kw-get-directions" target="_blank">
										<span class="lnr icon-road-sign"></span> <?php esc_html_e( 'Get Directions', 'knowherepro' ); ?>
									</a>
								<?php endif; ?>

							</div>

						</div>

					</div><!--/ .kw-tab-->

				<?php endif; ?>

			</div><!--/ .kw-tabs-container-->

		</div><!--/ .kw-tabs-->

	</div><!--/ .kw-listing-item-->

<?php endif; ?>
	<?php 
	$adresse = get_post_meta( $post->ID, '_job_location', true );
	if(!empty($adresse)) {
		preg_match("/^(.*)(\d{5})(.*)$/", $adresse, $elems);
	}  
	$product_id = get_post_meta( $post->ID, '_id_product', true );
	
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );

	function next_day($day_number)
	{
	  for ($i = 2; $i <= 8; $i++)
	  {
	    $next_day = mktime(0,0,0, date("m"), date("d")+$i, date("Y"));
	    if(date("w",$next_day)==$day_number)
	    {
	      $XDate = getdate($next_day);
	      $next_day_fund = sprintf('%02d', $XDate['mday']).'-'.sprintf('%02d', 
	      $XDate['mon']).'-'.sprintf('%02d', $XDate['year']);
	    }
	  }
	  return $next_day_fund;
	}

	
	foreach ($aDates as $date_activite) {
		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
				$startDate = $date_activite['from'];
			} 
		} else {
			if ($date_activite['type'] == "time:1") $startDate = date('d-m-Y', strtotime('next monday'));
			if ($date_activite['type'] == "time:2") $startDate = date('d-m-Y', strtotime('next tuesday'));
			if ($date_activite['type'] == "time:3") $startDate = date('d-m-Y', strtotime('next wednesday'));
			if ($date_activite['type'] == "time:4") $startDate = date('d-m-Y', strtotime('next thursday'));
			if ($date_activite['type'] == "time:5") $startDate = date('d-m-Y', strtotime('next friday'));
			if ($date_activite['type'] == "time:6") $startDate = date('d-m-Y', strtotime('next saturday'));
			if ($date_activite['type'] == "time:7") $startDate = date('d-m-Y', strtotime('next sunday'));
		}
		
	}

	$user_data = get_userdata($post->post_author);
	$username = $user_data->display_name;
	

if ($startDate) {
	?>

<div class="single_job_listing kw-listing-item-info" itemscope itemtype="http://schema.org/Event"
	 data-latitude="<?php echo get_post_meta($post->ID, 'geolocation_lat', true); ?>"
	 data-longitude="<?php echo get_post_meta($post->ID, 'geolocation_long', true); ?>"
	 data-categories="<?php echo esc_attr( $termString ); ?>"
	 data-img="<?php echo esc_attr( knowhere_get_post_image_src( $post->ID, 'thumbnail' ) ); ?>"
	<?php echo sprintf('%s', $data_output); ?>>
<?php 
} else {
	?>
<div class="single_job_listing kw-listing-item-info"
	 data-latitude="<?php echo get_post_meta($post->ID, 'geolocation_lat', true); ?>"
	 data-longitude="<?php echo get_post_meta($post->ID, 'geolocation_long', true); ?>"
	 data-categories="<?php echo esc_attr( $termString ); ?>"
	 data-img="<?php echo esc_attr( knowhere_get_post_image_src( $post->ID, 'thumbnail' ) ); ?>"
	<?php echo sprintf('%s', $data_output); ?>>
	<?php
}
?>
	<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />

	<div itemprop="location" itemscope itemtype="http://schema.org/Place">
		<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" >
			<meta itemprop="name" content="<?php echo esc_attr( $post->post_title ); ?>" />
			<meta itemprop="postalCode" content="<?php echo get_post_meta( $post->ID, 'geolocation_postcode', true ); ?>" />
			<meta itemprop="streetAddress" content="<?php echo get_post_meta( $post->ID, 'geolocation_street', true ); ?>" />
			<meta itemprop="addressLocality" content="<?php echo get_post_meta( $post->ID, 'geolocation_city', true ); ?>" />
		</div>
	</div>
	<div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
		<meta itemprop="priceCurrency" content="EUR"/>
		<meta itemprop="price" content="<?php echo  get_post_meta( $post->ID, '_job_price_range_min', true ); ?>"/>
		<meta itemprop="lowprice" content="<?php echo  get_post_meta( $post->ID, '_job_price_range_min', true ); ?>"/>
		<meta itemprop="availability" content="In stock"/>
		<meta itemprop="offerCount" content="<?php echo  get_post_meta( $post->ID, '_nb_personnes_max', true ); ?>"/>
		<meta itemprop="url" content="<?php echo  get_permalink(); ?>"/>
		<meta itemprop="validFrom" content="<?php echo $startDate; ?>"/>
	</div>

	<div itemprop="performer" itemscope="" itemtype="http://schema.org/Person">
		<meta itemprop="name" content='<?php echo $username; ?>'>
	</div>
	<meta itemprop="startDate" content="<?php echo  $startDate; ?>"/>
	<meta itemprop="endDate" content="<?php echo  $startDate; ?>"/>
	<meta itemprop="image" content="<?php echo  get_the_post_thumbnail_url($post->ID,'full'); ?>"/>
	
	<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="job-manager-info"><?php esc_html_e( 'This listing has expired.', 'knowherepro' ); ?></div>
	<?php else : ?>

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

	<?php endif; ?>

</div><!--/ .single_job_listing-->
