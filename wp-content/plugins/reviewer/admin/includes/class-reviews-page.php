<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Reviews_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	protected $templates_option;
	
	public function __construct()
	{
		parent::__construct();

		$this->menu_slug = 'reviewer-reviews-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->templates_option = RWP_Reviewer::get_option( 'rwp_templates' );
		$this->add_menu_page();

        // Localize 
        add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'Review Boxes', $this->plugin_slug), __( 'Review Boxes', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	} 

    public function localize_script() 
    {
        $action_name = 'rwp_ajax_action_reset_users_score';
        wp_localize_script( $this->plugin_slug . '-admin-script', 'resetScoreObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
    
        $action_name = 'rwp_ajax_action_delete_review';
        wp_localize_script( $this->plugin_slug . '-admin-script', 'deleteReviewObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
    }

    public static function ajax_callback()
    {
        $ratings = get_post_meta( $_POST['postId'], 'rwp_ratings', true );

        if( isset( $ratings[ $_POST['reviewId'] ] ) )
            unset( $ratings[ $_POST['reviewId'] ] );

        update_post_meta($_POST['postId'], 'rwp_ratings' ,$ratings);

        delete_post_meta($_POST['postId'], 'rwp_rating_' . $_POST['reviewId'] );

        die( json_encode( array('msg' => __( 'Score reset', 'reviewer') ) ) );
    }


    public static function ajax_callback_delete_review()
    {
        $res = array( 'code' => 400, 'data'=> array( 'msg' => __( 'Unable to delete review', 'reviewer' ) ) );

        $post_reviews = get_post_meta( $_POST['postId'], 'rwp_reviews', true );

        if( isset( $post_reviews[ $_POST['reviewId'] ] ) && $post_reviews[ $_POST['reviewId'] ]['review_id'] == $_POST['reviewId'] ) {

            unset( $post_reviews[ $_POST['reviewId'] ] );
            $r = update_post_meta( $_POST['postId'], 'rwp_reviews', $post_reviews);

            if( $r === false)
                die( json_encode( $res ) );

            // Delete related ratings
            $ratings = get_post_meta( $_POST['postId'], 'rwp_ratings', true );

            if( isset( $ratings[ $_POST['reviewId'] ] ) )
                unset( $ratings[ $_POST['reviewId'] ] );

            update_post_meta($_POST['postId'], 'rwp_ratings' ,$ratings); // Old
            delete_post_meta($_POST['postId'], 'rwp_rating_' . $_POST['reviewId'] ); // New

            $res['code'] = 200;
            $res['data']['msg'] = __( 'Done', 'reviewer');
            die( json_encode( $res ) );
        }

        die( json_encode( $res ) );
    }

	public function display_plugin_admin_page()
	{
		echo '<div class="wrap">';
		echo '<h2>'. __( 'Review Boxes', $this->plugin_slug ) .'</h2>';

        if($this->is_licensed()):

		$reviews = $this->get_reviews();

		$reviews_table = new RWP_Reviews_List_Table( $this->templates_option, $reviews );
		$reviews_table->prepare_items();
		$reviews_table->display();

        else:
            $this->license_notice();
        endif;

		echo '</div><!--/wrap-->';
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_reviews()
	{
		global $wpdb;
		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rwp_reviews';", ARRAY_A );

        if( ! is_array( $post_meta ) ) {
            return $result;
        }
		
		foreach( $post_meta as $meta ) {
		
			$reviews = unserialize( $meta['meta_value'] );

            if( ! is_array( $reviews ) ) {
                continue;
            }
			
			foreach( $reviews as $review ){
				$review['review_post_id'] = $meta['post_id'];
				$result[] = $review;
			}
				
		}

		return $result;
	}
}

if( ! class_exists('WP_List_Table') )
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    
// The class extends the wordpress list table and will contain all reviews registered
class RWP_Reviews_List_Table extends WP_List_Table {
	
	public $reviews_per_page = 20; 
	public $templates;
	public $reviews;
	
	// Construct
	function __construct($templates, $reviews){
                
        // Set parent defaults
        parent::__construct( array(
            'singular'  => 'rwp_review',     //singular name of the listed records
            'plural'    => 'rwp_reviews',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
		
		$this->templates = $templates;
		$this->reviews = $reviews;
        
    }
    
    // Default method called when a specific column rendering method is not set
    function column_default($item, $column_name){
        switch($column_name){
            case 'rwp_reviews_table_review_title':
                return $item['review_title'];
            case 'rwp_reviews_table_review_users_score':
            	return 0;
            case 'rwp_reviews_table_review_id':
                return $item['review_id'];

            case 'rwp_reviews_table_review_post_id':
                return $item['review_post_id'];
            default:
                return 'TO IMPLEMENT!';
        }
    }
    
    // Get table columns
    function get_columns() {
        $columns = array(
            'rwp_reviews_table_review_title'		=> __( 'Box Title', 'reviewer' ),
            'rwp_reviews_table_review_post'			=> __( 'Post', 'reviewer' ),
            'rwp_reviews_table_review_template'		=> __( 'Template', 'reviewer' ),
			'rwp_reviews_table_review_score'		=> __( 'Reviewer Score', 'reviewer' ),
            'rwp_reviews_table_review_users_score'	=> __( 'User Score', 'reviewer' ),
            'rwp_reviews_table_review_post_id'      => __( 'Post ID', 'reviewer' ),
            'rwp_reviews_table_review_id'           => __( 'Box ID', 'reviewer' ),
            'rwp_reviews_table_review_actions'		=> __( 'Actions', 'reviewer' )  
        );

        return $columns;
    }

    // Actions
     function column_rwp_reviews_table_review_actions( $item ) 
     {
        $html  = '<a class="rwp-tooltips rwp-reset-score-btn" href="#" data-post-id="'.$item['review_post_id'].'" data-review-id="'.$item['review_id'].'" data-confirm-msg="'. __( 'Confirm the action?', 'reviewer' ) .'" data-res-msg="'. __( 'Ratings deleted', 'reviewer' ) .'"><i class="dashicons dashicons-groups"></i> <span>'. __( 'Reset Users Score', 'reviewer' ) .'</span></a> ';
        $html .= '<a class="rwp-tooltips rwp-delete-single-review-btn" href="#" data-post-id="'.$item['review_post_id'].'" data-review-id="'.$item['review_id'].'" data-confirm-msg="'. __( 'Confirm the action? Also related users ratings will be deleted.', 'reviewer' ) .'" data-res-msg="'. __( 'Review deleted', 'reviewer' ) .'"><i class="dashicons dashicons-dismiss"></i> <span>'. __( 'Delete Review', 'reviewer' ) .'</span></a>';
        $html .= '<img class="rwp-loader rwp-reset" src="'.admin_url() .'images/spinner.gif" alt="loading" />';

        return $html;
     }

    // Rating Column
    function column_rwp_reviews_table_review_users_score( $item )
    {

        $ratings_scores = RWP_Reviewer::get_ratings_single_scores( $item['review_post_id'], $item['review_id'], $item['review_template'] );
        $data           = RWP_Reviewer::get_users_overall_score( $ratings_scores, $item['review_post_id'], $item['review_id'] );
        $score = $data['score'];
        $count = $data['count'];

        return '<strong>'. $score .'</strong> / ' . $this->templates[ $item[ 'review_template' ] ]['template_maximum_score'] . ' | # ' . $count . '';
    }

    // Post Column
    function column_rwp_reviews_table_review_post( $item ) 
    {
    	return '<a href="'. get_permalink( $item['review_post_id'] ) .'">'. get_post_field( 'post_title', $item['review_post_id'] ) .'</a>';
    }

    // Template Column
    function column_rwp_reviews_table_review_template( $item ) 
    {
    	return $this->templates[ $item[ 'review_template' ] ]['template_name'];
    }

    // Total Score
    function column_rwp_reviews_table_review_score( $item ) 
    {
        $type = isset( $item['review_type'] ) ? $item['review_type'] : 'PAR+UR';

        if( $type == 'PAR+UR' ) {

            $overall = isset( $item['review_scores'] ) ? round( RWP_Reviewer::get_avg( $item['review_scores'] ) , 1 ) : 0;
            $score = ( isset( $item['review_custom_overall_score'] ) && ! empty( $item['review_custom_overall_score']) ) ? $item['review_custom_overall_score'] : $overall;

            return '<strong>'. $score .'</strong> / ' . $this->templates[ $item[ 'review_template' ] ]['template_maximum_score'];

        } else {

            return '---';
        }
    }
    
    // Get sortable columns
    function get_sortable_columns() {
        
        $sortable_columns = array(
            'rwp_reviews_table_review_title'	=> array('rwp_reviews_table_review_title',false),    //true means it's already sorted
            //'review_post'    		=> array('review_post',true),
            //'review_template'   	=> array('review_template',false),
            //'review_score'     		=> array('review_score',false),
            //'review_users_score'	=> array('review_users_score',false),
        );
        
        return $sortable_columns;
    }
    
    
    // Prepare the items for the table
    function prepare_items() {
    
        // Records per table page
        $per_page = $this->reviews_per_page;
        
		// Define column headers
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        // Build column headers
        $this->_column_headers = array($columns, $hidden, $sortable);

        $data = ( empty( $this->reviews) ) ? array() : $this->reviews;
	
		// Sort Data
        function _usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'rwp_reviews_table_review_title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = ( isset( $a[$orderby] ) && isset( $b[$orderby] ) ) ? strcmp($a[$orderby], $b[$orderby]) : -1; //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, '_usort_reorder');
        
        // Pagination. Let's figure out what page the user is currently looking at. 
        $current_page = $this->get_pagenum();
        
        // Pagination. Let's check how many items are in our data array. 
        $total_items = count($data);
        
        // Data Paginations
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
		// Add Data to the core class
        $this->items = $data;
        
		// Register pagination options
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );	
    }
}
