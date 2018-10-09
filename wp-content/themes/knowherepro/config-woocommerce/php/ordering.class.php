<?php

if ( !class_exists('Knowhere_Catalog_Ordering') ) {

	class Knowhere_Catalog_Ordering {

		function __construct() {

		}

		public function woo_build_query_string ($params = array(), $key, $value) {
			$params[$key] = $value;
//			$paged = (array_key_exists('product_count', $params)) ? 'paged=1&' : '';
			return "?" . http_build_query($params);
		}

		public function woo_active_class($key1, $key2) {
			if ( $key1 == $key2 ) return " class='kw-active'";
		}

		public function output() {

			global $knowhere_config, $knowhere_settings;
			parse_str($_SERVER['QUERY_STRING'], $params);

			$product_order = array();
			$product_order['default'] 	= esc_html__("Default",'knowherepro');
			$product_order['title'] 	= esc_html__("Name",'knowherepro');
			$product_order['price'] 	= esc_html__("Price",'knowherepro');
			$product_order['date'] 		= esc_html__("Date",'knowherepro');
			$product_order['popularity'] = esc_html__("Popularity",'knowherepro');

			$product_order_key = !empty($knowhere_config['woocommerce']['product_order']) ? $knowhere_config['woocommerce']['product_order'] : 'default';
			?>

			<header class="kw-sorting">

				<div class="kw-results">
					<?php woocommerce_result_count() ?>
				</div><!--/ .kw-results -->

				<div class="kw-sort">

					<div class="kw-custom-select kw-over">
						<div class="kw-selected-option"><?php echo esc_html( $product_order[$product_order_key] ) ?></div>
						<ul class="kw-options-list">
							<li><a <?php echo $this->woo_active_class($product_order_key, 'default'); ?> href="<?php echo $this->woo_build_query_string($params, 'product_order', 'default') ?>"><?php echo $product_order['default'] ?></a></li>
							<li><a <?php echo $this->woo_active_class($product_order_key, 'title'); ?> href="<?php echo $this->woo_build_query_string($params, 'product_order', 'title') ?>"><?php echo $product_order['title'] ?></a></li>
							<li><a <?php echo $this->woo_active_class($product_order_key, 'price'); ?> href="<?php echo $this->woo_build_query_string($params, 'product_order', 'price') ?>"><?php echo $product_order['price'] ?></a></li>
							<li><a <?php echo $this->woo_active_class($product_order_key, 'date'); ?> href="<?php echo $this->woo_build_query_string($params, 'product_order', 'date') ?>"><?php echo $product_order['date'] ?></a></li>
							<li><a <?php echo $this->woo_active_class($product_order_key, 'popularity'); ?> href="<?php echo $this->woo_build_query_string($params, 'product_order', 'popularity') ?>"><?php echo $product_order['popularity'] ?></a></li>
						</ul>
					</div><!--/ .kw-custom-select-->

				</div><!--/ .kw-sort -->

			</header><!--/ .kw-sorting-->

			<?php
		}

	}
}

?>
