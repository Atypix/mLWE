<?php

namespace Stachethemes\Stec;




abstract class Stec_Post {



    protected $id          = null;
    protected $custom_meta = array();



    public function set_id($id) {
        $this->id = $id;
    }



    public function get_id() {
        return $this->id;
    }



    abstract public function get_post_data();



    public function get_custom_meta($name, $single = true) {
        if ( $this->id ) {
            return get_post_meta($this->id, $name, $single);
        }

        return null;
    }



    public function set_custom_meta(Array $custom_meta) {
        $this->custom_meta = array_merge($this->custom_meta, $custom_meta);
    }



    /**
     * Inserts or Updates post
     */
    public function insert_post() {


        $post_data = apply_filters('stec_insert_post', $this->get_post_data());

        if ( $this->id ) {
            $result = wp_update_post($post_data, true);
        } else {
            $result = wp_insert_post($post_data, true);
        }


        if ( is_wp_error($result) ) {
            $errors = $result->get_error_messages();
            $msg    = implode('<br>', $errors);
            throw new Stec_Exception($msg);
        }

        if ( !$result ) {
            throw new Stec_Exception(__('Error processing post', 'stec'));
        }

        return $result;
    }



    public function delete_post() {
        if ( $this->id ) {
            return wp_delete_post($this->id, true);
        }

        return false;
    }



    public function get_author() {

        if ( $this->id ) {
            return (int) get_post_field('post_author', $this->id);
        }

        return get_current_user_id();
    }

}
