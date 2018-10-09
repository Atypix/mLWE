<?php

if (!class_exists('Knowhere_Featured_Galleries')) {

	class Knowhere_Featured_Galleries {

		public function path($name, $file = '') {
			return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
		}

		public function assetUrl($file)  {
			return $this->paths['BASE_URI'] . $this->path('ASSETS_DIR_NAME', $file);
		}

		function __construct() {

			$this->paths = array(
				'ASSETS_DIR_NAME' => 'assets',
				'BASE_URI' => get_theme_file_uri('includes/plugins/featured-galleries/')
			);

			add_action( 'add_meta_boxes', array($this, 'register_metabox') );
			add_action( 'save_post', array($this, 'save_perm_metadata'), 1, 2 );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles') );
			add_action( 'wp_ajax_mad_update_temp', array($this, 'update_temp_action') );

		}

		public function register_metabox() {

			$post_types	= apply_filters( 'knowhere_post_types', array( 'job_listing' ) );
			$context	= apply_filters( 'knowhere_context', 'side' );
			$priority	= apply_filters( 'knowhere_priority', 'default' );

			foreach ( $post_types as $post_type ) {
				add_meta_box( 'mad_featured_gallery', esc_html__( 'Gallery Images', 'knowherepro' ), array($this, 'display_metabox'), $post_type, $context, $priority );
			}

		}

		public function enqueue_scripts_and_styles() {
			wp_enqueue_media();

			$css_file = $this->assetUrl('css/admin.css');
			$js_file = $this->assetUrl('js/admin.js');

			wp_enqueue_style( 'knowhere-admin-style', $css_file );
			wp_enqueue_script( 'knowhere-admin-script', $js_file, array('jquery') );
		}

		public function display_metabox() {

			global $post;

			// Get the Information data if its already been entered
			$galleryHTML = '';;

			$selectText    = esc_html__( 'Select Images', 'knowherepro' );
			$visible       = '';
			$galleryArray  = self::get_post_gallery_ids( $post->ID );
			$galleryString = self::get_post_gallery_ids( $post->ID, 'string' );
			if ( ! empty( $galleryString ) ) {
				foreach ( $galleryArray as &$id ) {
					$galleryHTML .= '<li><button class="remove-featured-image"></button><img id="'. esc_attr($id) .'" src="'. wp_get_attachment_url( $id ) .'"></li> ';
				}
				$selectText = esc_html__( 'Edit Selection', 'knowherepro' );
				$visible    = " visible";
			}
			update_post_meta( $post->ID, 'mad_temp_metadata', $galleryString ); ?>

			<input type="hidden" name="mad_temp_noncedata" id="mad_temp_noncedata" value="<?php echo wp_create_nonce( 'mad_temp_noncevalue' ); ?>" />
			<input type="hidden" name="mad_perm_noncedata" id="mad_perm_noncedata" value="<?php echo wp_create_nonce( get_theme_file_path('includes/plugins/featured-galleries') ); ?>" />
			<input type="hidden" name="mad_perm_metadata" id="mad_perm_metadata" value="<?php echo esc_attr($galleryString); ?>" data-post_id="<?php echo absint($post->ID); ?>" />
			<button class="button" id="mad_add_featured_images"><?php echo esc_html($selectText); ?></button>
			<button class="button <?php echo sanitize_html_class($visible); ?>" id="mad_remove_all"><?php esc_html_e( 'Remove All', 'knowherepro' ); ?></button>

			<ul class="mad-featured-galleries"><?php echo $galleryHTML; ?></ul>

			<div style="clear:both;"></div><?php
		}

		public function save_perm_metadata( $post_id, $post ) {

			//Only run the call when updating a Featured Gallery.
			if ( empty( $_POST['mad_perm_noncedata'] ) ) {
				return;
			}
			// Noncename
			if ( ! wp_verify_nonce( $_POST['mad_perm_noncedata'], get_theme_file_path('includes/plugins/featured-galleries') ) ) {
				return;
			}
			// Verification of User
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return;
			}
			// OK, we're authenticated: we need to find and save the data
			$events_meta['mad_perm_metadata'] = $_POST['mad_perm_metadata'];
			// Add values of $events_meta as custom fields
			foreach ( $events_meta as $key => $value ) {
				if ( $post->post_type == 'revision' ) {
					return;
				}
				$value = implode( ',', (array)$value );
				if ( get_post_meta( $post->ID, $key, FALSE ) ) {
					update_post_meta( $post->ID, $key, $value );
				} else {
					add_post_meta( $post->ID, $key, $value );
				}
				if ( ! $value ) {
					delete_post_meta( $post->ID, $key );
				}
			}

		}

		public function update_temp_action() {

			if ( ! wp_verify_nonce( $_REQUEST['mad_temp_noncedata'], "mad_temp_noncevalue" ) ) {
				exit( "You shouldn't have gotten here, something is going wrong." );
			}
			if ( ! current_user_can( 'edit_post', $_REQUEST['mad_post_id'] ) ) {
				exit( "You don't appear to be logged in, something is going wrong here." );
			}

			$newValue = $_REQUEST['mad_temp_metadata'];
			$oldValue = get_post_meta( $_REQUEST['mad_post_id'], 'mad_temp_metadata', 1 );
			$response = "success";

			if ( $newValue != $oldValue ) {

				$success = update_post_meta( $_REQUEST['mad_post_id'], 'mad_temp_metadata', $newValue );

				if ( $success == false ) {
					$response = "error";
				}

			}

			echo json_encode( $response );
			die();
		}

		public static function get_post_gallery_ids($id, $max_images = -1, $method = "array") {

			if ( is_preview($id) ) {
				$galleryString = get_post_meta( $id, 'mad_temp_metadata', 1);
			} else {
				$galleryString = get_post_meta( $id, 'mad_perm_metadata', 1);
			}

			if ($method == "string" || $max_images == "string") {
				return $galleryString;
			} else {

				if ($max_images == -1) {
					return array_filter(explode(',', $galleryString));
				} else {
					return array_slice(explode(',', $galleryString), 0, $max_images);
				}

			}

		}

	}

	new Knowhere_Featured_Galleries();
}
