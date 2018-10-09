<?php

namespace Stachethemes\Stec\Updater;




class response_object {



    private $error     = 0;
    private $error_msg = '';
    private $data      = null;



    public function get_error() {
        return $this->error;
    }



    public function get_error_msg() {
        return $this->error_msg;
    }



    public function get_data() {
        return $this->data;
    }



    public function set_error($error) {
        $this->error = $error;
    }



    public function set_error_msg($error_msg) {
        $this->error_msg = $error_msg;
    }



    public function set_data($data) {
        $this->data = $data;
    }



    public function get() {
        echo json_encode(array(
                'error'     => $this->get_error(),
                'error_msg' => $this->get_error_msg(),
                'data'      => $this->get_data()
        ));
    }

}
