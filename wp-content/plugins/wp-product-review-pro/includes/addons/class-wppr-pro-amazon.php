<?php
/**
 * The file that defines Amazon Addon.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/addons/abstract
 */

/**
 * Class WPPR_Pro_Amazon
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/addons
 */
class WPPR_Pro_Amazon extends WPPR_Pro_Addon_Abstract {

	/**
	 * WPPR_Pro_Listings constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->name = __( 'Pro Amazon' , 'wp-product-review' );
		$this->slug = 'wppr-pro-amazon';
		$this->version = '1.2.2';
	}

	/**
	 * Registers the hooks needed by the addon.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function hooks() {

		$this->model = new WPPR_Query_Model();

		$this->loader->add_action( 'wp_ajax_' . $this->slug . '-ajax-action', $this, 'ajax' );
		$this->loader->add_action( 'cron_hook_' . $this->slug, $this, 'cron_amazon' );

		$this->loader->add_action( 'wppr_editor_details_before', $this, 'editor_fields', 10, 1 );
		$this->loader->add_action( 'wppr_before_save', $this, 'save_fields', 10, 2 );

		$this->loader->add_filter( 'wppr_settings_sections', $this, 'add_sections', 10, 1 );
		$this->loader->add_filter( 'wppr_settings_fields', $this, 'add_fields', 10, 1 );

		$this->loader->add_filter( 'wppr_get_old_option', $this, 'get_old_option', 10, 2 );

		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue' );

		register_deactivation_hook( __FILE__ , array( $this, 'deactivate' ) );
		register_activation_hook( __FILE__ , array( $this, 'activate' ) );
	}

	/**
	 * Method to deactivate cron hook.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'cron_hook_wppr-pro-amazon' );
	}

	/**
	 * Method to activate cron hook.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public static function activate() {
		self::register_cron();
	}

	/**
	 * Method to register a cron job.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   bool $clear Optiona. Param to specify if should not reschedule.
	 */
	public function register_cron( $clear = false ) {
		wp_clear_scheduled_hook( 'cron_hook_wppr-pro-amazon' );
		if ( ! $clear ) {
			wp_schedule_event( time(), 'daily', 'cron_hook_wppr-pro-amazon' );
		}
	}

	/**
	 * The cron job method.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function cron_amazon() {
		$ids = $this->model->wppr_get_option( 'cwppos_amazon_ids' );
		if ( ! $ids ) {
			return;
		}
		$details = $this->call_amazon_api( array_values( $ids ), false );
		foreach ( $details as $detail ) {
			$key = array_search( $detail['asin'], $ids );
			if ( $key !== false ) {
				$post_id = intval( str_replace( 'p', '', $key ) );
				$review_model = new WPPR_Review_Model( $post_id );
				$review_model->set_price( $post_id, 'cwp_rev_price', $detail['price'] );
			}
		}
	}

	/**
	 * Utility method for the ajax call.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function ajax() {
		$action = $_POST['_action'];
		switch ( $action ) {
			case 'get':
				$this->call_amazon_api( $_POST['cwp_rev_amazon_id'] );
				break;
		}
		die();
	}

	/**
	 * Utility method to call Amazon API
	 *
	 * @since   2.0.0
	 * @access  private
	 * @param   integer $id The product ID.
	 * @param   bool    $ajax If call is from ajax.
	 * @return array|null|WP_Error
	 */
	private function call_amazon_api( $id, $ajax = true ) {
		require_once WPPR_PRO_PATH . '/includes/addons/vendors/AmazonAPI.php';
		$amazonAPI  = new AmazonAPI( $this->model->wppr_get_option( 'amazon_key_id' ), $this->model->wppr_get_option( 'amazon_secret_key' ), $this->model->wppr_get_option( 'amazon_associate_tag' ) );
		$amazonAPI->SetRetrieveAsArray();
		$amazonAPI->SetLocale( strtolower( $this->model->wppr_get_option( 'amazon_country' ) ) );

		$details    = null;
		$items      = $amazonAPI->ItemLookUp( $id );
		if ( $ajax ) {
			if ( is_array( $items ) && count( $items ) == 1 ) {
				wp_send_json_success(
					array(
						'details' => array(
							'name'          => $items[0]['title'],
							'url'           => $items[0]['largeImage'],
							'smallurl'      => $items[0]['smallImage'],
							'affiliatelink' => $items[0]['url'],
							'price'         => $items[0]['formattedPrice'],
						),
					)
				);
			}
		} else {
			if ( count( $amazonAPI->GetErrors() ) > 0 ) {
				$errors = $amazonAPI->GetErrors();
				var_dump( $errors );
				return new WP_Error();
			}
			$details    = array();
			foreach ( $items as $indx => $item ) {
				$details[]  = array(
					'asin'          => $item['asin'],
					'name'          => $item['title'],
					'url'           => $item['largeImage'],
					'affiliatelink' => $item['url'],
					'price'         => $item['formattedPrice'],
				);
			}
		}
		return $details;
	}

	/**
	 * Utility method to save Amazon fields from editor..
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   object $post The WP Post Object.
	 * @param   array  $data The $_POST data.
	 */
	public function save_fields( $post, $data ) {
		$status = isset( $data['wppr-review-status'] ) ? strval( $data['wppr-review-status'] ) : 'no';
		if ( $status === 'yes' ) {
			$id = sanitize_text_field( $data['cwp_rev_amazon_id'] );
			update_post_meta( $post->ID, 'cwp_rev_amazon_id', $id );

			$ids = $this->model->wppr_get_option( 'cwppos_amazon_ids' );
			if ( ! $ids ) {
				$ids    = array();
			}
			$ids[ 'p' . $post->ID ]   = $id;
			$this->model->wppr_set_option( 'cwppos_amazon_ids', $ids );
		}
	}

	/**
	 * Method to add editor fields.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   object $post The WP Post Object.
	 */
	public function editor_fields( $post ) {
		$amazon_id = get_post_meta( $post->ID, 'cwp_rev_amazon_id', true );
		ob_start();
		include WPPR_PRO_PATH . '/includes/addons/layouts/amazon/editor-fields.php';
		$layout = ob_get_clean();
		echo $layout;
	}

	/**
	 * Method to enqueue scripts and styles.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function enqueue() {
		wp_enqueue_script( $this->slug . '-script', WPPR_PRO_ADDONS_ASSETS . 'js/amazon.js', array(), $this->version, true );
		wp_localize_script(
			$this->slug . '-script', 'wppra', array(
				'action'    => $this->slug . '-ajax-action',
				'i18n'      => array(
					'buyonamazon'   => __( 'Buy On Amazon', 'wp-product-review' ),
				),
			)
		);
	}

	/**
	 * Method to filter old value from DB.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   string $value The value passed by the filter.
	 * @param   string $key The key passed by the filter.
	 * @return mixed
	 */
	public function get_old_option( $value, $key ) {
		$allowed_options = array(
			'amazon_key_id',
			'amazon_secret_key',
			'amazon_associate_tag',
			'amazon_country',
			'amazon_cron',
		);
		if ( in_array( $key, $allowed_options ) && $value == false ) {
			$value = get_option( $key );
		}
		return $value;
	}

	/**
	 * Registers a new section in the global settings.
	 *
	 * @since   2.0.0
	 * @acccess public
	 * @param   array $sections The sections array.
	 * @return mixed
	 */
	public function add_sections( $sections ) {
		$sections['pro_amazon'] = __( 'Pro Amazon', 'wp-product-review' );
		return $sections;
	}

	/**
	 * Registers a new fields list for the section defined in add_section().
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param   array $fields The fields array.
	 * @return mixed
	 */
	public function add_fields( $fields ) {
		$helpLink1  = sprintf( __( 'Click %1$shere%2$s to see how to get this value', 'wp-product-review' ), '<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/your-account.html" target="_new">', '</a>' );
		$helpLink2  = sprintf( __( 'Click %1$shere%2$s to see how to get this value', 'wp-product-review' ), '<a href="http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AssociateTag.html" target="_new">', '</a>' );

		$fields['pro_amazon'] = array(
			'amazon_key_id' => array(
				'type'        => 'input_text',
				'name'        => __( 'Access Key ID', 'wp-product-review' ),
				'description' => $helpLink1,
				'id'          => 'amazon_key_id',
				'default' => '',
			),
			'amazon_secret_key' => array(
				'type'        => 'input_text',
				'name'        => __( 'Secret Access Key', 'wp-product-review' ),
				'description' => $helpLink2,
				'id'          => 'amazon_secret_key',
				'default' => '',
			),
			'amazon_associate_tag' => array(
				'type'        => 'input_text',
				'name'        => __( 'Associate Tag', 'wp-product-review' ),
				'description' => $helpLink2,
				'id'          => 'amazon_associate_tag',
				'default' => '',
			),
			'amazon_country' => array(
				'id'      => 'amazon_country',
				'name'    => __( 'Country', 'wp-product-review' ),
				'description'    => __( 'Select the country to localize results.', 'wp-product-review' ),
				'type'    => 'select',
				'options' => array(
					'BR' => 'Brazil',
					'CA' => 'Canada',
					'CN' => 'China, People\'s Republic of',
					'DE' => 'Germany',
					'ES' => 'Spain',
					'FR' => 'France',
					'IN' => 'India',
					'IT' => 'Italy',
					'JP' => 'Japan',
					'MX' => 'Mexico',
					'UK' => 'United Kingdom',
					'US' => 'United States Of America',
				),
				'default' => 'US',
			),
			'amazon_cron' => array(
				'id'      => 'amazon_cron',
				'name'    => __( 'Cron', 'wp-product-review' ),
				'description'    => __( 'Enable daily update of price.', 'wp-product-review' ),
				'type'    => 'select',
				'options' => array(
					'1' => 'Yes',
					'0' => 'No',
				),
				'default' => '0',
			),
		);
		return $fields;
	}
}
