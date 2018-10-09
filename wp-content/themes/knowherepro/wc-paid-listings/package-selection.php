<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>

	<ul class="job_packages">

		<?php if ( $user_packages ) : ?>

			<?php foreach ( $user_packages as $key => $package ) :

				$package = wc_paid_listings_get_package( $package );

				?>

				<li class="user-job-package">

					<section class="kw-pricing-plan">

						<header class="kw-pp-header">
							<h5 class="kw-pp-type"><?php echo esc_html($package->get_title()); ?></h5>
							<div class="kw-pp-price">

								<div class="kw-pp-lifetime">

									<?php

									if ( $package->get_limit() ) {
										printf( _n( '%s job posted out of %d', '%s jobs posted out of %d', $package->get_count(), 'knowherepro' ), $package->get_count(), $package->get_limit() );
									} else {
										printf( _n( '%s job posted', '%s jobs posted', $package->get_count(), 'knowherepro' ), $package->get_count() );
									}

									if ( $package->get_duration() ) {
										printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'knowherepro' ), $package->get_duration() );
									}
									?>

								</div>

							</div>
						</header>

						<footer class="kw-pp-footer">
							<button  class="btn package__btn kw-btn-medium kw-theme-color" type="submit" name="job_package" value="user-<?php echo esc_attr($key); ?>" id="user-package-<?php echo esc_attr($package->get_id()); ?>">
								<?php esc_html_e('Get Started', 'knowherepro') ?>
							</button>
						</footer>

					</section>

				</li>

			<?php endforeach; ?>

		<?php endif; ?>

		<?php if ( $packages ) : ?>

			<?php foreach ( $packages as $key => $package ) :

				$product = wc_get_product( method_exists( $package, 'get_id' ) ? $package : $package->ID );

				if ( ! $product->is_type( array( 'job_package', 'job_package_subscription' ) ) || ! $product->is_purchasable() ) {
					continue;
				}

				$featured_class = $product->is_featured() ? 'kw-active' : '';

				?>
				<li class="job-package">

					<section class="kw-pricing-plan <?php echo sanitize_html_class($featured_class) ?>">

						<header class="kw-pp-header">
							<h5 class="kw-pp-type"><?php echo esc_html($product->get_title()); ?></h5>
							<div class="kw-pp-price">
								<?php
								echo sprintf( '%s <div class="kw-pp-lifetime"> %s %s </div>', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'knowherepro' ), $product->get_duration() ? sprintf( _n( 'listed for %s day', 'listed for %s days', $product->get_duration(), 'knowherepro' ), $product->get_duration() ) : '');
								?>
							</div>
						</header>

						<?php $content = $product->get_description(); ?>

						<div class="kw-package-content"><?php echo apply_filters( 'the_content', $content ); ?></div>

						<footer class="kw-pp-footer">
							<button  class="btn package__btn kw-btn-medium kw-theme-color" type="submit" name="job_package" value="<?php echo esc_attr($product->get_id()); ?>" id="package-<?php echo esc_attr($product->get_id()); ?>">
								<?php esc_html_e('Get Started', 'knowherepro') ?>
							</button>
						</footer>

					</section>

				</li>

			<?php endforeach; ?>

		<?php endif; ?>

	</ul>

<?php else : ?>

	<p><?php esc_html_e( 'No packages found', 'knowherepro' ); ?></p>

<?php endif; ?>
