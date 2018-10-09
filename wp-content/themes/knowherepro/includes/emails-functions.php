<?php

if( !function_exists( 'knowhere_listing_contact_form' ) ) {
	function knowhere_listing_contact_form() {

		$nonce = $_POST['knowhere-detail-ajax-nonce'];

		if ( !wp_verify_nonce( $nonce, 'knowhere-contact-form-nonce') ) {
			echo json_encode(array(
				'success' => false,
				'msg' => esc_html__('Unverified nonce!', 'knowherepro' )
			));
			wp_die();
		}

		$sender_phone = sanitize_text_field( $_POST['phone'] );
		$target_email = sanitize_email($_POST['target_email']);
		$target_email = is_email($target_email);

		if ( !$target_email ) {
			echo json_encode(array(
				'success' => false,
				'msg' => sprintf( esc_html__('%s Target Email address is not properly configured!', 'knowherepro'), $target_email )
			));
			wp_die();
		}

		$sender_name = sanitize_text_field($_POST['name']);

		if ( empty($sender_name) ) {
			echo json_encode(array(
				'success' => false,
				'msg' => esc_html__('Name field is empty!', 'knowherepro')
			));
			wp_die();
		}

		$sender_email = sanitize_email($_POST['email']);
		$sender_email = is_email($sender_email);

		if ( !$sender_email ) {
			echo json_encode(array(
				'success' => false,
				'msg' => esc_html__('Email address is invalid!', 'knowherepro')
			));
			wp_die();
		}

		$sender_msg = wp_kses_post( $_POST['message'] );

		if ( empty($sender_msg) ) {
			echo json_encode(array(
				'success' => false,
				'msg' => esc_html__('Your message empty!', 'knowherepro')
			));
			wp_die();
		}

		$email_subject = sprintf( esc_html__('New message sent by %s using contact form at %s', 'knowherepro'), $sender_name, get_bloginfo('name') );

		$email_body = esc_html__("You have received a message from: ", 'knowherepro') . $sender_name . " <br/>";
		if ( !empty($sender_phone) ) {
			$email_body .= esc_html__("Phone Number : ", 'knowherepro') . $sender_phone . " <br/>";
		}
		$email_body .= esc_html__("Additional message is as follows.", 'knowherepro') . " <br/>";
		$email_body .= wpautop( $sender_msg ) . " <br/>";
		$email_body .= sprintf( esc_html__( 'You can contact %s via email %s', 'knowherepro'), $sender_name, $sender_email );

		$header = 'Content-type: text/html; charset=utf-8' . "\r\n";
		$header .= 'From: ' . $sender_name . " <" . $sender_email . "> \r\n";

		if ( wp_mail( $target_email, $email_subject, $email_body, $header) ) {
			echo json_encode( array(
				'success' => true,
				'msg' => esc_html__("Message Sent Successfully!", 'knowherepro')
			));
			wp_die();
		} else {
			echo json_encode(array(
					'success' => false,
					'msg' => esc_html__("Server Error: Make sure Email function working on your server!", 'knowherepro')
				)
			);
			wp_die();
		}

		wp_die();
	}
}

add_action( 'wp_ajax_nopriv_knowhere_listing_contact_form', 'knowhere_listing_contact_form' );
add_action( 'wp_ajax_knowhere_listing_contact_form', 'knowhere_listing_contact_form' );