<?php

namespace Stachethemes\Stec;




/**
 * API/AJAX Response Object Template
 */
abstract class Response_Object_Template {



    protected $data;
    protected $error         = 0;
    protected $error_message = '';
    protected $forbidden;



    public function __construct() {

        /**
         * Set some irrelevant or sensitive parameters in the $forbidden array
         * When retrieving data these keys will not be included
         */
        $this->set_forbidden(array(
                'post_password',
                'filter',
                'post_mime_type',
                'ping_status',
                'to_ping',
                'pinged',
                'comment_status',
                'comment_count',
                'post_parent',
                'menu_order',
                'post_type',
                'guid',
        ));

    }



    /**
     * Set custom forbidden key parameters. Will override the default array
     * @param array $array array with forbidden key parameters
     */
    public function set_forbidden(Array $array) {

        $this->forbidden = $array;

    }



    /**
     * @return array Array with forbidden key params
     */
    public function get_forbidden() {
        return $this->forbidden;

    }



    /**
     * Get data content
     * @return mixed the actual data content
     */
    public function get_data() {
        return $this->data;

    }



    /**
     * Get error status
     * @return int 1/0 True of False
     */
    public function get_error() {
        return $this->error;

    }



    /**
     * Removes forbidden key parameters from objects and arrays
     * @param string|array|object $data 
     * @return string|array|object the filtered $data
     */
    public function strip_forbidden($data) {

        if ( is_array($data) || is_object($data) ) {

            foreach ( $data as $key => $value ) {

                if ( is_string($key) && in_array($key, $this->forbidden) ) {
                    if ( is_object($data) ) {
                        unset($data->$key);
                    } else {
                        unset($data[$key]);
                    }
                } elseif ( is_array($value) ) {
                    if ( is_object($data) ) {
                        $data->$key = $this->strip_forbidden($value);
                    } else {
                        $data[$key] = $this->strip_forbidden($value);
                    }
                } elseif ( is_object($value) ) {
                    if ( is_object($data) ) {
                        $data->$key = $this->strip_forbidden($value);
                    } else {
                        $data[$key] = $this->strip_forbidden($value);
                    }
                }
            }
        }

        return $data;

    }



    /**
     * Set requested data. 
     * @uses strip_forbidden data will pass through filter
     * @param mixed $data
     */
    public function set_data($data) {

        $this->data = $this->strip_forbidden($data);

    }



    /**
     * Set/Remove error
     * @param int|bool $error 1/0
     */
    public function set_error($error) {
        $this->error = (int) $error;

    }



    /**
     * Get error text
     * @return string
     */
    public function get_error_message() {
        return $this->error_message;

    }



    /**
     * Set error text
     * @param string $error_message
     */
    public function set_error_message($error_message) {
        $this->error_message = $error_message;

    }



    /**
     * Recommended implementation
     * 
     * 'error_message' => String,
     * 'error'         => 1/0 true/false,
     * 'data'          => object/array
     * 
     * @return json
     * 
     */
    abstract public function get();



}
