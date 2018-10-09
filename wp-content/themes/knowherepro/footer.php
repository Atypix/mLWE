<?php global $knowhere_config; ?>

<?php if ( is_page_template() ): ?>

	<?php else: ?>

						</main><!--/ #main-->

					<?php get_sidebar(); ?>

				</div><!--/ .row-->

			</div><!--/ .container-->

		</div><!--/ .kw-entry-content-->

	<?php endif; ?>

	</div><!--/ #primary-->

	<!-- - - - - - - - - - - - - - Footer - - - - - - - - - - - - - - - - -->

	<footer id="footer" class="kw-footer <?php echo esc_attr($knowhere_config['footer_classes']); ?>">

		<?php
		/**
		 * knowhere_footer_in_top_part hook
		 *
		 */

		do_action('knowhere_footer_in_top_part');
		?>

	</footer><!--/ #footer-->

	<!-- - - - - - - - - - - - - -/ Footer - - - - - - - - - - - - - - - - -->

<?php 	if ($_SERVER["REQUEST_URI"] == "/my-account/edit-account/") { ?>
<script type='text/javascript' src='https://www.mylittlewe.com/wp-content/plugins/wp-job-manager/assets/js/jquery-fileupload/jquery.iframe-transport.js?ver=1.8.3'></script>
<script type='text/javascript' src='https://www.mylittlewe.com/wp-content/plugins/wp-job-manager/assets/js/jquery-fileupload/jquery.fileupload.js?ver=9.11.2'></script>
<?php  } ?>

</div><!--/ .kw-wide-layout-type-->

<?php get_template_part( 'template-parts/panel' ); ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v3.0&appId=1083731045101772&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php wp_footer(); ?>

</body>
</html>