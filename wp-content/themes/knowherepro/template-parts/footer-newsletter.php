
<?php
global $knowhere_settings;

if ( !$knowhere_settings['show-footer-newsletter'] || get_post_meta( get_the_ID(), 'knowhere_footer_hidden_newsletter', true ) ) return;
?>

<div class="kw-newsletter-section">

	<!-- - - - - - - - - - - - - - Background Icon - - - - - - - - - - - - - - - - -->

	<i class="lnr icon-envelope kw-section-icon"></i>

	<!-- - - - - - - - - - - - - - End of Background Icon - - - - - - - - - - - - - - - - -->

	<div class="container">

		<div class="kw-newsletter">

			<?php if ( $knowhere_settings['footer-newsletter-title'] ): ?>
				<h3 class="kw-title"><?php echo esc_html($knowhere_settings['footer-newsletter-title']) ?></h3>
			<?php endif; ?>

			<?php if ( $knowhere_settings['footer-newsletter-desc'] ): ?>
				<p class="kw-description"><?php echo sprintf('%s', $knowhere_settings['footer-newsletter-desc']) ?></p>
			<?php endif; ?>

			<?php

			if ( defined('WYSIJA') ) {

				wp_enqueue_script( 'wysija-validator-lang' );
				wp_enqueue_script( 'wysija-validator' );
				wp_enqueue_script( 'wysija-front-subscribers' );

				$data = '';
				$disabled_submit = $msg_success_preview='';

				$select_form = $knowhere_settings['footer-select-form'];
				$form_id_real = 'form-' . absint($select_form);

				$model_forms = WYSIJA::get('forms', 'model');
				$form = $model_forms->getOne( array( 'form_id' => (int)$select_form ) );

				if ( !empty($form) ) {
					$helper_form_engine = WYSIJA::get('form_engine', 'helper');
					$helper_form_engine->set_data( $form['data'], true );

					// get html rendering of form
					$form_html = $helper_form_engine->render_web();
					remove_shortcode('user'); remove_shortcode('user_list'); remove_shortcode('list_ids'); remove_shortcode('list_id'); remove_shortcode('firstname');
					remove_shortcode('lastname'); remove_shortcode('email'); remove_shortcode('custom'); remove_shortcode('required'); remove_shortcode('field');

					// interpret shortcodes
					$form_html = do_shortcode($form_html);

							$data .= '<form id="'. $form_id_real .'" method="post" action="#wysija" class="widget_wysija kw-newsletter-form kw-inline-form">';
								$data .= '<div class="kw-input-wrapper">';
								$data .= $form_html;
								$data .= '</div>';

								$data .= '<div id="msg-'.$form_id_real.'" class="wysija-msg ajax">'.$msg_success_preview.'</div>';

							$data .= '</form>';

					$form = $data;
				}

			} else {
				$form = '<h6>'. esc_html__( 'Please install required plugin - MailPoet Newsletters', 'knowherepro' ) .'</h6>';
			}

			echo sprintf( '%s', $form ); ?>

		</div><!--/ .kw-newsletter -->

	</div><!--/ .container -->

</div>



