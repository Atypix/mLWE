<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_Users_Ratings_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	protected $templates_option;

	public function __construct()
	{
		parent::__construct();

		$this->menu_slug = 'reviewer-users-ratings-page';
		$this->parent_menu_slug = 'reviewer-main-page';
        $this->capability = 'rwp_manage_user_reviews';
		//$this->templates_option = RWP_Reviewer::get_option( 'rwp_templates' );
		$this->add_menu_page();

        // Localize
        add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'User Reviews', $this->plugin_slug), __( 'User Reviews', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	}

    public function localize_script()
    {
        $action_name = 'rwp_ajax_action_ratings_page';
        wp_localize_script( $this->plugin_slug . '-admin-script', 'ratingsManagerActionsObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

        $action_name = 'rwp_ajax_bulk_action_ratings_page';
        wp_localize_script( $this->plugin_slug . '-admin-script', 'ratingsManagerActionsBulkObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
    }

    public static function ajax_callback_bulk()
    {
        $res = array( 'code' => 400, 'data'=> array( 'msg' => __( 'Unable to perform action', 'reviewer' ) ) );

        $key = 'rwp_pending_ratings';
        $pend_count = get_option( $key, 0 );

        switch ( $_POST['ac']) { // action

            case 'rwp_unapprove':

                $count = self::approve_unapprove_bulk( $_POST['rows'], $_POST['ac'] );

                $pend_count = $pend_count + $count;
                update_option( $key, $pend_count );
            break;

            case 'rwp_approve':

                $count = self::approve_unapprove_bulk( $_POST['rows'], $_POST['ac'] );


                $pend_count = $pend_count + $count;
                update_option( $key, $pend_count );

                break;

            case 'rwp_verify':
            case 'rwp_unverify':
                $count = self::apply_badge_bulk( $_POST['rows'], $_POST['ac'] );
                break;

            case 'delete':

                $count = self::del_bulk( $_POST['rows'] );

                $pend_count = $pend_count + $count;
                update_option( $key, $pend_count );

            break;
            default:
                $res['data']['msg'] = 'Action not found! :(';
                break;
        }

            $res['code'] = 200;
            $res['count'] = count($_POST['rows']);
            $res['realCount'] = $count;

        die( json_encode( $res ) );

    }

    public static function ajax_callback()
    {
        $res = array( 'code' => 400, 'data'=> array( 'msg' => __( 'Unable to perform action', 'reviewer' ) ) );
        $key = 'rwp_pending_ratings';
        $pend_count = get_option( $key, 0 );

        switch ( $_POST['ac']) { // action

            case 'unapprove':

                $result = self::approve_unapprove( $_POST['metaId'], $_POST['ratingId'], $_POST['ac'] );

                if( $result ) {
                    $pend_count++;
                    update_option( $key, $pend_count );
                }

            break;

            case 'approve':

                $result = self::approve_unapprove( $_POST['metaId'], $_POST['ratingId'], $_POST['ac'] );

                if( $result ) {
                    $pend_count--;
                    update_option( $key, $pend_count );
                }

                break;

            case 'delete':
                $result = self::get_rating( $_POST['metaId'], $_POST['ratingId'] );
                if( $result ) {
                    RWP_Rating::schedule_postmeta_updating($result['rating_post_id'], $result['rating_review_id']);
                }

                $result = self::del( $_POST['metaId'] );

                if( $result && ( isset( $_POST['pending'] ) && $_POST['pending'] == 'yes' ) ) {
                    $pend_count--;
                    update_option( $key, $pend_count );
                }
                break;

            case 'edit':
                $result = self::get_rating( $_POST['metaId'], $_POST['ratingId'] );

                $rating  = $result;

                if( $rating !== false) {
                    $res['data']['rating']  = $rating;
                    $res['data']['msg'] = 'OK';

                    $form  = '';
                    $form .= '<tr id="rwp-tr-r-'. $rating['rating_id'] .'" class="rwp-tr-edit-rating"><td colspan="6">';

                    if( $rating['rating_user_id'] <= 0 ) {

                        $form .= '<span class="rwp-rmi-wrap">';
                            $form .= '<label for="">'. __( 'User Name', 'reviewer' ) .'</label>';
                            $form .= '<input type="text" name="rwp-rmi[rating_user_name]" value="'. $rating['rating_user_name'].'" />';
                        $form .= '</span>';

                        $email = isset( $rating['rating_user_email'] ) ? $rating['rating_user_email'] : '';
                        $form .= '<span class="rwp-rmi-wrap">';
                            $form .= '<label for="">'. __( 'User Email', 'reviewer' ) .'</label>';
                            $form .= '<input type="text" name="rwp-rmi[rating_user_email]" value="'. $email.'" />';
                        $form .= '</span>';
                    }

                    $form .= '<span class="rwp-rmi-wrap">';
                        $form .= '<label for="">'. __( 'Review Title', 'reviewer' ) .'</label>';
                        $form .= '<input type="text" name="rwp-rmi[rating_title]" value="'. $rating['rating_title'].'" />';
                    $form .= '</span>';

                    $form .= '<span class="rwp-rmi-wrap">';
                        $form .= '<label for="">'. __( 'Review Comment', 'reviewer' ) .'</label>';
                        $form .= '<textarea type="text" name="rwp-rmi[rating_comment]">'. $rating['rating_comment'] .'</textarea>';
                    $form .= '</span>';

                    if( isset( $rating['rating_template'] ) ) {
                        $templates      = get_option( 'rwp_templates', array() );
                        if( isset( $templates[ $rating['rating_template'] ] ) ) {
                            $template   = $templates[ $rating['rating_template'] ];
                            $order      = isset( $template['template_criteria_order'] ) ? $template['template_criteria_order'] : null;
                            $criteria   = $template['template_criterias'];
                            $order      = is_null( $order ) ? array_keys( $criteria ) : $order;
                            $min        = $template['template_minimum_score'];
                            $max        = $template['template_maximum_score'];

                            $form .= '<span class="rwp-rmi-wrap">';
                                $form .= '<label for="">'. __( 'Review Scores', 'reviewer' ) .'</label>';
                                // $form .= '<input type="text" name="rwp-rmi[rating_scores]" value="" />';
                                $form .= '<ul class="rwp-scores-sliders">';
                                foreach ($order as $i) {
                                    $score = isset( $rating['rating_score'][$i] ) ? $rating['rating_score'][$i] : $min;
                                    $form .= '<li>';
                                        $form .= '<label>'. $criteria[$i] .'</label>';
                                        $form .= '<input type="text" name="rwp-rmi[rating_scores]['.$i.']" value="'. $score .'" placeholder="'. $min .'" data-index="'. $i .'"/>';
                                        $form .= '<div class="rwp-slider" data-val="'. $score .'" data-min="'. $min .'" data-max="'. $max .'"></div>';
                                    $form .= '</li>';
                                }
                                $form .= '</ul>';
                            $form .= '</span>';
                        }
                    } // end if about isset rating template

                    $rating_date = date_i18n( 'Y/m/d ' . get_option( 'time_format' ), $rating['rating_date'] );
                    $form .= '<span class="rwp-rmi-wrap">';
                        $form .= '<label for="">'. __( 'Review Date', 'reviewer' ) .'</label>';
                        $form .= '<input type="text" name="rwp-rmi[rating_date]" value="'. $rating_date.'" />';
                    $form .= '</span>';

                    // Images
                    $rating_images = isset( $rating['rating_images'] ) && is_array( $rating['rating_images'] ) ? $rating['rating_images'] : array();
                    $form .= '<div class="rwp-rmi-wrap rwp_rt_images">';
                        $form .= '<p class="description">'. __( 'Review Images', 'reviewer' ) .'</p>';
                        if( count( $rating_images ) > 0 ) {
                            $form .= '<ul>';
                            foreach ( $rating_images as $attachment_id ) {
                                $thumb = wp_get_attachment_image_src( $attachment_id, array( 160, 160 ) );
                                if( $thumb === false ) {
                                    continue;
                                }
                                $image = wp_get_attachment_image_src( $attachment_id, 'full' );
                                $form .= '<li>';
                                    $form .= '<a href="'. $image[0] .'" target="_blank">';
                                        $form .= '<span style="background-image: url('. $thumb[0] .')"></span>';
                                    $form .= '</a>';
                                    $form .= '<em class="rwp-rmi-delete-image" data-attachment="'. $attachment_id .'">'. __('Remove', 'reviewer') .'</em>';
                                $form .= '</li>';
                            }
                            $form .= '</ul>';
                    } else {
                        $form .= '<p>'. __( 'No images for the review', 'reviewer' ) .'</p>';
                    }
                    $form .= '<input type="hidden" name="rwp-rmi[rating_images_to_delete]" value="" />';
                    $form .= '</div>';

                    $form .= '<div class="rwp-rmi-wrap">';
                        $form .= '<p class="description">'. __( 'You can attach images to the review by adding the "File ID" separated by a comma', 'reviewer' ) .'</p>';
                        $form .= '<input type="text" name="rwp-rmi[rating_images_new]" value="" placeholder="32, 45, 98" />';
                    $form .= '</div>';

                    $form .= '<input type="button" name="rwp-rmi[cancel]" class="button rwp-rmi-cancel" value="'. __( 'Cancel', 'reviewer' ) .'" data-meta-id="'. $rating['rating_meta_id'] .'" data-rating-id="'. $rating['rating_id'] .'"/>';
                    $form .= '<input type="submit" name="rwp-rmi[submit]" class="button button-primary rwp-rmi-submit rwp-ratings-action" value="'. __( 'Save Changes', 'reviewer' ) .'" data-action="edit-done" data-meta-id="'. $rating['rating_meta_id'] .'" data-rating-id="'. $rating['rating_id'] .'" />';
                    $form .= '<img class="rwp-loader" src="'.admin_url() .'images/spinner.gif" alt="loading" />';

                    $form .= '</td></tr><!--end form-->';

                    $res['data']['form']  = $form;
                }
                break;

            case 'edit-done':

                $result = self::update_rating( $_POST['metaId'], $_POST['ratingId'], $_POST['newRating'] );
                $res['data']['rating']  = $result;
                $res['data']['msg']     = 'OK';
                break;

            case 'verify':
            case 'unverify':
                $result = self::apply_badge( $_POST['metaId'], $_POST['ratingId'], $_POST['ac'] );
                break;

            default:
                $result = false;
                $res['data']['msg'] = 'Action not found! :(';
                break;
        }

        if( $result !== false ) {
            $res['code'] = 200;
        }


        die( json_encode( $res ) );
    }

    public static function get_rating( $meta_id, $rating_id )
    {
        global $wpdb;

        $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $meta_id ), ARRAY_A);

        $rating = maybe_unserialize( $row['meta_value'] );

        if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $rating_id ) {
            $rating['rating_meta_id'] = $meta_id;
            return $rating;
        } else {
            return false;
        }
    }

    public static function update_rating( $meta_id, $rating_id, $data )
    {
        global $wpdb;

        $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $meta_id ), ARRAY_A);

        $rating = maybe_unserialize( $row['meta_value'] );

        if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $rating_id ) {

            foreach ($data as $key => $value) {
                if( $key == 'rating_date' ) {
                    $new_time = strtotime( $value, current_time('timestamp') );
                    if( $new_time === false ) continue;
                    $value = $new_time;
                }

                if( $key == 'rating_score' ) {
                    $templates = get_option( 'rwp_templates', array() );
                    if( isset( $templates[ $rating['rating_template'] ] ) ) {
                        $template   = $templates[ $rating['rating_template'] ];
                        $min        = $template['template_minimum_score'];
                        $max        = $template['template_maximum_score'];

                        $score = array();
                        if( is_array( $value) ) {
                            foreach ($value as $i => $val) {
                                $v = floatval( $val );
                                $score[ $i ] =  ( $v <= $max && $v >= $min ) ? $v : $min;
                            }

                            $rating[ $key ] = $score;
                            continue;
                        }
                        continue;
                    }
                    continue;
                } // end rating_score

                if( $key == 'rating_images_new' ) {
                    $images = isset( $rating['rating_images'] ) && is_array( $rating['rating_images'] ) ? $rating['rating_images'] : array();
                    $new_images = explode(',', $value);
                    if( !is_array( $new_images ) || count( $new_images ) < 1 ) { // no new images
                        continue;
                    }

                    foreach ( $new_images as $image_id ) {
                        $attachment_id = intval( $image_id );
                        if( $attachment_id > 0 && wp_attachment_is_image( $attachment_id ) && !in_array( $attachment_id, $images ) ) {
                            $images[] = $attachment_id;
                        }
                    }
                    $rating['rating_images'] = $images;
                    continue;
                } // end rating_images_new

                if( $key == 'rating_date_to_delete' ) {
                    $images = isset( $rating['rating_images'] ) && is_array( $rating['rating_images'] ) ? $rating['rating_images'] : array();
                    $to_delete = explode(',', $value);
                    if( !is_array( $to_delete ) || count( $to_delete ) < 1 ) { // no new images
                        continue;
                    }

                    foreach ( $to_delete as $image_id ) {
                        $attachment_id = intval( $image_id );
                        if( $attachment_id > 0 && in_array( $attachment_id, $images ) ) {
                           $images = array_diff( $images, array( $attachment_id ) );
                        }
                    }
                    $rating['rating_images'] = $images;
                    continue;
                } // end rating_images_new

                $rating[ $key ] = ($key == 'rating_comment') ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", stripslashes_deep( $value ) ) ) ) : sanitize_text_field( stripslashes_deep( $value ) );
            }

            $res = $wpdb->update(
                $wpdb->postmeta,
                array( 'meta_value' => maybe_serialize( $rating ) ),
                array( 'meta_id'    => intval( $meta_id ) ),
                array( '%s' ),
                array( '%d' )
            );

            $rating['rating_date'] = date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $rating['rating_date'] );
            $rating['rating_comment'] = nl2br($rating['rating_comment'] );

            RWP_Rating::schedule_postmeta_updating($row['post_id'], $rating['rating_review_id']);

            return ( $res === false ) ? false : $rating;
        } else {
            return false;
        }
    }

    public static function approve_unapprove( $meta_id, $rating_id, $action = 'approve' )
    {
        global $wpdb;

        $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $meta_id ), ARRAY_A);

        $rating = maybe_unserialize( $row['meta_value'] );

        if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $rating_id ) {

            $rating['rating_status'] = ( $action == 'approve'  ) ? 'published' : 'pending';

            return $wpdb->update(
                $wpdb->postmeta,
                array(
                    'meta_value' => maybe_serialize( $rating )
                ),
                array( 'meta_id' => intval( $meta_id ) ),
                array( '%s' ),
                array( '%d' )
            );

        } else {
            return false;
        }
    }

    public static function apply_badge( $meta_id, $rating_id, $action = 'verify' )
    {
        global $wpdb;

        $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $meta_id ), ARRAY_A);

        $rating = maybe_unserialize( $row['meta_value'] );

        if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $rating_id ) {

            $rating['rating_verified'] = ( $action == 'verify'  ) ? true : false;

            return $wpdb->update(
                $wpdb->postmeta,
                array(
                    'meta_value' => maybe_serialize( $rating )
                ),
                array( 'meta_id' => intval( $meta_id ) ),
                array( '%s' ),
                array( '%d' )
            );

        } else {
            return false;
        }
    }


    public static function apply_badge_bulk( $items = array(), $action = 'rwp_approve'  )
    {
        global $wpdb;

        $count = 0;

        foreach ($items as $item) {

            $res = false;

            $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $item['metaId'] ), ARRAY_A);

            $rating = maybe_unserialize( $row['meta_value'] );

            if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $item['ratingId'] ) {

                $verified = ( isset($rating['rating_verified'] ) ) ? $rating['rating_verified'] : false ;
                $rating['rating_verified'] = ( $action == 'rwp_verify'  ) ? true : false;

                $res = $wpdb->update(
                    $wpdb->postmeta,
                    array(
                        'meta_value' => maybe_serialize( $rating )
                    ),
                    array( 'meta_id' => intval( $item['metaId'] ) ),
                    array( '%s' ),
                    array( '%d' )
                );
            }

            if( $res !== false ) {

                if( $verified === true && $action == 'rwp_verify' )
                    continue;

                if( $verified === false && $action == 'rwp_verify' )
                    $count++;

                if( $verified === true && $action != 'rwp_verify' )
                    $count++;

                if( $verified === false && $action != 'rwp_verify' )
                    continue;
            }
        }

        return $count;
    }

    public static function approve_unapprove_bulk( $items = array(), $action = 'rwp_approve'  )
    {
        global $wpdb;

        $count = 0;

        foreach ($items as $item) {

            $res = false;

            $row = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = " . intval( $item['metaId'] ), ARRAY_A);

            $rating = maybe_unserialize( $row['meta_value'] );

            if( isset( $rating['rating_id'] ) && $rating['rating_id'] == $item['ratingId'] ) {

                $status = (isset($rating['rating_status'])) ? $rating['rating_status'] : 'published' ;

                $rating['rating_status'] = ( $action == 'rwp_approve' ) ? 'published' : 'pending';

                $res = $wpdb->update(
                    $wpdb->postmeta,
                    array(
                        'meta_value' => maybe_serialize( $rating )
                    ),
                    array( 'meta_id' => intval( $item['metaId'] ) ),
                    array( '%s' ),
                    array( '%d' )
                );
            }

            if( $res !== false ) {

                if( $status == 'published' && $action == 'rwp_approve' )
                    continue;

                if( $status == 'pending' && $action == 'rwp_approve' )
                    $count--;

                if( $status == 'published' && $action != 'rwp_approve' )
                    $count++;

                if( $status == 'pending' && $action != 'rwp_approve' )
                    continue;
            }
        }

        return $count;
    }

    public static function del( $meta_id )
    {
        global $wpdb;

        // Get rating before deleting it
        $meta = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = $meta_id", ARRAY_A);

        if( !isset( $meta['meta_id'] ) || $meta['meta_id'] != $meta_id )
            return false;

        $rating     = maybe_unserialize( $meta['meta_value'] );
        $post_id    = $rating['rating_post_id'];
        $review_id  = $rating['rating_review_id'];
        $user_id    = $rating['rating_user_id'];

        // Delete blacklist record
        if( $user_id > 0 ) {
            $blacklist = get_post_meta( $post_id, 'rwp_rating_blacklist', true );
            if( isset( $blacklist[ $post_id . '-' . $review_id ] ) ) {
                $key = array_search( $user_id, $blacklist[ $post_id . '-' . $review_id ] );
                if( $key !== false ) {
                    unset( $blacklist[ $post_id . '-' . $review_id ][ $key ] );
                    update_post_meta( $post_id, 'rwp_rating_blacklist', $blacklist);
                }
            }
        }

        // Delete cookie
        $cookie_name    = 'rwp_rating_' . $post_id .'_' . $review_id .'_' . $user_id;
        unset( $_COOKIE[ $cookie_name ] );
        setcookie( $cookie_name , 'true', time() - 3600, '/' );

        return $wpdb->delete( $wpdb->postmeta, array( 'meta_id' => $meta_id ), array( '%d' ) );
    }

    public static function del_bulk( $items = array() )
    {
        global $wpdb;

        $count = 0;

        foreach ($items as $item) {

            $res = self::del( $item['metaId'] );

            if( $res === false )
                continue;

            if( $item['pending'] == 'yes' )
                $count --;

        }

        return $count;
    }

	public function display_plugin_admin_page()
	{
        $ratings = $this->get_ratings();

        echo '<div class="wrap" id="rwp-ratings-mega-wrap">';
        echo '<h2>'. __( 'User Reviews', $this->plugin_slug ) .' <span class="rwp-template-count">'. count( $ratings ) .'</span></h2>';

        if($this->is_licensed()):

        $ratings_table = new RWP_Ratings_List_Table( $ratings );
        $ratings_table->prepare_items();
        $ratings_table->display();

        else:
            $this->license_notice();
        endif;

        //RWP_Reviewer::pretty_print( $ratings );

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

	public function get_ratings()
	{
		global $wpdb;
		$result = array();

		$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rwp_rating%';", ARRAY_A );

		foreach( $post_meta as $meta ) {

			$rating = unserialize( $meta['meta_value'] );

            if( !isset( $rating['rating_id'] ) )
                continue;

            $rating['rating_meta_id'] = $meta['meta_id'];

            $result[ $rating['rating_id'] ] = $rating;
		}

		return $result;
	}

}

if( ! class_exists('WP_List_Table') )
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

// The class extends the wordpress list table and will contain all ratings
class RWP_Ratings_List_Table extends WP_List_Table {

    private $items_per_page = 50;
    private $ratings;
    private $templates;
    private $preferences;

    // Construct
    function __construct( $ratings ){

        // Set parent defaults
        parent::__construct( array(
            'ajax'      => false        //does this table support ajax?
        ) );

        $this->ratings = $ratings;
        $this->templates = RWP_Reviewer::get_option('rwp_templates');
        $this->preferences = RWP_Reviewer::get_option('rwp_preferences');
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns    = $this->get_columns();
        $hidden     = $this->get_hidden_columns();
        $sortable   = $this->get_sortable_columns();

        $data = $this->ratings;
        usort( $data, array( $this, 'sort_latest' ) );

        $per_page       = $this->items_per_page;
        $current_page   = $this->get_pagenum();
        $total_items    = count($data);

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $data = array_slice( $data, ( ( $current_page -1 ) * $per_page), $per_page );

        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'               => '<input type="checkbox" />',
            'rwp_rt_author'    => __( 'Author', 'reviewer' ),
            'rwp_rt_comment'   => __( 'Comment', 'reviewer' ),
            'rwp_rt_score'     => __( 'Score', 'reviewer' ),
            'rwp_rt_review'     => __( 'Review Box', 'reviewer' ),
            'rwp_rt_images'    => __('Images', 'reviewer'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array();
        //return array('title' => array('title', false));
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        return var_dump( $item ) ;
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }


        $result = strnatcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }

    public function no_items() {
      _e( 'No ratings yet :(' );
    }

    public function get_bulk_actions()
    {
      $actions = array(
        'rwp_approve'    => __('Approve', 'reviewer'),
        'rwp_unapprove'  => __('Unapprove', 'reviewer'),
        'rwp_verify'     => __('Apply Verified Badge', 'reviewer'),
        'rwp_unverify'     => __('Remove Verified Badge', 'reviewer'),
        'delete'         => __('Delete', 'reviewer'),
      );

      return $actions;
    }

    public function sort_latest( $a, $b )
    {
        if ($a["rating_date"] == $b["rating_date"])
            return 0;

        return ($a["rating_date"] > $b["rating_date"]) ? -1 : 1;
    }

    public function column_cb($item)
    {
        $h = ( isset( $item['rating_status'] ) && $item['rating_status'] == 'pending' ) ? 'yes' : 'no';

         $html  = '<input type="checkbox" name="rwp_bulk_action[]" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-pending="'. $h .'"  value="" />';
        $html .='<img class="rwp-loader" src="'.admin_url() .'images/spinner.gif" alt="loading" />';
        return $html;
    }


     public function column_rwp_rt_author( $item )
     {
        $user_id    = $item['rating_user_id'];
        $user_name  = ( $user_id > 0 ) ? get_user_by( 'id', $user_id )->display_name : $item['rating_user_name'];

        $avatar = ( $user_id == 0 && isset( $item['rating_user_email'] ) && !empty( $item['rating_user_email'] ) ) ? $item['rating_user_email'] : $user_id;

        $html  = '' . get_avatar( $avatar, 32 );
       // $html .= '<div class="rwp-info">';
        $html .= '<span class="rwp-rt-user-name">'. $user_name .'</span>';

        if (  $user_id == 0 && isset( $item['rating_user_email'] ) && !empty( $item['rating_user_email'] ) ) {

            $email = $item['rating_user_email'];

        } else if( $user_id != 0) {

            $email = get_user_by( 'id', $user_id )->user_email;
        } else {
            $email = '';
        }

        if( !empty( $email ) )
            $html .= '<a href="mailto:'.$email.'">'.$email.'</a>';

        $h = ( isset( $item['rating_status'] ) && $item['rating_status'] == 'pending' ) ? '' : 'rwp-hidden';
        $html .= '<span class="dashicons dashicons-flag rwp-marker '. $h .'"></span>';

        $badge = isset( $this->preferences['preferences_user_review_verified_badge'] ) ? $this->preferences['preferences_user_review_verified_badge'] : array( 'label' => __( 'Verified', 'reviewer' ), 'color' => '#E91E63' );
        $h = ( isset( $item['rating_verified'] ) && $item['rating_verified'] ) ? '' : 'rwp-hidden';
        $html .= '<div class="rwp-badges"><span class="rwp-badge '. $h .'" style="background: '. $badge['color'] .';">'. $badge['label'] .'</span></div>';
        //$html .= '</div>';

        return $html;
     }

     public function column_rwp_rt_score( $item )
     {
        $post_id        = $item['rating_post_id'];
        $review_id      = $item['rating_review_id'];
        $reviews        = get_post_meta( $post_id, 'rwp_reviews', true);

        if( isset( $reviews[ $review_id ] ) && $reviews[ $review_id ]['review_id'] == $review_id )  {

            $template_id = $reviews[ $review_id ]['review_template'];

        } else if( isset( $item['rating_template'] ) ){

            $template_id = $item['rating_template'];

        } else
            return __('Unable to display scores', 'reviewer');

        $template   = $this->templates[ $template_id ];

        $html = '';

        if ( $this->preferences['preferences_rating_mode'] == 'five_stars' ) { // Five Stars

            return $this->get_stars( $item['rating_score'], $template );

        } else {

            $criteria   = $template['template_criterias'];
            $order      = ( isset( $template['template_criteria_order'] ) ) ? $template['template_criteria_order'] : array_keys( $criteria);

            $html .= '<div class="rwp-ur-ratings">';

            foreach ($order as $i)  {

                $html .= '<div class="rwp-criterion">';
                    $html .= '<div class="rwp-criterion-text">';
                        $html .= '<span class="rwp-criterion-label">'. $criteria[$i] .'</span>';
                        $html .= '<span class="rwp-criterion-score">'.  RWP_Reviewer::format_number(  $item['rating_score'][$i] ) .'</span>';
                    $html .= '</div><!-- /criterion-text -->';

                    $html .= '<div class="rwp-criterion-bar-base">';
                        $html .= $this->get_score_bar( $item['rating_score'][$i], $template );
                    $html .= '</div><!-- /criterion-bar -->';
                $html .= '</div><!-- /criterion -->';

            }

            $html .= '</div>';

        }

        return $html;
     }


     public function column_rwp_rt_comment( $item )
     {
        $date = date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $item['rating_date'] );

        $actions = array();

        if( isset( $item['rating_status'] ) && $item['rating_status'] == 'pending' ) {
            $actions['rwp_approve'] = '<a href="#" class="rwp-ratings-action" data-action="approve" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-counterpart="'. __('Unapprove', 'reviewer') .'">'. __('Approve', 'reviewer') .'</a>';
        } else {
            $actions['rwp_unapprove'] = '<a href="#" class="rwp-ratings-action" data-action="unapprove" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-counterpart="'. __('Approve', 'reviewer') .'">'. __('Unapprove', 'reviewer') .'</a>';
        }

        if( !isset( $item['rating_verified'] ) || !$item['rating_verified'] ) {
            $actions['rwp_verify'] = '<a href="#" class="rwp-ratings-action rwp_verify" data-action="verify" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-counterpart="'. __('Remove Verified Badge', 'reviewer') .'">'. __('Apply Verified Badge', 'reviewer') .'</a>';
        } else {
            $actions['rwp_unverify'] = '<a href="#" class="rwp-ratings-action rwp_unverify" data-action="unverify" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-counterpart="'. __('Apply Verified Badge', 'reviewer') .'">'. __('Remove Verified Badge', 'reviewer') .'</a>';
        }

        $link = add_query_arg( 'rwpurid', $item['rating_id'], get_permalink( $item['rating_post_id'] ) );
        $actions['rwp_view'] = '<a href="'. $link .'">'. __('View', 'reviewer') .'</a>';
        $actions['rwp_edit'] = '<a href="#" class="rwp-ratings-action" data-action="edit" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'">'. __('Edit', 'reviewer') .'</a>';


        $h = ( isset( $item['rating_status'] ) && $item['rating_status'] == 'pending' ) ? 'yes' : 'no';
        $actions['delete'] = '<a href="#" class="rwp-ratings-action" data-action="delete" data-meta-id="'. $item['rating_meta_id'] .'" data-rating-id="'. $item['rating_id'] .'" data-pending="'. $h .'" data-confirm-msg="'. __( 'Confirm the action?', 'reviewer' ) .'">'. __('Delete', 'reviewer') .'</a>';


        $html  = '<em>'. __('Submitted on') .' '. $date .'</em>';
        $html .= '<span class="rwp-the-comment">'. $item['rating_title'] .'</span>';
        $html .= '<p>'. nl2br($item['rating_comment']) .'</p>';

        return sprintf('%1$s %2$s', $html, $this->row_actions($actions) );
     }

     public function column_rwp_rt_review( $item )
     {

        $post_id        = $item['rating_post_id'];
        $review_id      = $item['rating_review_id'];
        $reviews        = get_post_meta( $post_id, 'rwp_reviews', true);

        if( isset( $reviews[ $review_id ] ) && $reviews[ $review_id ]['review_id'] == $review_id )  {

           $review = $reviews[ $review_id ]; //rwp-review-112-0

           $review['review_title'] = $review['review_title'] . ' | ' . get_the_title( $post_id );

        } else {
            $review = array(
                'review_title' => get_the_title( $post_id ),
            );
        }

        $link = 'rwp-review-'. $post_id.'-'.$review_id;
        return '<a href="'. get_permalink( $item['rating_post_id'] ) .'#'.$link.'">'. $review['review_title'] .'</a>';
     }

     public function column_rwp_rt_images( $item )
     {
        $images = isset( $item['rating_images'] ) && is_array( $item['rating_images'] ) ? $item['rating_images'] : array();
        $html  = '';
        $html .= '<ul>';
        foreach ( $images as $attachment_id ) {
            $thumb = wp_get_attachment_image_src( $attachment_id, array( 160, 160 ) );
            if( $thumb === false ) {
                continue;
            }
            $image = wp_get_attachment_image_src( $attachment_id, 'full' );
            $html .= '<li>';
                $html .= '<a href="'. $image[0] .'" target="_blank">';
                    $html .= '<span style="background-image: url('. $thumb[0] .')"></span>';
                $html .= '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
     }

     protected function get_stars( $scores = array(), $template, $stars = 5 ) {

        $avg    = ( is_array( $scores ) ) ? RWP_Reviewer::get_avg( $scores ) : floatval( $scores );
        $value  = RWP_Reviewer::get_in_base( $template['template_maximum_score'], $stars, $avg);

        $int_value = intval( $value );
        $decimal_value = $value - $int_value;

        if( $decimal_value >= .4 && $decimal_value <= .6 ) {
            $score = $int_value + 0.5;
        } else if( $decimal_value > .6 ) {
            $score = $int_value + 1;
        } else {
            $score = $int_value;
        }

        $count = $stars * 2;

        $html  = '<div class="rwp-str">';

        $j = 0;
        for ($i = 0; $i < $count; $i++) {

            $oe = ($i % 2 == 0) ? 'rwp-o' : 'rwp-e';
            $fx = ($j < $score) ? 'rwp-f' : 'rwp-x';

            $html .= '<span class="rwp-s '. $oe .' '. $fx .'" style="background-image: url('. $template['template_rate_image'] .');"></span>';

            $j += .5;
        }

        $html .= '</div><!-- /stars -->';

        return $html;
    }

    protected function get_score_bar( $score, $template, $theme = '', $size = 0 ) {

        $max    = floatval( $template['template_maximum_score'] );
        $value  = floatval( $score );
        $range  = explode( '-', $template['template_score_percentages'] );
        $low    = floatval( $range[0] );
        $high   = floatval( $range[1] );

        $pct = round ( (( $value / $max ) * 100), 1);

        if ( $pct < $low ) {
            $color = $template['template_low_score_color'];
        } else if( $pct > $high ) {
            $color = $template['template_high_score_color'];
        } else {
            $color = $template['template_medium_score_color'];
        }

        $in = ( !empty( $theme ) ) ? '<span class="rwp-criterion-score" style="font-size: '. ($size + 2) .'px;">'. RWP_Reviewer::format_number( $score ) .'</span>' : '';

        return '<div class="rwp-score-bar" style="width: '. $pct .'%; background: '. $color .';">'. $in .'</div>';
    }

}
