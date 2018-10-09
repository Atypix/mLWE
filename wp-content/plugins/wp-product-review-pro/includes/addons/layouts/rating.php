<?php
/**
 *  WP Prodact Review front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      $review_object WPPR_Review_Model
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
$rating = $review_object->get_rating();
?>

<div class="review-wu-grade">
	<div class="cwp-review-chart">
		<span>
			<div class="cwp-review-percentage" data-percent="<?php echo $rating; ?>">
				<span class="cwp-review-rating"><?php echo $rating; ?></span>
			</div>
		</span>
	</div><!-- end .chart -->
</div>
