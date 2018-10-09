<?php
/**
 * The WPPR Pro Related Reviews Widget Class.
 *
 * @package WPPR_Pro
 * @subpackage Widget
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 2.0.0
 */

/**
 * Class WPPR_Pro_Related_Reviews_Widget
 */
class WPPR_Pro_Related_Reviews_Widget extends WP_Widget {

	/**
	 * Widget name
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     string $widget_name Widget name.
	 */
	public $widget_name;

	/**
	 * Widget description
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     string $widget_desc Widget description.
	 */
	public $widget_desc;

	/**
	 * Widget identifier of this plugin for WP
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     string $plugin_slug Widget identifier of this plugin for WP.
	 */
	public $plugin_slug;

	/**
	 * Widget text domain of this plugin
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     string $text_domain Widget text domain of this plugin.
	 */
	public $text_domain;

	/**
	 * Widget number of posts to show in the widget
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     string $number_posts Widget number of posts to show in the widget.
	 */
	public $number_posts;

	/**
	 * The post ID
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     integer $post_id The post ID.
	 */
	public $post_id;

	/**
	 * Is review
	 *
	 * @since   2.0.0
	 * @access  public
	 * @var     boolean $is_review If is review return true or false.
	 */
	public $is_review;

	/**
	 * WPPR_Pro_Related_Reviews_Widget constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->widget_name   = 'Related reviews';
		$this->widget_desc   = 'Earn more visitors, displaying related reviews for each product.';
		$this->plugin_slug   = 'WPPR-Related-Reviews-Widget';
		$this->number_posts  = 5;
        // @codingStandardsIgnoreStart
		$widget_ops = array(
			'classname' => 'widget_cwp_latest_products_widget',
			'description' => __( $this->widget_desc, 'wp-product-review' ),
		);
		parent::__construct( $this->plugin_slug, __( $this->widget_name, 'wp-product-review' ), $widget_ops );
        // @codingStandardsIgnoreEnd
	}

	/**
	 * Utility method to register the widget.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function register() {
		register_widget( 'WPPR_Pro_Related_Reviews_Widget' );
	}

	/**
	 * Method for widget form creation
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   array $instance The form instance.
	 */
	public function form( $instance ) {
		$title        = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number_posts = isset( $instance['number_posts'] ) ? absint( $instance['number_posts'] ) : $this->number_posts;
		$show_thumb   = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : true;
        // @codingStandardsIgnoreStart
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'wp-product-review' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number_posts' ); ?>"><?php _e( 'Number of posts to show:', 'wp-product-review' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_posts' ); ?>" type="text" value="<?php echo $number_posts; ?>" size="3" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_thumb ); ?> id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>"><?php _e( 'Display thumbnail?', 'wp-product-review' ); ?></label>
		</p>
		<?php
        // @codingStandardsIgnoreEnd
	}

	/**
	 * Method to updated widget data.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   array $new_instance The new form instance.
	 * @param   array $old_instance The old form instance.
	 * @return mixed
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Fields
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['number_posts'] = absint( $new_instance['number_posts'] );
		$instance['show_thumb']   = isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;
		return $instance;
	}

	/**
	 * Display method for widget
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   array $args The widget args.
	 * @param   array $instance The widget instance.
	 * @return mixed
	 */
	public function widget( $args, $instance ) {

		   // these are the widget options
		   $title           = apply_filters( 'widget_title', $instance['title'] );
		$number_posts    = ( ! empty( $instance['number_posts'] )) ? absint( $instance['number_posts'] ) : $this->number_posts;
		$show_thumb      = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : false;
		$this->post_id   = (is_single()) ? get_the_ID() : false;
		$this->is_review = ((get_post_meta( $this->post_id, 'cwp_meta_box_check', true ) == 'Yes') && $this->post_id) ? true : false;

		// empty if does not exist review
		if ( ! $this->is_review ) {
			return false;
		}

		   echo $args['before_widget'];

			   // Check if title is set
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

			   // Show reviews
			   $this->get_reviews( $number_posts, $show_thumb );

		   echo $args['after_widget'];
	}

	/**
	 * Utility method to get reviews.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   integer $number_posts The number of posts to display.
	 * @param   boolean $show_thumb Display the thumbnail.
	 */
	public function get_reviews( $number_posts, $show_thumb ) {
		global $post;

		$post_categories = wp_get_post_categories( $this->post_id );
		$post_tags       = wp_get_post_tags(
			$this->post_id, array(
				'fields' => 'ids',
			)
		);

		$args = array(
			'posts_per_page' => $number_posts,
			'post_status'    => 'publish',
			'meta_key'       => 'cwp_meta_box_check',
			'meta_value'     => 'Yes',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => array( $this->post_id ),
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => $post_categories,
					'include_children' => false,
				),
				array(
					'taxonomy' => 'post_tag',
					'field' => 'id',
					'terms' => $post_tags,
				),
			),
		);

		$reviews = new WP_Query( apply_filters( 'widget_posts_args', $args ) );

		if ( $reviews->have_posts() ) :
		?>
			<ul>
				<?php
				while ( $reviews->have_posts() ) :
					$reviews->the_post();
?>
					<li class="cwp-popular-review cwp_top_posts_widget_<?php the_ID(); ?>">
						<?php
							// show thumb
						if ( $show_thumb ) {
							$product_image = get_post_meta( $post->ID, 'cwp_rev_product_image', true );
							if ( $product_image ) {
								echo '<img src="' . $product_image . '" alt="' . get_the_title() . '" class="cwp_rev_image"/>';
							} elseif ( has_post_thumbnail() ) {
								echo wp_get_attachment_image(
									get_post_thumbnail_id(), 'thumbnail', 0, array(
										'alt' => get_the_title(),
										'class' => 'cwp_rev_image',
									)
								);
							}
						}
							// show title
							?>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php
		wp_reset_postdata();
		endif;
	}

	/**
	 * Method for when the plugin is activated.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function widget_admin_notice() {
		if ( isset( $_GET['activate'] ) && $_GET['activate'] == true ) {
			$url_widget = admin_url( 'widgets.php' );
			?>
			<div class="updated">
				<p>
				<?php
                    // @codingStandardsIgnoreStart
					/* translators: %s is replaced a url */
					printf( __( 'Great, now go under <a href="%s">Appearance &#8250 Widgets</a> and place your widget in your sidebar.', 'wp-product-review' ), $url_widget );
                    // @codingStandardsIgnoreEnd
				?>
				</p>
			</div>
			<?php
		}
	}

}

