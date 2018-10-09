<?php if ( knowhere_using_facetwp() ) : ?>

	<?php get_template_part( 'includes/templates/loader-svg' ); ?></div>

<?php else: ?>

	</div><?php get_template_part( 'includes/templates/loader-svg' ); ?>

	<?php echo '<a class="load_more_jobs" href="#" style="display:none;"><strong>' . __( 'Load more listings', 'knowherepro' ) . '</strong></a>'; ?>

	</div></div>

<?php endif; ?>



