<?php

/**
 * Reviewer Plugin v.3
 * Created by Michele Ivani
 */
class RWP_Snippets
{
	// Instance of this class
	protected static $instance = null;
	protected $properties;

	function __construct( $properties = array(), $type = 'Product' )
	{
		$default = array(
			'@context' => 'http://schema.org/',
			'@type' => $type
		);

		$this->properties = array_merge( $default, $properties );
	}

	public function insert( $structure = 'RDFA' ) 
	{
		switch ( $structure ) {
			case 'JSON-LD':
				echo '<script type="application/ld+json">';
				echo json_encode( $this->properties );
				echo '</script>';
				break;
			
			case 'RDFA' :
			default:
				echo '<div vocab="http://schema.org/" typeof="'. $this->get('@type') .'" hidden>';
					echo '<meta property="name" content="'. $this->get('name') .'" />';
					echo !is_null( $this->get('image') ) ? '<meta property="image" content="'. $this->get('image') .'" />' : '';
					
					if( $this->has('review') ) {
						$review = $this->get('review');
						echo '<div property="review" typeof="Review">';
							echo '<meta property="datePublished" content="'. $review['datePublished'] .'" />';
							// echo '<meta property="reviewBody" content="'. $review['reviewBody'] .'" />';

							$author = $review['author'];
							echo '<span property="author" typeof="http://schema.org/Person">';
						  		echo '<meta property="name" content="'. $author['name'] .'" />';
							echo '</span>';

							$rating = $review['reviewRating'];
							echo '<span property="reviewRating" typeof="Rating">';
						  		echo '<meta property="ratingValue" content="'. $rating['ratingValue'] .'" />';
						  		echo '<meta property="worstRating" content="'. $rating['worstRating'] .'" />';
						  		echo '<meta property="bestRating" content="'. $rating['bestRating'] .'" />';
						  	echo '</span>';
						echo '</div>';
					}

					if( $this->has('aggregateRating') ) {
						$rating = $this->get('aggregateRating');
						echo '<div property="aggregateRating" typeof="AggregateRating">';
							echo '<meta property="ratingValue" content="'. $rating['ratingValue'] .'" />';
					  		echo '<meta property="worstRating" content="'. $rating['worstRating'] .'" />';
					  		echo '<meta property="bestRating" content="'. $rating['bestRating'] .'" />';
					  		echo '<meta property="ratingCount" content="'. $rating['ratingCount'] .'" />';
						echo '</div>';
					}
				echo '</div>';
				break;
		}
		//RWP_Reviewer::pretty_print( $this->get_properties() );
	}

	public function add( $key = '', $value = '' ) 
	{
		$exploded = explode('.', $key);
		$temp = &$this->properties;
		foreach($exploded as $key) {
		    $temp = &$temp[$key];
		}
		$temp = $value;
		unset($temp);
	}

	public function get( $property )
	{
		return $this->has( $property ) ? $this->properties[ $property ] : null;
	}

	public function has( $property = '') 
	{
		return ( isset( $this->properties[ $property ] ) && !empty( $this->properties[ $property] ) ); 
	}

	public function get_properties() 
	{
		return $this->properties;
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function schema( $branch = '', $is_UR = false ) {
		if( empty( $branch ) || $branch == 'recap' ) {
			if( !$is_UR ) {
				echo ' vocab="http://schema.org/" typeof="Review" ';
			} else {
				echo ' vocab="http://schema.org/" typeof="AggregateRating" ';
			}
		}
	}

	public static function itemReviewed( $branch = '', $type = 'Product' ) {
		if( empty( $branch ) || $branch == 'recap' ) {
			echo ' property="itemReviewed" typeof="'. $type .'" ';
		}
	}

	public static function name( $branch = '' ) {
		if( empty( $branch ) || $branch == 'recap' ) {
			echo ' property="name" ';
		}
	}

	public static function ratingValue( $branch = '', $is_UR = false ) {
		if( empty( $branch ) || $branch == 'recap' ) {
			if( $is_UR ) {
				echo ' property="ratingValue" ';
			} 
		}
	}

}
