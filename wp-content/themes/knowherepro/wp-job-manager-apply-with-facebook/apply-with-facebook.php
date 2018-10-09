<?php
/**
 * Adds the Apply with Facebook functionality to the application form.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-apply-with-facebook/apply-with-facebook.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Apply with Facebook
 * @category    Template
 * @version     1.0.3
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_script( 'wp-job-manager-apply-with-facebook-js' );
?>

<input class="apply-with-facebook apply-with-facebook-button" type="button" value="<?php esc_html_e( 'Apply with Facebook', 'knowherepro' ); ?>" />

