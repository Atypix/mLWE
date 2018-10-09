<?php

namespace Stachethemes\Stec;




class Remind_Object {



    private $uid = null;



    public function __construct($eventid, $repeat_offset, $email, $date) {


        $this->uid = serialize(array(
                'eventid'       => $eventid,
                'repeat_offset' => $repeat_offset,
                'email'         => $email,
                'date'          => $date
        ));
    }



    public function get_uid() {
        return $this->uid;
    }

}
