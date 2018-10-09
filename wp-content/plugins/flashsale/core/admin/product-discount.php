<?php 
$setting=get_option("pw_flashsale_discount_options");

		$pw_array=(get_post_meta($_GET['pw_id'],'pw_array',true)==""?array():get_post_meta($_GET['pw_id'],'pw_array',true));
		$pw_to=get_post_meta($_GET['pw_id'],'pw_to',true);					
		$id=rand(0,1000);
		$countdown="style1";
		$fontsize="medium";			
		$offset_utc=get_option('pw_woocommerce_flashsale_timezone_countdown','-8');
		$html='
			<div class="fl-pcountdown-cnt">
				<ul class="fl-'.$countdown.' fl-'.$fontsize.' fl-countdown fl-countdown-pub countdown_'.$id.'">
				  <li><span class="days">00</span><p class="days_text">'.$setting['Days'].'</p></li>
					<li class="seperator">:</li>
					<li><span class="hours">00</span><p class="hours_text">'.$setting['Hour'].'</p></li>
					<li class="seperator">:</li>
					<li><span class="minutes">00</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
					<li class="seperator">:</li>
					<li><span class="seconds">00</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
				</ul>
			</div>
			<script type="text/javascript">
				jQuery(".countdown_'.$id.'").countdown({
					date: "'.$pw_to.'",
					offset: "'.$offset_utc.'",
					day: "Day",
					days: "Days"
				}, function () {
				//	alert("Done!");
				});
			</script>';						
?>
<div class="product-list-title"><p><?php _e('Time Remening','pw_wc_flash_sale')?></p> <?php echo $html;?></div>
<table class="wp-list-table widefat fixed posts" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" cellspacing="0">
		<thead>
			<tr>
				<th scope='col' data-toggle="true" class='manage-column column-serial_number'  style="">
					<a href="#"><span><?php _e('S.No', 'pw_wc_flash_sale'); ?></span></a>
				</th>
				<th scope='col' class='manage-column'  style=""><?php _e('Name', 'pw_wc_flash_sale'); ?></th>
				<th scope='col' class='manage-column'  style=""><?php _e('SKU', 'pw_wc_flash_sale'); ?></th>						
				<th scope='col' class='manage-column'  style=""><?php _e('Price', 'pw_wc_flash_sale'); ?></th>				
				<th scope='col' class='manage-column'  style=""><?php _e('Categories', 'pw_wc_flash_sale'); ?></th>				
				<th scope='col' class='manage-column'  style=""><?php _e('Tags', 'pw_wc_flash_sale'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Featured', 'pw_wc_flash_sale'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Date', 'pw_wc_flash_sale'); ?></th>
			</tr>
		</thead>				
		<tbody id="grid_level_result">
		   <?php
		$args = array(
			'post_type'  => 'product',
			'posts_per_page'=>-1,
			'post__in'		=> $pw_array,
			
		);
		$loop = new WP_Query( $args );					
	//	
				//$pr=$product->get_price_html();
			//	echo  $pr;
			//	echo get_the_content();
		$output="";
		if ( $loop->have_posts() )
		{
			$i=1;
			$pw_discount = get_post_meta( $_GET['pw_id'] , 'pw_discount', true );
			$pw_type_discount = get_post_meta( $_GET['pw_id'] , 'pw_type_discount', true );
			
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );				
			
			while ( $loop->have_posts() ) : 
				$loop->the_post();	
				//$price = get_post_meta( get_the_ID(), '_regular_price',true);
				$product = get_product( get_the_ID() );
				$base_price = $tax_display_mode == 'incl' ? $product->get_price_including_tax() : $product->get_price_excluding_tax();
				$sku = get_post_meta( get_the_ID(), '_sku',true);
				$_stock_status = get_post_meta( get_the_ID(), '_stock_status',true);
				$_featured = get_post_meta( get_the_ID(), '_featured',true);				
				
				if($pw_type_discount=="percent")
					$pw_discount_show='-'.$pw_discount.'%';
				else
					$pw_discount_show='-'.wc_price($pw_discount);				
				
				$type=array(
					'type'=>$pw_type_discount,
					'discount'=>$pw_discount,
				);	
				$result=PW_Discount_Price::price_discunt($base_price,$type);	
				
				//$discount=woocommerce_flashsale::calculate_discount($pw_type_discount,$price,$pw_discount);
				//$result= woocommerce_flashsale::calculate_price_discount($price,$discount);

				//if($discount>0)
					$show_price='<del>'.wc_price($base_price)  .'</del> '.wc_price($result);
				//else
				//	$show_price=wc_price($result);
				
				$cate =get_the_term_list( get_the_ID(), 'product_cat','',',');
				$tag =get_the_term_list( get_the_ID(), 'product_tag','',',');
				//pw_woocommerc_get_cat( $post->ID, ', ', ' <span class="posted_in">' . $tax . ': ', '</span>');
				$output.='
				<tr class="pw_level_tr" id="'.get_the_ID().'">
					<td>'.$i++.'</td>
					<td><a href="'.get_permalink().'">'.get_the_title().'</a></td>
					<td>'.$sku.'</td>							
					<td>'.$show_price.'</td>
					<td>'.$cate.'</td>
					<td>'.$tag.'</td>
					<td>'.$_featured.'</td>
					<td>'.get_the_date().'</td>
				</tr>';						
			endwhile;						
		}
		echo $output;
		?>
		</tbody>
</table>
