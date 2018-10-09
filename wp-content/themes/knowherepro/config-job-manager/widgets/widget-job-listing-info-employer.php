<?php
/**
 * Widget: Listing Map
 *
 */
class Knowhere_Sidebar_Listing_Employer_Widget extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'A Info of the employer.', 'knowherepro' );
        $this->widget_cssclass 	  = 'widget_listing_info_employer';
        $this->widget_id          = 'knowhere_listing_sidebar_info_employer';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Info Employer', 'knowherepro' );
        parent::__construct();
    }

    function widget( $args, $instance ) {

		global $post;

		$author = $post->post_author;
		if ( !$author ) return;

		$company_archive_link = $company_logo = '';
		$desc = get_the_author_meta('description', $author);
		$company_website = get_the_company_website();

		if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != knowhere_get_the_company_name() ) {
			$companies   = Astoundify_Job_Manager_Companies::instance();
			$company_url = esc_url( $companies->company_url( knowhere_get_the_company_name() ) );
			$company_archive_link = $company_url;
		}

        extract( $args );

        ob_start();

        echo $before_widget; ?>

        <div class="kw-listings-holder">

			<div class="kw-listings kw-type-3">

				<div class="kw-listing-item-wrap">

					<div class="kw-listing-item">

						<?php if ( knowhere_get_the_company_logo() ): ?>

							<div class="kw-listing-item-media kw-listing-style-4">
								<a href="<?php echo esc_url($company_archive_link); ?>" class="kw-listing-item-thumbnail">
									<?php knowhere_the_company_logo(); ?>
								</a>
							</div>

						<?php endif; ?>

						<div class="kw-listing-item-info">

							<?php if ( !empty($desc) ): ?>
								<div class="kw-listing-item-description">
									<?php echo sprintf('%s', $desc) ?>
								</div>
							<?php endif; ?>

							<ul class="kw-listing-item-data kw-icons-list">

								<?php if ( get_the_job_location($post) ): ?>
									<li><span class="lnr icon-map-marker"></span><?php echo get_the_job_location($post); ?></li>
								<?php endif; ?>

								<?php if ( !empty($company_website) ): ?>
									<li><span class="lnr icon-link"></span><a href="<?php echo esc_url($company_website) ?>"><?php echo esc_html($company_website) ?></a></li>
								<?php endif; ?>

							</ul>

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

							<?php if ( $company_archive_link ): ?>
								<a class="kw-more-link" href="<?php echo esc_url($company_archive_link) ?>"><?php esc_html_e('More Vacancies From This Employer', 'knowherepro') ?></a>
							<?php endif; ?>

						</div>

					</div>

				</div>
			</div>
        </div>

        <?php echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
    }

	public function form( $instance ) {
		echo '<p>' . $this->widget_options['description'] . '</p>';
	}

}
