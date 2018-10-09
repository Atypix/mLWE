<?php

namespace Stachethemes\Stec;




use WP_REST_Response;
use WP_HTTP_Response;




class Api_Response extends Response_Object_Template {



    protected $code = 200;



    /**
     * Sets response status code
     * @param int $code status code; defaults to 200 (OK)
     */
    public function set_code($code = 200) {
        $this->code = (int) $code;
    }



    public function get() {

        $response = new WP_REST_Response(array(
                'error_message' => $this->error_message,
                'error'         => $this->error,
                'code'          => $this->code,
                'data'          => $this->data
                ), $this->code);

        return $response;
    }

}
