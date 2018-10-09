<?php 
$ret.= '
<div class="fl-post-cnt">
<div class="fl-thumbnail">';
	if(trim($brands_attr['show_discount']) == "yes" && $flag==true){
	   $ret.='<div class="fl-banner fl-roundbanner">';
		$ret.='<span>' . $pw_discount_show.'</span>';
	   $ret.='</div>';
	  }
	$ret.= '
	<a href="'.$permalink.'"><div class="fl-thumbnail-img  '.$brands_attr['item_image_eff'].'">'.$image.'</div></a></div>';
$ret .='<div class="fs-itemdesc">';
    $ret.= '<h3><a href="'.$permalink.'">'.$title.'</a></h3>';
    /*if($result!="")
        $ret.= $result;
    else
    {*/
		$ret.='<del> <span class="amount">'.wc_price($base_price).'</span></del>';
		$ret.='<ins> <span class="amount">'.wc_price($price).'</span></ins>';
	//}
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
$ret .='</div>';
$ret .='</div>';
?>