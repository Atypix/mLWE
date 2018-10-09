<?php

/*
 * Plugin Admin Settings
 */


add_action( 'admin_menu', 'ga_events_menu');

function ga_events_menu() {
	add_menu_page('WP Google Analytics Settings','WP GA Events','manage_options', 'wp-google-analytics-events', 'ga_events_settings_page', plugins_url( 'images/icon.png', dirname(__FILE__)));
	add_submenu_page('wp-google-analytics-events','General Settings','General Settings', 'manage_options', 'wp-google-analytics-events' , 'ga_events_settings_page' );
	add_submenu_page('wp-google-analytics-events','Click Tracking','Click Tracking', 'manage_options', 'wp-google-analytics-events-click' , 'ga_events_settings_page' );
	add_submenu_page('wp-google-analytics-events','Scroll Tracking','Scroll Tracking', 'manage_options', 'wp-google-analytics-events-scroll' , 'ga_events_settings_page' );
	add_submenu_page('wp-google-analytics-events','Getting Started Guide','Getting Started Guide', 'manage_options', 'wp-google-analytics-events-started' , 'ga_events_settings_page' );
	add_submenu_page('wp-google-analytics-events',"What's New","What's New", 'manage_options', 'wp-google-analytics-events-whatsnew' , 'ga_events_settings_page' );
	add_submenu_page('wp-google-analytics-events','Upgrade','Upgrade Now', 'manage_options', 'wp-google-analytics-events-upgrade', 'ga_events_settings_page' );
}

function ga_events_settings_page() {
$active_page = isset( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : 'wp-google-analytics-events';
$wpgae_main_class = "ga_main_full_width";
$show_sidebar = false;
if ($active_page == 'wp-google-analytics-events') {
	$wpgae_main_class= "ga_main_general_width";
	$show_sidebar = true;
}
?>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<div id="ga_mainwrap">
	<div id="ga_main" class="wrap <?php echo $wpgae_main_class; ?>">
		<?php screen_icon( 'plugins' ); ?>
		<h2>GA Scroll Events Plugin</h2>

		<h2 class="nav-tab-wrapper">
			<a href="?page=wp-google-analytics-events" class="nav-tab <?php echo $active_page == 'wp-google-analytics-events' ? 'nav-tab-active' : ''; ?>">General Settings</a>
			<a href="?page=wp-google-analytics-events-click" class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-click' ? 'nav-tab-active' : ''; ?>">Click Tracking</a>
			<a href="?page=wp-google-analytics-events-scroll" class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-scroll' ? 'nav-tab-active' : ''; ?>">Scroll Tracking</a>
			<a href="?page=wp-google-analytics-events-started" class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-started' ? 'nav-tab-active' : ''; ?>"><i class="fa fa-question-circle ga-events-help"></i> Getting Started Guide</a>
			<a href="?page=wp-google-analytics-events-whatsnew&wpgae_whatsnew_notify=1" class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-whatsnew' ? 'nav-tab-active' : ''; ?>"><i class="fa fa-question-circle ga-events-help"></i> What's New</a>
		</h2>
		<?php
		if ($active_page == 'wp-google-analytics-events-started') {
			do_settings_sections('ga_events_started');

		}
		else if ($active_page == 'wp-google-analytics-events-whatsnew') {
			do_settings_sections('ga_events_whatsnew');
		}
		else {
		?>

		<form id="ga-events-settings-form" method="post" action='options.php'>
			<?php settings_fields('ga_events_options'); ?>
			<?php
			if ($active_page == 'wp-google-analytics-events-click') {
				do_settings_sections('ga_events_click');
			} else if ($active_page == 'wp-google-analytics-events-scroll') {
				do_settings_sections('ga_events_scroll');
			}else {
				do_settings_sections('ga_events');
			}
			?>

			<?php
			if ($active_page == 'wp-google-analytics-events') {
				?>
				<input class="button-primary" type="submit" name="submit" value="Save Changes" />
				<?php
			}
			?>
		</form>
		<div class="settings_content">
			<form action="" method="post" enctype="multipart/form-data">
				<a href="#" class="btn_close"><img src="<?=plugins_url( 'images/close.png', dirname(__FILE__))?>"></a>
				<input type="file" name="settings">
				<input type="submit" name="set_settings">
			</form>
		</div>
	</div>
	<?php
	if ($show_sidebar) {
		?>
		<div class="wrap ga_events_banner ga_events_sidebar">
			<table class="form-table widefat" >
				<thead>
				<th>Need More Features?</th>
				</thead>
				<tbody>
				<tr class="features">
					<td>
						<ul>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>Link Tracking</strong></li>
							<li title="Dynamic Event Data"><i  class="fa fa-check-square-o fa-lg"></i><strong>Placeholders</strong></li>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>YouTube Video Tracking</strong></li>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>Vimeo Video support</strong></li>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>Allow non-admin users to manage the plugin</strong></li>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>HTML Tag support</strong></li>
							<li><i class="fa fa-check-square-o fa-lg"></i><strong>Pro Support</strong></li>
						</ul>
					</td>
				</tr>
				<tr class="tfoot">
					<td>
						<div class="wpcta">
							<a class="button-primary button-large" target="_blank" href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=genreal">
									<span class="btn-title ">
										Upgrade Now
									</span>
							</a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

		</div>
	<?php } else {
	?>
	<div class="wrap ga_events_banner ga_events_top">
		<div class="ga_events_featurebox ga_events_box_general">
			<div class="ga_events_box_title">
				<span>
					Become a Pro
				</span>
			</div>
			<div class="ga_events_box_body">
				<ul class="ga_events_box_list">
					<li>
						<div class="ga_events_box_li_icon">
							<img src="<?php echo plugins_url( 'images/icon_block.png', dirname(__FILE__)) ?>" />
						</div>
						<div class="ga_events_box_li_content">
							<span class="ga_events_box_li_title">Link Tracking</span>
							<span class="ga_events_box_li_txt">Automatically track any link on your website</span>
						</div>
					</li>
					<li>
						<div class="ga_events_box_li_icon">
							<img src="<?php echo plugins_url( 'images/icon_block.png', dirname(__FILE__)) ?>" />
						</div>
						<div class="ga_events_box_li_content">
							<span class="ga_events_box_li_title">Placeholder Variables</span>
							<span class="ga_events_box_li_txt">Include dynamic information in your events</span>
						</div>
					</li>
					<li>
						<div class="ga_events_box_li_icon">
							<img src="<?php echo plugins_url( 'images/icon_block.png', dirname(__FILE__)) ?>" />
						</div>
						<div class="ga_events_box_li_content">
							<span class="ga_events_box_li_title">User Permissions</span>
							<span class="ga_events_box_li_txt">Allow non Administrators to manage the plugin</span>
						</div>
					</li>
				</ul>
			</div>
			<div>
				<a class="btn" href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=btng">Go Pro</a>
			</div>
		</div>
		<div class="ga_events_featurebox ga_events_box_video">
			<div class="ga_events_box_title">
				<span>
					Track Videos like a Pro
				</span>
			</div>
			<div class="ga_events_box_body">
				<ul class="ga_events_box_list">
					<li>
						<div class="ga_events_box_li_icon">
							<img src="<?php echo plugins_url( 'images/icon_lock.png', dirname(__FILE__)) ?>" />
						</div>
						<div class="ga_events_box_li_content">
							<span class="ga_events_box_li_title">YouTube Video Tracking</span>
							<span class="ga_events_box_li_txt">Track all video play/stop events and make
smarter segments on how people watch your content.</span>
						</div>
					</li>
					<li>
						<div class="ga_events_box_li_icon" style="margin-top:10px">
							<img src="<?php echo plugins_url( 'images/icon_lock.png', dirname(__FILE__)) ?>" />
						</div>
						<div class="ga_events_box_li_content" style="margin-top:10px">
							<span class="ga_events_box_li_title">Vimeo Video support</span>
							<span class="ga_events_box_li_txt">And yes, you can also track Vimeo videos</span>
						</div>
					</li>
				</ul>
			</div>
			<a class="btn" href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=btnv">Go Premium</a>
		</div>
		<div class="ga_events_featurebox ga_events_box_support">
			<div class="ga_events_box_title">
				<span>
					Product Support
				</span>
			</div>
			<div class="ga_events_box_body">
				<ul class="ga_events_box_list">
					<li>
						<div class="ga_events_box_li_content">
							<span class="ga_events_box_li_title" style="margin-left:40px;">Premium Support</span>
							<span class="ga_events_box_li_txt" style="margin-left:40px;">Direct super-fast help from our dedicated support team</span>
						</div>
					</li>
			</ul>
			</div>
			<a class="btn" href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=btns">Get Pro Support</a>
		</div>
	</div>
 	<?php } ?>
</div> <!-- END #ga_mainwrap ->
		<?php
} ?>


<?php

echo "<script>
			jQuery('.remove').click(function (event) {
				event.preventDefault();
				jQuery(this).closest('tr').remove();
			});
			jQuery('.add').click(function (event) {
				event.preventDefault();
			});
		  </script>
	";
}

function load_custom_wp_admin_style() {
	wp_register_style( 'wpgae-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
	wp_register_style( 'custom_wp_admin_css', plugins_url('css/style.css', dirname(__FILE__)), array('wpgae-font-awesome'));
	wp_enqueue_style( 'custom_wp_admin_css' );

	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'advaced' => $advanced,
		'ajaxnonce' => wp_create_nonce('wpgae_nonce_CRX0XDPfqe5dd3P')
	);
	wp_register_script( 'wpgae-ajax', plugins_url('js/ajax.js', dirname(__FILE__)) , array('jquery') );
	wp_localize_script( 'wpgae-ajax', 'wpgae_ajax', $params);

	wp_enqueue_script( 'admin-init', plugins_url('js/admin.js', dirname(__FILE__)) , array('jquery','jquery-ui-tooltip','wpgae-ajax'), null, true );
}

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

add_action('admin_init', 'ga_events_admin_init');

function ga_events_admin_init() {
	if(isset($_GET['download']) && isset($_GET['page'])){
		if ($_GET['page'] == 'wp-google-analytics-events') {
			ga_events_file();
		}
	}

	if(isset($_POST['set_settings'])){
		ga_events_upload_settings($_FILES);
	}


	register_setting('ga_events_options','ga_events_options','ga_events_validate');
	add_settings_section('ga_events_main','WP Google Analytics Events Settings', 'ga_events_section_text','ga_events');
	add_settings_section('ga_events_click_section','', 'ga_events_section_text','ga_events_click');
	add_settings_section('ga_events_scroll_section','', 'ga_events_section_text','ga_events_scroll');
	add_settings_section('ga_events_started_section','Getting Started Guide', 'ga_events_section_text','ga_events_started');
	add_settings_section('ga_events_whatsnew_section','', 'ga_events_whatsnew_section_content','ga_events_whatsnew');
	add_settings_field('ga_events_id', '','ga_events_setting_input','ga_events','ga_events_main');
	add_settings_field('ga_events_exclude_snippet', '','ga_events_setting_snippet_input','ga_events','ga_events_main');
	add_settings_field('ga_events_gtm', '', 'ga_events_setting_gtm_input', 'ga_events', 'ga_events_main');
	add_settings_field('ga_events_gst', '', 'ga_events_setting_gst_input', 'ga_events', 'ga_events_main');
	add_settings_field('ga_events_universal', '','ga_events_setting_uni_input','ga_events','ga_events_main');
	add_settings_field('ga_events_anonymizeip', '','ga_events_setting_anon_input','ga_events','ga_events_main');
	add_settings_field('ga_events_advanced', '','ga_events_setting_adv_input','ga_events','ga_events_main');
	add_settings_field('ga_events_divs', '','ga_events_setting_divs_input','ga_events_scroll','ga_events_scroll_section');
	add_settings_field('ga_events_started', '','ga_events_setting_started','ga_events_started','ga_events_started_section');
	add_settings_field('ga_events_click', '','ga_events_setting_click_input','ga_events_click','ga_events_click_section');
	add_settings_field('ga_events_sidebar', '','ga_events_setting_sidebar','ga_events','ga_events_main');
	add_settings_field('ga_events_download_settings', '','ga_events_settings_download','ga_events','ga_events_main');
	add_settings_field('ga_events_upload_settings', '','ga_events_settings_upload','ga_events','ga_events_main');


}

function ga_events_section_text() {
	echo "<br><a style='margin-left:8px;' href='http://wpflow.com/documentation' target='_blank'>Plugin Documentation</a>";
}

function ga_events_whatsnew_section_content() {
	ob_start(); ?>
    <div class="wpgae-container wpgae-container__warning">
   <h3><span class="dashicons dashicons-flag"></span> What's New (1)</h3>
   <div id="wpgae-warnings">
      <p></p>
      <div class="container" id="wpgae-warnings-active">
         <div class="wpgae-alert-holder">
            <div class="wpgae-alert">
					  	<p>Global Site Tag code support. Google recently added the new Global Site Tag feature, and made it the default tracking code. If you migrated to this new code, check the relevant option in the General Settings tab.
            </div>
         </div>
         <div class="wpgae-alert-holder">
            <div class="wpgae-alert">
               <p>The admin UI got a little update - We started working with ajax forms. These changes solve a problem that some of the users had with adding a long list of events.</p>
            </div>
         </div>
         <div class="wpgae-alert-holder">
            <div class="wpgae-alert">
               <p>We added the value field to click and scroll events. This used to be a Pro version feature and now it is part of this plugin as well.</p>
            </div>
         </div>
         <div class="wpgae-alert-holder">
            <div class="wpgae-alert">
               <p>We added support for the <strong>Google Tag Manager</strong>.
                   <br>This feature requires some preparation on the Google Tag Manager side so make sure to <a target="_blank" href="https://wpflow.com/knowledgebase/google-tag-manager-support/">check out the video guide</a>.</p>
            </div>
         </div>
      </div>
      <div class="container" id="wpgae-warnings-dismissed">
      </div>
   </div>
</div>
<?php
	$content = ob_get_clean();
	echo $content;
}

function ga_events_setting_started() {
	echo '
		  <h2>Getting Started Guide</h2>
		 <form action="https://www.getdrip.com/forms/4588171/submissions" method="post" data-drip-embedded-form="4588171">
						  <div style="background:white; line-height:20px; padding: 5px 15px 15px 15px;
 font-size: 15px; max-width:600px;">

			 <h3 style="margin-top: 10px;" data-drip-attribute="headline">Want to learn more about event tracking?</h3>
			 <div data-drip-attribute="description">Now that you installed the plugin, we want to help you get everything up and running.&nbsp;<br />
				 <br>Join our short email course and get started with event tracking.</div>
			 <div style="margin-top:10px;">
        <label for="fields[first_name]">First Name</label><br />
        <input type="text" name="fields[first_name]" value="" />
    	</div>
			 <div>
				 <label style="margin-top:10px;"for="fields[email]">Email Address:</label><br />
				 <input type="email" name="fields[email]" value="" />
			 </div>
			 <div>
				<input style="margin-top:15px;" class="button-primary" type="submit" name="submit" value="Get Started" data-drip-attribute="sign-up-button" />
			 </div>
			</div>
		 </form>';
}

function ga_events_setting_input() {
	$options = get_option('ga_events_options');
	$id = $options['id'];
	echo "<label>Google Analytics Identifier</label>";
	echo "<span class='ga_intable'><input class='inputs' id='id' name='ga_events_options[id]' type='text' value='$id' /></span>";

}


function ga_events_setting_snippet_input() {
	$options = get_option('ga_events_options');
	$id = $options['exclude_snippet'];
	echo "<label>Don't add the GA tracking code ".ga_tooltip('Useful if you already have the code snippet loaded by a different plugin')."</label>";
	echo "<span class='ga_intable'><input id='snippet' name='ga_events_options[exclude_snippet]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></span>";

}

function ga_events_setting_gtm_input() {
	$options = get_option('ga_events_options');
	if (isset($options['gtm'])) {
		$id = $options['gtm'];
	} else {
		$id = 0;
	}
	echo "<label >I'm using the Google Tag Manager " . ga_tooltip('Make sure to configure on the GTM side as well') . " </label>";

	echo "<input style='margin-left: 5px;' id='gtm' name='ga_events_options[gtm]' type='checkbox' value='1' " . checked($id, 1, false) . " />";
	echo "<a style='margin-left: 5px;' href='https://wpflow.com/knowledgebase/google-tag-manager-support/' target='_blank'>Read More...</a>";
}

function ga_events_setting_gst_input() {
	$options = get_option('ga_events_options');
	if (isset($options['gst'])) {
		$id = $options['gst'];
	} else {
		$id = 0;
	}
	echo "<label >I'm using the Global Site Tags code " . ga_tooltip('Support for the Global Site Tag - Not to be confused with the Google Tag Manager') . " </label>";
	echo "<input style='margin-left: 5px;' id='gst' name='ga_events_options[gst]' type='checkbox' value='1' " . checked($id, 1, false) . " />";
}

function ga_events_setting_uni_input() {
	$options = get_option('ga_events_options');
	$id = $options['universal'];
	echo "<label>Universal Tracking Code</label>";
	echo "<span class='ga_intable'><input id='universal' name='ga_events_options[universal]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></span>";
}

function ga_events_setting_anon_input() {
	$options = get_option('ga_events_options');
	$id = $options['anonymizeip'];
	echo "<label>IP Anonymization".ga_tooltip('Tell Google Analytics not to log IP Addresses. Requires code snippet to be checked')."</label>";
	echo "<span class='ga_intable'><input id='anonymizeip' name='ga_events_options[anonymizeip]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></span>";
}

function ga_events_setting_adv_input() {
	$options = get_option('ga_events_options');
	$id = $options['advanced'];
	echo "<label>Advanced Mode ".ga_tooltip('Enable Advanced Selectors')."</label>";
	echo "<span class='ga_intable'><input id='advanced' name='ga_events_options[advanced]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></span>";
}

function ga_events_settings_download(){
	echo '<a class="button" href="http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .'&download=1">Export settings</a>';
}

function ga_events_settings_upload(){
	echo '<a href="#" class="button btn_upload">Import settings</a>';
}

function ga_events_setting_divs_input() {
	$options = get_option('ga_events_options');
	$divs= $options['divs'];

	$menu_options = array(
		'id' => 'id',
		'class' => 'class',
	);

	if(is_advanced_mode()){
		$menu_options['advanced'] = 'advanced'; // if enabled, add 'avanced' on the menu
	}

	$type='divs';

	echo "<table id='ga-events-inputs' class='widefat inputs inner_table divs_table' data-name='divs'><thead><th>Element Name</th><th>Type</th><th>Event Category</th><th>Event Action</th><th>Event Label</th><th>Event Value</th><th>Non-Interaction</th><th></th></thead><tbody>";
	if (!($divs[0][0]) && !($divs[0][1])){
		$name = "ga_events_options[$type][0][1]";
		$type_menu = createDropdown($name, $type, $menu_options,'id');

		echo "<tr>";
		echo "<td data-title='Element Name'><input id='divs' name='ga_events_options[divs][0][0]' type='text' value='".$divs[0][0]."' /></td>";
		echo "<td data-title='Type'>$type_menu</td>";
		echo "<td data-title='Event Category'><input id='divs' name='ga_events_options[divs][0][2]' type='text' value='".$divs[0][2]."' /></td>";
		echo "<td data-title='Event Action'><input id='divs' name='ga_events_options[divs][0][3]' type='text' value='".$divs[0][3]."' /></td>";
		echo "<td data-title='Event Label'><input id='divs' name='ga_events_options[divs][0][4]' type='text' value='".$divs[0][4]."' /></td>";

		echo "<td data-title='Event Value'><input id='click' name='ga_events_options[divs][0][6]' type='number' value='".$divs[0][6]."' /></td>";

		echo "<td data-title='Non-Interaction'><select id='".$type."' name='ga_events_options[".$type."][$i][5]'>";
		if ($divs[$i][5] == 'true') {
			echo "<option selected value='true' >true</option><option value='false'>false</option></select></td>";
		} else {
			echo "<option  value='true' >true</option><option selected value='false'>false</option></select></td>";
		}
		echo "<td><a class='btn-add' href='#'>Add</a></td>";
		echo "</tr>";

	}else{
		for ($i = 0; $i < sizeof($divs)+1; $i++){
			$name = "ga_events_options[$type][$i][1]";
			$selected = $divs[$i][1];
			$type_menu = createDropdown($name, $type, $menu_options, $selected);
			echo "<tr>";
			echo "<td data-title='Element Name'><input id='divs' name='ga_events_options[divs][$i][0] type='text' value='".$divs[$i][0]."' /></td>";
			echo "<td data-title='Type'>$type_menu</td>";
			echo "<td data-title='Event Category'><input id='divs' name='ga_events_options[divs][$i][2]' type='text' value='".$divs[$i][2]."' /></td>";
			echo "<td data-title='Event Action'><input id='divs' name='ga_events_options[divs][$i][3]' type='text' value='".$divs[$i][3]."' /></td>";
			echo "<td data-title='Event Label'><input id='divs' name='ga_events_options[divs][$i][4]' type='text' value='".$divs[$i][4]."' /></td>";
			echo "<td data-title='Event Value'><input id='click' name='ga_events_options[divs][$i][6]' type='number' value='".$divs[$i][6]."' /></td>";
			echo "<td data-title='Non-Interaction'><select id='".$type."' name='ga_events_options[".$type."][$i][5]'>";
			if ($divs[$i][5] == 'true') {
				echo "<option selected value='true' >true</option><option value='false'>false</option></select></td>";
			} else {
				echo "<option  value='true' >true</option><option selected value='false'>false</option></select></td>";
			}

			if($divs[$i][0] || $divs[$i][1]){
				echo "<td><a class='btn-update' href=''><i class='fa fa-floppy-o' title='Update' aria-hidden='true'></i></a></td>";
				echo "<td><a class='btn-remove' href=''><i class='fa fa-times' title='Remove' aria-hidden='true'></i></a></td>";
			}else{
				echo "<td><a class='btn-add' href='#'>Add</a></td>";
			}

			echo "</tr>";

		}

	}
	echo "</tbody></table>";
}


function ga_events_setting_click_input() {
	$options = get_option('ga_events_options');
	$click = $options['click'];
	$divs= $options['click'];

	$menu_options = array(
		'id' => 'id',
		'class' => 'class',
	);

	if(is_advanced_mode()){
		$menu_options['advanced'] = 'advanced'; // if enabled, add 'avanced' on the menu
	}

	$type='click';


	echo "<table id='ga-events-inputs' class='widefat inputs inner_table click_table' data-name='click'><thead><th>Element Name</th><th>Type</th><th>Event Category</th><th>Event Action</th><th>Event Label</th><th>Event Value</th><th>Non-Interaction</th><th></th></thead><tbody>";
	if (!($click[0][0]) && !($click[0][1])){
		$name = "ga_events_options[click][0][1]";
		$type_menu = createDropdown($name, $type, $menu_options,'id');

		echo "<tr>";
		echo "<td data-title='Element Name'><input id='click' name='ga_events_options[click][0][0]' type='text' value='".$click[0][0]."' /></td>";
		echo "<td data-title='Type'>$type_menu</td>";
		echo "<td data-title='Event Category'><input id='click' name='ga_events_options[click][0][2]' type='text' value='".$click[0][2]."' /></td>";
		echo "<td data-title='Event Action'><input id='click' name='ga_events_options[click][0][3]' type='text' value='".$click[0][3]."' /></td>";
		echo "<td data-title='Event Label'><input id='click' name='ga_events_options[click][0][4]' type='text' value='".$click[0][4]."' /></td>";

		echo "<td data-title='Event Value'><input id='click' name='ga_events_options[click][0][6]' type='number' value='".$click[0][6]."' /></td>";

		echo "<td data-title='Non-Interaction'><select id='".$type."' name='ga_events_options[".$type."][$i][5]'>";
		if ($divs[$i][5] == 'true') {
			echo "<option selected value='true' >true</option><option value='false'>false</option></select></td>";
		} else {
			echo "<option  value='true' >true</option><option selected value='false'>false</option></select></td>";
		}


		echo "<td><a class='btn-add' href='#'>Add</a></td>";
		echo "</tr>";

	}else{
		for ($i = 0; $i < sizeof($click)+1; $i++){
			$name = "ga_events_options[click][$i][1]";
			$selected = $click[$i][1];
			$type_menu = createDropdown($name, $type, $menu_options, $selected);

			echo "<tr>";
			echo "<td data-title='Element Name'><input id='divs' name='ga_events_options[click][$i][0]' type='text' value='".$click[$i][0]."' /></td>";
			echo "<td data-title='Type'>$type_menu</td>";
			echo "<td data-title='Event Category'><input id='click' name='ga_events_options[click][$i][2]' type='text' value='".$click[$i][2]."' /></td>";
			echo "<td data-title='Event Action'><input id='click' name='ga_events_options[click][$i][3]' type='text' value='".$click[$i][3]."' /></td>";
			echo "<td data-title='Event Label'><input id='click' name='ga_events_options[click][$i][4]' type='text' value='".$click[$i][4]."' /></td>";

			echo "<td data-title='Event Value'><input id='click' name='ga_events_options[click][$i][6]' type='number' value='".$click[$i][6]."' /></td>";

			echo "<td data-title='Non-Interaction'><select id='".$type."' name='ga_events_options[".$type."][$i][5]'>";
			if ($divs[$i][5] == 'true') {
				echo "<option selected value='true' >true</option><option value='false'>false</option></select></td>";
			}
			else {
				echo "<option  value='true' >true</option><option selected value='false'>false</option></select></td>";
			}


			if($click[$i][0] || $click[$i][1]){
				echo "<td><a class='btn-update' href=''><i class='fa fa-floppy-o' title='Update' aria-hidden='true'></i></a></td>";
				echo "<td><a class='btn-remove' href=''><i class='fa fa-times' title='Remove' aria-hidden='true'></i></a></td>";
			}else{
				echo "<td><a class='btn-add' href='#'>Add</a></td>";
			}

			echo "</tr>";

		}

	}
	echo "</tbody></table>";


}

function ga_events_setting_sidebar(){
}

function ga_events_validate($form){

	$options = get_option('ga_events_options');
	$updated = $options;

	if( array_key_exists('divs', $form)) {

		$updated['divs'] = array();
		$divFields = array_values($form['divs']); //force array index to start with 0
		for ($i = 0, $j = 0; $i< sizeof($divFields); $i++){
			if ($divFields[$i][0]){
				$updated['divs'][$j] = cleanEventFeilds($divFields[$i]);
				$j++;
			}
		}
	}
	else if(array_key_exists('click', $form)) {
		$updated['click'] = array();
		$clickFields = array_values($form['click']); //force array index to start with 0
		for ($i = 0, $j = 0; $i< sizeof($clickFields); $i++){
			if ($clickFields[$i][0]){
				$updated['click'][$j] = cleanEventFeilds($clickFields[$i]);
				$j++;
			}
		}
	}
	else {
		$updated['id'] = $form['id'];
		$updated['exclude_snippet'] = $form['exclude_snippet'];
		$updated['universal'] = $form['universal'];
		$updated['anonymizeip'] = $form['anonymizeip'];
		$updated['advanced'] = $form['advanced'];
		$updated['gtm'] = $form['gtm'];
		$updated['gst'] = $form['gst'];
	}

	return $updated;
}


add_action('admin_footer', 'ga_events_admin_footer');

function ga_events_admin_footer() {
	?>
	<script>
		jQuery('body').on('click','a[href="admin.php?page=wp-google-analytics-events-upgrade"]', function (e) {
					e.preventDefault();
					window.open('https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=nav', '_blank');
				});
	</script>
	<?php
}


function ga_events_get_settings(){
	$options = get_option('ga_events_options');
	$current = json_encode($options);
	return $current;
}

function ga_events_upload_settings($file){
	$uploadedfile = $file['settings'];
	if($uploadedfile['type'] != 'application/octet-stream'){
		ga_event_popup();
		return;
	}
	$content = file_get_contents($uploadedfile["tmp_name"]);
	ga_event_get_content($content);
}

function ga_event_get_content($content){
	if(!$current = json_decode($content,true)){
		ga_event_popup();
		return;
	}
	if (!array_key_exists('id', $current) && !array_key_exists('domain', $current)) {
		ga_event_popup();
		return;
	}
	update_option( 'ga_events_options', $current );

}
function ga_event_popup(){
	echo "<dev class='popup'>";
	echo '<h1>Wrong file format <a href="#" class="btn_close_popup"><img src="'.plugins_url( 'images/close.png', dirname(__FILE__)).'"></a></h1>';
	echo "</dev>";
}
function ga_events_file(){
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename='settings.ini'");
	echo ga_events_get_settings();
	exit();
}

function cleanEventFeilds($arr) {
	if('advanced' == $arr[1]){
		$arr[0] = str_replace("'",'"',$arr[0]);
	}else{
		$arr[0] = str_replace("'","",$arr[0]);
	}

	for ($i = 1; $i < sizeof($arr); $i++) {
		$arr[$i] = esc_html($arr[$i]);
	}
	return $arr;
}

function cleanAjaxFeilds($data) {
	$newData = array();

	foreach( $data as $key => $value ) {

		$newData[$key] = htmlspecialchars( stripslashes($value) ,ENT_QUOTES, 'UTF-8');
	}

	return $newData;
}

function ga_tooltip($content = '') {
	$html = '<span class="ga-tooltip" title="'.$content.'"></span>';
	return $html;
}

?>
