<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package KnowherePro
 */

get_header();
?>
<style>
.kw-wide-layout-type > .kw-page-content {
  padding-top:30px!important;
  
}
</style>
<?php global $post, $knowhere_config;

?>

<?php while ( have_posts() ) : the_post(); ?>

	<div class="kw-single-box" id="kw-overview">

		<article id="post-<?php the_ID(); ?>" <?php job_listing_class( 'kw-single kw-type-2' ); ?> >
			<?php

			if ( ! post_password_required() ) {
				
					
	$product_id = get_post_meta( $post->ID, '_id_product', true );
	
	$aDates = get_post_meta( $product_id, '_wc_booking_availability', true );


	foreach ($aDates as $date_activite) {

		if ( $date_activite['type'] == "custom") {
			if(strtotime($date_activite['from']) > strtotime(date("d-m-Y")) && empty($startDate)) {
			 if ( $date_activite[ 'bookable' ] == 'yes' ) {
				$startDate = $date_activite['from'];
			 }
			} 
		} else {
			
			$startDate ="empty";
		}
		
	}


	
	
	
				
				if ( $knowhere_config['job-single-style'] != 'kw-style-4' && $knowhere_config['job-single-style'] != 'kw-style-5' ) {
					knowhere_job_single_gallery( array( 'echo' => true ) );
				}

				$job_manager = $GLOBALS['job_manager'];

				remove_filter( 'the_content', array( $job_manager->post_types, 'job_content' ) );

				ob_start();

				do_action( 'job_content_start' );

				get_job_manager_template_part( 'content-single', 'job_listing' );

				
				
				do_action( 'job_content_end' );

				$content = ob_get_clean();

				add_filter( 'the_content', array( $job_manager->post_types, 'job_content' ) );

				echo apply_filters( 'job_manager_single_job_content', $content, $post ); ?>

				<?php

			} else {
				echo '<div class="kw-entry-content">';
				echo get_the_password_form();
				echo '</div>';
			} ?>

			<?php knowhere_output_single_listing_icon(); ?>

		</article>

	</div>
	<?php
	if (!empty($startDate)) {
			$nb = get_post_meta( $post->ID, '_nb_personnes_max', true );
			if ($nb > 1 ) {
				$str = $nb." personnes";
			} else {
				$str = $nb." personne";
			}
	 ?>	
	<div class="kw-box panier-mobile">
		<p><img src="https://www.mylittlewe.com/wp-content/uploads/2018/07/rasurre_2.jpg" width="100%"></p>
		
		<h2 id="reservez">Réservez maintenant !</h2>

		<?php 
	$id_product = get_post_meta ($post->ID, '_id_product', true);
	echo do_shortcode('[product_page  id="'.$id_product .'" ]'); 
	?></div>
	<?php } ?>
	<?php knowhere_job_single_details(); ?>
	<?php 
	$author = get_the_author(); 
	$user_id=$post->post_author; 
	if (!isset($author->first_name)) {
		$authorname = get_the_author_meta('first_name', $user_id);
	} else {
	 	$authorname = '';
	}
$description_author = get_the_author_meta('description');
	if (strlen($description_author) <  6) {
		$description_author = "<p>L'hôte n'a pas encore rempli sa description, contactez le pour avoir plus de renseignements ;)</p><br/><br><br>";
	} 
	
?><div style="clear:both"></div> 
<div id="mks_author_widget-2" class="widget  mks_author_widget kw-box " style="text-align:left !important"><h3 class="kw-widget-title" style="font-size:25px!important;margin-bottom:30px">A propos de l’hôte de cette activité</h3>
	<div style="float:left;margin:0px 30px 20px 30px!important">
	<?php 
	
	echo get_avatar( get_the_author_meta('ID') , 300, null, null, array('class' => array('avatar', 'avatar-300', 'photo') ) ); 
	?>
</div>
  <h3><?php echo $authorname; ?></h3>

		<?php echo $description_author; ?>
	

</div>
<?php
        
        $address = knowhere_get_formatted_address();

        if ( empty( $address ) ||  get_post_meta( $post->ID, '_job_location_visible', true ) == 1 ) { 
            echo "<div class='kw-listing-map-container' style='background-color:white;padding:20px'><h3>Lieu du Rendez vous</h3>L' adresse exacte vous sera communiquée une fois la réservation effectuée. Pour toute question contactez l'hôte.</div>";
            return; 
        }


        $geolocation_lat  = get_post_meta( get_the_ID(), 'geolocation_lat', true );
        $geolocation_long = get_post_meta( get_the_ID(), 'geolocation_long', true );

        $get_directions_link = '';
        if ( ! empty( $geolocation_lat ) && ! empty( $geolocation_long ) && is_numeric( $geolocation_lat ) && is_numeric( $geolocation_long ) ) {
            $get_directions_link = '//maps.google.com/maps?daddr=' . $geolocation_lat . ',' . $geolocation_long;
        }

        if ( empty( $get_directions_link ) ) { return; }

        echo $before_widget; ?>

        <div class="kw-listing-map-container kw-box">

            <div id="kw-listings-gmap" class="kw-listing-widget-gmap"></div>
            <br/>
            <?php echo sprintf( '%s', $address ); ?>

            <?php if ( ! empty( $get_directions_link ) ) { ?>
                <a href="<?php echo esc_url($get_directions_link); ?>" class="kw-get-directions" target="_blank">
                    <span class="lnr icon-road-sign"></span> <?php esc_html_e( 'Get Directions', 'knowherepro' ); ?>
                </a>
            <?php } ?>

            <?php
            $facebook = get_post_meta( get_the_ID(), '_company_facebook', true);
            $googleplus = get_post_meta( get_the_ID(), '_company_googleplus', true);
            $twitter = get_post_meta( get_the_ID(), '_company_twitter', true);
            $linkedin = get_post_meta( get_the_ID(), '_company_linkedin', true);
            $pinterest = get_post_meta( get_the_ID(), '_company_pinterest', true);
            $instagram = get_post_meta( get_the_ID(), '_company_instagram', true);
            ?>

            <?php if ( !empty($facebook) || !empty($googleplus) || !empty($twitter) || !empty($linkedin) || !empty($instagram) ): ?>
                <p class="kw-social-profiles-title"><?php esc_html_e('Social Profiles', 'knowherepro') ?>:</p>
            <?php endif; ?>

            <ul class="kw-social-links">

                <?php if ( !empty( $facebook ) ): ?>
                    <li><a target="_blank" href="<?php echo esc_url($facebook) ?>"><i class="fa fa-facebook"></i></a></li>
                <?php endif; ?>

                <?php if ( !empty( $instagram ) ): ?>
                    <li><a target="_blank" href="<?php echo esc_url($instagram) ?>"><i class="fa fa-instagram"></i></a></li>
                <?php endif; ?>

                <?php if ( !empty( $googleplus ) ): ?>
                    <li><a target="_blank" href="<?php echo esc_url($googleplus) ?>"><i class="fa fa-google-plus"></i></a></li>
                <?php endif; ?>

                <?php if ( ! empty( $twitter ) ): ?>
                    <li><a target="_blank" href="https://twitter.com/<?php echo preg_replace("[@]", "", $twitter); ?>"><i class="fa fa-twitter"></i></a></li>
                <?php endif; ?>

                <?php if ( !empty( $linkedin ) ): ?>
                    <li><a target="_blank" href="<?php echo esc_url($linkedin) ?>"><i class="fa fa-linkedin"></i></a></li>
                <?php endif; ?>

                <?php if ( !empty( $pinterest ) ): ?>
                    <li><a target="_blank" href="<?php echo esc_url($pinterest) ?>"><i class="fa fa-pinterest"></i></a></li>
                <?php endif; ?>

            </ul>

        </div>
        <br/><br/>

	<?php knowhere_job_single_related(); ?>

	<?php knowhere_job_single_reviewer(); ?>

<?php endwhile; ?>

<?php get_footer(); ?>

<?php goach_cron_function() ?>

<?php 
global $wpdb;
$count = 1; 
synchronize_onebrain ();
$wpdb->query(
			$wpdb->prepare( "
				INSERT INTO " . $wpdb->prefix . "knowhere_job_listing_post_views (id, type, period, count)
				VALUES (%d, %d, %s, %d)
				ON DUPLICATE KEY UPDATE count = count + %d", $post->ID, 4, 'total', $count, $count
			)
		); 

		 ?>
