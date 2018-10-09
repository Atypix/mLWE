<div class="rwp-scores <?php if ( $is_UR ) echo 'rwp-ur' ?>">

<?php
$cr_source	= $this->review_field('review_criteria_source', true);
if( !$is_UR ) {
	$scores = ( $cr_source == 'reviewer' ) ? $this->review_field('review_scores', true) : $this->users_data['criteria'];
} else {
	$scores = $this->users_data['criteria'];
}
$criteria 	= $this->template_field('template_criterias', true);
$order  	= $this->template_field('template_criteria_order', true);
$order		= ( $order == null ) ? array_keys( $criteria) : $order;
$theme 		= $this->template_field('template_theme', true);
$font_size  = $this->template_field('template_box_font_size', true);
$score_size = intval( $font_size ) + 4;
$max 		= $this->template_field('template_maximum_score', true);

foreach ($order as $i) {

	$score = isset( $scores[$i] ) ? $scores[$i] : 0;

	switch ( $theme ) {

		case 'rwp-theme-8':

			echo '<div class="rwp-criterion">';
				echo '<span class="rwp-criterion-label">'.  $criteria[$i] .'</span>';
				echo $this->get_stars( $score );
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-6':
			echo '<div class="rwp-criterion">';
				echo '<span class="rwp-criterion-label" style="font-size: '. ($font_size - 2) .'px; line-height: '. ($font_size -2) .'px;">'.  $criteria[$i] .'</span>';
				echo $this->get_stars( $score, $max );
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-5':
			echo '<div class="rwp-criterion">';
				echo '<div class="rwp-criterion-text">';
					echo '<span class="rwp-criterion-label" style="font-size: '. ($font_size - 2) .'px; line-height: '. ($font_size - 2) .'px;">'.  $criteria[$i] .'</span>';
					echo '<span class="rwp-criterion-score" style="font-size: '. $font_size     .'px; line-height: '. $font_size .'px;">'.  RWP_Reviewer::format_number( $score ) .'</span>';
				echo '</div><!-- /criterion-text -->';

				echo '<div class="rwp-criterion-bar-base">';
					echo $this->get_score_bar( $score );
				echo '</div><!-- /criterion-bar -->';
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-4':

			echo '<div class="rwp-criterion">';
				echo '<div class="rwp-criterion-bar-base">';
					echo $this->get_score_bar( $score, $theme, $font_size );
				echo '</div><!-- /criterion-bar -->';
				echo '<span class="rwp-criterion-label">'.  $criteria[$i] .'</span>';
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-3':

			echo '<div class="rwp-criterion">';
				echo $this->get_circle( $score );
				echo '<span class="rwp-criterion-label" style="line-height: '. $font_size .'px;">'.  $criteria[$i] .'</span>';
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-2':

			echo '<div class="rwp-criterion">';
				echo '<span class="rwp-criterion-label" style="line-height: '. $font_size .'px;">'.  $criteria[$i] .'</span>';
				echo $this->get_stars( $score, $max );
			echo '</div><!-- /criterion -->';
			break;

		case 'rwp-theme-1':
		default:

			echo '<div class="rwp-criterion">';
				echo '<div class="rwp-criterion-text">';
					echo '<span class="rwp-criterion-label" style="line-height: '. $font_size .'px;">'.  $criteria[$i] .'</span>';
					echo '<span class="rwp-criterion-score" style="line-height: '. $score_size .'px; font-size: '. $score_size .'px;">'.  RWP_Reviewer::format_number( $score ) .'</span>';
				echo '</div><!-- /criterion-text -->';

				echo '<div class="rwp-criterion-bar-base">';
					echo $this->get_score_bar( $score );
				echo '</div><!-- /criterion-bar -->';
			echo '</div><!-- /criterion -->';
			break;
	}
}
?>

</div> <!-- /scores -->
