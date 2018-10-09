<?php
if (!class_exists('knowhere_sidebar_generator')) {

	class knowhere_sidebar_generator extends Knowhere_Widgets_Meta_Box {
		public $sidebars  = array();
		public $stored = "knowhere_sidebars";
		public $paths  = array();

		function __construct() {
			$this->paths['js'] = parent::$pathes['BASE_URI'] . 'assets/js/';
		    $this->paths['css'] = parent::$pathes['BASE_URI'] . 'assets/css/';

		    $this->title = esc_html__('KnowherePro Custom Widget Area', 'knowherepro');

			add_action('load-widgets.php', array($this, 'enqueue_assets') , 4);
			add_action('load-widgets.php', array($this, 'add_sidebar'), 99);

			add_action('widgets_init', array($this, 'registerSidebars') , 900 );

			// ajax
			add_action('wp_ajax_delete_custom_sidebar', array($this, 'delete_sidebar') , 50);
		}

		public function registerSidebars() {

			if ( empty($this->sidebars) ) {
				$this->sidebars = get_option($this->stored);
			}

			$before_widget = '<div id="%1$s" class="widget %2$s">';

			$args = array(
				'before_widget' => $before_widget,
				'after_widget' => '</div>',
				'before_title' => '<h3 class="kw-widget-title">',
				'after_title' => '</h3>'
			);

			if ( is_array($this->sidebars) ) {
				foreach ($this->sidebars as $sidebar) {
					$args['class'] = 'knowhere-widget-custom';
					$args['name']  = $sidebar;
					$args['id']  = sanitize_title($sidebar);
					register_sidebar($args);
				}
			}
		}

		public function registerSidebar($args) {
			if ( is_array($this->sidebars) ) {
				foreach ( $this->sidebars as $sidebar ) {
					$args['class'] = 'knowhere-widget-custom';
					$args['name']  = $sidebar;
					register_sidebar($args);
				}
			}
		}

		public function enqueue_assets() {

			if ( !current_user_can('edit_theme_options') ) return;

			add_action( 'admin_enqueue_scripts', array($this, 'add_field') );
			wp_enqueue_script( 'knowhere_custom_sidebar_js' , $this->paths['js'] . 'custom_sidebar.js');
			wp_enqueue_style( 'knowhere_custom_sidebar_css' , $this->paths['css'] . 'custom_sidebar.css');
		}

		public function add_field() {
            $output = "\n<script type='text/html' id='knowhere-tmpl-add-widget'>";
			$output .= "\n  <form class='knowhere-form-add-widget' method='POST'>";
			$output .= "\n  <h3>". $this->title ."</h3>";
			$output .= "\n    <p><input size='30' type='text' value='' placeholder = '". esc_html__('Enter Name for new Widget Area', 'knowherepro') ."' name='knowhere-form-add-widget' /></p>";
			$output .= "\n    <input class='button button-primary' type='submit' value='". esc_html__('Add Widget Area', 'knowherepro') ."' />";
			$output .= "\n    <input type='hidden' name='knowhere-custom-sidebar-nonce' value='". wp_create_nonce('knowhere-custom-sidebar-nonce') ."' />";
			$output .= "\n  </form>";
			$output .= "\n</script>\n";
			echo $output;
		}

		public function add_sidebar() {

			if ( !current_user_can('edit_theme_options') ) return;

            if ( !empty($_POST['knowhere-form-add-widget']) ) {
                $this->sidebars = get_option($this->stored);
                $name = $this->get_name($_POST['knowhere-form-add-widget']);
                if ( empty($this->sidebars) ) {
                    $this->sidebars = array($name);
                } else {
                    $this->sidebars = array_merge($this->sidebars, array($name));
                }
                update_option($this->stored, $this->sidebars);
                wp_redirect(admin_url('widgets.php'));
                die();
            }
		}

		public function delete_sidebar() {

            check_ajax_referer('knowhere-custom-sidebar-nonce');

			if ( empty($_POST['name']) ) return;

			$name = stripslashes($_POST['name']);
			$this->sidebars = get_option($this->stored);

			if ( ( $key = array_search($name, $this->sidebars) ) !== false ) {
				unset($this->sidebars[$key]);
				update_option($this->stored, $this->sidebars);
			}

			die('widget-deleted');
		}

		public function get_name($name) {
			global $wp_registered_sidebars;
			$take = array();

			if ( empty($this->sidebars) ) $this->sidebars = array();
			if ( empty($wp_registered_sidebars) ) return $name;

            foreach ($wp_registered_sidebars as $sidebar) {
				$take[] = $sidebar['name'];
		    }
			$take = array_merge($take, $this->sidebars);

		    if ( in_array($name, $take) ) {

                 $counter = substr($name, -1);

                if ( !is_numeric($counter) )  {
					$newName = $name . " 1";
                } else {
					$newName = substr($name, 0, -1) . ((int) $counter + 1);
                }
                $name = $this->get_name($newName);
		    }
		    return $name;
		}

	}

	new knowhere_sidebar_generator();

}









