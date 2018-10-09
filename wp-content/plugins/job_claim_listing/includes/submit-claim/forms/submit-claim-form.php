<?php
/**
 * Submit Claim Forms
 *
 * @since 3.0.0
 */
namespace wpjmcl\submit_claim;
use wpjmcl\claim\Functions as Claim;
use wpjmcl\job_listing\Functions as Listing;
if ( ! defined( 'WPINC' ) ) { die; }

/* If form class not exist, load it. */
if ( ! class_exists( 'WP_Job_Manager_Form' ) ) {

	/* Always check, file could be moved to another location. */
	$path = '/includes/abstracts/abstract-wp-job-manager-form.php';
	if ( defined( 'JOB_MANAGER_PLUGIN_DIR' ) && file_exists( JOB_MANAGER_PLUGIN_DIR . $path ) ) {
		include( JOB_MANAGER_PLUGIN_DIR . $path );
	}

	/* Class still not exist, bail. */
	if ( ! class_exists( 'WP_Job_Manager_Form' ) ) { return;
	}
}

/**
 * Extend WP Job Manager Form to Build Submit Claim Form.
 *
 * @since 3.0.0
 */
class Submit_Claim_Form extends \WP_Job_Manager_Form {

	/* Class instance */
	public static $_instance = null;
	public $listing_obj = null;
	public $listing_id = 0;
	public $claim_id = 0;
	public $form_data = array();
	public $step = 0;

	/**
	 * Main Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Construct
	 */
	public function __construct() {

		/* No listing, bail. */
		if ( ! isset( $_GET['listing_id'] ) ) {
			wp_redirect( esc_url_raw( home_url() ) );
			die();
		} // End if().

		else {
			$listing_id = intval( $_GET['listing_id'] );
			if ( Listing::is_claimable( $listing_id ) ) {
				$this->listing_obj = get_post( $listing_id );
				$this->listing_id = $listing_id;
			} else {
				wp_redirect( esc_url_raw( home_url() ) );
				die();
			}
		}

		/* Form Datas */
		$form_data = stripslashes_deep( $_POST );
		$form_datas_default = array(
			'wpjmcl_claim_data'       => '',
			'create_account_username' => '',
			'create_account_email'    => '',
			'listing_id'              => $this->listing_id,
		);
		$this->form_data = wp_parse_args( $form_data, $form_datas_default );

		/* Form Name */
		$this->form_name = 'wpjmcl_submit_claim';

		/* Steps */
		$steps  = array(
			'login_register' => array(
				'name'     => __( 'Login / Register', 'wp-job-manager-claim-listing' ),
				'view'     => array( $this, 'login_register_view' ),
				'handler'  => array( $this, 'login_register_handler' ),
				'priority' => 1,
				'submit'   => __( 'Register Account &rarr;', 'wp-job-manager-claim-listing' ),
			),
			'claim_listing' => array(
				'name'     => __( 'Claim Listing', 'wp-job-manager-claim-listing' ),
				'view'     => array( $this, 'claim_listing_view' ),
				'handler'  => array( $this, 'claim_listing_handler' ),
				'priority' => 3,
				'submit'   => __( 'Submit Claim &rarr;', 'wp-job-manager-claim-listing' ),
			),
			'claim_detail' => array(
				'name'     => __( 'Claim Detail', 'wp-job-manager-claim-listing' ),
				'view'     => array( $this, 'claim_detail_view' ),
				// 'handler'  => '__return_false',
				'priority' => 5,
				// 'submit'   => '',
			),
		);

		$this->steps = apply_filters( 'wpjmcl_submit_claim_form_steps', $steps );

		uasort( $this->steps, array( $this, 'sort_by_priority' ) );

		/* Get step. */
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}
	}


	/*
	 Login Register Callback
	------------------------------------------ */

	/**
	 * Login Register View
	 *
	 * @since 3.0.0
	 */
	public function login_register_view() {
		$get_step = $this->get_step();
?>

<form id="<?php echo esc_attr( $this->get_form_name() ); ?>" class="job-manager-form wpjmcl_form wpjmcl_form_login_register" method="post">

	<?php do_action( 'wpjmcl_submit_claim_form_login_register_view_open' ); ?>

	<?php $this->signin_field(); // display login/register form ?>
	<?php $this->listing_field(); // display listing info ?>

	<?php do_action( 'wpjmcl_submit_claim_form_login_register_view_close' ); ?>

	<p>
		<input type="submit" value="<?php echo esc_attr( $this->get_step_submit() ); ?>" class="button" name="submit">

		<input type="hidden" value="<?php echo intval( $get_step ); ?>" name="step">
		<?php wp_nonce_field( $action = __FILE__, $name = '_wpjmcl_register_nonce' ) ?>
	</p>

</form><!-- .wpjmcl_form -->

<?php
	}


	/**
	 * Login Register Handler
	 *
	 * @since 3.0.0
	 */
	public function login_register_handler() {
		global $current_user;

		do_action( 'wpjmcl_submit_claim_form_login_register_handler_before' );

		// User already logged in, go to next step.
		if ( is_user_logged_in() ) {

			$this->next_step();

			// Claim not set.
			if ( ! $this->claim_id ) {

				// Check if user already claim this listing.
				$claims = get_posts( array(
					'post_type'      => 'claim',
					'posts_per_page' => 1, // only one.
					'author'         => get_current_user_id(),
					'meta_key'       => '_listing_id',
					'meta_value'     => $this->listing_id,
				) );

				// Match found. Set claim_id.
				if ( $claims && isset( $claims[0]->ID ) ) {
					$this->claim_id = $claims[0]->ID;
				}
			}

			// User already claim this, go to next step.
			if ( $this->claim_id ) {
				$this->next_step();
			}

		} else { // User not logged-in. Register.

			// Current Claim URL:
			$claim_url = add_query_arg( array(
				'listing_id' => $this->listing_id,
			), get_permalink( get_queried_object_id() ) );

			// Register Form Submitted.
			if ( isset( $this->form_data['submit'] ) && isset( $this->form_data['_wpjmcl_register_nonce'] ) ) {

				// Register args.
				$args = array(
					'username' => '',
					'email'    => '',
					'password' => false,
					'role'     => get_option( 'job_manager_enable_registration', '1' ) ? get_option( 'job_manager_registration_role', 'employer' ) : get_option( 'default_role' ),
				);

				// Verify Nonce.
				if ( ! wp_verify_nonce( $this->form_data['_wpjmcl_register_nonce'], __FILE__ ) ) {
					$this->add_error( __( 'Security nonce not valid.', 'wp-job-manager-claim-listing' ) );
					return;
				}

				// Email field: always required.
				if ( ! isset( $_POST['create_account_email'] ) ) {
					$this->add_error( __( 'Email field is required.', 'wp-job-manager-claim-listing' ) );
					return;
				} else {
					$args['email'] = sanitize_email( $_POST['create_account_email'] );
				}

				// Username field: if not set, it's using email.
				if ( isset( $_POST['create_account_username'] ) ) {
					$args['username'] = sanitize_user( $_POST['create_account_username'] );
				}

				// Password field: if not set, it's disabled.
				if ( isset( $_POST['create_account_password'], $_POST['create_account_password_verify'] ) ) {

					// Password.
					$pass1 = $_POST['create_account_password'];
					$pass2 = $_POST['create_account_password_verify'];
					$password_hint = wpjm_get_password_rules_hint();

					// Validate Password.
					if ( ! wpjm_validate_new_password( $pass1 ) ) {
						$this->add_error( sprintf( __( 'Invalid Password: %s', 'wp-job-manager-claim-listing' ), $password_hint ) );
						return;
					}

					// Check verify password field.
					if ( $pass1 !== $pass2 ) {
						$this->add_error( __( 'Passwords must match.', 'wp-job-manager-claim-listing' ) );
						return;
					}

					// Set Password:
					$args['password'] = $pass1;
				}

				// Register User.
				$create_account = wp_job_manager_create_account( $args );

				/* Error in registering user */
				if ( is_wp_error( $create_account ) ) {
					$this->add_error( $create_account->get_error_message() );
				} else {
					wp_redirect( esc_url_raw( $claim_url ) );
					die();
				}

			} else { // Initial View.
				$this->add_error( sprintf( __( 'You must <a href="%s">sign in</a> to claim a listing.', 'wp-job-manager-claim-listing' ), esc_url( wp_login_url( $claim_url ) ) ) );
			}
		}

		do_action( 'wpjmcl_submit_claim_form_login_register_handler_after', $this->claim_id );
	}


	/*
	 Claim Listing Callback
	------------------------------------------ */

	/**
	 * Claim Listing View
	 *
	 * @since 3.0.0
	 */
	public function claim_listing_view() {
		$get_step = $this->get_step();
		?>

		<?php do_action( 'wpjmcl_submit_claim_form_claim_listing_view_before' ); ?>

		<form id="<?php echo esc_attr( $this->get_form_name() ); ?>" class="job-manager-form wpjmcl_form wpjmcl_form_claim_listing" method="post">

			<?php do_action( 'wpjmcl_submit_claim_form_claim_listing_view_open' ); ?>

			<?php $this->listing_field(); // display listing info ?>
			<?php $this->claim_data_field(); // display wp_editor claim data if enabled ?>

			<?php do_action( 'wpjmcl_submit_claim_form_claim_listing_view_close' ); ?>

			<p>
				<input type="submit" value="<?php echo esc_attr( $this->get_step_submit() ); ?>" class="button" name="submit">

				<input type="hidden" value="<?php echo intval( $get_step ); ?>" name="step">
				<?php wp_nonce_field( $action = __FILE__, $name = '_wpjmcl_submit_claim_nonce' ) ?>
			</p>

		</form><!-- .wpjmcl_form -->

		<?php do_action( 'wpjmcl_submit_claim_form_claim_listing_view_after' ); ?>

		<?php
	}

	/**
	 * Claim Listing Handler
	 * This function only loaded when use submit the form.
	 *
	 * @since 3.0.0
	 */
	public function claim_listing_handler() {

		do_action( 'wpjmcl_submit_claim_form_claim_listing_handler_before' );

		/* User Logged In, form submitted. */
		if ( isset( $_POST['submit'] ) && isset( $_POST['_wpjmcl_submit_claim_nonce'] ) && wp_verify_nonce( $_POST['_wpjmcl_submit_claim_nonce'], __FILE__ ) ) {

			/* Claim Data ? */
			$claim_data = '';
			$submit_claim_data = get_option( 'wpjmcl_submit_claim_data' );
			if ( $submit_claim_data && isset( $this->form_data['wpjmcl_claim_data'] ) ) {
				$claim_data = $this->form_data['wpjmcl_claim_data'];
			}

			/**
			 * Create Claim Entry
			 */
			$this->claim_id = Claim::create_new_claim( $this->listing_id, get_current_user_id(), $claim_data );

			/* Go to next step. */
			$this->next_step();
		}

		/* After hook. */
		do_action( 'wpjmcl_submit_claim_form_claim_listing_handler_after', $this->claim_id );
	}


	/*
	 Claim detail.
	------------------------------------------ */

	/**
	 * Claim Detail.
	 *
	 * @since 3.0.0
	 */
	public function claim_detail_view() {

		/* Claim */
		$claim_id = $this->claim_id;
		$claim_obj = get_post( $claim_id );
		$claim_status = Claim::get_claim_status_label( $claim_id );
		$claim_data = get_post_meta( $claim_id, '_claim_data', true );

		/* Listing */
		$listing_id = $this->listing_id;
		$listing_link = '<a href="' . esc_url( get_permalink( $listing_id ) ) . '">' . get_the_title( $listing_id ) . '</a>';

		/* Claimer */
		$claimer = get_userdata( $claim_obj->post_author );
		?>
		<div class="job-manager-message">
			<?php printf( __( 'Your claim status for %1$s is %2$s.', 'wp-job-manager-claim-listing' ), $listing_link, '<strong>' . $claim_status . '</strong>' );?>
		</div>

		<?php do_action( 'wpjmcl_submit_claim_form_claim_detail_view_before', $claim_id ); ?>

		<form id="<?php echo esc_attr( $this->get_form_name() ); ?>" class="job-manager-form wpjmcl_form wpjmcl_form_claim_detail" method="post">
			<?php $this->listing_field(); // display listing info ?>

			<?php do_action( 'wpjmcl_submit_claim_form_claim_detail_view_open', $claim_id ); ?>

			<fieldset>
				<label><?php _e( 'Claimed by', 'wp-job-manager-claim-listing' ); ?></label>
				<div class="field">
					<?php echo $claimer->data->display_name; ?>
				</div>
			</fieldset>

			<fieldset>
				<label><?php _e( 'Submitted on', 'wp-job-manager-claim-listing' ); ?></label>
				<div class="field">
					<?php echo get_the_date( get_option( 'date_format' ), $claim_id ); ?>
				</div>
			</fieldset>

			<?php if ( $claim_data ) { ?>
			<fieldset>
				<label><?php _e( 'Claim data', 'wp-job-manager-claim-listing' ); ?></label>
				<div class="field">
					<?php echo wp_kses_post( $claim_data ); ?>
				</div>
			</fieldset>
			<?php } ?>

			<?php do_action( 'wpjmcl_submit_claim_form_claim_detail_view_close', $claim_id ); ?>

		</form><!-- .wpjmcl_form -->

		<?php do_action( 'wpjmcl_submit_claim_form_claim_detail_view_after', $claim_id ); ?>

		<?php
	}


	/*
	 Utility
	------------------------------------------ */

	/**
	 * Get Step Title/Name
	 */
	public function get_step_title() {
		$step_key = $this->get_step_key( $this->step );
		return isset( $this->steps[ $step_key ]['name'] ) ? $this->steps[ $step_key ]['name'] : '';
	}

	/**
	 * Get Step Submit Text
	 */
	public function get_step_submit() {
		$step_key = $this->get_step_key( $this->step );
		return isset( $this->steps[ $step_key ]['submit'] ) ? $this->steps[ $step_key ]['submit'] : '';
	}

	/**
	 * Listing Field
	 */
	public function listing_field() {
		?>
		<fieldset class="fieldset-wpjmcl_listing">
			<label><?php _e( 'Listing to claim', 'wp-job-manager-claim-listing' ); ?></label>
			<div class="field">
				<a href="<?php echo esc_url( get_permalink( $this->listing_id ) ); ?>"><strong><?php echo get_the_title( $this->listing_id ); ?></strong></a>
				<input type="hidden" value="<?php echo intval( $this->listing_id ); ?>" name="listing_id">
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Signin Field
	 */
	public function signin_field() {
		$listing_id = $this->listing_id;

		/* Logout URL: Redirect to Listing ID */
		add_filter( 'submit_job_form_logout_url', function( $url ) use ( $listing_id ) {
			return esc_url( wp_logout_url( get_permalink( $listing_id ) ) );
		} );

		/* Login URL: Redirect to claim listing page */
		add_filter( 'submit_job_form_login_url', function( $url ) use ( $listing_id ) {
			$redirect = add_query_arg( 'listing_id', $listing_id, get_permalink() );
			return esc_url( wp_login_url( $redirect ) );
		} );

		/* Login Required Message */
		add_filter( 'submit_job_form_login_required_message', function( $url ) {
			return '';
		} );

		/* Load field template */
		get_job_manager_template( 'account-signin.php' );
	}

	/**
	 * Claim Data Field
	 */
	function claim_data_field() {
		$submit_claim_data = get_option( 'wpjmcl_submit_claim_data' );
		if ( ! $submit_claim_data ) { return false;
		}
		$claim_data = wp_kses_post( $this->form_data['wpjmcl_claim_data'] );
		?>
		<fieldset class="fieldset-wpjmcl_claim_data">
			<label><?php _e( 'Additional Information', 'wp-job-manager-claim-listing' ); ?></label>
			<div class="field">
				<?php
				$name  = 'wpjmcl_claim_data';
				$editor = apply_filters( 'wpjmcl_submit_claim_form_wp_editor_args', array(
					'textarea_name' => $name,
					'media_buttons' => false,
					'textarea_rows' => 8,
					'quicktags'     => false,
					'tinymce'       => array(
						'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
						'paste_as_text'                 => true,
						'paste_auto_cleanup_on_paste'   => true,
						'paste_remove_spans'            => true,
						'paste_remove_styles'           => true,
						'paste_remove_styles_if_webkit' => true,
						'paste_strip_class_attributes'  => true,
						'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
						'toolbar2'                      => '',
						'toolbar3'                      => '',
						'toolbar4'                      => '',
					),
				) );
				wp_editor( $claim_data, $name, $editor );
				?>
				<small class="description"><?php _e( 'Please explain in detail, so we can verify your claim.', 'wp-job-manager-claim-listing' ); ?></small>
			</div>
		</fieldset>
		<?php
	}

}
