<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Emails_Admin_Help {

	/**
	 *  Help tab configuration array
	 *
	 *  Should be in format similar to:
	 *  $this->tabs = array(
	 * 	    'slug' => array(
	 *			'title' => __( 'Tab Title' )
	 *      )
	 *  );
	 *
	 * @type array
	 */
	public $tabs;

	/**
	 *  Default Tabs
	 *
	 * @type array
	 */
	public $default_tabs;

	/**
	 *  Screens configuration array
	 *
	 *  Array of screens for the help tabs to show on
	 *
	 *  Should be in format similar to:
	 *  $this->screens = array(
	 *        'new' => TRUE,
	 * 		  'edit' => TRUE,
	 * 		  'list' => TRUE
	 *  );
	 *
	 * @type array
	 */
	public $screens;

	/**
	 * @type WP_Job_Manager_Emails_Admin_Help_Job
	 */
	protected $cpt;
	/**
	 * WP_Job_Manager_Emails_Admin_Help constructor.
	 *
	 */
	public function __construct( $cpt ) {

		$this->cpt = $cpt;

		add_action( "load-edit.php", array( $this, 'post_list' ) );
		add_action( 'load-post-new.php', array( $this, 'post_new' ) );
		add_action( 'load-post.php', array( $this, 'post_edit' ) );

	}

	/**
	 * Output/Return Formatted Code Block
	 *
	 * This method is used to format a code block with shortcodes.  The shortcodes will be replaced with HTML
	 * to color the shortcodes, inside a Semantic UI label.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param        $content		String to format
	 * @param bool   $return		Return the HTML instead of echo
	 * @param string $label_size	Size of label in HTML (mini, small, etc)
	 *
	 * @return string
	 */
	function code_block( $content, $return = false, $label_size = '' ){

		if( preg_match_all( "@\\[[^\\]]*\\]@", $content, $shortcodes ) ) {

			foreach( $shortcodes[0] as $shortcode ) {
				$content = str_replace( $shortcode, "<span style='color: #E4EB7A;'>{$shortcode}</span>", $content );
			}

		}

		$formatted_content = "<div class='ui label grey help-code-block {$label_size}'><pre style='font-weight: normal; margin: 2px; overflow-x: visible; overflow: inherit;'>{$content}</pre></div>";

		if( $return ) return $formatted_content;

		echo $formatted_content;
	}
	
	/**
	 * Initialize Tabs and Tab Sidebars
	 *
	 * This method gets called by the magic method for any CPT pages, or called
	 * specifically by a class that extends this class (ie settings page) in order
	 * to initialize and add tabs and tab sidebars.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param string $page_type
	 */
	function init( $page_type = '' ) {

		if( ! $this->check_post_type() || empty( $page_type ) ) return;

		$screen = get_current_screen();

		if( ! $this->screens ) $this->init_config();

		if( ! isset( $this->screens[ $page_type ] ) ) return;

		$tabs = isset( $this->screens[$page_type]['tabs'] ) ? $this->screens[ $page_type ]['tabs'] : $this->default_tabs;

		foreach ( $tabs as $tab => $conf ) {

			$args = array(
				'id'    => "jme_{$tab}_{$page_type}",
				'title' => $conf[ 'title' ],
			);

			// Check if specific method for page tab exists, otherwise use standard tab method
			$callback_method = method_exists( $this, "{$tab}_{$page_type}" ) ? "{$tab}_{$page_type}" : $tab;

			// Set callback to specific page method, or regular tab method (if exists), otherwise set to false ( will use 'content' arg )
			$args[ 'callback' ] = method_exists( $this, $callback_method ) ? array( $this, "output_{$callback_method}" ) : FALSE;

			// Add help tab with arguments
			$screen->add_help_tab( $args );

			// Check if specific method for page exists for sidebar, if not use standard sidebar tab method
			$sidebar_method = method_exists( $this, "sidebar_{$tab}_{$page_type}" ) ? "{$tab}_{$page_type}" : $tab;

			// Check if regular or page specific method exists, if not, use the sidebar_all method instead
			$sidebar_method = method_exists( $this, "sidebar_{$sidebar_method}" ) ? "sidebar_{$sidebar_method}" : "sidebar_all";

			// Get the sidebar content (or false if no content)
			$sidebar_content = $this->$sidebar_method();

			// Set the sidebar if there is content
			if( $sidebar_content ) $screen->set_help_sidebar( $sidebar_content );

		}

	}

	/**
	 * Magic Method for output_ sidebar_ and post_ method calls
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param $method_name
	 * @param $args
	 *
	 * @return mixed|void
	 */
	public function __call( $method_name, $args ) {

		if ( preg_match( '/(?P<action>(output|sidebar|post)+)_(?P<variable>\w+)/', $method_name, $matches ) ) {
			$variable = strtolower( $matches[ 'variable' ] );
			switch ( $matches[ 'action' ] ) {
				case 'output':
					if( ! $this->check_post_type() || ! method_exists( $this, $variable ) ) return false;
					return $this->$variable();

				case 'sidebar':
					$sidebar_method = $variable === "all" ? "sidebar" : "sidebar_{$variable}";
					if ( ! $this->check_post_type() || ! method_exists( $this, $sidebar_method ) ) return false;
					ob_start();
					$this->$sidebar_method();
					$sidebar_content = ob_get_clean();
					return $sidebar_content;

				case 'post':
					if ( ! $this->screens ) $this->init_config();
					if ( ! $this->check_post_type() || empty( $this->screens[ $variable ] ) ) return false;
					$this->init( $variable );
					break;

				case 'default':
					error_log( 'Method ' . $method_name . ' not exists' );
			}
		}
	}

	/**
	 * Check Post Type against $this->post_type
	 *
	 * This method should be ran from action for edit.php, post.php, and post-new.php pages to check
	 * in $_GET['post_type'] or $_GET['post'] to see if they match $this->post_type.
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function check_post_type(){

		// Check for post type in $_GET
		if( isset( $_GET['post_type'] ) && $_GET['post_type'] === $this->cpt->get_post_type() ) return true;
		// Check for post in $_GET (probably post.php page)
		if ( isset( $_GET[ 'post' ] ) && get_post_type( $_GET['post'] ) === $this->cpt->get_post_type() ) return TRUE;

		return false;
	}

	/**
	 * Default Sidebar Output
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function sidebar() {
		?>
		<p><a href="https://plugins.smyl.es" target="_blank">sMyles Plugins</a></p>
		<p><a href="https://plugins.smyl.es/docs-kb/" target="_blank">Documentation</a></p>
		<?php
	}

	/**
	 * Initialize Config Placeholder
	 *
	 * Default tab configuration initialization, should be overriden
	 * by extending class
	 *
	 * @since 1.0.0
	 *
	 */
	function init_config() {

		$this->tabs = array(
			'overview' => array(
					'title' => __( 'Overview', 'wp-job-manager-emails' ),
			),
		);

		$this->screens = array(
			'new'  => TRUE,
			'edit' => TRUE,
			'list' => FALSE
		);
	}

	/**
	 * Shortcode Help Tab Content
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function shortcodes() {

		?>
		<div class="ui segment basic">
			<h4 class="ui header horizontal divider"><i class="wordpress icon"></i><?php _e( 'Shortcodes', 'wp-job-manager-emails' ); ?></h4>
			<p><?php _e( 'Populate dynamic values into your email template.', 'wp-job-manager-emails' ); ?></p>
			<p><?php _e( 'As most WordPress users are already familiar with shortcodes, and how they work, that is why I decided to use shortcodes as the <em>template variables</em> to insert dynamic values from listings and other sources, as well as to allow you to form conditional statements, and another features that will soon be included with this plugin.', 'wp-job-manager-emails' ); ?></p>

			<h5 class="ui horizontal divider header">
				<?php _e( 'Tips', 'wp-job-manager-emails' ); ?>
			</h5>
			<div class="ui bulleted list">
				<div class="item"><?php _e('Double click on a shortcode to insert in the content area', 'wp-job-manager-emails'); ?></div>
				<div class="item"><?php _e('Hover over a shortcode to view the description', 'wp-job-manager-emails'); ?></div>
			</div>

			<h5 class="ui horizontal divider header">
				<?php _e('Metabox Colors', 'wp-job-manager-emails'); ?>
			</h5>

			<table class="ui very basic celled table">
				<thead>
					<tr class="center aligned">
						<th><?php _e('Optional Meta Key', 'wp-job-manager-emails'); ?></th>
						<th><?php _e('Required Meta Key', 'wp-job-manager-emails'); ?></th>
						<th><?php _e('Non-Meta Key Shortcode', 'wp-job-manager-emails'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="center aligned">
						<td>
							<div class="ui label">[job_optional_field]</div>
						</td>
						<td>
							<div class="ui label grey">[job_required_field]</div>
						</td>
						<td>
							<div class="ui label basic grey">[job_fields]</div>
						</td>
					</tr>
				</tbody>
			</table>

			<h5 class="ui horizontal divider header">
				<i class="keyboard icon"></i>
				<?php _e( 'Attributes/Arguments & Usage Examples', 'wp-job-manager-emails' ); ?>
			</h5>
			<br />
			<div class="ui styled fluid accordion" id="shortcodes_accordion">
				<div class="title active">
					<i class="dropdown icon"></i>
					<?php _e( 'Standard Shortcode Attributes/Arguments', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content active">
					<div class="ui bulleted list">
						<div class="item"><?php _e( 'The arguments below can be added in the opening shortcode tag, based on your preference.', 'wp-job-manager-emails' ); ?></div>
						<div class="item"><?php _e( 'Multiple arguments can be specified in a shortcode.', 'wp-job-manager-emails' ); ?></div>
					</div>

					<div class="ui message yellow small">
						<div class="header">
							<?php _e('All standard shortcode arguments <strong>must</strong> use the standard argument format', 'wp-job-manager-emails'); ?>
						</div>
						<p><?php _e( 'You must use the format of <code>argument="value"</code> for any standard shortcode arguments. The argument equal to the value, enclosed in single or double quotes.', 'wp-job-manager-emails' ); ?></p>
					</div>

					<table class="ui celled striped table">
						<thead>
							<tr class="center aligned">
								<th><?php _e( 'Argument', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Example', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">before<div class="sub header"><small><?php _e( 'string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[job_salary before="$"]' );
									?>
								</td>
								<td>
									<?php _e( 'String to output after field value (and label).', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">after<div class="sub header"><small><?php _e( 'string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[job_salary after=" per year"]' );
									?>
								</td>
								<td>
									<?php _e( 'String to output after field value.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">divider<div class="sub header"><small><?php _e( 'string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_salary divider divider="_"]' ); ?>
								</td>
								<td>
									<?php _e( 'Customize the string used for building the divider (see advanced arguments), default is <code>-</code>', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">repeat<div class="sub header"><small><?php _e( 'integer', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_salary divider repeat="12"]' ); ?>
								</td>
								<td>
									<?php _e( 'Customize how many times to repeat the divider string, default is <code>12</code>', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">order<div class="sub header"><small><?php _e( 'csv string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_salary divider before="&lt;br&gt;" <br>order="before,top_divider,content,<br>label,value,after,bottom_divider"]' ); ?>
								</td>
								<td>
									<?php _e( 'Customize order of shortcode output. In this example we move <code>before</code> in front of <code>top_divider</code>.  <strong>Order must be in a comma separated!</strong>Default order is:<br/><em>top_divider,before,content,label,value,after,bottom_divider</em>', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">separator<div class="sub header"><small><?php _e( 'string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_category separator="&lt;br&gt;"]' ); ?>
								</td>
								<td>
									<?php _e( 'Customize the separator used for any multiple field types (saved as array), such as file (multiple), and taxonomy field types.  This example we use a line break instead of comma.  Default separator is <code>, </code>', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">skip_keys<div class="sub header"><small><?php _e( 'csv string', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_fields skip_keys="job_title,job_file"]' ); ?>
								</td>
								<td>
									<?php _e( 'Customize meta keys to skip when outputting groups of fields (like job_fields and resume_fields).  In this example we skip outputting the job_title and job_file.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
					<?php _e( 'Standard Shortcode Usage', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<p><?php _e( 'The default and standard usage of a shortcode is very simple, and all you need to do is enter the shortcode exactly as you see it in the shortcode metaboxes on this page.', 'wp-job-manager-emails' ); ?></p>
					<p><?php _e( 'If using a shortcode that is a meta key, and the listing has no value, the shortcode will ', 'wp-job-manager-emails' ); ?></p>
					<table class="ui celled striped definition table">
						<thead>
							<tr>
								<th></th>
								<th class="center aligned"><?php _e( 'Content', 'wp-job-manager-emails' ); ?></th>
								<th class="center aligned"><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><h5 class="ui header"><?php _e( 'Usage', 'wp-job-manager-emails' ); ?></h5></td>
								<td>
									<?php $this->code_block( __( 'Candidate Name:', 'wp-job-manager-emails' ) . ' [candidate_name]' ); ?>
								</td>
								<td>
									<?php _e( 'Standard usage with field meta key used as shortcode.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td><h5 class="ui header"><?php _e( 'Result', 'wp-job-manager-emails' ); ?></h5></td>
								<td>
									<?php $this->code_block( __( 'Candidate Name:', 'wp-job-manager-emails' ) . ' John Doe' ); ?>
								</td>
								<td>
									<?php _e( 'Shortcode is replaced with value from field.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
					<?php _e( 'Standard Shortcode Usage (Open & Closing Tags)', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<p><?php _e( 'This method is useful whenever you only want to output text if the listing has a value for that field/shortcode.', 'wp-job-manager-emails' ); ?></p>
					<p><?php _e( 'For instance, if you were to use the standard shortcode method on a field that is not required, that means there may be some listings that do not have a value. Using the advanced method you can set the text to display before the value of the field, and only have it output if that field has a value.', 'wp-job-manager-emails' ); ?></p>
					<table class="ui celled definition table">
						<thead>
						<tr>
							<th></th>
							<th class="center aligned"><?php _e( 'Content', 'wp-job-manager-emails' ); ?></th>
							<th class="center aligned"><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<h5 class="ui header"><?php _e( 'Usage', 'wp-job-manager-emails' ); ?></h5>
							</td>
							<td>
								<?php
									$this->code_block(
											__( 'Here are the job details from the listing:', 'wp-job-manager-emails' ) . '<br/>'
											. '[job_salary]' . __( 'Job Salary:', 'wp-job-manager-emails' ) . '[/job_salary]<br/>'
											. __( 'Job Title: ', 'wp-job-manager-emails' ) . '[job_title]'
									);
								?>
							</td>
							<td>
								<?php _e( 'Use an open and closing shortcode to output the content inside, if the field has a value.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						<tr class="positive">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
										<small><?php _e( 'with value', 'wp-job-manager-emails' ); ?></small>
									</div>
								</h5>
							</td>
							<td>
								<?php
									$this->code_block(
											__( 'Here are the job details from the listing:', 'wp-job-manager-emails' ) . '<br/>'
											. __( 'Job Salary: ', 'wp-job-manager-emails' ) . '$50,000/yr<br/>'
											. __( 'Job Title: ', 'wp-job-manager-emails' ) . 'Example Job Title'
									);
								?>
							</td>
							<td>
								<?php _e( 'Because the field had a value, the content inside of open/close shortcode is output.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						<tr class="negative">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
										<small><?php _e( 'without value', 'wp-job-manager-emails' ); ?></small>
									</div>
								</h5>
							</td>
							<td>
								<?php
									$this->code_block(
											__( 'Here are the job details from the listing:', 'wp-job-manager-emails' ) . '<br/>'
											. __( 'Job Title: ', 'wp-job-manager-emails' ) . 'Example Job Title'
									);
								?>
							</td>
							<td>
								<?php _e( 'If the field does not have a value, no content inside the open/close shortcode is output.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
									<?php _e( 'Advanced Shortcode Attributes/Arguments', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<div class="ui bulleted list">
						<div class="item"><?php _e( 'Advanced shortcode arguments <strong>can</strong> be mixed with standard shortcode arguments.', 'wp-job-manager-emails' ); ?></div>
						<div class="item"><?php _e( 'Placement of advanced shortcode arguments does not matter (ie <code>top</code> before <code>divider</code> )', 'wp-job-manager-emails' ); ?></div>
					</div>

					<div class="ui message yellow small">
						<div class="header">
							<?php _e( 'Do <strong>not</strong> use an advanced shortcode argument in a standard argument format!', 'wp-job-manager-emails' ); ?>
						</div>
						<p><?php _e( 'You can mix both advanced and standard arguments, but do not use an advanced argument in the standard argument format.', 'wp-job-manager-emails' ); ?></p>
					</div>
					<div class="ui message negative small">
						<div class="header">
							<?php _e( 'Advanced Arguments will only output if there is a value', 'wp-job-manager-emails' ); ?>
						</div>
						<p><?php _e( 'If you use one of the advanced arguments like <code>label</code> or <code>divider</code>, it will only output if the field has a value.', 'wp-job-manager-emails' ); ?></p>
					</div>

					<table class="ui celled striped table">
						<thead>
							<tr class="center aligned">
								<th><?php _e( 'Argument', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Example', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">label<div class="sub header"><small><?php _e( '', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[job_salary label]' );
									?>
								</td>
								<td>
									<?php _e( 'Adding the <code>label</code> argument will output the label for the field before the value.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">divider<div class="sub header"><small><?php _e( '', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[job_salary divider]' );
									?>
								</td>
								<td>
									<?php _e( 'This will output a divider BOTH above AND below the value (see standard args for customizing divider).  This is the same as using <code>[job_salary divider top bottom]</code>.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">top<div class="sub header"><small><?php _e( 'divider', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_salary divider top]' ); ?>
								</td>
								<td>
									<?php _e( 'This will output a divider, but only the top divider.  This argument should only be used along with the divider argument.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">bottom<div class="sub header"><small><?php _e( 'divider', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php $this->code_block( '[job_salary label divider bottom]' ); ?>
								</td>
								<td>
									<?php _e( 'This will output a divider, but only the bottom divider, as well as the label.  This argument should only be used along with the divider argument.', 'wp-job-manager-emails' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Actions Help Tab Content
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function actions() {

		?>
		<div class="ui segment basic">
			<h4 class="ui header horizontal divider"><i class="mail icon"></i><?php _e( 'Send Email Actions', 'wp-job-manager-emails' ); ?></h4>
			<p><?php _e( 'Defines when to send the email', 'wp-job-manager-emails' ); ?></p>
			<p><?php _e( 'The actions listed in the dropdown box define when you want this email to be sent.  Only one email template can be active for each action.  As an example, if you enable this template and there is already another template enabled with the same action ... the other template will be disabled and this one will be used.', 'wp-job-manager-emails' ); ?></p>
			<p><?php _e( 'You can create multiple templates for a single action, but again, only one can be active at a time.', 'wp-job-manager-emails' ); ?></p>
			<h5 class="ui horizontal divider"><?php _e('Dropdown Labels', 'wp-job-manager-emails'); ?></h5>
			<p><?php _e('You may notice that some actions/hooks have different wording next to them.  This is used to show if there is already another template tied to that hook/action, etc.', 'wp-job-manager-emails'); ?></p>
			<p><div class="ui mini orange label"><?php _e( 'Currently Active Email ID', 'wp-job-manager-emails' ); ?></div>  <?php _e( 'is an action that already has an email template.  If you choose that action, this email template will be saved as disabled.', 'wp-job-manager-emails' ); ?></p>
			<p><div class="ui mini teal label"><?php _e( 'This Email Template', 'wp-job-manager-emails' ); ?></div>  <?php _e( 'means this email template is active for that action.', 'wp-job-manager-emails' ); ?></p>
			<p><?php _e( 'If neither are showing, that means there is no custom email attached to that action.', 'wp-job-manager-emails' ); ?></p>
		</div>
		<?php
	}

	/**
	 * If Shortcode Help Tab Content
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function if_shortcode() {

		?>
		<div class="ui segment basic">
			<h4 class="ui header horizontal divider"><i class="code icon"></i><?php _e( 'If Shortcode', 'wp-job-manager-emails' ); ?></h4>
			<p><?php _e( 'Shortcode to create conditional statements in your email template.', 'wp-job-manager-emails' ); ?></p>
			<p>
				<?php
					 printf( __( 'The %s shortcode is optional', 'wp-job-manager-emails' ), '<strong>[else]</strong>' );
				?>
			</p>
			<div class="ui styled fluid accordion" id="if_shortcode_accordion">
				<div class="title active">
					<i class="dropdown icon"></i>
					<?php _e( 'If/Else Attributes/Arguments', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content active">
					<p><?php _e( 'Currently the only supported argument for if statements is a field meta key.  In an upcoming release you will be able to call supported functions, as well as use AND/OR in the if statement.', 'wp-job-manager-emails' ); ?></p>
					<table class="ui celled striped table">
						<thead>
							<tr class="center aligned">
								<th><?php _e( 'Argument', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Example', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">xxx_xxx<div class="sub header"><small><?php _e( 'meta key', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[if job_location][/if]' );
									?>
								</td>
								<td>
									<?php printf( __( 'Replace %1$s with the meta key of the field you want to check for a value.  In the example on the left, the meta key checked for a value is %2$s', 'wp-job-manager-emails' ), '<strong>xxx_xxx</strong>', '<strong>job_location</strong>' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
					<?php _e( 'Standard If/Else Usage', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<p><?php printf( __( 'Currently, the only supported argument for the if statement is a meta key (more to be added soon).  In the example below, replace %s with the meta key you want to use.', 'wp-job-manager-emails' ), '<strong>candidate_phone</strong>' ); ?></p>
					<table class="ui celled definition table">
						<thead>
						<tr>
							<th></th>
							<th class="center aligned"><?php _e( 'Content', 'wp-job-manager-emails' ); ?></th>
							<th class="center aligned"><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<h5 class="ui header"><?php _e( 'Usage', 'wp-job-manager-emails' ); ?></h5>
							</td>
							<td>
								<?php
									$this->code_block(
											'[if candidate_phone]<br/>'
											. __( 'Candidate Phone Number: ', 'wp-job-manager-emails' ) . '[candidate_phone]<br/>'
											. '[else]<br/>'
											. __( 'Candidate phone not provided', 'wp-job-manager-emails' ) . '<br/>'
											. '[/if]'
									);
								?>
							</td>
							<td>
								<?php _e( 'If/Else statement to output specific content based on whether a field has a value or not.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						<tr class="positive">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
										<small><?php _e( 'with value', 'wp-job-manager-emails' ); ?></small>
									</div>
								</h5>
							</td>
							<td>
								<?php
									$this->code_block(
											__( 'Candidate Phone Number: ', 'wp-job-manager-emails' ) . 'XXX-XXX-XXXX'
									);
								?>
							</td>
							<td>
								<?php _e( 'Because the if statement validated as true, content inside the if statement is output.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						<tr class="negative">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
										<small><?php _e( 'without value', 'wp-job-manager-emails' ); ?></small>
									</div>
								</h5>
							</td>
							<td>
								<?php $this->code_block( __( 'Candidate phone not provided', 'wp-job-manager-emails' ) ); ?>
							</td>
							<td>
								<?php _e( 'Because the if statement validated as false, and there was an [else] shortcode, so all content after [else] is output.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Each Shortcode Help Tab Content
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function each_shortcode() {

		?>
		<div class="ui segment basic">
			<h4 class="ui header horizontal divider"><i class="code icon"></i><?php _e( 'Each Shortcode', 'wp-job-manager-emails' ); ?></h4>
			<p><?php _e( 'Shortcode to customize output of array fields in a foreach loop.', 'wp-job-manager-emails' ); ?></p>
			<div class="ui message negative small">
				<div class="header">
					<?php _e('The <code>[each xxx_xxx]</code> shortcode can only be used with multiple field types!', 'wp-job-manager-emails'); ?>
				</div>
				<p><?php _e( 'You can only use this shortcode with field types that are saved as an array of values. This includes file (must be multiple), and taxonomy field types.', 'wp-job-manager-emails' ); ?></p>
			</div>
			<div class="ui message yellow small">
				<div class="header">
					Use <code>[value]</code> to output the value inside <code>[each xxx_xxx][/each]</code>
				</div>
				<p><?php _e( 'All content inside the <code>[each xxx_xxx][value][/each]</code> shortcode will be output for each value.', 'wp-job-manager-emails' ); ?></p>
			</div>

			<div class="ui styled fluid accordion" id="each_shortcode_accordion">
				<div class="title active">
					<i class="dropdown icon"></i>
					<?php _e( 'Each Attributes/Arguments', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content active">
					<p><?php _e( 'Currently the only supported argument for each statements is a field meta key.', 'wp-job-manager-emails' ); ?></p>
					<table class="ui celled striped table">
						<thead>
							<tr class="center aligned">
								<th><?php _e( 'Argument', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Example', 'wp-job-manager-emails' ); ?></th>
								<th><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="single line center aligned">
									<h5 class="ui header">xxx_xxx<div class="sub header"><small><?php _e( 'meta key', 'wp-job-manager-emails' ); ?></small></div></h5>
								</td>
								<td>
									<?php
										$this->code_block( '[each job_region][value][/each]' );
									?>
								</td>
								<td>
									<?php printf( __( 'Replace %1$s with the meta key of the field you want to run through the foreach loop.  In the example on the left, the meta key to loop through is %2$s', 'wp-job-manager-emails' ), '<strong>xxx_xxx</strong>', '<strong>job_region</strong>' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
					<?php _e( 'Standard Each Usage', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<p><?php printf( __( 'Currently, the only supported argument for the each shortcode is a meta key (more to be added soon).  In the example below, replace %s with the meta key you want to use.', 'wp-job-manager-emails' ), '<strong>job_downloads</strong>' ); ?></p>
					<p><?php _e( 'The meta key you use <strong>MUST</strong> be a field that is saved as an array.  Example would be a file field type that is configured to support multiple files.  You can also use any kind of taxonomy field as well.', 'wp-job-manager-emails' ); ?></p>

					<table class="ui celled definition table">
						<thead>
						<tr>
							<th></th>
							<th class="center aligned"><?php _e( 'Content', 'wp-job-manager-emails' ); ?></th>
							<th class="center aligned"><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<h5 class="ui header"><?php _e( 'Usage', 'wp-job-manager-emails' ); ?></h5>
							</td>
							<td>
								<?php
									$this->code_block(
											'[each job_downloads]<br/>'
											. __( 'Download: ', 'wp-job-manager-emails' ) . '&lt;a href="[value]"&gt;[value]&lt;/a&gt;<br/>'
											. '[/each]'
									);
								?>
							</td>
							<td>
								<?php _e( 'Loop through each file in the job_downloads meta, and output Download: before a link to each download.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						<tr class="positive">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
										<small><?php _e( 'with value', 'wp-job-manager-emails' ); ?></small>
									</div>
								</h5>
							</td>
							<td>
								<?php
									$this->code_block(
											__( 'Download: ', 'wp-job-manager-emails' ) . '<a href="http://site.com/file1.pdf">http://site.com/file1.pdf</a><br/>'
											. __( 'Download: ', 'wp-job-manager-emails' ) . '<a href="http://site.com/file2.pdf">http://site.com/file2.pdf</a><br/>'
											. __( 'Download: ', 'wp-job-manager-emails' ) . '<a href="http://site.com/file3.pdf">http://site.com/file3.pdf</a><br/>'
									);
								?>
							</td>
							<td>
								<?php _e( 'Assuming there were three total files, this would be the output after the loop finishes.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="title">
					<i class="dropdown icon"></i>
					<?php _e( 'Advanced Each Usage', 'wp-job-manager-emails' ); ?>
				</div>
				<div class="content">
					<p><?php _e('The majority of standard shortcode arguments are supported in the each shortcode.  This include before, after, order, divider, etc.', 'wp-job-manager-emails'); ?></p>
					<table class="ui celled definition table">
						<thead>
						<tr>
							<th></th>
							<th class="center aligned"><?php _e( 'Content', 'wp-job-manager-emails' ); ?></th>
							<th class="center aligned"><?php _e( 'Description', 'wp-job-manager-emails' ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<h5 class="ui header"><?php _e( 'Usage', 'wp-job-manager-emails' ); ?></h5>
							</td>
							<td>
								<?php
									$this->code_block(
											'[each job_region before="&lt;ul&gt;" after="&lt;/ul&gt;"]<br/>'
											. '&lt;li&gt;[value]&lt;/li&gt;<br/>'
											. '[/each]'
									);
								?>
							</td>
							<td>
								<?php printf( __( 'Loop through each file in the job_region meta, and output each value inside %s HTML element, surrounded by a %2$s HTML element.', 'wp-job-manager-emails' ), '&lt;li&gt;', '&lt;ul&gt;' ); ?>
							</td>
						</tr>
						<tr class="positive">
							<td class="single line">
								<h5 class="ui header">
									<?php _e( 'Result', 'wp-job-manager-emails' ); ?>
									<div class="sub header">
									</div>
								</h5>
							</td>
							<td>
								<?php
									$this->code_block(
											'<ul><li>East Coast</li><li>West Coast</li><li>Other</li></ul>'
									);
								?>
							</td>
							<td>
								<?php _e( 'Values are output in an unordered list format, with each item surrounded by a list item element.', 'wp-job-manager-emails' ); ?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Overview Help Tab Content for List Table
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	function overview_list() {

		?>
		<br/>
		<p><?php _e( 'This page you are viewing contains a list of all the email templates for this specific group.', 'wp-job-manager-emails' ); ?></p>
		<div class="ui warning message">
			<?php _e( 'There is now a fully integrated templating system included with this plugin, available on the edit/add template page. To get there, just click the Add New button, and on that page click Email Templates (defaults below will be removed in upcoming release).', 'wp-job-manager-emails' ); ?>
		</div>
		<?php
	}

	function cpt(){
		return $this->cpt;
	}
}