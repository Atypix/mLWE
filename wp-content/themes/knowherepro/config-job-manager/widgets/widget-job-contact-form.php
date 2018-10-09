<?php
/**
 * Widget: Listing Contact Form
 *
 */
class Knowhere_Widget_Listing_Contact_Form extends Knowhere_Widget {

	public function __construct() {

		$this->widget_description = esc_html__( 'Display contact form for detail listing, agent, agency, candidate.', 'knowherepro' );
		$this->widget_id          = 'knowhere_listing_contact_form';
		$this->widget_cssclass    = 'kw-listing-contact-form';
		$this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Listing Contact Form', 'knowherepro' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Contact Form', 'knowherepro' ),
				'label' => esc_html__( 'Title:', 'knowherepro' )
			)
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {

		extract( $args );

		$email = ' ';

		if ( is_singular('job_listing') || get_query_var('company') ) {
			$email = get_post_meta(get_the_ID(), '_application', true);
		}

		if ( is_singular('resume') ) {
			$email = get_post_meta(get_the_ID(), '_candidate_email', true);
		}

		if ( is_singular('knowhere_agency') ) {
			$email = sanitize_email(get_post_meta(get_the_ID(), 'knowhere_agency_email', true));
		}

		if ( is_singular('knowhere_agent') ) {
			$email = sanitize_email( get_post_meta( get_the_ID(), 'knowhere_agent_email', true ) );
		}

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		ob_start();

		echo $before_widget;

		if ( is_email( $email ) ): ?>

		<?php if (is_user_logged_in()) { ?>

				<?php if ( $title ) echo $before_title . sanitize_text_field($title) . $after_title; ?>

				<?php 
				global $post;
				$user = get_user_by( "ID", $post->post_author );
				
				?>
				<p>N'hésitez pas à demander des renseignements à cet hôte en remplissant le formulaire ci-dessous.</p>
				
				<p><?php echo do_shortcode ('[fep_shortcode_new_message_form to="{current-post-author}" subject="{current-post-title}"]'); ?></p>
		<?php } else { ?>
			<?php if ( $title ) echo $before_title . sanitize_text_field($title) . $after_title; ?>
			<p>Veuillez creer un compte ou vous connecter pour contacter l'hôte et participer à cette activité</p> 
			<p><a href="https://www.mylittlewe.com/mon-compte/"><div class="kw-oneline-action">
				<button type="submit" data-label="Chercher" class="kw-update-form">Créer un compte</button>
			</div></a></p>

		<?php } ?>
		<?php endif; ?>

		<?php

		echo $after_widget;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
	}
}
