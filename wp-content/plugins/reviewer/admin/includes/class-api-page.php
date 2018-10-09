<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_API_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	public $api_fields;
	public $option_value;
	public $api_version = '1.3';

	public function __construct()
	{
		parent::__construct();
		$this->menu_slug = 'reviewer-api-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->add_menu_page();
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'Reviewer API', $this->plugin_slug), __( 'Reviewer API', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	} 

	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Reviewer API', $this->plugin_slug ); ?> <span class="rwp-template-count">v <?php echo $this->api_version; ?></span></h2>
			
			<p class="description"><?php _e( 'The Reviewer plugin offers some API to integrate the reviewer functionalities easly inside your theme. The page below describe all available APIs you can use. Click on the each api for more informations.', $this->plugin_slug ); ?></p>

			<hr>

			<p>The Reviewer API are implemented inside the <code>RWP_API</code> PHP class. The class is already included in your WordPress if the Reviewer plugin is active.</p>
			
			<h3>APIs</h3>
			
			
			<div class="api">
				<h4>get_review()</h4>

				<div class="api-content">

					<h5>Description</h5>
					<p>Get the data about a review of a specific post.</p>

					<h5>Parameters</h5>
					<p>The api requires the following parameters:</p>
					<ul>
						<li><code>$post_id <em>(int)</em></code>  The post id that contains the review</li>
						<li><code>$review_id <em>(int)</em></code> The review id </li>
						<li><code>$include_user_rating <em>(boolean)</em></code> If it is set to true the request will include the users rating values. Default: <code>FALSE</code></li>
					</ul>
					

					<h5>Return</h5>
					<p>It returns an empty array if the review does not exist, otherwise an array with following fields:</p>
					<pre>
Array
(
    [review_id] => 0
    [review_status] => publish
    [review_title] => My Review
    [review_template] => rwp_template_544cb7f906b27
    [review_overall_score] => 5.7
    [review_scores] => Array
        (
            [0] => 7
            [1] => 4
            [2] => 6
        )
    [review_overall_score] => 6.0
    [review_custom_overall_score] => 5.7
    [review_pros] => Lorem ipsum dolor sit amet.
    [review_cons] => Lorem ipsum dolor sit amet.
    [review_summary] => Lorem ipsum dolor sit amet.
    [review_image] => http://localhost/review-img.jpg
    [review_user_rating_options] => Array
        (
            [0] => rating_option_title
            [1] => rating_option_comment
            [2] => rating_option_score
            [3] => rating_option_like
        )
    [review_custom_links] => Array
        (
            [0] => Array
                (
                    [label] => Link
                    [url] => #
                )

        )
    [review_users_score] => Array
        (
            [scores] => Array
                (
                    [0] => 3
                    [1] => 7
                    [2] => 9
                )

            [count] => 16
        )
)			</pre>

					<p>Note: The <code>review_users_score</code> field will be set if the <code>$include_user_rating</code> paramenter is set to <code>TRUE</code></p>

					<h5>Usage</h5>
					<p><code>&lt;?php $review = RWP_API::get_review( $post_id, $review_id, true); ?&gt;</code></p>

				</div><!-- api-content -->

			</div><!-- /api -->

			<hr>

			<div class="api">
				<h4>get_post_reviews()</h4>

				<div class="api-content">

					<h5>Description</h5>
					<p>Get the data about all reviews of a specific post.</p>

					<h5>Parameters</h5>
					<p>The api requires the following parameters:</p>
					<ul>
						<li><code>$post_id <em>(int)</em></code> The post id that contains the review</li>
						<li><code>$include_user_rating <em>(boolean)</em></code> If it is set to true the request will include the users rating values. Default: <code>FALSE</code></li>
					</ul>
					
					<h5>Return</h5>
					<p>It returns an empty array if the post has not reviews, otherwise an array of reviews:</p>
	<pre>Array
(
    [0] => Array
	(
	    [review_id] => 0
	    [review_status] => publish
	    [review_title] => My Review
	    [review_template] => rwp_template_544cb7f906b27
	    [review_overall_score] => 5.7
	    [review_scores] => Array
	        (
	            [0] => 7
	            [1] => 4
	            [2] => 6
	        )
	    [review_overall_score] => 6.0
	    [review_custom_overall_score] => 5.7
	    [review_pros] => Lorem ipsum dolor sit amet.
	    [review_cons] => Lorem ipsum dolor sit amet.
	    [review_summary] => Lorem ipsum dolor sit amet.
	    [review_image] => http://localhost/review-img.jpg
	    [review_user_rating_options] => Array
	        (
	            [0] => rating_option_title
	            [1] => rating_option_comment
	            [2] => rating_option_score
	            [3] => rating_option_like
	        )
	    [review_custom_links] => Array
	        (
	            [0] => Array
	                (
	                    [label] => Link
	                    [url] => #
	                )

	        )
	    [review_users_score] => Array
	        (
	            [scores] => Array
	                (
	                    [0] => 3
	                    [1] => 7
	                    [2] => 9
	                )

	            [count] => 16
	        )
	)

    [1] => Array
	(
	    [review_id] => 1
	    [review_status] => publish
	    [review_title] => My Review 2
	    [review_template] => rwp_template_544cb7f906b27
	    [review_overall_score] => 5.7
	    [review_scores] => Array
	        (
	            [0] => 7
	            [1] => 4
	            [2] => 6
	        )
	    [review_overall_score] => 6.0
	    [review_custom_overall_score] => 5.7
	    [review_pros] => Lorem ipsum dolor sit amet.
	    [review_cons] => Lorem ipsum dolor sit amet.
	    [review_summary] => Lorem ipsum dolor sit amet.
	    [review_image] => http://localhost/review-img.jpg
	    [review_user_rating_options] => Array
	        (
	            [0] => rating_option_title
	            [1] => rating_option_comment
	            [2] => rating_option_score
	            [3] => rating_option_like
	        )
	    [review_custom_links] => Array
	        (
	            [0] => Array
	                (
	                    [label] => Link
	                    [url] => #
	                )

	        )
	    [review_users_score] => Array
	        (
	            [scores] => Array
	                (
	                    [0] => 3
	                    [1] => 7
	                    [2] => 9
	                )

	            [count] => 16
	        )
	)
)</pre>

					<p>Note: The <code>review_users_score</code> field will be set if the <code>$include_user_rating</code> paramenter is set to <code>TRUE</code></p>

					<h5>Usage</h5>
					<p><code>&lt;?php $reviews = RWP_API::get_post_reviews( $post_id, true); ?&gt;</code></p>

				</div><!-- api-content -->

			</div><!-- /api -->

			<hr>

			<div class="api">
				<h4>get_review_users_rating()</h4>
				
				<div class="api-content">
	
					<h5>Description</h5>
					<p>Get the users rating score and count about a review of a specific post.</p>

					<h5>Parameters</h5>
					<p>The api requires the following parameters:</p>
					<ul>
						<li><code>$post_id <em>(int)</em></code> The post id that contains the review</li>
						<li><code>$review_id <em>(int)</em></code> The review id. Use -1 if you setup Auto Box type</li>
						<li><code>$template_id <em>(string)</em></code> The template id of review boxe. Required if you setup Auto Box type</li>
					</ul>
					
					<h5>Return</h5>
					<p>It returns an array with score and count of users rating:</p>
					<pre>
Array
(
    [scores] => Array
        (
            [0] => 3
            [1] => 7
            [2] => 9
        )

    [count] => 16
)				</pre>

					<h5>Usage</h5>
					<p><code>&lt;?php $rating = RWP_API::get_review_users_rating( $post_id, $review_id, $template_id ); ?&gt;</code></p>

				</div><!-- api-content -->

			</div><!-- /api -->

			
			<hr>

			<div class="api">
				<h4>get_review_users_rating_in_html()</h4>
				
				<div class="api-content">
	
					<h5>Description</h5>
					<p>Get the html 5 stars, score rating and count about a review of a specific post.</p>

					<h5>Parameters</h5>
					<p>The api requires the following parameters:</p>
					<ul>
						<li><code>$post_id <em>(int)</em></code> The post id that contains the review</li>
						<li><code>$review_id <em>(int)</em></code> The review id </li>
						<!--<li><code>$icon_size <em>(int)</em></code> The icon size for stars in px. Deafult: <code>24</code> </li>
						<li><code>$use_icon_template <em>(boolean)</em></code> If it is set to true the request will use the custom icons for stars. Default: <code>FALSE</code> </li>-->
					</ul>
					
					<h5>Return</h5>
					<p>It returns an array with score and count of users rating and the html string that contains icons:</p>
					<pre>
Array
(
    [scores] => Array
        (
            [0] => 3
            [1] => 7
            [2] => 9
        )

    [count] => 16
    [html] => ...
)				</pre>

					<p>Note: The html string uses the follwing classes you can use for customization: <code>.rwp-api-rating-wrapper</code>: icons wrapper.</p>

					<h5>Usage</h5>
					<p><code>&lt;?php $rating = RWP_API::get_review_users_rating_in_html( $post_id, $review_id, $icon_size, true ); echo $rating['html']; ?&gt;</code></p>

				</div><!-- api-content -->

			</div><!-- /api -->

		</div><!--/wrap-->
		<?php
	}

	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function display_section()
	{
		// Do Nothing!
	}
}
