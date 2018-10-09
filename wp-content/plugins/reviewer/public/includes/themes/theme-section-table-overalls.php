<div class="rwp-scores-wrap">

    <?php if ( !$is_UR ): ?>
    
    <?php 
    $custom_score   = (isset( $review['review_custom_overall_score'] ) && !empty( $review['review_custom_overall_score'] ) ) ? $review['review_custom_overall_score'] : '';
    $overall        =  ( empty( $custom_score ) ) ? RWP_Reviewer::get_review_overall_score( $review )  : $custom_score;
    ?>

    <div class="rwp-total-score-wrap" <?php $this->style_total_score(); ?>>
        <span class="rwp-total-score"><?php echo $overall ?></span>
        <span class="rwp-total-score-label"><?php echo $this->template['template_total_score_label']; ?></span>
    </div><!--/total-score-->
    
    <?php endif ?>

    <?php if( $this->pref['preferences_authorization'] != 'disabled' ): ?>

    <?php 
        $data = RWP_Reviewer::get_users_overall_score( $review['review_ratings_scores'], $review['review_post_id'], $review['review_id']  );
        $score = $data['score'];
        $count = $data['count'];
        $count_label = ( $count == 1 ) ? $this->template_field('template_users_count_label_s', true) : $this->template_field('template_users_count_label_p', true);
    ?>

    <div class="rwp-users-score-wrap <?php echo $is_UR ? 'rwp-ur' : '' ?>"<?php $this->style_users_score(); ?>>
        <span class="rwp-users-score"><?php echo $score; ?></span>
        <span class="rwp-users-score-label"><?php echo $count; ?> <?php echo $count_label ?></span>
    </div><!--/users-score-->
	<?php endif;?>
</div><!--/scores-wrap-->