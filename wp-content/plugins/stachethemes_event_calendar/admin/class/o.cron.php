<?php

namespace Stachethemes\Stec;




class Cron_Post extends Stec_Post {



    private $post_type   = 'stec_cron';
    private $post_status = 'publish';
    private $title       = '';
    private $author      = null;



    public function __construct($post_id = null) {

        if ( $post_id && $this->post_type === get_post_type($post_id) ) {
            $this->id          = $post_id;
            $this->title       = get_post_field('post_title', $post_id);
            $this->author      = get_post_field('post_author', $post_id);
            $this->post_status = get_post_status($post_id);
        }
    }



    public function get_author() {
        return $this->author ? $this->author : get_current_user_id();
    }



    public function get_title() {
        return $this->title;
    }



    public function set_title($title) {
        $this->title = $title;
    }



    public function get_post_data() {

        return array(
                'ID'          => $this->id,
                'post_author' => get_current_user_id(),
                'post_title'  => $this->title,
                'post_type'   => $this->post_type,
                'post_status' => $this->post_status,
                'meta_input'  => array_merge(array(
                        ), $this->custom_meta)
        );
    }

}
