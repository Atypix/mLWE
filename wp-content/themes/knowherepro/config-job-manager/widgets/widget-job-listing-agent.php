<?php
/**
 * Widget: Listing Open Hours
 *
 */
class Knowhere_Sidebar_Listing_Agent extends Knowhere_Widget {

    public function __construct() {
        $this->widget_description = esc_html__( 'Display the agent of the listing.', 'knowherepro' );
        $this->widget_cssclass 	  = 'widget_listing_agent';
        $this->widget_id          = 'knowhere_listing_agent';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Agent', 'knowherepro' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => esc_html__('About The Agent', 'knowherepro'),
                'label' => esc_html__( 'Title:', 'knowherepro' )
            )
        );

        parent::__construct();
    }

    function widget( $args, $instance ) {

		if ( !is_singular('job_listing') ) return;

		global $post;
		$post_id = $post->ID;

		$agent_id = get_post_meta( $post_id, '_agent_id', true );

		if ( empty($agent_id) || $agent_id < 1 ) return;

		$address = get_post_meta( $agent_id, 'knowhere_agent_address', true );
		$position = get_post_meta( $agent_id, 'knowhere_agent_position', true );
		$phone = get_post_meta( $agent_id, 'knowhere_agent_phone', true );
		$email = get_post_meta( $agent_id, 'knowhere_agent_email', true );
		$facebook = get_post_meta( $agent_id, 'knowhere_agent_facebook', true );
		$google_plus = get_post_meta( $agent_id, 'knowhere_agent_google_plus', true );
		$twitter = get_post_meta( $agent_id, 'knowhere_agent_twitter', true );
		$linkedin = get_post_meta( $agent_id, 'knowhere_agent_linkedin', true );
		$pinterest = get_post_meta( $agent_id, 'knowhere_agent_pinterest', true );

        extract( $args );

		$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

        ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title; ?>

		<div class="kw-agent-widget">

			<div class="kw-author-box">

				<?php if ( has_post_thumbnail( $agent_id ) ): ?>
					<div class="kw-agent-photo">
						<?php echo get_the_post_thumbnail( $agent_id, array(100, 100) ) ?>
					</div>
				<?php endif; ?>

				<div class="kw-agent-meta">

					<a href="<?php echo get_the_permalink( $agent_id ) ?>" class="kw-author-name">
						<?php echo get_the_title( $agent_id ) ?>
					</a>

					<?php if ( !empty($position) ): ?>
						<div class="kw-author-position"><?php echo sprintf('%s', $position); ?></div>
					<?php endif; ?>

						<ul>

							<?php if ( is_email($email) ): ?>
								<li>
									<a href="mailto:<?php echo antispambot($email, 1) ?>">
										<?php esc_html_e('Email Agent', 'knowherepro') ?>
									</a>
								</li>
							<?php endif; ?>

							<li>
								<a href="<?php echo get_the_permalink( $agent_id ) ?>">
									<?php esc_html_e('Agent\'s Other Listings', 'knowherepro') ?>
								</a>
							</li>

						</ul>

				</div>

			</div>

			<ul class="kw-icons-list">

				<?php if ( !empty($address) ): ?>
					<li><span class="lnr icon-map-marker"></span><?php echo sprintf('%s', $address); ?></li>
				<?php endif; ?>

				<?php if ( !empty($phone) ): ?>
					<li><span class="lnr icon-telephone"></span><?php echo sprintf('%s', $phone); ?></li>
				<?php endif; ?>

			</ul>

			<ul class="kw-social-links">

				<?php if ( !empty($facebook) ): ?>
					<li><a target="_blank" href="<?php echo esc_url($facebook) ?>"><i class="fa fa-facebook"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($google_plus) ): ?>
					<li><a target="_blank" href="<?php echo esc_url($google_plus) ?>"><i class="fa fa-google-plus"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($twitter) ): ?>
					<li><a target="_blank" href="<?php echo esc_url($twitter) ?>"><i class="fa fa-twitter"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($linkedin) ): ?>
					<li><a target="_blank" href="<?php echo esc_url($linkedin) ?>"><i class="fa fa-linkedin"></i></a></li>
				<?php endif; ?>

				<?php if ( !empty($pinterest) ): ?>
					<li><a target="_blank" href="<?php echo esc_url($pinterest) ?>"><i class="fa fa-pinterest"></i></a></li>
				<?php endif; ?>

			</ul>

		</div><!--/ .kw-agent-meta-->

        <?php echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
    }

}
