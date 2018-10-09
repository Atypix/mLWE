<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Knowhere_Post_Type_Agent extends Knowhere_Functionality {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */

	public $slug = 'knowhere_agent';

	function __construct() {

		$this->definition();

        add_filter( "manage_edit-". $this->slug . "_columns", array( $this, 'custom_columns' ) );
        add_action( "manage_". $this->slug ."_posts_custom_column", array( $this, 'custom_columns_manage' ) );

//		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    function definition() {

        $labels = array(
            'name' => esc_html__( 'Agents','knowherepro_app_textdomain' ),
            'singular_name' => esc_html__( 'Agent','knowherepro_app_textdomain' ),
            'add_new' => esc_html__( 'Add New','knowherepro_app_textdomain' ),
            'add_new_item' => esc_html__( 'Add New Agent', 'knowherepro_app_textdomain' ),
            'edit_item' => esc_html__( 'Edit Agent', 'knowherepro_app_textdomain' ),
            'new_item' => esc_html__( 'New Agent', 'knowherepro_app_textdomain' ),
            'view_item' => esc_html__( 'View Agent', 'knowherepro_app_textdomain' ),
            'search_items' => esc_html__( 'Search Agent', 'knowherepro_app_textdomain' ),
            'not_found' =>  esc_html__( 'No Agent found', 'knowherepro_app_textdomain' ),
            'not_found_in_trash' => esc_html__( 'No Agent found in Trash', 'knowherepro_app_textdomain' ),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'hierarchical' => true,
            'can_export' => true,
//            'capabilities'    => array( $this, 'get_agent_capabilities' ),
            'menu_icon' => 'dashicons-admin-users',
            'menu_position' => 15,
            'supports' => array('title','editor', 'thumbnail', 'page-attributes','revisions'),
            'rewrite' => array( 'slug' => esc_html__('agent', 'knowherepro_app_textdomain') )
        );

        register_post_type( $this->slug, $args );

		register_taxonomy( 'agent_category', array( $this->slug ), array(
                'labels' => $this->getTaxonomyLabels(
                    esc_html__('Category', 'knowherepro_app_textdomain'),
                    esc_html__('Categories', 'knowherepro_app_textdomain')
                ),
				'hierarchical'  => true,
				'query_var'     => true,
				'rewrite'       => array( 'slug' => 'agent_category' )
			)
		);

        register_taxonomy( 'agent_skills', array( $this->slug ), array(
            "hierarchical" => true,
            "labels" => $this->getTaxonomyLabels(
                esc_html__('Skill', 'knowherepro_app_textdomain'),
                esc_html__('Skills', 'knowherepro_app_textdomain')
            ),
            "singular_label" => __("skill", 'mad_app_textdomain'),
            "show_tagcloud" => true,
            'query_var' => true,
            'rewrite'       => array( 'slug' => 'agent_skills' ),
            'show_in_nav_menus' => false,
            'capabilities' => array('manage_terms'),
            'show_ui' => true
        ));

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
            'agent_id' 			=> esc_html__( 'Agent ID', 'knowherepro_app_textdomain' ),
            'title' 			=> esc_html__( 'Agent Name', 'knowherepro_app_textdomain' ),
            'agent_thumbnail' 	=> esc_html__( 'Picture', 'knowherepro_app_textdomain' ),
            'category' 		    => esc_html__( 'Category', 'knowherepro_app_textdomain' ),
            'address' 		   	=> esc_html__( 'Address', 'knowherepro_app_textdomain' )
//            'email'      		=> esc_html__( 'E-mail', 'knowherepro_app_textdomain' ),
//            'web'      		    => esc_html__( 'Web', 'knowherepro_app_textdomain' ),
//            'mobile'      		=> esc_html__( 'Mobile', 'knowherepro_app_textdomain' ),
        );

        return $fields;
    }

    public function get_agent_capabilities() {

        $caps = array(
            // meta caps (don't assign these to roles)
            'edit_post'              => 'edit_agent',
            'read_post'              => 'read_agent',
            'delete_post'            => 'delete_agent',

            // primitive/meta caps
            'create_posts'           => 'create_agents',

            // primitive caps used outside of map_meta_cap()
            'edit_posts'             => 'edit_agents',
            'edit_others_posts'      => 'edit_others_agents',
            'publish_posts'          => 'publish_agents',
            'read_private_posts'     => 'read_private_agents',

            // primitive caps used inside of map_meta_cap()
            'read'                   => 'read',
            'delete_posts'           => 'delete_agents',
            'delete_private_posts'   => 'delete_private_agents',
            'delete_published_posts' => 'delete_published_agents',
            'delete_others_posts'    => 'delete_others_agents',
            'edit_private_posts'     => 'edit_private_agents',
            'edit_published_posts'   => 'edit_published_agents'
        );

        return apply_filters( 'knowhere_get_agent_capabilities', $caps );
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
            case 'agent_thumbnail':
                if ( has_post_thumbnail() ) {
                   the_post_thumbnail( 'thumbnail' );
                } else {
                    echo '-';
                }
                break;
            case 'agent_id':
                echo $post->ID;
                break;
            case 'category':
                echo knowhere_admin_taxonomy_terms ( $post->ID, 'agent_category', 'knowhere_agent' );
                break;
			case 'address':
                echo mad_meta( 'knowhere_agent_address', '', $post->ID );
				break;
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