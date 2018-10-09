<?php 
if ( isset( $this->review['review_custom_tabs'] ) && !empty( $this->review['review_custom_tabs'] ) ) {

    $review_tabs        = $this->review_field('review_custom_tabs', true);
    $template_tabs      = $this->template_field('template_custom_tabs', true);
    $review_tabs_opts   = $this->default_review['review_custom_tabs']['options']; 
    $theme              = $this->template_field('template_theme', true);

    echo '<div class="rwp-custom-tabs-wrap">';

    foreach ($review_tabs as $key => $tab) {
        
        if( !isset( $template_tabs[ $key ] ) ) continue; // The tab has not own template reference

        if( empty( $tab[ 'tab_link' ] ) && empty( $tab[ 'tab_value' ] ) ) continue;

        $has_link   = ( isset( $tab[ 'tab_link' ] ) && !empty( $tab[ 'tab_link' ] ) );
        $ur         = ( $is_UR ) ? 'rwp-ur' : '';
        $style      = ( $theme != 'rwp-theme-8' ) ? 'style="background-color:'. $template_tabs[ $key ]['tab_color'] .'"' : '';
        $style2     = ( $theme == 'rwp-theme-8' ) ? 'style="background-color:'. $template_tabs[ $key ]['tab_color'] .'"' : '';

        echo ( $has_link ) ? '<a href="'. $tab['tab_link'] .'" class="rwp-custom-tab '.$ur.'" '. $style .'>' : '<div class="rwp-custom-tab '.$ur.'" '. $style .'>';
        
        foreach ($review_tabs_opts as $k => $v) {

            if( $k == 'tab_link' || !isset( $tab[ $k ] ) || empty( $tab[ $k ] ) ) continue;

            echo '<span class="rwp-'. $k .'" '. $style2.'>'. $tab[ $k ] .'</span>';
        }

        echo '<span class="rwp-tab_label">'. $template_tabs[ $key ]['tab_label'] .'</span>';

        echo ( $has_link ) ? '</a>' : '</div>';
    }

    echo '</div><!-- /custom tabs-->';
}
?>
