<?php global $wp; ?>

<form method="post" action="<?php echo defined( 'DOING_AJAX' ) ? '' : esc_url( remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) ) ); ?>" class="wp-job-manager-bookmarks-form">

	<div class="knowhere-favorite-holder">

		<?php if ( $is_bookmarked ) : ?>

			<div><span class="fa fa-heart"></span>
				<a class="remove-bookmark" href="<?php echo wp_nonce_url( add_query_arg( 'remove_bookmark', absint( $post->ID ), get_permalink() ), 'remove_bookmark' ); ?>">
					<span class="bookmark-text"><?php esc_html_e('Favorited', 'knowherepro') ?></span>
				</a>
			</div>

		<?php else : ?>

			<div><span class="lnr icon-heart"></span>
				<a class="bookmark-notice knowhere-tooltip-trigger" href="#">
					<span class="bookmark-text"><?php esc_html_e('Add to Favorites', 'knowherepro') ?></span>
				</a>
			</div>

		<?php endif; ?>

		<div class="tooltip-bookmark-details">
			<p><label for="bookmark_notes"><?php esc_html_e( 'Notes:', 'knowherepro' ); ?></label><textarea name="bookmark_notes" id="bookmark_notes" cols="25" rows="3"><?php echo esc_textarea( $note ); ?></textarea></p>
			<p>
				<?php wp_nonce_field( 'update_bookmark' ); ?>
				<input type="hidden" name="bookmark_post_id" value="<?php echo absint( $post->ID ); ?>" />
				<input type="submit" name="submit_bookmark" value="<?php echo esc_attr($is_bookmarked) ? esc_html__( 'Update Bookmark', 'knowherepro' ) : esc_html__( 'Add Bookmark', 'knowherepro' ); ?>" />
			</p>
		</div>

	</div>

</form>