<?php
/**
 * Listing: Related Listings
 *
 */
class Knowhere_Widget_Listing_Related_Listings extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'Display listings related to the listing currently being viewed.', 'knowherepro' );
        $this->widget_cssclass = 'job_manager widget_related_listings';
        $this->widget_id          = 'knowhere_related_listings';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Related Listings', 'knowherepro' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => esc_html__('Related Listings', 'knowherepro'),
                'label' => esc_html__( 'Title:', 'knowherepro' )
            ),
            'style' => array(
                'type' => 'select',
                'label' => esc_html__('Style', 'knowherepro'),
                'options' => array(
                    'kw-style-list' => esc_html__('List', 'knowherepro'),
                    'kw-style-grid' => esc_html__('Grid', 'knowherepro')
                ),
                'std' => 'kw-style-list'
            ),
            'location' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => esc_html__( 'Limit based on location', 'knowherepro' )
            ),
            'category' => array(
                'type'  => 'checkbox',
                'std'   => 1,
                'label' => esc_html__( 'Limit based on category', 'knowherepro' )
            ),
            'featured' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => esc_html__( 'Show only featured listings', 'knowherepro' )
            ),
            'type' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => esc_html__( 'Show type', 'knowherepro' )
            ),
            'company' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => esc_html__( 'Show company name', 'knowherepro' )
            ),
            'limit' => array(
                'type'  => 'number',
                'std'   => 3,
                'min'   => 3,
                'max'   => 30,
                'step'  => 3,
                'label' => esc_html__( 'Number to show:', 'knowherepro' )
            )
        );
        parent::__construct();
    }

    function widget( $args, $instance ) {

		global $post;

        extract( $args );

        $title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );
        $style = isset( $instance['style'] ) ? $instance['style'] : 'kw-style-list';
        $featured = isset( $instance[ 'featured' ] ) && 1 == $instance[ 'featured' ] ? true : null;
        $type = isset( $instance[ 'type' ] ) && 1 == $instance[ 'type' ] ? 1 : 0;
        $company = isset( $instance[ 'company' ] ) && 1 == $instance[ 'company' ] ? 1 : 0;
        $limit = isset( $instance[ 'limit' ] ) ? absint( $instance[ 'limit' ] ) : 3;

        $location = isset( $instance[ 'location' ] ) && 1 == $instance[ 'location' ] ? true : null;
        $category = isset( $instance[ 'category' ] ) && 1 == $instance[ 'category' ] ? true : null;

		add_filter( 'get_job_listings_query_args', array( $this, 'exclude_current_listing' ) );

		$args = array(
            'posts_per_page' => $limit,
            'featured' => $featured,
            'no_found_rows' => true,
			'update_post_term_cache' => false,
		);

		if ( $location && get_post()->geolocation_state_long ) {
			$args[ 'search_location' ] = get_post()->geolocation_state_long;
		}

		if ( $category ) {
			$args[ 'search_categories' ] = wp_get_post_terms( get_post()->ID, 'job_listing_category', array( 'fields' => 'ids' ) );
		}

        $listings = get_job_listings( $args );

		remove_filter( 'get_job_listings_query_args', array( $this, 'exclude_current_listing' ) );

		if ( ! $listings->have_posts() ) {
			return;
		}

        ob_start();

        echo $before_widget;

        if ( $title ) echo $before_title . $title . $after_title;
        ?>

        <?php if ( $style == 'kw-style-list' ): ?>

        <div class="kw-listings kw-list-view kw-type-3">
            <?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

				<div class="kw-listing-item-wrap">

					<article class="kw-listing-item">

                        <?php knowhere_listing_media_output(array('post' => $listings) );  ?>

						<!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

						<div class="kw-listing-item-info">

							<h3 class="kw-listing-item-title"><a href="<?php the_job_permalink(); ?>"><?php the_title() ?></a></h3>

                            <?php if ( get_option( 'job_manager_enable_types' ) && $job_type = wpjm_get_the_job_types( $post ) ) : ?>
                                <header class="kw-listing-item-header">
                                    <div class="kw-xs-table-row">
                                        <div class="col-xs-6">
                                            <?php knowhere_job_listing_rating() ?>
                                            <?php knowhere_bg_color_label( $job_type ); ?>
                                        </div>
                                    </div>
                                </header>
                             <?php endif; ?>

							<ul class="kw-listing-item-data kw-icons-list">

                                <?php if ( $company ): ?>
								    <?php knowhere_job_listing_company( $post ); ?>
                                <?php endif; ?>

                                <?php if ( get_the_job_location($post) ): ?>
                                    <li class="kw-item-location-data"><?php echo get_the_job_location($post); ?></li>
                                <?php endif; ?>

							</ul>

						</div>

						<!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

					</article>

				</div>

            <?php endwhile; ?>
        </div>

        <?php elseif ( $style == 'kw-style-grid' ): ?>

            <div class="kw-listings kw-type-2">

                 <?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

                     <?php
					 $terms = get_the_terms( $post->ID, 'job_listing_category' );
					 $post_image_src = knowhere_get_post_image_src( $post->ID, 'knowhere-card-image' );
					 ?>

                    <div class="kw-listing-item-wrap">

                        <article <?php job_listing_class('kw-listing-item'); ?>>

							<?php if ( !empty($post_image_src) ): ?>

								<!-- - - - - - - - - - - - - - Media - - - - - - - - - - - - - - - - -->

								<div class="kw-listing-item-media">

									<a href="<?php the_job_permalink(); ?>" class="kw-listing-item-thumbnail">
										<img src="<?php echo esc_url($post_image_src); ?>" alt="">
									</a>

									<ul class="kw-listing-card-meta">
										<?php if ( ! is_wp_error( $terms ) && ( is_array( $terms ) || is_object( $terms ) ) ) { ?>

											<?php $i = 0; ?>

											<?php foreach ( $terms as $term ) {
												$icon_url      = knowhere_get_term_icon_url( $term->term_id );
												$attachment_id = knowhere_get_term_icon_id( $term->term_id );

												if ( $i > 0 ) { continue; }
												if ( empty( $icon_url ) ) {  continue; } ?>

												<li class="kw-listing-term-list">
													<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" title="<?php echo sprintf('%s', $term->name ) ?>" class="kw-listing-item-icon">
														<?php knowhere_display_icon_or_image( $icon_url, '', true, $attachment_id ); ?>
													</a>
												</li>

												<?php $i++; ?>

											<?php } ?>
										<?php } ?>

										<li>
											<a href="<?php the_job_permalink() ?>" class="kw-listing-item-like">
												<span class="lnr icon-heart"></span>
											</a>
										</li>

									</ul><!--/ .kw-listing-card-meta-->

								</div>

								<!-- - - - - - - - - - - - - - End of Media - - - - - - - - - - - - - - - - -->

							<?php endif; ?>

                            <!-- - - - - - - - - - - - - - Description - - - - - - - - - - - - - - - - -->

                            <div class="kw-listing-item-info">

                                <header class="kw-listing-item-header">

                                    <h3 class="kw-listing-item-title">
                                        <a href="<?php the_job_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <div class="kw-card-rating">
                                        <?php knowhere_job_listing_rating() ?>
                                    </div>

                                </header>

                                <ul class="kw-listing-item-data kw-icons-list">
                                    <li class="kw-listing-item-location"><span class="lnr icon-map-marker"></span><?php echo get_the_job_location(); ?></li>
                                    <li class="kw-listing-item-phone"><span class="lnr icon-telephone"></span><?php echo get_post_meta( $post->ID, '_company_phone', true); ?></li>
                                </ul>

                            </div>

                            <!-- - - - - - - - - - - - - - End of Description - - - - - - - - - - - - - - - - -->

                        </article>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

        <?php
        echo $after_widget;

        wp_reset_postdata();

        $content = ob_get_clean();

        echo apply_filters( $this->widget_id, $content );
    }

	/**
	 * Exclude the current listing from the `get_job_listings()` call.
	 *
	 * @since 1.6.0
	 * @param array $query_args
	 * @return array $query_args
	 */
	public function exclude_current_listing( $query_args ) {
		$query_args[ 'post__not_in' ] = array( get_post()->ID );

		return $query_args;
	}
}
