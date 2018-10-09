<?php 
$ret.= '
<div class="fl-post-cnt">
<div class="fl-thumbnail">';
if(trim($brands_attr['show_discount']) == "yes" ){
   $ret.='<div class="fl-banner fl-roundbanner">';
    $ret.='<span>' . $pw_discount_show.'</span>';
   $ret.='</div>';
  }
$ret.= '
<a href="'.$permalink.'"><div class="fl-thumbnail-img  '.$brands_attr['item_image_eff'].'">'.$image.'</div></a></div>';
$ret .='<div class="fs-itemdesc">';
    $ret.= '<h3><a href="'.$permalink.'">'.$title.'</a></h3>';
  /*  if($result!="")
        $ret.= $result;
    else{*/
        //$ret.= wc_price($base_price);
		$ret.='<del> <span class="amount">'.wc_price($base_price).'</span></del>';
		$ret.='<ins> <span class="amount">'.wc_price($price).'</span></ins>';
	//}
	$ret .='</div>';
$ret .='</div>';
?>