<?php

/**
 * Enhanced Ecommerce for Woo-commerce stores
 *
 * Allows tracking code to be inserted into store pages.
 *
 * @class       WC_Enhanced_Ecommerce_Google_Analytics
 * @extends     WC_Integration
 * @author      Jigar Navadiya <jigar@tatvic.com>
 */
class WC_Enhanced_Ecommerce_Google_Analytics extends WC_Integration {

    /**
     * Init and hook in the integration.
     *
     * @access public
     * @return void
     */
    //set plugin version
    public $tvc_eeVer = '2.0.2';
    public function __construct() {
        
         //Set Global Variables
        global $homepage_json_fp,$homepage_json_ATC_link, $homepage_json_rp,$prodpage_json_relProd,$catpage_json,
               $prodpage_json_ATC_link,$catpage_json_ATC_link,$woocommerce;
       if (version_compare($woocommerce->version, "2.1", ">="))
        //define plugin ID       
        $this->id = "enhanced_ecommerce_google_analytics";
        $this->method_title = __("Enhanced Ecommerce Google Analytics", "enhanced-e-commerce-for-woocommerce-store");
        $this->method_description = __("Enhanced Ecommerce is a new feature of Universal Analytics that generates detailed statistics about the users journey from product page to thank you page on your e-store. <br/><a href='http://www.tatvic.com/blog/enhanced-ecommerce/' target='_blank'>Know more about Enhanced Ecommerce.</a><br/><br/><b>Quick Tip:</b> We also have an Advanced Google Analytics Plugin for WooCommerce! The plugin offers tracking of 9 Reports of Enhanced Ecommerce, User ID Tracking, Add Google Optimize Snippet, 15+ Custom Dimenensions & Metrics, Content Grouping & much more. <a href='https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic' target='_blank'>Learn More</a>", "woocommerce");

        //session for product position count
        //session_start inserted bcoz it gives warning
        if (session_status() == PHP_SESSION_NONE) {
          session_start();
        }
        $_SESSION['t_npcnt']=0;
        $_SESSION['t_fpcnt']=0;
        // Load the integration form
        $this->init_form_fields();
        //load all the settings
        $this->init_settings();

        // Define user set variables -- Always use short names   
        $this->tvc_aga = $this->get_option("tvc_aga"); 
        $this->ga_id = $this->get_option("ga_id");
        $this->ga_Dname = $this->get_option("ga_Dname");
        $this->ga_LC = get_woocommerce_currency(); //Local Currency yuppi! Got from Back end 
        //set local currency variable on all page
        $this->wc_version_compare("tvc_lc=" . json_encode($this->ga_LC) . ";");
        $this->ga_ST = $this->get_option("ga_ST");
        $this->ga_gCkout = $this->get_option("ga_gCkout") == "yes" ? true : false; //guest checkout
        $this->ga_gUser = $this->get_option("ga_gUser") == "yes" ? true : false; //guest checkout
        $this->ga_eeT = $this->get_option("ga_eeT");
        $this->ga_DF = $this->get_option("ga_DF") == "yes" ? true : false;
        $this->ga_imTh = $this->get_option("ga_imTh") == "" ? 6 : $this->get_option("ga_imTh"); 
        $this->ga_OPTOUT = $this->get_option("ga_OPTOUT") == "yes" ? true : false; //Google Analytics Opt Out
        $this->ga_PrivacyPolicy = $this->get_option("ga_PrivacyPolicy") == "yes" ? true : false;
        $this->ga_IPA = $this->get_option("ga_IPA") == "yes" ? true : false; //IP Anony.
        //Save Changes action for admin settings
        add_action("woocommerce_update_options_integration_" . $this->id, array($this, "process_admin_options"));
        
        // API Call to LS with e-mail
        // Tracking code
        add_action("wp_head", array($this, "ee_settings"));
       
        add_action("woocommerce_thankyou", array($this, "ecommerce_tracking_code"));

        // Enhanced Ecommerce product impression hook
        add_action("wp_footer", array($this, "t_products_impre_clicks"));
        
        add_action("woocommerce_after_shop_loop_item", array($this, "bind_product_metadata")); //for cat, shop, prod(related),search and home page
        add_action("woocommerce_after_single_product", array($this, "product_detail_view"));
        add_action("woocommerce_after_cart", array($this, "remove_cart_tracking"));
        add_action("woocommerce_before_checkout_billing_form", array($this, "checkout_step_1_tracking"));
        add_action("woocommerce_after_checkout_billing_form", array($this, "checkout_step_2_tracking"));
        add_action("woocommerce_after_checkout_billing_form", array($this, "checkout_step_3_tracking"));

        // Event tracking code
        add_action("woocommerce_after_add_to_cart_button", array($this, "add_to_cart"));
        
        //Enable display feature code checkbox 
        add_action("admin_footer", array($this, "admin_check_UA_enabled"));

        //add version details in footer
        add_action("wp_footer", array($this, "add_plugin_details"));
        
        //Add Dev ID
        add_action("wp_head", array($this, "add_dev_id"), 1);
        
         //Advanced Store data Tracking
        add_action("wp_footer", array($this, "tvc_store_meta_data"));
         
    }
    /**
     * Get store meta data for trouble shoot
     * @access public
     * @return void
     */
    function tvc_store_meta_data() {
        //only on home page
        global $woocommerce;
        $tvc_sMetaData = array();

            $tvc_sMetaData = array(
                'tvc_wcv' => $woocommerce->version,
                'tvc_wpv' => get_bloginfo('version'),
                'tvc_eev' => $this->tvc_eeVer,
                'tvc_cnf' => array(
                    't_ee' => $this->ga_eeT,
                    't_df' => $this->ga_DF,
                    't_gUser'=>$this->ga_gUser,
                    't_UAen'=>$this->ga_ST,
                    't_thr' => $this->ga_imTh,                  
                )
            );
            $this->wc_version_compare("tvc_smd=" . json_encode($tvc_sMetaData) . ";");        
    }

     /**
     * add dev id
     *
     * @access public
     * @return void
     */
    function add_dev_id() {
        echo "<script>(window.gaDevIds=window.gaDevIds||[]).push('5CDcaG');</script>";
    }

    /**
     * display details of plugin
     *
     * @access public
     * @return void
     */
    function add_plugin_details() {
        echo '<!--Enhanced Ecommerce Google Analytics Plugin for Woocommerce by Tatvic Plugin Version:'.$this->tvc_eeVer.'-->';
    }
    /**
     * check UA is enabled or not
     *
     * @access public
     */
    function admin_check_UA_enabled() {
        
       if(isset($_GET['tab']) && $_GET['tab'] =='integration' ){

        echo '<script>
               jQuery("#woocommerce_enhanced_ecommerce_google_analytics_ga_ST").change(function(){
                t_ga_chk=jQuery(this).is(":checked");
                
                if(t_ga_chk){
                   jQuery("#woocommerce_enhanced_ecommerce_google_analytics_ga_DF").removeAttr("disabled");
                }else{
                    jQuery("#woocommerce_enhanced_ecommerce_google_analytics_ga_DF").attr("disabled",true);
                    t_display_chk=jQuery("#woocommerce_enhanced_ecommerce_google_analytics_ga_DF").is(":checked");
                    if(t_display_chk){
                      jQuery("#woocommerce_enhanced_ecommerce_google_analytics_ga_DF").removeAttr("checked");
                    }                 }
                   });      
               //Pugin Promotion
                jQuery("form#mainform").after("<a href=https://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552?ref=tatvic target=_blank><img src='.plugins_url( '/aga_premium.png' , __FILE__ ).' title=Actionable Google Analytics Plugin by Tatvic alt=Actionable Google Analytics Plugin by Tatvic></a>");
                </script>';
        }
    }

    /**
     * Check if tracking is disabled
     *
     * @access private
     * @param mixed $type
     * @return bool
     */
    private function disable_tracking($type) {
        if (is_admin() || current_user_can("manage_options") || (!$this->ga_id ) || "no" == $type) {
            return true;
        }
    }

    /**
     * woocommerce version compare
     *
     * @access public
     * @return void
     */
    function wc_version_compare($codeSnippet) {
        global $woocommerce;
    
        if (version_compare($woocommerce->version, "2.1", ">=")) {
            wc_enqueue_js($codeSnippet);
        } else {
            $woocommerce->add_inline_js($codeSnippet);
        }
    }
    /**
     * Enhanced Ecommerce GA plugin Settings 
     *
     * @access public
     * @return void
     */
    function ee_settings() {
        global $woocommerce;

        //common validation----start
        if (is_admin() || current_user_can("manage_options") || $this->ga_ST == "no") {
            return;
        }
        $tracking_id = $this->ga_id;

        if (!$tracking_id || !$this->ga_PrivacyPolicy) {
            return;
        }
        //common validation----end

        if (!empty($this->ga_Dname)) {
            $set_domain_name = esc_js($this->ga_Dname);
        } else {
            $set_domain_name = "auto";
        }
        // IP Anonymization
        if ($this->ga_IPA) {
            $ga_ip_anonymization = 'gtag("config", "'.$tracking_id.'", { "anonymize_ip": true });';
        } else {
            $ga_ip_anonymization = '';
        }
        if($this->ga_OPTOUT) {
                echo '<script>
                // Set to the same value as the web property used on the site
                var gaProperty = "'.$tracking_id.'";
        
                // Disable tracking if the opt-out cookie exists.
                var disableStr = "ga-disable-" + gaProperty;
                if (document.cookie.indexOf(disableStr + "=true") > -1) {
                  window[disableStr] = true;
                }
        
                // Opt-out function
                function gaOptout() {
                var expDate = new Date;
                expDate.setMonth(expDate.getMonth() + 26);
                  document.cookie = disableStr + "=true; expires="+expDate.toGMTString();+"path=/";
                  window[disableStr] = true;
                }
                </script>';
        }
        $code = '<script async src="https://www.googletagmanager.com/gtag/js?id='.esc_js($tracking_id).'"></script>
                <script>
                  window.dataLayer = window.dataLayer || [];
                  function gtag(){dataLayer.push(arguments);}
                  gtag("js", new Date());
                  '.$ga_ip_anonymization.'
                  gtag("config", "'.esc_js($tracking_id).'",{"cookie_domain":"'.$set_domain_name.'"});
                </script>
                ';
            echo $code;
        }

    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
        $this->form_fields = array(
            "ga_id" => array(
                "title" => __("Google Analytics ID", "woocommerce"),
                "description" => __("Enter your Google Analytics ID here. You can login into your Google Analytics account to find your ID. e.g.<code>UA-XXXXX-X</code>", "woocommerce"),
                "type" => "text",
                "placeholder" => "UA-XXXXX-X",
                "desc_tip"  =>  true,
                "default" => get_option("ga_id") // Backwards compat
            ),
            "ga_Dname" => array(
                "title" => __("Set Domain Name", "woocommerce"),
                "description" => sprintf(__("Enter your domain name here (Optional)")),
                "type" => "text",
                "placeholder" => "",
                "desc_tip"  =>  true,
                "default" => get_option("ga_Dname") ? get_option("ga_Dname") : "auto"
            ),
            "ga_ST" => array(
                "title" => __("Tracking code", "woocommerce"),
                "label" => __("Add Global Site Tracking Code 'gtag.js' (Optional)", "woocommerce"),
                "description" => sprintf(__("This feature adds New gtag.js Tracking Code to your Store. You don't need to enable this if gtag.js implemented via a 3rd party analytics plugin.", "woocommerce")),
                "type" => "checkbox",
                "checkboxgroup" => "start",
                "desc_tip"  =>  true,
                "default" => get_option("ga_ST") ? get_option("ga_ST") : "no"  // Backwards compat
            ),
            "ga_eeT" => array(
                "label" => __("Add Enhanced Ecommerce Tracking Code", "woocommerce"),
                "type" => "checkbox",
                "checkboxgroup" => "",
                "desc_tip"  =>  true,
                "description" => sprintf(__("This feature adds Enhanced Ecommerce Tracking Code to your Store", "woocommerce")),
                "default" => get_option("ga_eeT") ? get_option("ga_eeT")  : "no"  // Backwards compat
            ),
            "ga_gUser" => array(
                "label" => __("Add Code to Track the Login Step of Guest Users (Optional)", "woocommerce"),
                "type" => "checkbox",
                "checkboxgroup" => "",
                "desc_tip"  =>  true,
                "description" => sprintf(__("If you have Guest Check out enable, we recommend you to add this code", "woocommerce")),
                "default" => get_option("ga_gUser") ? get_option("ga_gUser") : "no"  // Backwards compat
            ),
            "ga_imTh" => array(
                "title" => __("Impression Threshold", "woocommerce"),
                "description" => sprintf(__("This feature sets Impression threshold for category page. It sends hit after these many numbers of products impressions", "woocommerce")),
                "type" => "number",
                "desc_tip" =>  true,
                "css"=>"width:112px !important;",
                'custom_attributes' => array(
                'min' => "1",
                ),
                "default" => get_option("ga_imTh") ? get_option("ga_imTh") : "6"  // Backwards compat
            ),
            "ga_IPA" => array(
                "title" => __("IP Anonymization", "woocommerce"),
                "label" => __("Enable IP Anonymization (Optional)", "woocommerce"),
                "type" => "checkbox",
                "checkboxgroup" => "",
                "description" => sprintf(__("Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance", "woocommerce")),
                "default" => get_option("ga_IPA") ? get_option("ga_IPA") : "no"  // Backwards compat
            ),
            "ga_OPTOUT" => array(
                "title" => __("Google Analytics Opt Out", "woocommerce"),
                "label" => __("Enable Google Analytics Opt Out (Optional)", "woocommerce"),
                "type" => "checkbox",
                "checkboxgroup" => "",
                "description" => sprintf(__("Use this feature to provide website visitors the ability to prevent their data from being used by Google Analytics As per the GDPR compliance.Click here to check the setup", "woocommerce")),
                "default" => get_option("ga_OPTOUT") ? get_option("ga_OPTOUT") : "no"  // Backwards compat
            ),
            "ga_PrivacyPolicy" => array(
                "title" => __("Privacy Policy", "woocommerce"),
                "label" => __("Accept Privacy Policy of Plugin", "woocommerce"),
                "type" => "checkbox",
                "checkboxgroup" => "",
                'custom_attributes' => array(
                    'required' => "required",
                ),
                "description" => sprintf(__("By using Tatvic Plugin, you agree to Tatvic plugin's <a href='https://www.tatvic.com/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woocommerce_free_plugin&utm_campaign=GDPR_complaince_ecomm_plugins' target='_blank'>Privacy Policy</a>", "woocommerce")),
                "default" => get_option("ga_PrivacyPolicy") ? get_option("ga_PrivacyPolicy") : "no"  // Backwards compat
            ),
        );
    }
   
    /**
     * Google Analytics eCommerce tracking
     *
     * @access public
     * @param mixed $order_id
     * @return void
     */
    function ecommerce_tracking_code($order_id) {
        
        global $woocommerce;
        if ($this->disable_tracking($this->ga_eeT) || current_user_can("manage_options") || get_post_meta($order_id, "_tracked", true) == 1)
            return;

        $tracking_id = $this->ga_id;
        if (!$tracking_id)
            return;
        // Doing eCommerce tracking so unhook standard tracking from the footer
        remove_action("wp_footer", array($this, "ee_settings"));

        // Get the order and output tracking code
        $order = new WC_Order($order_id);
        //Get Applied Coupon Codes
        $coupons_list = '';
        if ($order->get_used_coupons()) {
            $coupons_count = count($order->get_used_coupons());
            $i = 1;
            foreach ($order->get_used_coupons() as $coupon) {
                $coupons_list .= $coupon;
                if ($i < $coupons_count)
                    $coupons_list .= ', ';
                $i++;
            }
        }

        //get domain name if value is set
        if (!empty($this->ga_Dname)) {
            $set_domain_name = esc_js($this->ga_Dname);
        } else {
            $set_domain_name = "auto";
        }

        // Order items
        if ($order->get_items()) {
            foreach ($order->get_items() as $item) {
                $_product = $order->get_product_from_item($item);
                $tvc_prnm = get_the_title($item['product_id']);
                if (isset($_product->variation_data)) {
                  $categories=esc_js(wc_get_formatted_variation($_product->get_variation_attributes(), true));
                  
                } else {
                    $out = array();
                    if(version_compare($woocommerce->version, "2.7", "<")){
                         $categories = get_the_terms($_product->id, "product_cat");
                    }else{
                         $categories = get_the_terms($_product->get_id(), "product_cat");
                    }

                    if ($categories) {
                        foreach ($categories as $category) {
                            $out[] = $category->name;
                        }
                    }
                    $categories=esc_js(join(",", $out));
                }
                //orderpage Prod json
                if(version_compare($woocommerce->version, "2.7", "<")){
                    $orderpage_prod_Array[get_permalink($_product->id)]=array(
                        "tvc_id" => esc_html($_product->id),
                        "tvc_i" => esc_js($_product->get_sku() ? $_product->get_sku() : $_product->id),
                        "tvc_n" => html_entity_decode($item["name"]),
                        "tvc_p" => esc_js($order->get_item_total($item)),
                        "tvc_c" => $categories,
                        "tvc_q"=>esc_js($item["qty"])
                      );
                }else{
                     $orderpage_prod_Array[get_permalink($_product->get_id())]=array(
                        "tvc_id" => esc_html($_product->get_id()),
                        "tvc_i" => esc_js($_product->get_sku() ? $_product->get_sku() : $_product->get_id()),
                        "tvc_n" => html_entity_decode($tvc_prnm),
                        "tvc_p" => esc_js($order->get_item_total($item)),
                        "tvc_c" => $categories,
                        "tvc_q"=>esc_js($item["qty"])
                      );
                }   
            }
            //make json for prod meta data on order page
           $this->wc_version_compare("tvc_oc=" . json_encode($orderpage_prod_Array) . ";");
        }

            //get shipping cost based on version >2.1 get_total_shipping() < get_shipping
            if (version_compare($woocommerce->version, "2.1", ">=")) {
                $tvc_sc = $order->get_total_shipping();
            } else {
                $tvc_sc = $order->get_shipping();
            }
            //orderpage transcation data json
                $orderpage_trans_Array=array(
                                "id"=> esc_js($order->get_order_number()),      // Transaction ID. Required
                "affiliation"=> esc_js(get_bloginfo('name')), // Affiliation or store name
                "revenue"=>esc_js($order->get_total()),        // Grand Total
                                "tax"=> esc_js($order->get_total_tax()),        // Tax
                "shipping"=> esc_js($tvc_sc),    // Shipping
                                "coupon"=>$coupons_list  
                      );
                 //make json for trans data on order page
           $this->wc_version_compare("tvc_td=" . json_encode($orderpage_trans_Array) . ";");

         $code ='
                 var items = [];
                //set local currencies
            gtag("set", {"currency": tvc_lc});
            for(var t_item in tvc_oc){
                items.push({
                    "id": tvc_oc[t_item].tvc_i,
                    "name": tvc_oc[t_item].tvc_n, 
                    "category": tvc_oc[t_item].tvc_c,
                    "price": tvc_oc[t_item].tvc_p,
                    "quantity": tvc_oc[t_item].tvc_q,
                });
               
            }
            gtag("event", "purchase", {
                "transaction_id":tvc_td.id,
                "affiliation": tvc_td.affiliation,
                "value":tvc_td.revenue,
                "tax": tvc_td.tax,
                "shipping": tvc_td.shipping,
                "coupon": tvc_td.coupon,
                "event_category": "Enhanced-Ecommerce",
                "event_label":"order_confirmation",
                "non_interaction": true,
                "items":items
            });
    ';

        //check woocommerce version
        $this->wc_version_compare($code);
        update_post_meta($order_id, "_tracked", 1);
    }

    /**
     * Enhanced E-commerce tracking for single product add to cart
     *
     * @access public
     * @return void
     */
    function add_to_cart() {
        if ($this->disable_tracking($this->ga_eeT))
            return;
        //return if not product page       
        if (!is_single())
            return;
        global $product,$woocommerce;
        if(version_compare($woocommerce->version, "2.7", "<")){
             $category = get_the_terms($product->ID, "product_cat");
        }else{
            $category = get_the_terms($product->get_id(), "product_cat");
        }
        $categories = "";
        if ($category) {
            foreach ($category as $term) {
                $categories.=$term->name . ",";
            }
        }
        //remove last comma(,) if multiple categories are there
        $categories = rtrim($categories, ",");

        $code = '
               var items = [];
                //set local currencies
            gtag("set", {"currency": tvc_lc});
            jQuery("[class*=single_add_to_cart_button]").click(function() {
                            
                              // Enhanced E-commerce Add to cart clicks
                gtag("event", "add_to_cart", {
                    "event_category":"Enhanced-Ecommerce",
                    "event_label":"add_to_cart_click",
                    "non_interaction": true,
                    "items": [{
                        "id" : tvc_po.tvc_i,
                        "name": tvc_po.tvc_n,
                        "category" :tvc_po.tvc_c,
                        "price": tvc_po.tvc_p,
                        "quantity" :jQuery(this).parent().find("input[name=quantity]").val()
                    }]
                });
                             
            });
        ';
        //check woocommerce version
        $this->wc_version_compare($code);
    }

    /**
     * Enhanced E-commerce tracking for product detail view
     *
     * @access public
     * @return void
     */
    public function product_detail_view() {

        
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }

        global $product,$woocommerce;
        if(version_compare($woocommerce->version, "2.7", "<")){
             $category = get_the_terms($product->ID, "product_cat");
        }else{
            $category = get_the_terms($product->get_id(), "product_cat");
        }
        $categories = "";
        if ($category) {
            foreach ($category as $term) {
                $categories.=$term->name . ",";
            }
        }
        //remove last comma(,) if multiple categories are there
        $categories = rtrim($categories, ",");
        //product detail view json
        if(version_compare($woocommerce->version, "2.7", "<")){
            $prodpage_detail_json = array(
            "tvc_id" => esc_html($product->id),
            "tvc_i" => $product->get_sku() ? $product->get_sku() : $product->id,                   
            "tvc_n" => $product->get_title(),
            "tvc_c" => $categories,
            "tvc_p" => $product->get_price()
        );
        }else{
            $prodpage_detail_json = array(
            "tvc_id" => esc_html($product->get_id()),
            "tvc_i" => $product->get_sku() ? $product->get_sku() : $product->get_id(),                   
            "tvc_n" => $product->get_title(),
            "tvc_c" => $categories,
            "tvc_p" => $product->get_price()
        );
        }
        
        if (empty($prodpage_detail_json)) { //prod page array
            $prodpage_detail_json = array();
        }
        //prod page detail view json
       $this->wc_version_compare("tvc_po=" . json_encode($prodpage_detail_json) . ";");
        $code = '
        gtag("event", "view_item", {
					"event_category":"Enhanced-Ecommerce",
					"event_label":"product_impression_pp",
                    "items": [
                      {
                        "id": tvc_po.tvc_i,// Product details are provided in an impressionFieldObject.
                        "name":  tvc_po.tvc_n,
                        "category":tvc_po.tvc_c,
                      }
                    ],
                    "non_interaction": true
        })
        ';
        //check woocommerce version
        if(is_product()){
        $this->wc_version_compare($code);
    }
    }

    /**
     * Enhanced E-commerce tracking for product impressions on category pages (hidden fields) , product page (related section)
     * home page (featured section and recent section)
     *
     * @access public
     * @return void
     */
    public function bind_product_metadata() {

        
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }

        global $product,$woocommerce;
        if (version_compare($woocommerce->version, "2.7", "<")) {
                    $category = get_the_terms($product->Id, "product_cat");
            } else {
                     $category = get_the_terms($product->get_id(), "product_cat");
            }
        
        $categories = "";

        if ($category) {
            foreach ($category as $term) {
                $categories.=$term->name . ",";
            }
        }
        //remove last comma(,) if multiple categories are there
        $categories = rtrim($categories, ",");
        //declare all variable as a global which will used for make json
        global $homepage_json_fp,$homepage_json_ATC_link, $homepage_json_rp,$prodpage_json_relProd,$catpage_json,$prodpage_json_ATC_link,$catpage_json_ATC_link;
        //is home page then make all necessory json
        if (is_home() || is_front_page()) {
            if (!is_array($homepage_json_fp) && !is_array($homepage_json_rp) && !is_array($homepage_json_ATC_link)) {
                $homepage_json_fp = array();
                $homepage_json_rp = array();
                $homepage_json_ATC_link=array();                
            }

                // ATC link Array
                if(version_compare($woocommerce->version, "2.7", "<")){
                    $homepage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->id));
                }else{
                    $homepage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->get_id()));
                }
              //check if product is featured product or not  
            if ($product->is_featured()) {
               //check if product is already exists in homepage featured json   
               if(version_compare($woocommerce->version, "2.7", "<")){
                    if(!array_key_exists(get_permalink($product->id),$homepage_json_fp)){
                $homepage_json_fp[get_permalink($product->id)] = array(
                        "tvc_id" => esc_html($product->id),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->id),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_n" => esc_html($product->get_price()),
                        "tvc_c" => esc_html($categories),
                        "tvc_po" => ++$_SESSION['t_fpcnt'],
                        "ATC-link"=>$product->add_to_cart_url()
                );
                //else add product in homepage recent product json
               }else {
                    $homepage_json_rp[get_permalink($product->id)] =array(
                        "tvc_id" => esc_html($product->id),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->id),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                );
               }
               }else{
                    if(!array_key_exists(get_permalink($product->get_id()),$homepage_json_fp)){
                $homepage_json_fp[get_permalink($product->get_id())] = array(
                        "tvc_id" => esc_html($product->get_id()),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->get_id()),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_n" => esc_html($product->get_price()),
                        "tvc_c" => esc_html($categories),
                        "tvc_po" => ++$_SESSION['t_fpcnt'],
                        "ATC-link"=>$product->add_to_cart_url()
                );
                //else add product in homepage recent product json
               }else {
                    $homepage_json_rp[get_permalink($product->get_id())] =array(
                        "tvc_id" => esc_html($product->get_id()),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->get_id()),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                );
               }
               }
               
            } else {
                //else prod add in homepage recent json  
                if(version_compare($woocommerce->version, "2.7", "<")){
                    $homepage_json_rp[get_permalink($product->id)] =array(
                        "tvc_id" => esc_html($product->id),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->id),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                );
                }else{
                    $homepage_json_rp[get_permalink($product->get_id())] =array(
                        "tvc_id" => esc_html($product->get_id()),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->get_id()),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                );
                }
                
            }
        }
        //if product page then related product page array
        else if(is_product()){
            if(!is_array($prodpage_json_relProd) && !is_array($prodpage_json_ATC_link)){
                $prodpage_json_relProd = array();
                $prodpage_json_ATC_link = array();
            }
            // ATC link Array
            if(version_compare($woocommerce->version, "2.7", "<")){
                $prodpage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->id));
              
            $prodpage_json_relProd[get_permalink($product->id)] = array(
                        "tvc_id" => esc_html($product->id),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->id),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_c" => esc_html($categories),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                );
            }else{
                 $prodpage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->get_id()));
              
            $prodpage_json_relProd[get_permalink($product->get_id())] = array(
                        "tvc_id" => esc_html($product->get_id()),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->get_id()),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                        
                );
            }
        }
        //category page, search page and shop page json
        else if (is_product_category() || is_search() || is_shop()) {
             if (!is_array($catpage_json) && !is_array($catpage_json_ATC_link)){
                 $catpage_json=array();
                 $catpage_json_ATC_link=array();
             }
             //cat page ATC array
             if(version_compare($woocommerce->version, "2.7", "<")){
                $catpage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->id));

                  $catpage_json[get_permalink($product->id)] =array(
                        "tvc_id" => esc_html($product->id),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->id),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_c" => esc_html($categories),
                        "tvc_po" => ++$_SESSION['t_npcnt'], 
                );
             }else{
                $catpage_json_ATC_link[$product->add_to_cart_url()]=array("ATC-link"=>get_permalink($product->get_id()));

                $catpage_json[get_permalink($product->get_id())] =array(
                        "tvc_id" => esc_html($product->get_id()),
                        "tvc_i" => esc_html($product->get_sku() ? $product->get_sku() : $product->get_id()),
                        "tvc_n" => esc_html($product->get_title()),
                        "tvc_p" => esc_html($product->get_price()),
                        "tvc_po" => ++$_SESSION['t_npcnt'],
                        "tvc_c" => esc_html($categories)
                         
                );
             }
             
            }
    }

    /**
     * Enhanced E-commerce tracking for product impressions,clicks on Home pages
     *
     * @access public
     * @return void
     */
    function t_products_impre_clicks() {
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }
        
        //get impression threshold
        $impression_threshold = $this->ga_imTh;
        
        //Product impression on Home Page
        global $homepage_json_fp,$homepage_json_ATC_link, $homepage_json_rp,$prodpage_json_relProd,$catpage_json,$prodpage_json_ATC_link,$catpage_json_ATC_link;
        //home page json for featured products and recent product sections
        //check if php array is empty
        if(empty($homepage_json_ATC_link)){
            $homepage_json_ATC_link=array(); //define empty array so if empty then in json will be []
        }
        if(empty($homepage_json_fp)){
            $homepage_json_fp=array(); //define empty array so if empty then in json will be []
        }
        if(empty($homepage_json_rp)){ //home page recent product array
            $homepage_json_rp=array(); 
        }
        if(empty($prodpage_json_relProd)){ //prod page related section array
            $prodpage_json_relProd=array();
        }
        if(empty($prodpage_json_ATC_link)){
            $prodpage_json_ATC_link=array(); //prod page ATC link json
        }
        if(empty($catpage_json)){ //category page array
            $catpage_json=array();
        }
        if(empty($catpage_json_ATC_link)){ //category page array
            $catpage_json_ATC_link=array();
        }
        //home page json
        $this->wc_version_compare("homepage_json_ATC_link=" . json_encode($homepage_json_ATC_link) . ";");
        $this->wc_version_compare("tvc_fp=" . json_encode($homepage_json_fp) . ";");
        $this->wc_version_compare("tvc_rcp=" . json_encode($homepage_json_rp) . ";");
        //product page json
        $this->wc_version_compare("tvc_rdp=" . json_encode($prodpage_json_relProd) . ";");
        $this->wc_version_compare("prodpage_json_ATC_link=" . json_encode($prodpage_json_ATC_link) . ";");
        //category page json
        $this->wc_version_compare("tvc_pgc=" . json_encode($catpage_json) . ";");
        $this->wc_version_compare("catpage_json_ATC_link=" . json_encode($catpage_json_ATC_link) . ";");
        
        $hmpg_impressions_jQ = '
            var items = [];
                //set local currencies
            gtag("set", {"currency": tvc_lc});
        function t_products_impre_clicks(t_json_name,t_action){
                   t_send_threshold=0;
                   t_prod_pos=0;
                   
                    t_json_length=Object.keys(t_json_name).length;
                        
                    for(var t_item in t_json_name) {
            t_send_threshold++;
            t_prod_pos++;
                    items.push({
                        "id": t_json_name[t_item].tvc_i,
                        "name": t_json_name[t_item].tvc_n,
                        "category": t_json_name[t_item].tvc_c,
                        "price": t_json_name[t_item].tvc_p,
                        "list_position": t_json_name[t_item].tvc_po,
                    });        
                        
        if(t_json_length > ' . esc_js($impression_threshold) .' ){
                        if((t_send_threshold%' . esc_js($impression_threshold) . ')==0){
                            t_json_length=t_json_length-' . esc_js($impression_threshold) . ';
                            	gtag("event", "view_item_list", { "event_category":"Enhanced-Ecommerce",
                                     "event_label":"product_impression_"+t_action, "items":items,"non_interaction": true});
                        }
                     }else{
            
                       t_json_length--;
                       if(t_json_length==0){
                               gtag("event", "view_item_list", { "event_category":"Enhanced-Ecommerce",
                                    "event_label":"product_impression_"+t_action, "items":items,"non_interaction": true});
                        }
        }   
                }
        }
                
        //function for comparing urls in json object
        function prod_exists_in_JSON(t_url,t_json_name,t_action){
                                    if(t_json_name.hasOwnProperty(t_url)){
                                        t_call_fired=true;
                                        gtag("event", "select_content", {
                                            "event_category":"Enhanced-Ecommerce",
                                            "event_label":"product_click_"+t_action,
                                            "content_type": "product",
                                            "items": [
                                            {
                                                "id":t_json_name[t_url].tvc_i,
                                                "name": t_json_name[t_url].tvc_n,
                                                 "category":t_json_name[t_url].tvc_c,
                                                 "price": t_json_name[t_url].tvc_p,
                                                 "list_position": t_json_name[t_url].tvc_po,
                                            }
                                            ],
                                            "non_interaction": true
                                        });                    
                                   }else{
                                   t_call_fired=false;
                }
                                return t_call_fired;
            }
                function prod_ATC_link_exists(t_url,t_ATC_json_name,t_prod_data_json,t_qty){
                    t_prod_url_key=t_ATC_json_name[t_url]["ATC-link"];
                    
                        if(t_prod_data_json.hasOwnProperty(t_prod_url_key)){
                                t_call_fired=true;
                            // Enhanced E-commerce Add to cart clicks
                                gtag("event", "add_to_cart", {
                                    "event_category":"Enhanced-Ecommerce",
                                    "event_label":"add_to_cart_click",
                                    "non_interaction": true,
                                    "items": [{
                                        "id" : t_prod_data_json[t_prod_url_key].tvc_i,
                                        "name":t_prod_data_json[t_prod_url_key].tvc_i,
                                        "category" : t_prod_data_json[t_prod_url_key].tvc_c,
                                        "price": t_prod_data_json[t_prod_url_key].tvc_p,
                                        "quantity" :t_qty
                                    }]
                                });
                             
                        }else{
                                   t_call_fired=false;
                        }    
                         return t_call_fired;
                 
                }
                
                ';
        if(is_home() || is_front_page()){
       $hmpg_impressions_jQ .='
                if(tvc_fp.length !== 0){
                    t_products_impre_clicks(tvc_fp,"fp");       
                }
                if(tvc_rcp.length !== 0){
                    t_products_impre_clicks(tvc_rcp,"rp");    
                }
                jQuery("a:not([href*=add-to-cart],.product_type_variable, .product_type_grouped)").on("click",function(){
            t_url=jQuery(this).attr("href");
                        //home page call for click
                        t_call_fired=prod_exists_in_JSON(t_url,tvc_fp,"fp");
                        if(!t_call_fired){
                            prod_exists_in_JSON(t_url,tvc_rcp,"rp");
                        }    
                });
                //ATC click
                jQuery("a[href*=add-to-cart]").on("click",function(){
            t_url=jQuery(this).attr("href");
                        t_qty=$(this).parent().find("input[name=quantity]").val();
                             //default quantity 1 if quantity box is not there             
                            if(t_qty=="" || t_qty===undefined){
                                t_qty="1";
                            }
                        t_call_fired=prod_ATC_link_exists(t_url,homepage_json_ATC_link,tvc_fp,t_qty);
                        if(!t_call_fired){
                            prod_ATC_link_exists(t_url,homepage_json_ATC_link,tvc_rcp,t_qty);
                        }
                    });   
             
                ';
        }else if(is_search()){
            $hmpg_impressions_jQ .='
                //search page json
                if(tvc_pgc.length !== 0){
                    t_products_impre_clicks(tvc_pgc,"srch");   
                }
                //search page prod click
                jQuery("a:not(.product_type_variable, .product_type_grouped)").on("click",function(){
                    t_url=jQuery(this).attr("href");
                     //cat page prod call for click
                     prod_exists_in_JSON(t_url,tvc_pgc,"srch");
                     });
                
            '; 
        }else if (is_product()) {
                //product page releted products
                $hmpg_impressions_jQ .='
                if(tvc_rdp.length !== 0){
                    t_products_impre_clicks(tvc_rdp,"rdp");  
                }          
                //product click - image and product name
                jQuery("a:not(.product_type_variable, .product_type_grouped)").on("click",function(){
                    t_url=jQuery(this).attr("href");
                     //prod page related call for click
                     prod_exists_in_JSON(t_url,tvc_rdp,"rdp");
                });  
                //Prod ATC link click in related product section
                jQuery("a[href*=add-to-cart]").on("click",function(){
            t_url=jQuery(this).attr("href");
                        t_qty=$(this).parent().find("input[name=quantity]").val();
                             //default quantity 1 if quantity box is not there             
                            if(t_qty=="" || t_qty===undefined){
                                t_qty="1";
                            }
                prod_ATC_link_exists(t_url,prodpage_json_ATC_link,tvc_rdp,t_qty);
                });   
            ';
        }else if (is_product_category()) {
            $hmpg_impressions_jQ .='
                //category page json
                if(tvc_pgc.length !== 0){
                    t_products_impre_clicks(tvc_pgc,"cp");  
                }
               //Prod category ATC link click in related product section
                jQuery("a:not(.product_type_variable, .product_type_grouped)").on("click",function(){
                     t_url=jQuery(this).attr("href");
                     //cat page prod call for click
                     prod_exists_in_JSON(t_url,tvc_pgc,"cp");
                     });
               
        ';
        }else if(is_shop()){
            $hmpg_impressions_jQ .='
                //shop page json
                if(tvc_pgc.length !== 0){
                    t_products_impre_clicks(tvc_pgc,"sp");  
                }
                //shop page prod click
                jQuery("a:not(.product_type_variable, .product_type_grouped)").on("click",function(){
                    t_url=jQuery(this).attr("href");
                     //cat page prod call for click
                     prod_exists_in_JSON(t_url,tvc_pgc,"sp");
                     });
                
                     
        '; 
        }
        //common ATC link for Category page , Shop Page and Search Page
        if(is_product_category() || is_shop() || is_search()){
            $hmpg_impressions_jQ .='
                     //ATC link click
                jQuery("a[href*=add-to-cart]").on("click",function(){
            t_url=jQuery(this).attr("href");
                        t_qty=$(this).parent().find("input[name=quantity]").val();
                             //default quantity 1 if quantity box is not there             
                            if(t_qty=="" || t_qty===undefined){
                                t_qty="1";
                            }
                       prod_ATC_link_exists(t_url,catpage_json_ATC_link,tvc_pgc,t_qty);
                    });      
                    ';
        } 
        //on home page, product page , category page
        if (is_home() || is_front_page() || is_product() || is_product_category() || is_search() || is_shop()){
            $this->wc_version_compare($hmpg_impressions_jQ);
        }
    }

    /**
     * Enhanced E-commerce tracking for remove from cart
     *
     * @access public
     * @return void
     */
    public function remove_cart_tracking() {
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }
        global $woocommerce;
        $cartpage_prod_array_main = array();
        //echo "<pre>".print_r($woocommerce->cart->cart_contents,TRUE)."</pre>";
        foreach ($woocommerce->cart->cart_contents as $key => $item) {
            //Version compare
            if (version_compare($woocommerce->version, "2.7", "<")) {
                    $prod_meta = get_product($item["product_id"]);
            } else {
                     $prod_meta = wc_get_product($item["product_id"]);
            }
            if (version_compare($woocommerce->version, "3.3", "<")) {
                    $cart_remove_link=html_entity_decode($woocommerce->cart->get_remove_url($key)); 
            } else {
                    $cart_remove_link=html_entity_decode(wc_get_cart_remove_url($key)); 
            }     
            $category = get_the_terms($item["product_id"], "product_cat");
            $categories = "";
            if ($category) {
                foreach ($category as $term) {
                    $categories.=$term->name . ",";
                }
            }
            //remove last comma(,) if multiple categories are there
            $categories = rtrim($categories, ",");
            if(version_compare($woocommerce->version, "2.7", "<")){
                $cartpage_prod_array_main[$cart_remove_link] =array(
                    "tvc_id" => esc_html($prod_meta->id),
                    "tvc_i" => esc_html($prod_meta->get_sku() ? $prod_meta->get_sku() : $prod_meta->id),
                    "tvc_n" => html_entity_decode($prod_meta->get_title()),
                    "tvc_p" => esc_html($prod_meta->get_price()),
                    "tvc_c" => esc_html($categories),
                    "tvc_q"=>$woocommerce->cart->cart_contents[$key]["quantity"]
                );
            }else{
                $cartpage_prod_array_main[$cart_remove_link] =array(
                    "tvc_id" => esc_html($prod_meta->get_id()),
                    "tvc_i" => esc_html($prod_meta->get_sku() ? $prod_meta->get_sku() : $prod_meta->get_id()),
                    "tvc_n" => html_entity_decode($prod_meta->get_title()),
                    "tvc_p" => esc_html($prod_meta->get_price()),
                    "tvc_c" => esc_html($categories),
                    "tvc_q"=>$woocommerce->cart->cart_contents[$key]["quantity"]
                );
            }
        }

        //Cart Page item Array to Json
        $this->wc_version_compare("tvc_cc=" . json_encode($cartpage_prod_array_main) . ";");

        $code = '
            //set local currencies
            gtag("set", {"currency": tvc_lc});
        $("a[href*=\"?remove_item\"]").click(function(){
            t_url=jQuery(this).attr("href");
                    gtag("event", "remove_from_cart", {
						"event_category":"Enhanced-Ecommerce",
						"event_label":"remove_from_cart_click",
						"items": [{
							"id":tvc_cc[t_url].tvc_i,
                            "name": tvc_cc[t_url].tvc_n,
                            "category":tvc_cc[t_url].tvc_c,
                            "price": tvc_cc[t_url].tvc_p,
                            "quantity": tvc_cc[t_url].tvc_q
						}],
						"non_interaction": true
					});
              });
            ';
        //check woocommerce version
        $this->wc_version_compare($code);
    }

    /**
     * Enhanced E-commerce tracking checkout step 1
     *
     * @access public
     * @return void
     */
    public function checkout_step_1_tracking() {
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }
        //call fn to make json
        $this->get_ordered_items();
        $code= '
                var items = [];
                gtag("set", {"currency": tvc_lc});
                for(var t_item in tvc_ch){
                    items.push({
                        "id": tvc_ch[t_item].tvc_i,
                        "name": tvc_ch[t_item].tvc_n,
                        "category": tvc_ch[t_item].tvc_c,
                        "price": tvc_ch[t_item].tvc_p,
                        "quantity": tvc_ch[t_item].tvc_q
                    });
                    }';

        $code_step_1 = $code . 'gtag("event", "begin_checkout", {"event_category":"Enhanced-Ecommerce",
						"event_label":"checkout_step_1","items":items,"non_interaction": true });';
       //$code_step_1 .= 'gtag("event", "set_checkout_option", {"checkout_step": 1})'; 

        //check woocommerce version and add code
        $this->wc_version_compare($code_step_1);
    }

    /**
     * Enhanced E-commerce tracking checkout step 2
     *
     * @access public
     * @return void
     */
    public function checkout_step_2_tracking() {
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }
        $code= '
               var items = [];
                gtag("set", {"currency": tvc_lc});
                for(var t_item in tvc_ch){
                    items.push({
                        "id": tvc_ch[t_item].tvc_i,
                        "name": tvc_ch[t_item].tvc_n,
                        "category": tvc_ch[t_item].tvc_c,
                        "price": tvc_ch[t_item].tvc_p,
                        "quantity": tvc_ch[t_item].tvc_q
                    });
                    }';

        $code_step_2 = $code . 'gtag("event", "checkout_progress", {"checkout_step": 2,"event_category":"Enhanced-Ecommerce",
						"event_label":"checkout_step_2","items":items,"non_interaction": true });';

        //if logged in and first name is filled - Guest Check out
        if (is_user_logged_in()) {
            $step2_onFocus = 't_tracked_focus=0;  if(t_tracked_focus===0){' . $code_step_2 . ' t_tracked_focus++;}';
        } else {
            //first name on focus call fire
            $step2_onFocus = 't_tracked_focus=0; jQuery("input[name=billing_first_name]").on("focus",function(){ if(t_tracked_focus===0){' . $code_step_2 . ' t_tracked_focus++;}});';
        }
        //check woocommerce version and add code
        $this->wc_version_compare($step2_onFocus);
    }

    /**
     * Enhanced E-commerce tracking checkout step 3
     *
     * @access public
     * @return void
     */
    public function checkout_step_3_tracking() {
        if ($this->disable_tracking($this->ga_eeT)) {
            return;
        }
        $code= '
         var items = [];
            for(var t_item in tvc_ch){
                    items.push({
                        "id": tvc_ch[t_item].tvc_i,
                        "name": tvc_ch[t_item].tvc_n,
                        "category": tvc_ch[t_item].tvc_c,
                        "price": tvc_ch[t_item].tvc_p,
                        "quantity": tvc_ch[t_item].tvc_q
                    });
                    }';

        //check if guest check out is enabled or not
        $step_2_on_proceed_to_pay = (!is_user_logged_in() && !$this->ga_gCkout ) || (!is_user_logged_in() && $this->ga_gCkout && $this->ga_gUser);

        $code_step_3 = $code . 'gtag("event", "checkout_progress", {"checkout_step": 3,"event_category":"Enhanced-Ecommerce",
						"event_label":"checkout_step_3","items":items,"non_interaction": true });';

        $inline_js = 't_track_clk=0; jQuery(document).on("click","#place_order",function(e){ if(t_track_clk===0){';
        if ($step_2_on_proceed_to_pay) {
            if (isset($code_step_2))
                $inline_js .= $code_step_2;
        }
        $inline_js .= $code_step_3;
        $inline_js .= "t_track_clk++; }});";

        //check woocommerce version and add code
        $this->wc_version_compare($inline_js);
    }

    /**
     * Get oredered Items for check out page.
     *
     * @access public
     * @return void
     */
    public function get_ordered_items() {
        global $woocommerce;
        $code = "";
        //get all items added into the cart
        foreach ($woocommerce->cart->cart_contents as $item) {
            //Version Compare
            if ( version_compare($woocommerce->version, "2.7", "<")) {
                    $p = get_product($item["product_id"]);
            } else {
                    $p = wc_get_product($item["product_id"]);
            }

            $category = get_the_terms($item["product_id"], "product_cat");
            $categories = "";
            if ($category) {
                foreach ($category as $term) {
                    $categories.=$term->name . ",";
                }
            }
            //remove last comma(,) if multiple categories are there
            $categories = rtrim($categories, ",");
            if(version_compare($woocommerce->version, "2.7", "<")){
                $chkout_json[get_permalink($p->id)] = array(
                "tvc_id" => esc_html($p->id),
                "tvc_i" => esc_js($p->get_sku() ? $p->get_sku() : $p->id),
                "tvc_n" => html_entity_decode($p->get_title()),
                "tvc_p" => esc_js($p->get_price()),
                "tvc_c" => $categories,
                "tvc_q" => esc_js($item["quantity"]),
                "isfeatured"=>$p->is_featured()
                );
            }else{
                $chkout_json[get_permalink($p->get_id())] = array(
                "tvc_id" => esc_html($p->get_id()),
                "tvc_i" => esc_js($p->get_sku() ? $p->get_sku() : $p->get_id()),
                "tvc_n" => html_entity_decode($p->get_title()),
                "tvc_p" => esc_js($p->get_price()),
                "tvc_c" => $categories,
                "tvc_q" => esc_js($item["quantity"]),
                "isfeatured"=>$p->is_featured()
                );
            }     
        }
        //return $code;
        //make product data json on check out page
        $this->wc_version_compare("tvc_ch=" . json_encode($chkout_json) . ";");
    }
}
?>