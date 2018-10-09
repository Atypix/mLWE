<?php
/**
 * Files (Reply Attachments)
 * Handle attachments creation.
 *
 * @since 1.3.0
 */
class Private_Messages_Files {

	/**
	 * Base Dir
	 *
	 * @since 1.3.0
	 */
	public static function base_dir() {
		global $wp_filesystem; // Load WordPress FileSystem.

		if ( ! isset( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$wp_upload_dir = wp_upload_dir();
		$base_dir      = 'private-messages-files';
		$pm_base_dir   = "{$wp_upload_dir['basedir']}/{$base_dir}";
		$pm_base_dir   = trailingslashit( $wp_filesystem->find_folder( $pm_base_dir ) );
		return $pm_base_dir;
	}


	/**
	 * Prepare Uploads
	 *
	 * @since 1.3.0
	 */
	public static function prepare() {
		global $wp_filesystem; // Load WordPress FileSystem.

		if ( ! isset( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// Create Dir If Not Exists.
		$pm_base_dir = self::base_dir();
		if ( ! file_exists( $pm_base_dir ) ) {
			wp_mkdir_p( $pm_base_dir );
		}

		// Protect Dir with .htaccess file.
		if ( ! file_exists( $pm_base_dir . '.htaccess' ) ) {
			$ht  = 'Options -Indexes' . "\n"; // Prevent indexing.
			$ht .= '<Files *.*>' . "\n"; // Force download.
			$ht .= 'ForceType application/octet-stream' . "\n";
			$ht .= 'Header set Content-Disposition attachment' . "\n";
			$ht .= '</Files>';
			$wp_filesystem->put_contents( $pm_base_dir . '.htaccess', $ht, FS_CHMOD_FILE );
		}
	}


	/**
	 * Create File
	 *
	 * @since 1.3.0
	 * @link http://php.net/manual/en/features.file-upload.php
	 *
	 * @param array $uploads    File Upload data.
	 * @param int   $post_id    Post ID.
	 * @param int   $comment_id Post ID.
	 */
	public static function create_files( $uploads, $post_id, $comment_id ) {
		self::prepare(); // Prepare dir.
		$err_msg = array();

		// Check if data is valid.
		if ( ! isset( $uploads['error'] ) ) {
			throw new Exception( __( 'Invalid file upload parameters. Please contact the website administrator if this issue continues.', 'private-messages' ) );
			return;
		}

		// Format files data structure.
		$files_data = array();
		if ( isset( $uploads['name'] ) && is_array( $uploads['name'] ) ) {
			$file_count = count( $uploads['name'] );
			for ( $n = 0; $n < $file_count; $n++ ) {
				if ( $uploads['name'][ $n ] && $uploads['type'][ $n ] && $uploads['tmp_name'][ $n ] ) {

					if ( $uploads['error'][ $n ] ) { // Check For error.
						$err_msg[] = sprintf( __( 'Upload Error. Fail to upload %s.', 'private-messages' ), $uploads['name'][ $n ] );
					} else { // No Error.
						$type = wp_check_filetype( $uploads['name'][ $n ] );
						$files_data[] = array(
							'name'     => $uploads['name'][ $n ],
							'type'     => $type['type'],
							'tmp_name' => $uploads['tmp_name'][ $n ],
							'error'    => $uploads['error'][ $n ],
							'size'     => filesize( $uploads['tmp_name'][ $n ] ), // in byte
						);
					}
				}
			}
		}

		// Loop each files.
		if ( $files_data ) {
			$files = array();
			foreach ( $files_data as $file_data ) {

				// Max File Size.
				$max_size = intval( pm_get_option( 'pm_attachments_max_file_size' , wp_max_upload_size() ) );

				// Check file ext.
				if ( ! self::validate_file_name( $file_data['name'] ) ) {
					$err_msg[] = sprintf( __( 'Upload Error. "%s" file extension is not allowed.', 'private-messages' ), $file_data['name'] );
				} elseif ( $max_size && ( $file_data['size'] > $max_size ) ) { // Check file size.
					$err_msg[] = sprintf( __( 'Upload Error. "%1$s" file too large. Maximum file size is %2$s', 'private-messages' ), $file_data['name'], size_format( $max_size ) );
				} else { // Start Uploads.
					$upload = self::upload( $file_data, $post_id, $comment_id );
					if ( isset( $upload['error'] ) && $upload['error'] ) {
						$err_msg[] = $upload['error'];
					} else {
						$files[] = $upload;
					}
				}
			}
			// Store files data in comment meta.
			if ( $files ) {
				update_comment_meta( $comment_id, 'pm_attachments', $files );
			} else {
				$err_msg[] = __( 'Upload Error. No file uploaded.', 'private-messages' );
			}
		}
		if ( $err_msg && ! is_admin() ) {
			throw new Exception( implode( '; ', $err_msg ) );
		}
	}

	/**
	 * Upload file to custom folder.
	 *
	 * @since 1.3.0
	 *
	 * @param array $file_data  File upload data.
	 * @param int   $post_id    Post ID.
	 * @param int   $comment_id Comment ID.
	 * @return array of file data or error message.
	 */
	public static function upload( $file_data, $post_id, $comment_id ) {
		global $pm_dir_post_id, $pm_dir_comment_id; // Get folder global.

		// Set folder.
		$pm_dir_post_id = $post_id;
		$pm_dir_comment_id = $comment_id;

		// Change uploads folder.
		add_filter( 'upload_dir', array( __CLASS__, 'upload_dir_filter' ) );

		// Upload file.
		$upload = wp_handle_upload( $file_data, array(
			'test_form' => false,
		) );

		// Add additional file datas.
		if ( isset( $upload['file'] ) && ! empty( $upload['file'] ) ) {
			$upload['name']      = basename( $upload['file'] );
			$upload['size']      = $file_data['size'];
			$upload['extension'] = substr( strrchr( $upload['name'], '.' ), 1 );
		}

		// Revert back uploads folder.
		remove_filter( 'upload_dir', array( __CLASS__, 'upload_dir_filter' ) );

		// Unset global.
		unset( $pm_dir_post_id );
		unset( $pm_dir_comment_id );

		// return upload results.
		return $upload;
	}

	/**
	 * Filter Upload Dir
	 *
	 * @since 1.3.0
	 *
	 * @param array $pathdata Path Data.
	 */
	public static function upload_dir_filter( $pathdata ) {
		global $pm_dir_post_id, $pm_dir_comment_id; // Load folder info.

		$dir = 'private-messages-files'; // Main dir.

		$pathdata['path']   = $pathdata['basedir'] . '/' . $dir . '/' . $pm_dir_post_id . '/' . $pm_dir_comment_id;
		$pathdata['url']    = $pathdata['baseurl'] . '/' . $dir . '/' . $pm_dir_post_id . '/' . $pm_dir_comment_id;
		$pathdata['subdir'] = '';
		return $pathdata;
	}

	/**
	 * Delete all attachments for a post
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id Post ID.
	 */
	public static function delete_files( $post_id ) {
		global $wp_filesystem;
		if ( ! isset( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$pm_base_dir = self::base_dir();
		$post_dir    = "{$pm_base_dir}{$post_id}";
		$post_dir    = trailingslashit( $wp_filesystem->find_folder( $post_dir ) );
		if ( file_exists( $post_dir ) ) {
			$wp_filesystem->delete( $post_dir, true );
		}
	}

	/**
	 * Validate file by checking file extension.
	 *
	 * @since 1.3.0
	 * @link https://codex.wordpress.org/Function_Reference/get_allowed_mime_types
	 *
	 * @param string $file_name File name.
	 * @return bool True if file valid.
	 */
	public static function validate_file_name( $file_name ) {
		$allowed_mime = apply_filters( 'pm_allowed_mime_types', get_allowed_mime_types() );
		foreach ( $allowed_mime as $type => $mime ) {
			$filetype = wp_check_filetype( $file_name );
			if ( false !== strpos( $type, $filetype['ext'] ) ) {
				return true;
			}
		}
		return false;
	}

} // end class
