<?php

namespace Stachethemes\Stec;




class Ical_Import extends \ICal\ICal {



    protected $_recurrence = false;
    private $tz            = 'UTC';



    public function __construct($filename = false, $recurrence = false) {

        $this->_recurrence = $recurrence;

        parent::__construct($filename);
    }



    /**
     * Now checks if $recurrence is true/false
     * This creates duplicate for every recurrence
     */
    public function processRecurrences() {

        if ( $this->_recurrence === true ) {
            parent::processRecurrences();
        }
    }



    public function setTheDateTz($tz = 'UTC') {
        $this->tz = $tz;
    }



    public function theDate($format, $stamp) {
        $d = new \DateTime();
        $d->setTimezone(new \DateTimeZone($this->tz));
        $d->setTimestamp($stamp);
        return $d->format($format);
    }



    /**
     * Reads an entire file or URL into an array
     * Fixes Facebook problem with blocked content
     *
     * @param  string $filename
     * @return array
     * @throws Exception
     */
    protected function fileOrUrl($filename) {

        // Use the default method for files
        if ( file_exists($filename) ) {
            return parent::fileOrUrl($filename);
        }

        $result = wp_remote_get($filename);

        if ( $result['response']['code'] != 200 ) {
            throw new \Exception('Error code: ' . $result['response']['code']);
        }

        $body  = explode("\n", $result['body']);
        $lines = array();

        foreach ( $body as $line ) {
            if ( !trim($line) ) {
                continue;
            }

            $lines[] = $line;
        }

        return $lines;
    }

}
