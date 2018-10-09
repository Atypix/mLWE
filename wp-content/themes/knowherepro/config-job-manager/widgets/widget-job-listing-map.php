<?php
/**
 * Widget: Listing Map
 *
 */
class Knowhere_Sidebar_Listing_Map_Widget extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'A Map View of the listing location along with a Directions link to Google Map.', 'knowherepro' );
        $this->widget_cssclass 	  = 'widget_listing_map';
        $this->widget_id          = 'knowhere_listing_sidebar_map';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Location Map', 'knowherepro' );
        parent::__construct();
    }

    function widget( $args, $instance ) {

        extract( $args );
         global $post;   
        ob_start();

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

        <div class="kw-listing-map-container" itemscope itemtype="http://schema.org/GeoCoordinates">

            <div id="kw-listings-gmap" class="kw-listing-widget-gmap"></div>

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

        </div><!--/ .kw-listing-map-container-->

        <?php echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
    }

	public function form( $instance ) {
		echo '<p>' . $this->widget_options['description'] . '</p>';
	}

}
