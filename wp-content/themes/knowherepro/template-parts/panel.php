<!-- - - - - - - - - - - - - Navigation Panel - - - - - - - - - - - - - - -->

<div id="nav-panel" class="nav-panel">
	<?php
		$menu = knowhere_mobile_menu();
		if ( $menu ) {
			echo '<nav class="mobile-menu-wrap">'. $menu .'</nav>';
		}
	?>
</div><!--/ .kw-nav-panel-->

<!-- - - - - - - - - - - - / Navigation Panel - - - - - - - - - - - - - -->