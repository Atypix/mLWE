<?php
/**
 * Widget: Listing Search
 *
 */
class Knowhere_Sidebar_Listing_Search_Widget extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'Advanced Search.', 'knowherepro' );
		$this->widget_cssclass 	  = 'widget_listing_advanced_search';
        $this->widget_id          = 'knowhere_listing_advanced_search';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Advanced Search', 'knowherepro' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => esc_html__('Filter Listings', 'knowherepro'),
                'label' => esc_html__( 'Title:', 'knowherepro' )
            )
        );

        parent::__construct();
    }

    function widget( $args, $instance ) {

        extract( $args );

		global $knowhere_settings;

		$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

		$show_categories = true;
		if ( ! get_option( 'job_manager_enable_categories' ) ) {
			$show_categories = false;
		}
		$atts = apply_filters( 'job_manager_ouput_jobs_defaut', array(
			'per_page' => get_option( 'job_manager_per_page' ),
			'orderby' => 'featured',
			'order' => 'DESC',
			'show_categories' => $show_categories,
			'show_tags' => false,
			'categories' => true,
			'selected_category' => false,
			'job_types' => false,
			'location' => false,
			'keywords' => false,
			'selected_job_types' => false,
			'show_category_multiselect' => false,
			'selected_region' => false
		) );

        ob_start();

        echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		?>

		<form class="widget-listing-search-form" action="<?php echo get_post_type_archive_link( 'job_listing' ); ?>" method="GET">

			<div class="widget-search-jobs">

				<div class="search_keywords">
					<label for="search_keywords"><?php esc_html_e( 'Keywords', 'knowherepro' ); ?></label>
					<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'What are you looking for?', 'knowherepro' ); ?>" />
				</div>

				<div class="search_location">
					<label for="search_location"><?php esc_html_e( 'Location', 'knowherepro' ); ?></label>
					<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e( 'Location', 'knowherepro' ); ?>" />
				</div>

				<?php if ( get_option( 'job_manager_enable_categories' ) ) : ?>

					<div class="search_categories">
						<label for="search_categories"><?php esc_html_e( 'Category', 'knowherepro' ); ?></label>
						<?php job_manager_dropdown_categories( array( 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'show_option_all' => esc_html__( 'All categories', 'knowherepro' ), 'name' => 'search_categories', 'orderby' => 'name', 'multiple' => false ) ); ?>
					</div>

				<?php endif; ?>

				<?php $job_tags = get_terms( array( 'taxonomy' => 'job_listing_tag', 'hierarchical' => 1 ) ); ?>

				<?php
				if ( ! is_wp_error( $job_tags ) && ! empty ( $job_tags ) ) { ?>

					<div class="search_tags">
						<label for="search_categories"><?php esc_html_e( 'Tags', 'knowherepro' ); ?></label>
						<select multiple class="knowhere-tags-select" data-placeholder="<?php esc_html_e( 'Filter by tags', 'knowherepro' ); ?>"
								name="job_tag_select">
							<?php foreach ( $job_tags as $term ) : ?>
								<option value="<?php echo esc_attr($term->name) ?>"><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

				<?php } ?>

				<?php if ( class_exists('Astoundify_Job_Manager_Regions') && get_option( 'job_manager_regions_filter' ) ): ?>

					<div class="search_regions">

						<label for="search_regions"><?php esc_html_e( 'Region', 'knowherepro' ); ?></label>

						<?php
						if ( ( ! isset( $_GET[ 'selected_region' ] ) || '' == $_GET[ 'selected_region' ] ) && isset( $_GET[ 'search_region' ] ) ) {
							$_GET[ 'selected_region' ] = absint( $_GET[ 'search_region' ] );
						}

						wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
							'show_option_all' => __( 'All Regions', 'knowherepro' ),
							'hierarchical' => true,
							'orderby' => 'name',
							'taxonomy' => 'job_listing_region',
							'name' => 'search_region',
							'class' => 'filter-job-regions',
							'hide_empty' => 0,
							'selected' => isset( $_GET[ 'selected_region' ] ) ? $_GET[ 'selected_region' ] : ''
						) ) );
						?>

					</div>

				<?php endif; ?>

				<?php if ( $knowhere_settings['job-type-fields'] == 'job' ): ?>
					<?php if ( $knowhere_settings['show-search-price'] ): ?>

					<?php endif; ?>
				<?php endif; ?>

			</div><!--/ .widget-search-jobs-->

			<?php $label = _x( 'Filter', 'search filters submit', 'knowherepro' ); ?>

			<button type="submit" class="kw-btn-small kw-gray"><?php echo esc_html($label) ?></button>

			<div class="clear"></div>

		</form>
		<?php

        echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
    }

}
