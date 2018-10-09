<?php
    /**
     * Redux Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 2 of the License, or
     * any later version.
     * Redux Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package     ReduxFramework
     * @author      Dovy Paukstys
     * @version     3.1.5
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

// Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_tlm' ) ) {

        /**
         * Main ReduxFramework_tlm class
         *
         * @since       1.0.0
         */
        class ReduxFramework_tlm extends ReduxFramework {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            function __construct( $field = array(), $value = '', $parent ) {

                $this->parent   = $parent;
                $this->field    = $field;
                $this->value    = $value;

                $this->extension_url = plugins_url( '/', dirname(__FILE__) ); // Extention url

                // Set default args for this field to avoid bad indexes. Change this to anything you use.
                $defaults    = array(
                    'options'          => array(),
                    'stylesheet'       => '',
                    'output'           => true,
                    'enqueue'          => true,
                    'enqueue_frontend' => true
                );
                $this->field = wp_parse_args( $this->field, $defaults );

            }

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function render() {

                // No errors please
                $defaults = array(
                    'full_width' => true,
                    'overflow'   => 'inherit',
                );

                $this->field = wp_parse_args( $this->field, $defaults );

                $bDoClose = false;

                $id = $this->parent->args['opt_name'] . '-' . $this->field['id'];

                ?>
                    <h4><?php esc_html_e( 'Theme verification', 'redux-framework' ); ?></h4>
                    <div id="redux-tlm-code-wrapper">
                        <p class="description" id="tlm-code-description">
                            <?php echo esc_html( apply_filters( 'redux-tlm-description', __( 'Enter item purchase code:', 'redux-framework' ) ) ); ?>
                        </p>
                        <div class="register-theme-form">
                            <input type="text" name="<?php echo $this->parent->args['opt_name']; ?>[tlm]" id="purchase_code_verification" value="<?php if(isset($this->parent->options['tlm'])){echo $this->parent->options['tlm'];}else{echo '1234567890';} ?>" class="regular-text ">
                            <a href="javascript:void(0);" id="validation_activate" class="validation_activate_buttons" data-verify="0">Register the code</a> 
                            <a href="javascript:void(0);" id="validation_deactivate" class="validation_activate_buttons" data-verify="1">Deregister the code</a> 
                            <a href="#popup_license" id="popup_license_button">popup license</a>
                        </div>
                    </div>

                <?php
            }

            /**
             * Enqueue Function.
             * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function enqueue() {

                wp_enqueue_script(
                    'redux-tlm-remodal',
                    $this->extension_url . 'tlm/remodal.js',
                    array( 'jquery' ),
                    ReduxFramework_extension_tlm::$version,
                    true
                );

                wp_enqueue_script(
                    'redux-tlm',
                    $this->extension_url . 'tlm/field_tlm.js',
                    array( 'jquery' ),
                    ReduxFramework_extension_tlm::$version,
                    true
                );

                wp_enqueue_style(
                    'redux-tlm-remodal-default',
                    $this->extension_url . 'tlm/remodal-default-theme.css',
                    time(),
                    true
                );

                wp_enqueue_style(
                    'redux-tlm-remodal',
                    $this->extension_url . 'tlm/remodal.css',
                    time(),
                    true
                );

                wp_enqueue_style(
                    'redux-tlm',
                    $this->extension_url . 'tlm/field_tlm.css',
                    time(),
                    true
                );

            }

            /**
             * Output Function.
             * Used to enqueue to the front-end
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function output() {

                if ( $this->field['enqueue_frontend'] ) {

                }

            }

        }
    }
