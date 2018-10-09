<?php
/**
 * Dashboard Pagination
 *
 * Vars Loaded in This template:
 * - $pages : list of pages
 * - $current_page : current page loaded
 *
 * @since 1.4.0
 */
?>
<nav id="pm-pagination" class="wp-link-pages" role="navigation">

	<?php foreach ( $pages as $page ) : ?>

		<?php if ( $page == $current_page ) : // current page ?>

			<span class="page-numbers"><?php echo $page; ?></span>

		<?php else : ?>

			<a class="page-numbers" href="<?php echo pm_get_pagination_item_url( $page ); ?>"><?php echo $page; ?></a>

		<?php endif; ?>

	<?php endforeach; ?>

</nav><!-- #pm-pagination -->
