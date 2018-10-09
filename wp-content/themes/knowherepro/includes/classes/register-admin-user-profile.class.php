<?php
/**
 * User profile social links for wp-admin
 *
 */
if ( !class_exists('knowhere_admin_user_profile') ) {
	class knowhere_admin_user_profile {

		public function __construct() {

			if ( is_admin() ) {

//				add_action('show_user_profile', array($this, 'add_meta_fields'), 20);
//				add_action('edit_user_profile', array($this, 'add_meta_fields'), 20);
//
//				add_action('personal_options_update', array($this, 'save_meta_fields'));
//				add_action('edit_user_profile_update', array($this, 'save_meta_fields'));

			}


		}

		function get_store_info($seller_id)
		{
			$info = get_user_meta($seller_id, 'knowhere_profile_settings', true);
			$info = is_array($info) ? $info : array();
			$info = wp_parse_args($info, array('social' => array()));
			return $info;
		}

		function get_social_profile_fields() {
			$fields = array(
				'facebook' => array(
					'icon' => 'fa-facebook',
					'title' => esc_html__('Facebook', 'knowherepro'),
				),
				'twitter' => array(
					'icon' => 'fa-twitter',
					'title' => esc_html__('Twitter', 'knowherepro'),
				),
				'google' => array(
					'icon' => 'fa-google-plus',
					'title' => esc_html__('Google Plus', 'knowherepro'),
				),
				'instagram' => array(
					'icon' => 'fa-instagram',
					'title' => esc_html__('Instagram', 'knowherepro'),
				),
				'linkedin' => array(
					'icon' => 'fa-linkedin',
					'title' => esc_html__('LinkedIn', 'knowherepro'),
				),
				'email' => array(
					'icon' => 'fa-envelope',
					'title' => esc_html__('Email', 'knowherepro'),
				)

			);

			return apply_filters('knowhere_profile_social_fields', $fields);

		}

		/**
		 * Add fields to user profile
		 *
		 * @param WP_User $user
		 *
		 * @return void|false
		 */
		function add_meta_fields($user)
		{
			$store_settings = $this->get_store_info($user->ID);
			$social_fields = $this->get_social_profile_fields();
			?>
			<h3><?php esc_html_e('Social Options', 'knowherepro'); ?></h3>

			<table class="form-table">
				<tbody>

				<?php foreach ( $social_fields as $key => $value ) : ?>

					<tr>
						<th><?php echo esc_html($value['title']); ?></th>
						<td>
							<input type="text" name="knowhere_admin_social[<?php echo esc_attr($key); ?>]"
								   class="regular-text"
								   value="<?php echo isset($store_settings['social'][$key]) ? esc_url($store_settings['social'][$key]) : ''; ?>">
						</td>
					</tr>

				<?php endforeach; ?>

				<?php do_action('knowhere_seller_meta_fields', $user); ?>

				</tbody>
			</table>
			<?php
		}

		public function output_social_links() {
			$social_fields = $this->get_social_profile_fields();
			$profile_info = $this->get_store_info(get_current_user_id());
			?>
			<ul class="kw-social-links">

				<?php foreach ($social_fields as $key => $field) : ?>
					<?php if ( isset($profile_info['social'][$key]) && !empty($profile_info['social'][$key]) ) : ?>
						<li>
							<a target="_blank"
							   href="<?php echo isset($profile_info['social'][$key]) ? esc_url($profile_info['social'][$key]) : '' ?>">
								<i class="fa <?php echo isset($field['icon']) ? $field['icon'] : ''; ?>"></i>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>

			</ul>
			<?php
		}

		/**
		 * Save user data
		 *
		 * @param int $user_id
		 *
		 * @return void
		 */
		function save_meta_fields($user_id)
		{
			$store_settings = $this->get_store_info($user_id);

			$social = $_POST['knowhere_admin_social'];
			$social_fields = $this->get_social_profile_fields();

			// social settings
			if (is_array($social)) {
				foreach ($social as $key => $value) {
					if (isset($social_fields[$key])) {
						$store_settings['social'][$key] = filter_var($social[$key], FILTER_VALIDATE_URL);
					}
				}
			}

			update_user_meta($user_id, 'knowhere_profile_settings', $store_settings);
		}
	}
}

class knowhere_add_fields_editor {

	private $name = 'Visual Biography Editor';

	/**
	 * Setup WP hooks
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ) );

		// Don't sanitize the data for display in a textarea
		add_action( 'admin_init', array($this, 'save_filters') );

		// Add content filters to the output of the description
		add_filter( 'get_the_author_description', 'wptexturize' );
		add_filter( 'get_the_author_description', 'convert_chars' );
		add_filter( 'get_the_author_description', 'wpautop' );

		add_action( 'personal_options_update', array( $this, 'save_img_meta' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_img_meta' ) );

		add_filter( 'get_avatar', array( $this, 'get_avatar' ), 1, 5 );
	}

	function get_avatar( $avatar, $identifier, $size, $alt ) {

		if ( $user = $this->get_user_by_id_or_email( $identifier ) ) {
			if ( $custom_avatar = $this->get_meta( $user->ID, 'thumbnail' ) ) {
				return "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			}
		}

		return $avatar;

	}

	function get_user_by_id_or_email( $identifier ) {
		// If an integer is passed.
		if ( is_numeric( $identifier ) ) {
			return get_user_by( 'id', (int) $identifier );
		}

		// If the WP_User object is passed.
		if ( is_object( $identifier ) && property_exists( $identifier, 'ID' ) ) {
			return get_user_by( 'id', (int) $identifier->ID );
		}

		// If the WP_Comment object is passed.
		if ( is_object( $identifier ) && property_exists( $identifier, 'user_id' ) ) {
			return get_user_by( 'id', (int) $identifier->user_id );
		}

		return get_user_by( 'email', $identifier );
	}

	function get_meta( $user_id, $size = 'thumbnail' ) {
		global $post;

		if ( ! $user_id || ! is_numeric( $user_id ) ) {
			$user_id = $post->post_author;
		}

		// Check first for a custom uploaded image.
		$attachment_upload_url = esc_url( get_the_author_meta( 'knowhere_cupp_upload_meta', $user_id ) );

		if ( $attachment_upload_url ) {
			// Grabs the id from the URL using the WordPress function attachment_url_to_postid @since 4.0.0.
//			$attachment_id = attachment_url_to_postid( $attachment_upload_url );

			// Retrieve the thumbnail size of our image. Should return an array with first index value containing the URL.
//			$image_thumb = wp_get_attachment_image_src( $attachment_id, $size );

			return $attachment_upload_url;
		}

		// Finally, check for image from an external URL. If none exists, return an empty string.
		$attachment_ext_url = esc_url( get_the_author_meta( 'knowhere_cupp_meta', $user_id ) );

		return $attachment_ext_url ? $attachment_ext_url : '';
	}

	function save_img_meta( $user_id ) {
		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return;
		}

		$values = array(
			// String value. Empty in this case.
			'knowhere_cupp_meta'             => filter_input( INPUT_POST, 'knowhere_cupp_meta', FILTER_SANITIZE_STRING ),

			// File path, e.g., http://3five.dev/wp-content/plugins/custom-user-profile-photo/img/placeholder.gif.
			'knowhere_cupp_upload_meta'      => filter_input( INPUT_POST, 'knowhere_cupp_upload_meta', FILTER_SANITIZE_URL ),

			// Edit path, e.g., /wp-admin/post.php?post=32&action=edit&image-editor.
			'knowhere_cupp_upload_edit_meta' => filter_input( INPUT_POST, 'knowhere_cupp_upload_edit_meta', FILTER_SANITIZE_URL ),
		);

		foreach ( $values as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}
	}

	public function user_profile($user) {

//		if ( function_exists('wp_editor') ) {
//			$this->visual_editor($user);
//		} else {
//			add_action( 'admin_notices', array( $this, 'update_notice' ) );
//		}

		$this->profile_img_fields($user);
	}

	/**
	 * Friendly notice if WP is out of date
	 */
	public function update_notice() {

		// Notification is for administrators only
		if ( !current_user_can('administrator') )
			return;

		?>
		<div class="updated">
			<p>The <strong><?php echo esc_html( $this->name ); ?></strong> plugin requires WordPress 3.3 or higher. Update WordPress or <a href="<?php echo get_admin_url(null, 'plugins.php'); ?>">de-activate the plugin</a>.</p>
		</div>
		<?php
	}

	/**
	 *	Create Visual Editor
	 *
	 *	Add TinyMCE editor to replace the "Biographical Info" field in a user profile
	 *
	 * @uses wp_editor() http://codex.wordpress.org/Function_Reference/wp_editor
	 * @param $user An object with details about the current logged in user
	 */
	public function visual_editor( $user ) {

		// Contributor level user or higher required
		if ( !current_user_can('edit_posts') )
			return;
		?>
		<table class="form-table">
			<tr>
				<th><label for="description"><?php esc_html_e('Biographical Info', 'knowherepro'); ?></label></th>
				<td>
					<?php
					$description = get_user_meta( $user->ID, 'description', true);
					wp_editor( $description, 'description' );
					?>
					<p class="description"><?php esc_html_e('Share a little biographical information to fill out your profile. This may be shown publicly.', 'knowherepro'); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	function profile_img_fields( $user ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$url             = get_the_author_meta( 'knowhere_cupp_meta', $user->ID );
		$upload_url      = get_the_author_meta( 'knowhere_cupp_upload_meta', $user->ID );
		$upload_edit_url = get_the_author_meta( 'knowhere_cupp_upload_edit_meta', $user->ID );
		$button_text     = $upload_url ? esc_html__('Change Current Image', 'knowherepro') : esc_html__('Upload New Image', 'knowherepro');
		if ( $upload_url ) {
			$upload_edit_url = get_site_url() . $upload_edit_url;
		}
		?>

		<div id="knowhere_cupp_container">

			<h3><?php esc_html_e( 'Custom User Profile Photo', 'knowherepro' ); ?></h3>

			<table class="form-table">
				<tr>
					<th><label for="knowhere_cupp_meta"><?php esc_html_e( 'Profile Photo', 'knowherepro' ); ?></label></th>
					<td>
						<!-- Outputs the image after save -->
						<div id="knowhere_current_img">
							<?php if ( $upload_url ): ?>
								<img class="knowhere-cupp-current-img" src="<?php echo esc_url( $upload_url ); ?>" alt=""/>

								<div class="knowhere_edit_options uploaded">
									<a class="knowhere_remove_img">
										<span><?php esc_html_e( 'Remove', 'knowherepro' ); ?></span>
									</a>

									<a class="knowhere_edit_img" href="<?php echo esc_url( $upload_edit_url ); ?>" target="_blank">
										<span><?php esc_html_e( 'Edit', 'knowherepro' ); ?></span>
									</a>
								</div>
							<?php elseif ( $url ) : ?>
								<img class="knowhere-cupp-current-img" src="<?php echo esc_url( $url ); ?>" alt=""/>
								<div class="knowhere_edit_options single">
									<a class="knowhere_remove_img">
										<span><?php esc_html_e( 'Remove', 'knowherepro' ); ?></span>
									</a>
								</div>
							<?php else : ?>
								<img class="knowhere-cupp-current-img placeholder"
									 src="<?php echo esc_url( get_theme_file_uri( 'images/placeholder.gif' ) ); ?>"/>
							<?php endif; ?>
						</div>

						<!-- Select an option: Upload to WPMU or External URL -->
						<div id="knowhere_cupp_options">
							<input type="radio" id="knowhere_upload_option" name="knowhere_img_option" value="upload" class="tog" checked>
							<label for="knowhere_upload_option"><?php esc_html_e( 'Upload New Image', 'knowherepro' ); ?></label><br>
							<input type="radio" id="knowhere_external_option" name="knowhere_img_option" value="external" class="tog">
							<label for="knowhere_external_option"><?php esc_html_e( 'Use External URL', 'knowherepro' ); ?></label><br>
						</div>

						<!-- Hold the value here if this is a WPMU image -->
						<div id="knowhere_cupp_upload">
							<input class="hidden" type="hidden" name="knowhere_cupp_placeholder_meta" id="knowhere_cupp_placeholder_meta"
								   value="<?php echo esc_url( get_theme_file_uri( 'images/placeholder.gif' ) ); ?>"/>
							<input class="hidden" type="hidden" name="knowhere_cupp_upload_meta" id="knowhere_cupp_upload_meta"
								   value="<?php echo esc_url_raw( $upload_url ); ?>"/>
							<input class="hidden" type="hidden" name="knowhere_cupp_upload_edit_meta" id="knowhere_cupp_upload_edit_meta"
								   value="<?php echo esc_url_raw( $upload_edit_url ); ?>"/>
							<input id="uploadimage" type='button' class="knowhere_cupp_wpmu_button button-primary"
								   value="<?php echo sprintf(__('%s', 'knowherepro'), $button_text) ?>"/>
							<br/>
						</div>

						<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
						<div id="knowhere_cupp_external">
							<input class="regular-text" type="text" name="knowhere_cupp_meta" id="knowhere_cupp_meta" value="<?php echo esc_url_raw( $url ); ?>"/>
						</div>

					<span class="description">
						<?php
						esc_html_e( 'Upload a custom photo for your user profile or use a URL to a pre-existing photo.', 'knowherepro' );
						?>
					</span>
						<p class="description"><?php esc_html_e( 'Update Profile to save your changes.', 'knowherepro' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<?php
		// Enqueue the WordPress Media Uploader.
		wp_enqueue_media();
	}

	/**
	 * Remove textarea filters from description field
	 */
	public function save_filters() {
		if ( !current_user_can('edit_posts') ) return;
		remove_all_filters('pre_user_description');
	}
}


new knowhere_add_fields_editor();

