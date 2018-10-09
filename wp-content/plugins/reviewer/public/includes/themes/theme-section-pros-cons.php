<?php $pros = $this->review_field('review_pros', true);  if( ! empty( $pros ) ): ?>

<div class="rwp-pros-wrap">

    <span 
        class="rwp-pros-label" 
        style="color: <?php $this->template_field('template_pros_label_color') ?>; 
               font-size: <?php $this->template_field('template_pros_label_font_size') ?>px;
               line-height: <?php $this->template_field('template_pros_label_font_size') ?>px;"><?php $this->template_field('template_pros_label') ?></span>

    <div 
        class="rwp-pros"
        style="font-size: <?php $this->template_field('template_pros_text_font_size') ?>px;"><?php echo $this->review_field('review_pros', true) ?></div><!--/pros-->

</div><!--/pros-wrap-->

<?php endif; ?>

<?php $cons = $this->review_field('review_cons', true); if( ! empty( $cons ) ): ?>

<div class="rwp-cons-wrap">

    <span 
        class="rwp-cons-label" 
        style="color: <?php $this->template_field('template_cons_label_color') ?>; 
               font-size: <?php $this->template_field('template_cons_label_font_size') ?>px;
               line-height: <?php $this->template_field('template_cons_label_font_size') ?>px;"><?php $this->template_field('template_cons_label') ?></span>

    <div 
        class="rwp-cons"
        style="font-size: <?php $this->template_field('template_cons_text_font_size') ?>px;"><?php echo $this->review_field('review_cons', true) ?></div><!--/pros-->

</div><!--/pros-wrap-->

<?php endif; ?>