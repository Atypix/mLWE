<?php

namespace Stachethemes\Stec;




class Event_Meta_Attendee implements Event_Meta_Object {



    public $userid;
    public $email;
    public $status       = array();
    public $mail_sent    = 0;
    public $access_token = null;

    /*
      $status = array(

      '(int) Repeat Offset => (int) 0/1/2',

      )
     */



    public function __construct() {
        $this->access_token = md5(microtime());
    }



    public function get_data() {
        return array(
                'userid'       => $this->userid,
                'email'        => $this->email,
                'mail_sent'    => $this->mail_sent,
                'status'       => $this->status,
                'access_token' => $this->access_token,
        );
    }

}
