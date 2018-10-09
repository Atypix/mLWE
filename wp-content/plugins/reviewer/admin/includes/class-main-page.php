<?php

/**
 * Reviewer Plugin v.2
 * Created by Michele Ivani
 */
class RWP_Main_Page extends RWP_Admin_Page
{
	protected static $instance = null;
	protected $templates_option;

	public function __construct()
	{
		parent::__construct();

		$this->menu_slug = 'reviewer-main-page';
		$this->parent_menu_slug = 'reviewer-main-page';
		$this->add_menu_page();
		$this->templates_option = RWP_Reviewer::get_option( 'rwp_templates' );

		// Localize
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script') );
	}

	public function add_menu_page()
	{
		add_submenu_page( $this->parent_menu_slug, __( 'Templates', $this->plugin_slug), __( 'Templates', $this->plugin_slug), $this->capability, $this->menu_slug, array( $this, 'display_plugin_admin_page' ) );
	}

	public function localize_script()
	{
		$action_name = 'rwp_ajax_action_delete_template';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'deleteTemplateObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );

		$action_name = 'rwp_ajax_action_duplicate_template';
		wp_localize_script( $this->plugin_slug . '-admin-script', 'duplicateTemplateObj', array('ajax_nonce' => wp_create_nonce( $action_name ), 'ajax_url' => admin_url('admin-ajax.php'), 'action' => $action_name ) );
	}

	public static function ajax_callback()
	{
		if ( isset( $_POST['templateId'] ) ) {

			$templates = RWP_Reviewer::get_option( 'rwp_templates' );

			if(isset( $templates[ $_POST['templateId'] ])) {
				unset( $templates[ $_POST['templateId'] ] );
			}

			update_option('rwp_templates', $templates);
		}

		die( json_encode( array('msg' => __( 'Template deleted', 'reviewer') ) ) );
	}

	public static function ajax_callback_duplicate()
	{
		if ( isset( $_POST['templateId'] ) ) {

			$templates = RWP_Reviewer::get_option( 'rwp_templates' );

			if(isset( $templates[ $_POST['templateId'] ])) {

				$id = uniqid('rwp_template_');

				$new = $templates[ $_POST['templateId'] ];
				$new['template_id']   = $id;
				$new['template_name'] = $new['template_name'] . ' ' . __( 'Copy', 'reviewer');

				$templates[ $id ] = $new;
			}

			update_option('rwp_templates', $templates);
		}

		die( json_encode( array('msg' => __( 'Template duplicated', 'reviewer'), 'html' => self::get_template_thumb( $new ) ) ) );
	}

	public function display_plugin_admin_page()
	{
		?>
		<div class="wrap">
			<?php if($this->is_licensed()): ?>
			<h2 class="rwp-h2">
				<?php _e( 'Templates', $this->plugin_slug ); ?>
				<span class="rwp-template-count"><?php echo count( $this->templates_option ); ?></span>
				<a href="?page=reviewer-template-manager-page" class="add-new-h2"><?php _e( 'Add new template', $this->plugin_slug ); ?></a>
				<img class="rwp-loader rwp-restore" src="<?php echo admin_url(); ?>images/spinner.gif" alt="loading" />
			</h2>

			<div class="theme-browser redered">
				<div class="themes">

					<?php
					foreach ($this->templates_option as $template_id => $template) {
						echo self::get_template_thumb( $template );
					}
					?>

					<div class="theme add-new-theme">
						<a href="?page=reviewer-template-manager-page">
							<div class="theme-screenshot"><span></span></div>
							<h3 class="theme-name"><?php _e( 'Add new template', $this->plugin_slug ); ?></h3>
						</a>
					</div><!--/theme add-new-->

				</div><!--/themes-->
			</div><!--/theme-browser-->

			<?php else: ?>
			<?php $this->license_notice() ?>
			<?php endif; ?>
			<?php //RWP_Reviewer::pretty_print( $this->templates_option ); ?>
		</div><!--/wrap-->
		<?php
	}

	private static function get_template_thumb( $template ) {
		$template_id = $template['template_id'];

		$html  = '';
		$html .= '<div class="theme" id="rwp-theme-wrap-'. $template['template_id'] .'">';

			$html .= '<div class="theme-screenshot">';
				$html .= '<img src="'.RWP_PLUGIN_URL . 'admin/assets/images/template-preview.png" alt="">';
			$html .= '</div>';

			$html .= '<h3 class="theme-name">'. $template['template_name'] .'</h3>';

			$html .= '<div class="theme-actions">';
				$html .= '<a class="button button-secondary load-customize hide-if-no-customize" href="?page=reviewer-template-manager-page&template='.$template_id .'">'. __( 'Edit', 'reviewer') .'</a>';
				$html .= '<a class="button button-secondary load-customize hide-if-no-customize rwp-duplicate-template-btn" href="?page=reviewer-delete-template-page&template='.$template_id .'"  data-template-id="'.$template_id .'"> '.__( 'Duplicate', 'reviewer') .'</a>';
				$html .= '<a class="button button-secondary load-customize hide-if-no-customize rwp-delete-template-btn" href="?page=reviewer-delete-template-page&template='.$template_id .'" data-confirm-msg="'.__('Do you confirm the template deletion?', 'reviewer').'" data-template-id="'.$template_id .'">'.__( 'Delete', 'reviewer').'</a>';
			$html .= '</div>';
		$html .= '</div><!--/theme-->';

		return $html;
	}

	public static function get_instance()
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
