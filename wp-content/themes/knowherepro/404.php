<?php get_header(); ?>

<?php global $knowhere_settings;
$error_content = $knowhere_settings['error-content'];
?>

<div class="kw-template-404">

	<div class="row">

		<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">

			<?php echo html_entity_decode($error_content); ?>

			<!-- - - - - - - - - - - - - - Search Form - - - - - - - - - - - - - - - - -->

			<form class="kw-searchform kw-inline-form" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">

				<div class="kw-input-wrapper">
					<input type="search" name="s" id="s" autocomplete="off" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('Looking for...', 'knowherepro') ?>">
				</div><!--/ .kw-input-wrapper -->

				<button type="submit" class="kw-btn-big kw-yellow"><?php esc_html_e('Search', 'knowherepro') ?></button>

			</form>

			<!-- - - - - - - - - - - - - - End of Search Form - - - - - - - - - - - - - - - - -->

		</div>

	</div><!--/ .row -->

</div><!--/ .kw-template-404-->

<?php get_footer(); ?>