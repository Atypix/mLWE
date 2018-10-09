<?php
$ret .='<div class="fl-post-cnt fl-boxed-cnt fl-boxed-layout2">';
	$ret.='<div class="fl-thumbnail">';
		if(trim($brands_attr['show_discount']) == "yes" ){
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
			$ret.='<del> <span class="amount">'.wc_price($base_price).'</span></del>';
			$ret.='<ins> <span class="amount">'.wc_price($price).'</span></ins>';
		$ret.='</div>';
		
		
	$ret.='</div>';
		
	$ret.='<div class="fl-boxed-detail-unvis">';
		$ret.='<div class="fl-buttons">';
			$ret.='<a class="fl-btn fl-readmore-btn" href="'.PW_Discount_function::show_add_to_cart_product('link',$a).'" >Add To Cart</a>';
		$ret.='</div>';
	$ret.='</div>';
	$ret.='<div class="fl-thumbnail-img '.$brands_attr['item_image_eff'].'">';
		$ret.='<a href="'.$permalink.'">'.$image.'</a>';
	$ret.='</div>';
   $ret .='</div><!--fl-thumbnail -->';	
$ret .='</div>';
