<?php 
class pw_flash_sale_Widget extends WP_Widget {

	function __construct() {
			parent::__construct(
				'pw_flash_sale_Widget', // Base ID
				__('WooCommerce Flash Sale', 'woocommerce-brands'), // Name
				array( 'description' => __( 'Display a list of your Flash Sale.', 'woocommerce-brands' ), ) // Args
			);
	}
	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

/*		$cunt=$instance['count'];
		if($instance['count']=="")
			$cunt=-1;
		
		$args=array(
			'post_type'=>'flash_sale',
			'posts_per_page'=>$cunt,
			'order'=>'data',
			'orderby'=>'DESC',
		);

		$loop = new WP_Query( $args );
		$arr="";
		if ( $loop->have_posts() ) 
		{
			while ( $loop->have_posts() ) : 
				$loop->the_post();
				$arr= get_post_meta(get_the_ID(),'pw_array',true);
				
				if (in_array($product_id, $arr))
				{
					$pw_discount= get_post_meta(get_the_ID(),'pw_discount',true);
					
					echo 'asddddddddddddddd';
					break;
				}
			endwhile;
		}
		*/
		$carousel_type = $instance['carousel_type'];
		$count   = $instance['count'];				
		$rule = $instance['rule'];
		$countdown = ($instance['countdown']=="1"? "yes":"no");
		$carousel_per_view=$instance['carousel_per_view'];		
	
	//	[pw_flashsale type="ver-carousel" show_countdown="yes" count_items="" carousel_item_per_view="" rule="4449"  scroll_height=""]		
		//echo '[pw_flashsale type="'.$carousel_type.'" count_items="'.$count.'" carousel_item_per_view="'.$carousel_per_view.'" rule="'.$rule.'" show_countdown="'.$countdown.'"]';
		echo do_shortcode('[pw_flashsale type="'.$carousel_type.'" count_items="'.$count.'" carousel_item_per_view="'.$carousel_per_view.'" rule="'.$rule.'" show_countdown="'.$countdown.'"]');		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p>
			<label>Select Rules</label></th>
			<td>
			<select fieldname="rule" id="select_rule">
				<option value="">Select Rule</option>
			</select>
		</p>
		<p>
			<label>Count Of Product</label>
			<input type="text" value="" fieldname="count_items" />
		</p>
		<p>
			<label>Show Discunt</label>
			<select fieldname="show_discunt">
				<option value="yes">Yes</option>
				<option value="no">No</option>
			</select>
		</p>
		<p>
			<label>Show CountDown</label>
			<select fieldname="show_countdown">
				<option value="yes">Yes</option>
				<option value="no">No</option>
			</select>
		</p>
		<p>
			<label>Count Down Style</label>
			<select fieldname="countdown_style">
				<option value="style1">Style 1</option>
				<option value="style2">Style 2</option>
				<option value="style3">Style 3</option>
			</select>
		</p>
		<p>
			<label>Count Down Size</label>
			<select fieldname="countdown_size">
				<option value="small">Small</option>
				<option value="medium">Medium</option>
				<option value="large">Large</option>
			</select>
		</p>
		<p>
			<label>Countdown Text Color</label>
			<input type="text" class="flash_sale-shortcodes-button_colour_custom" fieldname="text_colour" value="" />
		</p>
		<p>
			<label>Countdown Back Colour</label>
			<input type="text" class="flash_sale-shortcodes-button_colour_custom" fieldname="countdown_backcolour" value="" />
		</p>
		<p>
			<label>Countdown Area Backcolour</label>
			<input type="text" class="flash_sale-shortcodes-button_colour_custom" fieldname="countdown_area_backcolour" value="" />
		</tr>
		<p>
			<th><label>Item Width</label></th>
			<td><input type="text" value="" fieldname="item_width" /></td>
		</p>
		<p>
			<th><label>Item Marrgin</label></th>
			<td><input type="text" value="" fieldname="item_marrgin" /></td>
		</p>
		<p>
			<label>Slide direction</label>
			<select fieldname="slide_direction">
				<option value="vertical">Vertical</option>
				<option value="horizontal">Horizontal</option>
			</select>
		</p>
		<p>
			<label>Show Pagination</label>
			<select fieldname="show_paginatin">
				<option value="true">Yes</option>
				<option value="false">No</option>
			</select>
		</p>
		<p>
			<label>Show Control</label>
			<select fieldname="show_control">
				<option value="true">Yes</option>
				<option value="false">No</option>
			</select>
		</p>
		<p>
			<label>Item Per View</label>
			<input type="text" value="" fieldname="item_per_view" />
		</p>
		<p>
			<label>Item Per Slide</label>
			<input type="text" value="" fieldname="item_per_slide" />
		</p>
		<p>
			<label>Slide Speed</label>
			<select fieldname="slide_speed">
				<option value="1000">1 sec</option>
				<option value="2000">2 sec</option>
				<option value="3000">3 sec</option>
				<option value="4000">4 sec</option>
				<option value="5000">5 sec</option>
				<option value="6000">6 sec</option>
				<option value="7000">7 sec</option>
			</select>
		</p>
		<p>
			<label>Auto play</label>
			<select fieldname="auto_play">
				<option value="true">Yes</option>
				<option value="false">No</option>
			</select>
		</p>
		<p>
			<label>Description Area BackColor</label>
			<input type="text" class="flash_sale-shortcodes-button_colour_custom" fieldname="description_area_backcolour" value="" />
		</p>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woocommerce-brands'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" />
		</p>

		<p><label for="<?php echo $this->get_field_id('carousel_type'); ?>"><?php _e('Carousel Type:','woocommerce-brands'); ?></label>
            <select class='widefat' id="<?php echo $this->get_field_id('carousel_type'); ?>"
                    name="<?php echo $this->get_field_name('carousel_type'); ?>" >
              <option value='hor-carousel' <?php selected( @$instance['carousel_type'] , "hor-carousel",1); ?>>
                Horizontal Carousel
              </option>
              <option value='ver-carousel' <?php selected( @$instance['carousel_type'] , "ver-carousel",1); ?>>
                Vertical Carousel
              </option>
              
              <option value='list-carousel' <?php selected( @$instance['carousel_type'] , "list-carousel",1); ?>>
                List
              </option>
            </select>
        </p>	
        <p><label for="rss-show-summary"><?php echo _e('Item count View','woocommerce-brands'); ?></label>
        <input id="rss-show-summary" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo @$instance['count']; ?>"/>
		</p>

        <p><label for="rss-show-summary"><?php echo _e('Item Per View','woocommerce-brands'); ?></label>
        <input id="rss-show-summary" name="<?php echo $this->get_field_name('carousel_per_view'); ?>" type="number" value="<?php echo @$instance['carousel_per_view']; ?>"/>
		</p>

		<p><label for="<?php echo $this->get_field_id('rule'); ?>"><?php _e('Rule Name:','woocommerce-brands'); ?></label>
		<?php 
               		$args=array(
						'post_type'=>'flash_sale',
						'posts_per_page'=>-1,
						'order'=>'data',
						'orderby'=>'DESC',
					);
					$loop = new WP_Query( $args );		
		?>
            <select class='widefat' id="<?php echo $this->get_field_id('rule'); ?>"
                    name="<?php echo $this->get_field_name('rule'); ?>" >
			<?php
				while ( $loop->have_posts() ) : 
					$loop->the_post();
					?>
					<option value='<?php echo get_the_ID();?>' <?php selected( @$instance['rule'] , get_the_ID() ,1 ); ?> >
					<?php echo get_post_meta(get_the_ID(),'pw_name',true);?>
					</option>
			<?php
				endwhile;
			?>
            </select>
        </p>
		
		<p><input id="rss-show-summary" name="<?php echo $this->get_field_name('countdown'); ?>" type="checkbox" value="1" <?php checked( @$instance['countdown'], 1 ); ?> />
		<label for="rss-show-summary"><?php echo _e('Show countdown','woocommerce-brands'); ?></label></p>

	
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();	
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['carousel_type'] = $new_instance['carousel_type'];
		$instance['count']     = @isset( $new_instance['count'] ) ? $new_instance['count']:"";				
		$instance['rule'] = $new_instance['rule'];
		$instance['countdown']     = isset($new_instance['countdown'] ) ? (int) $new_instance['countdown'] : 0;
		$instance['carousel_per_view']     = @isset( $new_instance['carousel_per_view'] ) ? $new_instance['carousel_per_view']:"";		
		return $instance;
	}
}

register_widget( 'pw_flash_sale_Widget' );	

?>