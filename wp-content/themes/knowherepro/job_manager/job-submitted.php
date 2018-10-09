<?php
global $wp_post_types;

switch ( $job->post_status ) :
	case 'publish' :
		echo '<div class="job-manager-message">';
		printf( __( '%s listed successfully. To view your listing <a href="%s">click here</a>.', 'knowherepro' ), $wp_post_types['job_listing']->labels->singular_name, get_permalink( $job->ID ) );
		echo '</div>';
	break;
	case 'pending' :
		echo '<div class="job-manager-message">';
		printf( __( '%s submitted successfully. Your listing will be visible once approved.', 'knowherepro' ), $wp_post_types['job_listing']->labels->singular_name, get_permalink( $job->ID ) );
		echo '</div>';
		break;
	default :
		do_action( 'job_manager_job_submitted_content_' . str_replace( '-', '_', sanitize_title( $job->post_status ) ), $job );
	break;
endswitch;

do_action( 'job_manager_job_submitted_content_after', sanitize_title( $job->post_status ), $job );