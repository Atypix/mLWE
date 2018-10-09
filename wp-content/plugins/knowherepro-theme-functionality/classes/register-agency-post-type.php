<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Knowhere_Post_Type_Agency extends Knowhere_Functionality {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */

	public $slug = 'knowhere_agency';

	function __construct() {

		$this->definition();

        add_filter( "manage_edit-". $this->slug . "_columns", array( $this, 'custom_columns' ) );
        add_action( "manage_". $this->slug ."_posts_custom_column", array( $this, 'custom_columns_manage' ) );

    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    function definition() {

        $labels = array(
            'name'               => esc_html__( 'Agencies', 'knowherepro_app_textdomain' ),
            'singular_name'      => esc_html__( 'Agency', 'knowherepro_app_textdomain' ),
            'add_new'            => esc_html__( 'Add New Agency', 'knowherepro_app_textdomain' ),
            'add_new_item'       => esc_html__( 'Add New Agency', 'knowherepro_app_textdomain' ),
            'edit_item'          => esc_html__( 'Edit Agency', 'knowherepro_app_textdomain' ),
            'new_item'           => esc_html__( 'New Agency', 'knowherepro_app_textdomain' ),
            'all_items'          => esc_html__( 'Agencies', 'knowherepro_app_textdomain' ),
            'view_item'          => esc_html__( 'View Agency', 'knowherepro_app_textdomain' ),
            'search_items'       => esc_html__( 'Search Agency', 'knowherepro_app_textdomain' ),
            'not_found'          => esc_html__( 'No agencies found', 'knowherepro_app_textdomain' ),
            'not_found_in_trash' => esc_html__( 'No agencies found in Trash', 'knowherepro_app_textdomain' ),
            'parent_item_colon'  => '',
            'menu_name'          => esc_html__( 'Agencies', 'knowherepro_app_textdomain' ),
        );

        $args = array(
            'labels' => $labels,
            'supports'        => array( 'title', 'editor', 'thumbnail' ),
            'public'          => true,
            'capability_type' => 'page',
            'show_ui'         => true,
            'menu_position' => 15,
            'has_archive'     => true,
            'rewrite'         => array( 'slug' => esc_html__( 'agencies', 'knowherepro_app_textdomain' ) ),
            'categories'      => array(),
        );

        register_post_type( $this->slug, $args );

    }

    /**
     * Custom admin columns for post type
     *
     * @access public
     * @return array
     */
    public function custom_columns() {
        $fields = array(
            'cb' 				=> '<input type="checkbox" />',
            'agency_id' 			=> esc_html__( 'Agency ID', 'knowherepro_app_textdomain' ),
            'title' 			=> esc_html__( 'Title', 'knowherepro_app_textdomain' ),
            'thumbnail' 		=> esc_html__( 'Thumbnail', 'knowherepro_app_textdomain' ),
//            'agency_thumbnail' 	=> esc_html__( 'Picture', 'knowherepro_app_textdomain' ),
//            'category' 		    => esc_html__( 'Category', 'knowherepro_app_textdomain' ),
//            'city' 		   		=> esc_html__( 'City', 'knowherepro_app_textdomain' )
//            'email'      		=> esc_html__( 'E-mail', 'knowherepro_app_textdomain' ),
//            'web'      		    => esc_html__( 'Web', 'knowherepro_app_textdomain' ),
//            'mobile'      		=> esc_html__( 'Mobile', 'knowherepro_app_textdomain' ),
        );

        return $fields;
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public function custom_columns_manage( $column ) {
        global $post;

        switch ( $column ) {
            case 'thumbnail':
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'thumbnail', array(
                        'class'     => 'attachment-thumbnail attachment-thumbnail-small',
                    ) );
                } else {
                    echo '-';
                }
                break;
            case 'agency_id':
                echo get_the_ID();
                break;
//            case 'category':
//                echo knowhere_admin_taxonomy_terms ( $post->ID, 'agent_category', 'knowhere_agent' );
//                break;
//			case 'city':
//				echo knowhere_admin_taxonomy_terms( $post->ID, 'agent_city', 'knowhere_agent' );
//				break;
//            case 'email':
//                $email = get_post_meta( get_the_ID(),  'agent_email', true );
//
//                if ( ! empty( $email ) ) {
//                    echo esc_attr( $email );
//                } else {
//                    echo '-';
//                }
//                break;
//            case 'web':
//                $web = get_post_meta( get_the_ID(), 'agent_website', true );
//
//                if ( ! empty( $web ) ) {
//                    echo '<a target="_blank" href="'.esc_url( $web ).'">'.esc_url( $web ).'</a>';
//                } else {
//                    echo '-';
//                }
//                break;
//            case 'mobile':
//                $phone = get_post_meta( get_the_ID(), 'agent_mobile', true );
//
//                if ( ! empty( $phone ) ) {
//                    echo esc_attr( $phone );
//                } else {
//                    echo '-';
//                }
//                break;
        }

    }

}