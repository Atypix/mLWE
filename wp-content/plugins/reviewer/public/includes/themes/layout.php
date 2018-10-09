<!--RWP Review-->
<div 
	class="rwp-review-wrap <?php $this->template_field('template_theme'); ?>" 
	id="<?php echo $this->vueID; ?>" 
	style="color: <?php $this->template_field('template_text_color'); ?>; font-size: <?php $this->template_field('template_box_font_size') ?>px"
    <?php RWP_Snippets::schema( $this->branch, $this->is_UR ); ?>
    data-post-id="<?php echo $this->post_id; ?>"
    data-box-id="<?php $this->review_field('review_id'); ?>"
    data-template-id="<?php $this->template_field('template_id') ?>"
    data-disabled="<?php echo intval($this->is_users_rating_disabled()) ?>"
    data-sharing-review-link-label="<?php _e( 'Copy and paste the URL to share the review', 'reviewer' ) ?>"
    data-per-page="<?php echo $this->ratings_per_page ?>"
>

    <div class="rwp-review">
        <?php 
            $sameas_enabled = ($this->preferences_field( 'preferences_sameas', true ) ) == 'no' ? false : true;
            
            if( empty( $this->branch ) || $this->branch == 'recap' ) {

                $title_option = $this->review_field('review_title_options', true);
                $same_as_url  = $this->review_field('review_sameas_attr', true);
                $same_as_url  = empty( $same_as_url ) ? esc_url( get_permalink( $this->post_id ) ) : esc_url( $same_as_url );
                
                switch ( $title_option ) {
                    case 'hidden':
                        $title = get_the_title( $this->post_id );
                        $this->snippets->add('name', $title);
                        echo '<div'; 
                        RWP_Snippets::itemReviewed( $this->branch, $this->schema_type );
                        echo '>';
                            echo '<meta'; 
                            RWP_Snippets::name( $this->branch );
                            echo 'content="'. $title .'">';
                            if ( $sameas_enabled ) {
                                echo '<meta property="sameAs" content="'. $same_as_url .'" />';
                            }
                        echo '</div>';
                        break;

                    case 'post_title':
                        $title = get_the_title( $this->post_id );
                        $this->snippets->add('name', $title);
                        echo '<span class="rwp-title"'; 
                        RWP_Snippets::itemReviewed( $this->branch, $this->schema_type );
                        echo '><em'; 
                        RWP_Snippets::name( $this->branch );
                        echo '>'. $title .'</em>';
                        if ( $sameas_enabled ) {
                            echo '<meta property="sameAs" content="'. $same_as_url .'" />';
                        }
                        echo '</span>';
                        break;
                    
                    default:
                        $title = $this->review_field('review_title', true );
                        if( empty( $title ) ) {
                            $title = get_the_title( $this->post_id );
                            $this->snippets->add('name', $title);
                            echo '<div'; 
                            RWP_Snippets::itemReviewed( $this->branch, $this->schema_type );
                            echo '>';
                                echo '<meta'; 
                                RWP_Snippets::name( $this->branch );
                                echo 'content="'. $title .'">';
                            if ( $sameas_enabled ) {
                                echo '<meta property="sameAs" content="'. $same_as_url .'" />';
                            }
                            echo '</div>';
                        } else {
                            $this->snippets->add('name', $title);
                            echo '<span class="rwp-title"'; 
                            RWP_Snippets::itemReviewed( $this->branch, $this->schema_type );
                            echo '><em'; 
                            RWP_Snippets::name( $this->branch );
                            echo '>'. $title .'</em>';
                            if ( $sameas_enabled ) {
                                echo '<meta property="sameAs" content="'. $same_as_url .'" />';
                            }
                            echo'</span>';
                        }
                        break;
                } 
            }

            if( !$this->is_UR ) {
                $post_author = get_post_field( 'post_author', $this->post_id );
                $this->snippets->add('review', array());
                $this->snippets->add('review.@type', 'Review');
                $this->snippets->add('review.datePublished', get_the_date( 'Y-m-d', $this->post_id ));
                $this->snippets->add('review.author', array());
                $this->snippets->add('review.author.@type', 'Person');
                $this->snippets->add('review.author.name', get_the_author_meta( 'display_name', $post_author ));

                echo '<div property="author" typeof="Person">';
                    echo '<meta property="name" content="'. get_the_author_meta( 'display_name', $post_author ) .'" />';
                    if ( $sameas_enabled ) {
                        echo '<meta property="sameAs" content="'. esc_url( get_the_author_meta( 'user_url', $post_author ) ) .'" />';
                    }
                echo '</div>';
                echo '<meta property="datePublished" content="'. get_the_date( 'Y-m-d', $this->post_id ) .'" />';
            }
        ?>

        <?php $this->include_sections( $this->themes[ $this->template_field('template_theme', true) ], $hide_criteria_scores ); ?>

    </div><!-- /review -->

    <?php //if( empty( $this->branch ) || $this->branch == 'recap' ) $this->snippets->insert(); ?>

    <!-- <pre>{{$data | json}}</pre> -->

</div><!--/review-wrap-->


