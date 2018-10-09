<?php get_header(); ?>
<?php
get_header( 'shop' ); ?>
	<?php
		do_action( 'woocommerce_before_main_content' );
		
		if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); 
			global $post;

			$matched_products = get_posts(
				array(
					'post_type' 	=> 'flash_sale',
					'numberposts' 	=> -1,
					'post_status' 	=> 'publish',
					'fields' 		=> 'ids',
					'post__in'		=>array($post->ID),
					'no_found_rows' => true,
				)
			);
			//echo $matched_products[0];
			$arr="";
			$arr= get_post_meta($matched_products[0],'pw_array',true);			
			$query_args = array(
				'post_status'    => 'publish', 
				'post_type'      => 'product', 
				'post__in'       => $arr, 
				'order'=>'data',
				'orderby'=>'DESC',					
				);

			// Add meta_query to query args
			$query_args['meta_query'] = array();

			// Create a new query
			$products = new WP_Query($query_args);
			if ( $products->have_posts() ) :
				woocommerce_product_loop_start();
				while ( $products->have_posts() ) :
					$products->the_post();
					woocommerce_get_template_part( 'content', 'product' );
				endwhile; // end of the loop. 
				woocommerce_product_loop_end(); 
			endif;
				
			do_action( 'woocommerce_before_shop_loop' );
			?>


	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
 <script type='text/javascript'>
	/* <![CDATA[  */     
	jQuery(document).ready(function() 
	{
		jQuery("body").addClass("woocommerce woocommerce-page");
	}); 
/* ]]> */
</script> 
<?php get_footer(); ?>