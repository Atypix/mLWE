<!--reviewer table-->
<div class="rwp-table-wrap <?php echo $table_theme; ?>">

    <span class="rwp-table-title"><?php echo $this->table['table_title']; ?></span>

    <?php
    	$order  	= $this->template_field('template_criteria_order', true);
    	$criteria 	= $this->template_field('template_criterias', true);
    	$max 		= $this->template_field('template_maximum_score', true);
    	$order		= ( $order == null ) ? array_keys( $criteria) : $order;
    ?>

    <?php $legend_required = array('rwp-theme-1', 'rwp-theme-4'); if ( in_array( $table_theme, $legend_required ) ): ?>

	    <div class="rwp-legend">

		<?php $letter = 'A'; foreach ($order as $i):?>

		    <div class="rwp-criteria">
		        <span class="rwp-key" <?php $this->style_letters(); ?>><?php echo $letter; ?></span> <?php echo $criteria[$i]; ?>
		    </div>

		<?php $letter++; endforeach; ?>

		</div><!--/legend-->

    <?php endif ?>

    <?php foreach( $this->reviews as $review ): $is_UR = (isset( $review['review_type'] ) && $review['review_type'] == 'UR') ? true : false; ?>

    <div class="rwp-table">

        <div class="rwp-title"><a href="<?php echo get_permalink( $review['review_post_id'] ); ?>"><?php echo $review['review_title']; ?></a></div>

        <?php $scores = (!$is_UR) ? $review['review_scores'] : $review['review_ratings_scores']['scores']; ?>

		<?php
			if( isset( $this->table['table_reviews_boxes_image'] ) && $this->table['table_reviews_boxes_image'] != 'no' ) {
				include 'theme-section-table-image.php';
			}

			include 'theme-section-table-overalls.php';

			switch ( $table_theme ) {

				case 'rwp-theme-5':

					echo '<div class="rwp-criterias">';
		        	foreach( $order as $i ) {
		           	 	echo '<div class="rwp-criteria-wrap">';
		                	echo '<span class="rwp-criteria-label">'. $this->template['template_criterias'][$i] .'</span>';
		                	echo '<div class="rwp-bar-wrap">' . $this->get_score_bar( $scores[$i], '', 0, true ) . '</div><!--/bar-wrap-->';
		           		echo '</div><!--/criteria-wrap-->';
		       		}
			        echo '</div><!--criterias-->';
			        break;

				case 'rwp-theme-3':

			        echo '<div class="rwp-criterias">';
		        	foreach( $order as $i ) {
		           	 	echo '<div class="rwp-criteria-wrap">';
		           	 		echo '<div class="rwp-score-circle-table" id="'. uniqid('rwp-score-circle-') .'" '. $this->get_circle_score_params( $scores[$i]) .'></div>';
			                echo '<span class="rwp-criteria-label">'. $this->template['template_criterias'][$i] .'</span>';
		           		echo '</div><!--/criteria-wrap-->';
		       		}
			        echo '</div><!--criterias-->';
					break;

				case 'rwp-theme-2':

					echo '<div class="rwp-criterias">';
		        	foreach( $order as $i ) {
		           	 	echo '<div class="rwp-criteria-wrap">';
		                	echo '<span class="rwp-criteria-label">'. $this->template['template_criterias'][$i] .'</span>';
		                	echo $this->get_stars( $scores[$i], $max );
		           		echo '</div><!--/criteria-wrap-->';
		       		}
			        echo '</div><!--criterias-->';
					break;

				case 'rwp-theme-1':
				case 'rwp-theme-4':
				default:

					echo '<div class="rwp-criterias">';

					foreach( $order as $i )
						echo '<div class="rwp-criteria-wrap">' . $this->get_score_bar( $scores[$i] ) . '</div><!--/criteria-wrap-->';

        			echo '</div><!--criterias-->';

        			echo '<div class="rwp-criterias-labels-wrap">';

        			$letter = 'A';

        			foreach ($order as $i) {

	   					echo '<span class="rwp-criteria-label" '. $this->style_letters(true) . '>'. $letter . '</span>';
						$letter++;
					}
        			echo '</div><!--/criterias-labels-wrap-->';
        			break;
			}
		?>

    </div><!--/table-->

    <?php endforeach; ?>

</div>
<!--/reviewer table-->
