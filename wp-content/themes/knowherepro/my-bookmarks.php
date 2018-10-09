<div id="job-manager-bookmarks" class="kw-box kw-type-3">

	<div class="kw-table-container kw-horizontal kw-table-bookmarks">

		<table class="job-manager-bookmarks">
			<thead>

				<tr>
					<th class="kw-image-col">
						<div class="kw-cell-content"><?php esc_html_e( 'Image', 'knowherepro' ); ?></div>
					</th>

					<th class="kw-title-col">
						<div class="kw-cell-content"><?php esc_html_e( 'Title', 'knowherepro' ); ?></div>
					</th>

					<th class="kw-note-col">
						<div class="kw-cell-content"><?php esc_html_e( 'Note', 'knowherepro' ); ?></div>
					</th>

					<th class="kw-actions-col">
						<div class="kw-cell-content"><?php esc_html_e( 'Actions', 'knowherepro' ); ?></div>
					</th>
				</tr>

			</thead>

			<tbody> <?php

				foreach ( $bookmarks as $bookmark ) :
					if ( get_post_status( $bookmark->post_id ) !== 'publish' ) {
						continue;
					}
					$has_bookmark = true;
					?>

					<tr>

						<td class="kw-image-col" data-title="<?php esc_html_e('Image', 'knowherepro') ?>">
							<div class="kw-cell-content"><a href="<?php echo esc_url(get_permalink( $bookmark->post_id )) ?>"><?php
								$bookmark_image_id = knowhere_get_post_image_id( $bookmark->post_id );

								$bookmark_image = '';
								if ( ! empty( $bookmark_image_id ) ) {
									$bookmark_image = wp_get_attachment_image_src( $bookmark_image_id );
								}

								if ( ! empty( $bookmark_image ) && ( strstr( $bookmark_image[0], 'http' ) || file_exists( $bookmark_image[0] ) ) ) {
									$bookmark_image = $bookmark_image[0];
									$bookmark_image = job_manager_get_resized_image( $bookmark_image, 'thumbnail' );
									echo '<img src="' . esc_attr( $bookmark_image ) . '" alt="' . esc_attr( get_the_title( $bookmark->post_id ) ) . '" />';
								}
								?></a></div>
						</td>

						<td class="kw-title-col" data-title="<?php esc_html_e('Title', 'knowherepro') ?>">
							<div class="kw-cell-content">
								<?php echo '<a href="' . get_permalink( $bookmark->post_id ) . '">' . get_the_title( $bookmark->post_id ) . '</a>'; ?>
							</div>
						</td>

						<td class="kw-note-col" data-title="<?php esc_html_e('Note', 'knowherepro') ?>">
							<div class="kw-cell-content">
								<?php echo wpautop( wp_kses_post( $bookmark->bookmark_note ) ); ?>
							</div>
						</td>

						<td class="kw-actions-col" data-title="<?php esc_html_e('Actions', 'knowherepro') ?>">
							<div class="kw-cell-content">

								<ul class="kw-actions-listing">
									<?php
									$actions = apply_filters( 'job_manager_bookmark_actions', array(
										'delete' => array(
											'label' => esc_html__( 'Delete', 'knowherepro' ),
											'url'   => wp_nonce_url( add_query_arg( 'remove_bookmark', $bookmark->post_id ), 'remove_bookmark' )
										)
									), $bookmark );

									foreach ( $actions as $action => $value ) {
										echo '<li><a href="' . esc_url( $value['url'] ) . '" class="job-manager-bookmark-action-' . $action . '"><span class="lnr icon-trash2"></span>' . $value['label'] . '</a></li>';
									}
									?>
								</ul>

							</div>
						</td>

					</tr>

				<?php endforeach; ?>

				<?php if ( empty( $has_bookmark ) ) : ?>
					<tr>
						<td class="knowhere-no-have-bookmarks" colspan="2"><?php esc_html_e( 'You currently have no bookmarks', 'knowherepro' ); ?></td>
					</tr>
				<?php endif; ?>

			</tbody>

		</table>

	</div>

	<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>

</div>
