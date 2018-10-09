<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ywgc-choose-design" class="ywgc-template-design" style="display: none">
	<div>
		<?php if ( count( $categories ) > 1 ): ?>
			<ul class="ywgc-template-categories">
				<li class="ywgc-template-item ywgc-category-all">
					<a href="#" class="ywgc-show-category ywgc-category-selected"
					   data-category-id="all">
						<?php _e( "Show all design", 'yith-woocommerce-gift-cards' ); ?>
					</a>
				</li>
				<?php foreach ( $categories as $item ): ?>
					<li class="ywgc-template-item ywgc-category-<?php echo $item->term_id; ?>">
						<a href="#" class="ywgc-show-category"
						   data-category-id="ywgc-category-<?php echo $item->term_id; ?>">
							<?php echo $item->name; ?>
						</a>
					</li>

				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="ywgc-design-list">

			<?php foreach ( $item_categories as $item_id => $categories ): ?>

				<div class="ywgc-design-item <?php echo $categories; ?> template-<?php echo $item_id; ?>">

					<div class="ywgc-preset-image">
						<?php echo wp_get_attachment_image( intval( $item_id ), apply_filters('yith_ywgc_preset_image_size','shop_catalog' ) ); ?>
						<?php if ( YITH_YWGC()->show_preset_title ):
							$post = get_post( $item_id );
							if ( $post ): ?>
								<span class="ywgc-preset-title"><?php echo $post->post_title; ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<button class="ywgc-choose-preset"
					        data-design-id="<?php echo $item_id; ?>"
					        data-design-url="<?php echo yith_get_attachment_image_url( intval( $item_id ), 'full' ); ?>"><?php _e( "Choose design", 'yith-woocommerce-gift-cards' ); ?></button>
				</div>

			<?php endforeach; ?>

		</div>
	</div>
</div>
