<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */

include_once( 'class-reviews-widget.php' );

class RWP_Reviews_List_Shortcode
{

	// Instace of this class
	protected static $instance = null;
	protected $shortcode_tag = 'rwp-reviews-list';

	function __construct()
	{
		$this->plugin_slug = 'reviewer';

		add_shortcode( $this->shortcode_tag , array( $this, 'do_shortcode' ) );
		add_shortcode( 'rwp_boxes' , array( $this, 'do_shortcode' ) );
	}

	public function do_shortcode( $atts ) {

		$templates = RWP_Reviewer::get_option( 'rwp_templates' );
		
		extract( shortcode_atts( array(
			'title' => '',
			'template' => implode(':', array_keys($templates)),
			'sorting' => 'latest',
			'count' => 5,
			'layout' => 'auto',
			'stars' => 'no',
		), $atts ) );

		$template = ( $template == 'all' ) ? array_keys($templates) : explode(':', $template);

		$reviews = RWP_Reviews_Widget::query_reviews( $template, $sorting, $count );

		$has_image = false;

		if( count( $reviews ) <= 0 )
			return '<p>' . __('No reviews found for the selected templates.', $this->plugin_slug) . '</p>';

		foreach ($reviews as $r) { // Check is at least a review as an image
			$img = $r['review_image'];
			if( $r['review_use_featured_image'] == 'no' ) {
				$has_image = (!empty( $img ) ) ? true : false;
			} else {
				$has_image =  has_post_thumbnail( $r['review_post_id'] );
			}
			if( $has_image ) break;
		}

		$count = intval( $count );
		$count = ( $count > 0 && $count <= count( $reviews ) ) ? $count : count( $reviews );
		 	
		ob_start();

		include 'themes/layout-list.php';

		//RWP_Reviewer::pretty_print($reviews);
		//RWP_Reviewer::pretty_print($templates);
		
		return ob_get_clean();
	}

	public static function get_form() {

		$parent = 'rwp_rl';
		
		$form  = '<div id="rwp-reviews-list-form">';
		$form .= '<form name="rwp_rl_form" id="rwp-rl-form">';
			$form .= '<table class="form-table">';
				$form .= '<tbody>';

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Title', 'reviewer' ) . '</th>';
						$form .= '<td><input type="text" name="'. $parent .'[title]" value="" /></td>';
					$form .= '</tr>';

					$templates = RWP_Reviewer::get_option( 'rwp_templates' );

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Template', 'reviewer' ) . '</th>';
						$form .= '<td>';

							foreach ($templates as $template_id => $template) {
								$form .= '<span class="rwp-block"><input type="checkbox" name="'. $parent .'[template]" value="'. $template_id .'" /> ' .$template['template_name'] . '</span>';
							}

						$form .= '</td>';
					$form .= '</tr>';

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Scores Options', 'reviewer' ) . '</th>';
						$form .= '<td>';
							$form .= '<span class="rwp-block"><input type="checkbox" name="'. $parent .'[stars]" value="yes" /> ' . __( 'Show scores as stars', 'reviewer' ) . '</span>';
						$form .= '</td>';
					$form .= '</tr>';

					$sortings = array(
						'latest'			=> __( 'Latest boxes', 'reviewer' ),
						'top_score'			=> __( 'Top scores boxes by reviewer', 'reviewer' ),
						'top_rated' 		=> __( 'Top rated boxes by users', 'reviewer' ),
						'top_users_scores' 	=> __( 'Top score boxes by users', 'reviewer' ),
						'combo_1' 			=> __( 'Combo 1 | Average of reviewer and users scores', 'reviewer' ),
					);

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Order', 'reviewer' ) . '</th>';
						$form .= '<td>';
							$form .= '<select name="'. $parent .'[sorting]">';

							foreach ($sortings as $sorting_id => $sorting) {
								$form .= '<option value="'. $sorting_id .'">' . $sorting . '</option>';
							}

							$form .= '</select>';
						$form .= '</td>';
					$form .= '</tr>';

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Number of reviews to display', 'reviewer' ) . '</th>';
						$form .= '<td><input type="text" name="'. $parent .'[count]" value="" placeholder="5" /></td>';
					$form .= '</tr>';

					$form .= '<tr valign="top">';
						$form .= '<th scope="row">' . __( 'Layout', 'reviewer' ) . '</th>';
						$form .= '<td>';
						$form .= '<input type="radio" name="'. $parent .'[layout]" value="auto" checked /> Full width <br/>';
						$form .= '<input type="radio" name="'. $parent .'[layout]" value="inline" /> Inline';
						$form .= '</td>';
					$form .= '</tr>';
					
					//$form .= '';
					
				$form .= '</tbody>';
			$form .= '</table>';

			$form .= '<input id="rwp-rl-gen-btn" class="button button-primary" type="button"  value="'. __( 'Generate Shortcode', 'reviewer' ) .'"  />';
		
		$form .= '</form>';
		$form .= '</div>';

		return $form;
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