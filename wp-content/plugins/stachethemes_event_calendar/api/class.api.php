<?php

namespace Stachethemes\Stec;




class Api {



    private $api_namespace;



    public function __construct() {

        // v2 refers to the wp rest api version
        $this->api_namespace = 'stec/v2';

        add_action('rest_api_init', function () {
            new Api_Routes($this->api_namespace);
        });

        add_filter('rest_pre_serve_request', array($this, 'stec_format_rest_pre_serve_request'), 10, 4);
    }



    public function stec_format_rest_pre_serve_request($served, $result, $request, $server) {

        switch ( $request['format'] ) {
            case 'stec_raw':
                header('Content-Type: text/html; charset=' . get_option('blog_charset'));
                echo $result->data['data'];
                $served = true;
                break;
        }

        return $served;
    }

}
