<?php

namespace Stachethemes\Stec;




class Event_Meta_Product implements Event_Meta_Object {



    public $id;



    public function get_data() {
        return array(
                'id' => $this->id
        );
    }

}
