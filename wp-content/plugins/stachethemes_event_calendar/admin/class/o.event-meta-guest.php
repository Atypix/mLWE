<?php

namespace Stachethemes\Stec;




class Event_Meta_Guest implements Event_Meta_Object {



    public $name;
    public $photo;
    public $links;
    public $about;
    public $photo_full;



    public function get_data() {
        return array(
                'name'  => $this->name,
                'photo' => $this->photo,
                'links' => $this->links,
                'about' => $this->about
        );
    }

}
