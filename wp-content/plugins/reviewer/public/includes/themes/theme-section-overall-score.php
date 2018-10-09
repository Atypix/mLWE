<?php if (!$is_UR): ?>
<div 
    class="rwp-overall-score <?php echo ( $this->is_users_rating_disabled() ) ? 'rwp-only' : '' ?>" 
    style="background: <?php $this->template_field('template_total_score_box_color') ?>;" 
    property="reviewRating" typeof="http://schema.org/Rating"
>

    <?php 
    $maximum_score = $this->template_field('template_maximum_score', true);
	$custom_score 	= $this->review_field( 'review_custom_overall_score', true );
	$overall 		=  ( empty( $custom_score ) ) ? RWP_Reviewer::get_review_overall_score( $this->review )  : $custom_score;
    $theme          = $this->template_field('template_theme', true);

    $this->snippets->add('review.reviewRating', array());
    $this->snippets->add('review.reviewRating.@type', 'Rating');
    $this->snippets->add('review.reviewRating.ratingValue', $overall);
    $this->snippets->add('review.reviewRating.worstRating', $this->template_field('template_minimum_score', true));
    $this->snippets->add('review.reviewRating.bestRating', $this->template_field('template_maximum_score', true));
    ?>
    <meta property="bestRating" content="<?php $this->template_field('template_maximum_score') ?>">
    <meta property="worstRating" content="<?php $this->template_field('template_minimum_score') ?>">

    <?php if ($theme != 'rwp-theme-4'): ?>
        <span class="rwp-overlall-score-value" property="ratingValue"><?php echo $overall ?> <i>/ <?php echo $maximum_score; ?></i></span>
        <span class="rwp-overlall-score-label"><?php $this->template_field('template_total_score_label') ?></span>
    <?php else: ?>        
        <span class="rwp-overlall-score-label"><?php $this->template_field('template_total_score_label') ?></span>
        <span class="rwp-overlall-score-value" property="ratingValue"><?php echo $overall ?> <i>/ <?php echo $maximum_score; ?></i></span>
    <?php endif ?>

    
</div><!--/overall-score-->
<?php endif ?>