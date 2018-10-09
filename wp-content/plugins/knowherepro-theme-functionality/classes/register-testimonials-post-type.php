<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Knowhere_Post_Type_Testimonials extends Knowhere_Functionality {

    public $slug = 'testimonials';

    function __construct() {
        $this->init();
    }

    public function init() {

        $args = array(
            'labels' => $this->getLabels(
                esc_html__('Testimonial', 'knowherepro_app_textdomain'),
                esc_html__('Testimonials', 'knowherepro_app_textdomain')
            ),
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => null,
            'taxonomies' => array( 'testimonials_category' ),
            'supports' => array( 'title', 'page-attributes', 'revisions' ),
            'rewrite' => array( 'slug' => $this->slug ),
            'show_in_admin_bar' => true,
            'menu_icon' => 'dashicons-businessman'
        );

        register_post_type( $this->slug, $args );

        register_taxonomy( 'testimonials_category', $this->slug, array(
            'hierarchical' => true,
            "label" => "Categories",
            'query_var' => true,
            'rewrite' => true,
            'public' => true,
            'show_admin_column' => true
        ) );

        add_filter("manage_". $this->slug ."_posts_columns", array(&$this, "manage_testimonials_columns"));
        add_action("manage_". $this->slug ."_posts_custom_column", array(&$this, "manage_testimonials_custom_column"));
    }

    public function manage_testimonials_columns($columns) {
        $new_columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "thumb column-comments" => esc_html__('Thumb', 'knowherepro_app_textdomain'),
            "title" => esc_html__("Title", 'knowherepro_app_textdomain'),
            "city" => esc_html__("City", 'knowherepro_app_textdomain')
        );
        $columns = array_merge($new_columns, $columns);
        return $columns;
    }

    public function manage_testimonials_custom_column($column) {
        global $post;

        switch ( $column ) {
            case "thumb column-comments":

                $thumb_id = get_post_meta($post->ID, 'knowhere_testi_photo', true);

                if ( !empty($thumb_id) ) {
                    echo wp_get_attachment_image($thumb_id);
                }

                break;
            case "city":
                echo get_post_meta($post->ID, 'knowhere_testi_city', true);
                break;
        }
    }

}