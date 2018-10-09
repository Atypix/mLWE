<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_Captcha
{
	// Instance of this class
	protected static $instance = null;

	protected $size 	= 24;
	protected $width 	= 80;
	protected $height 	= 33;
	protected $lines	= 20;
	protected $font;

	public function __construct()
	{
		$this->font = dirname(__FILE__) . '/captcha-font.ttf';
	}

	public function generate( $post_id = 0, $review_id = 0 ) 
	{
		// Random number
		$code = rand( 1000, 9999 );

		// Captcha image
		$image = imagecreate( $this->width, $this->height );

		// Set the backgorund color
		imagecolorallocate( $image, 255, 255, 255 );

		// Set the text color
		$text_color = imagecolorallocate( $image, 64, 64, 64 ); #404040

		// Create lines
		for ($x = 1; $x <= $this->lines; $x++) { 
			$x1 = rand( 1, 100 );
			$y1 = rand( 1, 50 );
			$x2 = rand( 1, 50 );
			$y2 = rand( 1, 100 );

			imageline( $image, $x1, $y1, $x2, $y2, $text_color );
		}

		// Apply font attributes 
		imagettftext( $image, $this->size, 0, 0, 24, $text_color, $this->font, $code );

		// Begin capturing the byte stream
        ob_start();

		// Output the image
		imagejpeg( $image, NULL, 100 );

		// and finally retrieve the byte stream
        $raw_image_bytes = ob_get_clean();

        // Free up memory
		imagedestroy($image);

		//echo $code;
		$session_key = 'rwp-captcha-' . $post_id . '-' . $review_id;

		$_SESSION[ $session_key ] = $code;

        return 'data:image/jpeg;base64,' . base64_encode( $raw_image_bytes );
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
