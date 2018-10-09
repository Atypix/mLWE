<?php

/**
 * A class to rapid develop meta boxes for custom & built in content types
 * Piggybacks on WordPress
 *
 * @author  Tran Ngoc Tuan Anh <rilwis@gmail.com>
 * @license GNU GPL2+
 * @package Meta Box
 */
class MAD_RW_Meta_Box {

	/**
	 * @var array Meta box information
	 */
	public $meta_box;

	/**
	 * @var array Fields information
	 */
	public $fields;

	/**
	 * @var bool Used to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
	 */
	public $saved = false;

	/**
	 * Create meta box based on given data
	 *
	 * @param array $meta_box Meta box definition
	 */
	public function __construct( $meta_box ) {
		$meta_box           = self::normalize( $meta_box );
		$meta_box['fields'] = self::normalize_fields( $meta_box['fields'] );

		$this->meta_box = $meta_box;
		$this->fields   = $this->meta_box['fields'];

		$show = $this->meta_box;
		if ( ! $show ) {
			return;
		}

		// Enqueue common styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 5 );

		// Add additional actions for fields
		foreach ( $this->fields as $field ) {
			MAD_Field::call( $field, 'add_actions' );
		}

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save post meta
		foreach ( $this->meta_box['post_types'] as $post_type ) {
			if ( 'attachment' === $post_type ) {
				// Attachment uses other hooks
				// @see wp_update_post(), wp_insert_attachment()
				add_action( 'edit_attachment', array( $this, 'save_post' ) );
				add_action( 'add_attachment', array( $this, 'save_post' ) );
			} else {
				add_action( "save_post_{$post_type}", array( $this, 'save_post' ) );
			}
		}
	}

	/**
	 * Enqueue common scripts and styles.
	 */
	public function enqueue() {
		if ( ! $this->is_edit_screen() ) {
			return;
		}

		wp_enqueue_style( 'mad', MAD_CSS_URL . 'style.css', array(), MAD_VER );

		// Enqueue scripts and styles for fields
		foreach ( $this->fields as $field ) {
			MAD_Field::call( $field, 'admin_enqueue_scripts' );
		}

		/**
		 * Allow developers to enqueue more scripts and styles
		 *
		 * @param MAD_RW_Meta_Box $object Meta Box object
		 */
		do_action( 'mad_enqueue_scripts', $this );
	}

	/**
	 * Add meta box for multiple post types
	 */
	public function add_meta_boxes() {

		foreach ( $this->meta_box['post_types'] as $post_type ) {
			add_meta_box(
				$this->meta_box['id'],
				$this->meta_box['title'],
				array( $this, 'show' ),
				$post_type,
				$this->meta_box['context'],
				$this->meta_box['priority']
			);
		}
	}

	/**
	 * Callback function to show fields in meta box
	 */
	public function show() {
		$saved = $this->is_saved();

		// Container
		printf( '<div class="mad-meta-box">' );

		wp_nonce_field( "mad-save-{$this->meta_box['id']}", "nonce_{$this->meta_box['id']}" );

		do_action( 'mad_before', $this );
		do_action( "mad_before_{$this->meta_box['id']}", $this );

		foreach ( $this->fields as $field ) {
			MAD_Field::call( 'show', $field, $saved );
		}

		do_action( 'mad_after', $this );
		do_action( "mad_after_{$this->meta_box['id']}", $this );

		// End container
		echo '</div>';
	}

	/**
	 * Save data from meta box
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {
		if ( ! $this->validate() ) {
			return;
		}
		$this->saved = true;

		// Make sure meta is added to the post, not a revision
		if ( $the_post = wp_is_post_revision( $post_id ) ) {
			$post_id = $the_post;
		}

		foreach ( $this->fields as $field ) {
			$single = $field['clone'] || ! $field['multiple'];
			$old    = MAD_Field::call( $field, 'raw_meta', $post_id );
			$new    = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : ( $single ? '' : array() );

			// Allow field class change the value
			if ( $field['clone'] ) {
				$new = MAD_Clone::value( $new, $old, $post_id, $field );
			} else {
				$new = MAD_Field::call( $field, 'value', $new, $old, $post_id );
				$new = MAD_Field::filter( 'sanitize', $new, $field );
			}
			$new = MAD_Field::filter( 'value', $new, $field, $old );

			// Call defined method to save meta value, if there's no methods, call common one
			MAD_Field::call( $field, 'save', $new, $old, $post_id );
		}
	}

	/**
	 * Validate form when submit. Check:
	 * - If this function is called to prevent duplicated calls like revisions, manual hook to wp_insert_post, etc.
	 * - Autosave
	 * - If form is submitted properly
	 *
	 * @return bool
	 */
	protected function validate() {
		$nonce = (string) filter_input( INPUT_POST, "nonce_{$this->meta_box['id']}" );
		return
			true !== $this->saved
			&& ( ! defined( 'DOING_AUTOSAVE' ) || $this->meta_box['autosave'] )
			&& wp_verify_nonce( $nonce, "mad-save-{$this->meta_box['id']}" );
	}

	/**
	 * Normalize parameters for meta box
	 *
	 * @param array $meta_box Meta box definition
	 * @return array $meta_box Normalized meta box
	 */
	public static function normalize( $meta_box ) {
		// Set default values for meta box
		$meta_box = wp_parse_args( $meta_box, array(
			'id'             => sanitize_title( $meta_box['title'] ),
			'context'        => 'normal',
			'priority'       => 'high',
			'post_types'     => 'post',
			'autosave'       => false,
			'default_hidden' => false,
		) );

		/**
		 * Use 'post_types' for better understanding and fallback to 'pages' for previous versions
		 *
		 * @since 4.4.1
		 */
		if ( ! empty( $meta_box['pages'] ) ) {
			$meta_box['post_types'] = $meta_box['pages'];
		}

		// Make sure the post type is an array.
		$meta_box['post_types'] = (array) $meta_box['post_types'];

		return $meta_box;
	}

	/**
	 * Normalize an array of fields
	 *
	 * @param array $fields Array of fields
	 * @return array $fields Normalized fields
	 */
	public static function normalize_fields( $fields ) {
		foreach ( $fields as $k => $field ) {
			$field = MAD_Field::call( 'normalize', $field );

			$fields[ $k ] = $field;
		}

		return $fields;
	}

	/**
	 * Check if meta box is saved before.
	 * This helps saving empty value in meta fields (text, check box, etc.) and set the correct default values.
	 *
	 * @return bool
	 */
	public function is_saved() {
		$post = get_post();

		foreach ( $this->fields as $field ) {
			if ( empty( $field['id'] ) ) {
				continue;
			}
			$value = MAD_Field::call( $field, 'raw_meta', $post->ID );
			if (
				( ! $field['multiple'] && '' !== $value )
				|| ( $field['multiple'] && array() !== $value )
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if we're on the right edit screen.
	 *
	 * @param WP_Screen $screen Screen object. Optional. Use current screen object by default.
	 * @return bool
	 */
	public function is_edit_screen( $screen = null ) {
		if ( ! ( $screen instanceof WP_Screen ) ) {
			$screen = get_current_screen();
		}
		return 'post' == $screen->base && in_array( $screen->post_type, $this->meta_box['post_types'] );
	}
}
