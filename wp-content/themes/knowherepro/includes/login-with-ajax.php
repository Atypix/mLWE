<?php
/**
 * Custom functions that deal with the integration of Login with Ajax.
 * See: https://wordpress.org/plugins/login-with-ajax/
 *
 * @package KnowherePro
 */

function knowhere_lwa_modal() {
	//double check just to be sure
	if ( knowhere_using_login_with_ajax() ) {
		$atts = array(
			'profile_link' => true,
			'template'     => 'modal',
			'registration' => true,
			'redirect'     => false,
			'remember'     => true
		);

		return LoginWithAjax::shortcode( $atts );
	}

	return '';
}

function knowhere_add_lwa_modal_in_footer() {

	if ( knowhere_using_login_with_ajax() && ! is_user_logged_in() ) :

		echo '<div id="knowhere-lwa-modal-holder">' . knowhere_lwa_modal() . '</div>'; ?>

		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$(window).load(function() {
					var $the_lwa_login_modal = $('.lwa-modal').first();
					$('.lwa-links-modal').each(function (i, e) {
						$(e).parents('.lwa').data('modal', $the_lwa_login_modal);
					});
				});
			});
		</script>

	<?php endif;
}

add_action( 'wp_footer', 'knowhere_add_lwa_modal_in_footer' );