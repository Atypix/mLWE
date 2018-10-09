<?php
$ret .='<div class="fl-post-cnt fl-boxed-cnt fl-boxed-layout1">';
	$ret.='<div class="fl-thumbnail">';
		if(trim($brands_attr['show_discount']) == "yes" && $flag==true){
			$ret.='<div class="fl-banner fl-roundbanner">';
				$ret.='<span>' . $pw_discount_show.'</span>';
			$ret.='</div>';
		}
		$ret.='<a href="'.$permalink.'">'.'<div class="fl-thumbnail-overlay"></div>'.'</a>';
	$ret.='<div class="fl-boxed-detail-vis">';
		$ret.='<div class="fl-cat-meta">';
		$ret.='<span class="fl-meta-item">';
	$terms = get_the_terms( $a, 'product_cat' );
	if ( !is_wp_error( $terms ) ){
		if ( !empty( $terms ) ){
			foreach ( $terms as $term ) {	
				$link = get_term_link( $term, 'product_cat' );
				$ret .='<a href="'.$link.'">'. $term->name.'</a>';
			}	
		}
	}		
			$ret.='</span>';
		$ret.='</div>';
		$ret.='<div class="fl-title">';
			$ret.='<a href="'.$permalink.'">'.$title.'</a>';
		$ret.='</div>';
		
		$ret.='<div class="fl-price-cnt">';
			if($base_price!=$price)
				$ret.='<del> <span class="amount">'.wc_price($base_price).'</span></del>';
			$ret.='<ins> <span class="amount">'.wc_price($price).'</span></ins>';
		$ret.='</div>';
		if ( trim($brands_attr['show_countdown']) == "yes")
		{
			$ret .= '<div class="fl-countdown-cnt countdown-'.$rand_id.'">';
			if(strtotime($blogtime)<strtotime($pw_to) && $flag==true )
			{
				$id=rand(0,1000);			
				$ret.='<ul class="fl-'.$brands_attr['countdown_style'].' fl-'.$brands_attr['countdown_size'].' fl-countdown countdown_'.$id.'">
						  <li><span class="days">00</span><p class="days_text">'.$setting['Days'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="hours">00</span><p class="hours_text">'.$setting['Hour'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="minutes">00</span><p class="minutes_text">'.$setting['Minutes'].'</p></li>
							<li class="seperator">:</li>
							<li><span class="seconds">00</span><p class="seconds_text">'.$setting['Seconds'].'</p></li>
						</ul>
					<script type="text/javascript">
						jQuery(".countdown_'.$id.'").countdown({
							date: "'.$pw_to.'",
							offset: "'.$offset_utc.'",
							day: "Day",
							days: "'.$setting['Days'].'",
							hours: "'.$setting['Hour'].'",
							minutes: "'.$setting['Minutes'].'",
							seconds: "'.$setting['Seconds'].'",
						}, function () {
						//	alert("Done!");
						});
					</script>';
			}
			$ret .= '</div>';				
		}
	$ret.='</div>';
	$var = new PW_Discount_function();
	$ret.='<div class="fl-boxed-detail-unvis">';
		$cart='';
		$cart = do_shortcode('[add_to_cart id="'.$a.'"]');			
		$ret.='<div class="fl-buttons">';
			$ret .='<div class="fl-btn fl-readmore-btn">'.$cart.'</div>';
			//$ret.='<a class="fl-btn fl-readmore-btn" href="'.$var->show_add_to_cart_product('link',$a).'" >Add To Cart</a>';
		$ret.='</div>';
	$ret.='</div>';
	$ret.='<div class="fl-thumbnail-img '.$brands_attr['item_image_eff'].'">';
		$ret.=$image;
	$ret.='</div>';
  $ret .='</div><!--fl-thumbnail -->';
$ret .='</div>';
