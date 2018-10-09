<?php
/**
 * Layout for the Amazon Addon
 *
 * @since   2.0.0
 * @package WPPR_Pro
 */

?>

<div class="wppr-review-details-fields wppr-review-fieldset">
	<ul>
		<li>
			<label for="cwp_rev_amazon_id"><?php _e( 'Amazon Product ID', 'wp-product-review' ); ?></label>
			<input type="text" name="cwp_rev_amazon_id" id="cwp_rev_amazon_id" value="
			<?php
			if ( isset( $amazon_id ) ) {
				echo $amazon_id;
			}

			?>
			" placeholder="B01483X0HY"/>
			<input type="button" class="button button-secondary" id="cwp_rev_amazon_id_bttn" value="<?php _e( 'Import', 'wp-product-review' ); ?>">
			<p class="description"><?php _e( 'You can find the ID from the URL. Sample', 'wp-product-review' ); ?> : http://www.amazon.com/Apple-MH0W2LL-10-Inch-Retina-Display/dp/<span style="background: yellow">B00OTWOAAQ</span>/ref=sr_1_1?s=pc&ie=UTF8&qid=1455193662&sr=1-1&keywords=ipad</p>
		</li>
	</ul>
</div>
