<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

	<div class="ywgc-generator" <?php if ( ! ( $product instanceof WC_Product_Gift_Card ) ): echo 'style="display:none"'; endif; ?>>

		<input type="hidden" name="ywgc-is-physical" value="1" />

		<?php if ( ! ( $product instanceof WC_Product_Gift_Card ) ): ?>
			<input type="hidden" name="ywgc-as-present-enabled" value="1">
		<?php endif; ?>

		<div class="gift-card-content-editor">

			<?php do_action( 'yith_ywgc_physical_gift_card_preview_content', $product ); ?>

			<?php do_action( 'yith_ywgc_physical_generator_buttons_before', $product ); ?>

		</div>
	</div>
<?php

do_action( 'yith_ywgc_physical_gift_card_preview_end', $product );
