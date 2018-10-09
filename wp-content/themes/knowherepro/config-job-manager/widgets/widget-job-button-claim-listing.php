<?php
/**
 * Widget: Button claim listing
 *
 */
class Knowhere_Widget_Button_Claim_Listing extends Knowhere_Widget {

    public function __construct() {

        $this->widget_description = esc_html__( 'Display button claim listing.', 'knowherepro' );
		$this->widget_cssclass = 'widefat text wp-editor-area';
        $this->widget_id          = 'knowhere_claim_listing_button';
        $this->widget_name        = '&#x27A4; ' . esc_html__( 'Listing', 'knowherepro' ) . '  - ' . esc_html__( 'Claim button', 'knowherepro' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => esc_html__('Is This Your Listing?', 'knowherepro'),
                'label' => esc_html__( 'Title:', 'knowherepro' )
            ),
			'text' => array(
                'type'  => 'textarea',
                'std'   => '',
                'label' => esc_html__( 'Content:', 'knowherepro' )
            )

        );
        parent::__construct();
    }

    function widget( $args, $instance ) {

		if ( !defined( 'WPJMCL_VERSION' ) ) return;

        extract( $args );

		$text = !empty($instance['text']) ? $instance['text'] : '';

        $title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

        ob_start();

        echo $before_widget;

        if ( $title ) echo $before_title . sanitize_text_field($title) . $after_title;

		echo wpautop($text);

		$job_listing = wpjmcl\job_listing\Setup::get_instance();
		echo $job_listing->add_claim_link();

        echo $after_widget;

        wp_reset_postdata();

        $content = ob_get_clean();

        echo apply_filters( $this->widget_id, $content );
    }
}
