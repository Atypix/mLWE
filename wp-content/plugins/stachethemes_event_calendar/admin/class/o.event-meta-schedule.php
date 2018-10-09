<?php

namespace Stachethemes\Stec;




class Event_Meta_Schedule implements Event_Meta_Object {



    public $start_date;
    public $title;
    public $icon;
    public $icon_color;
    public $details;



    public function get_data() {
        return array(
                'title'      => $this->title,
                'icon'       => $this->icon,
                'icon_color' => $this->icon_color,
                'details'    => $this->details,
                'start_date' => $this->start_date,
        );
    }

}
