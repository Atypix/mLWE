<?php
if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Stripe_Gateway_Connect_Vendor {

    public function __construct() {
        // Connect Button Vendor Shop Page
        if ('stripe_masspay' === get_user_meta(get_current_vendor_id(), '_vendor_payment_mode', true) && 'Enable' === get_wcmp_vendor_settings('payment_method_stripe_masspay', 'payment')) {
            if (WCMp_PLUGIN_VERSION <= '2.7.5') {
                add_action('other_exta_field_dcmv', array($this, 'vendor_stripe_connect'));
            } else {
                add_action('wcmp_after_vendor_billing', array($this, 'vendor_stripe_connect'));
            }
        }
        // Add stripe in the payment mode list
        add_filter('automatic_payment_method', array($this, 'admin_stripe_payment_mode'), 10);

        $this->payment_admin_settings = get_option('wcmp_payment_settings_name');
        add_filter('wcmp_vendor_payment_mode', array($this, 'vendor_stripe_payment_mode'), 10);
        // Disconnect Vendor stripe account
        add_action('before_wcmp_vendor_dashboard', array($this, 'disconnect_stripe_account'));
    }

    public function admin_stripe_payment_mode($arg) {
        $admin_payment_mode_select = array_merge($arg, array('stripe_masspay' => __('Stripe Connect', 'marketplace-stripe-gateway')));
        return $admin_payment_mode_select;
    }

    public function vendor_stripe_payment_mode($arg) {
        $payment_mode = array();
        if (isset($this->payment_admin_settings['payment_method_stripe_masspay']) && $this->payment_admin_settings['payment_method_stripe_masspay'] = 'Enable') {
            $payment_mode['stripe_masspay'] = __('Stripe Connect', 'marketplace-stripe-gateway');
        }
        $vendor_payment_mode_select = array_merge($arg, $payment_mode);
        return $vendor_payment_mode_select;
    }

    /**
     * This will connect a vendor's stripe account with marketplace
     */
    public function vendor_stripe_connect($user = '') {
        global $WCMp_Stripe_Gateway;
        if (empty($user)) {
            $user = wp_get_current_user();
        }
        $user_id = $user->ID;
        $vendor = get_wcmp_vendor($user_id);
        echo '<div class="payment-gateway payment-gateway-stripe_masspay">';
        if ($vendor) {
            $stripe_settings = get_option('woocommerce_stripe_settings');
            if (isset($stripe_settings) && !empty($stripe_settings)) {
                if (isset($stripe_settings['enabled']) && $stripe_settings['enabled'] == 'no') {
                    return;
                }
                $testmode = $stripe_settings['testmode'] === "yes" ? true : false;
                $client_id = $testmode ? get_wcmp_stripe_gateway_settings('test_client_id', 'payment', 'stripe_gateway') : get_wcmp_stripe_gateway_settings('live_client_id', 'payment', 'stripe_gateway');
                $secret_key = $testmode ? $stripe_settings['test_secret_key'] : $stripe_settings['secret_key'];
                if (isset($client_id) && isset($secret_key)) {
                    if (isset($_GET['code'])) {
                        $code = $_GET['code'];
                        if (!is_user_logged_in()) {
                            if (isset($_GET['state'])) {
                                $user_id = $_GET['state'];
                            }
                        }
                        $token_request_body = array(
                            'grant_type' => 'authorization_code',
                            'client_id' => $client_id,
                            'code' => $code,
                            'client_secret' => $secret_key
                        );
                        $req = curl_init('https://connect.stripe.com/oauth/token');
                        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($req, CURLOPT_POST, true);
                        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
                        curl_setopt($req, CURLOPT_VERBOSE, true);
                        // TODO: Additional error handling
                        $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                        $resp = json_decode(curl_exec($req), true);
                        curl_close($req);
                        if (!isset($resp['error'])) {
                            update_user_meta($user_id, 'vendor_connected', 1);
                            update_user_meta($user_id, 'admin_client_id', $client_id);
                            update_user_meta($user_id, 'access_token', $resp['access_token']);
                            update_user_meta($user_id, 'refresh_token', $resp['refresh_token']);
                            update_user_meta($user_id, 'stripe_publishable_key', $resp['stripe_publishable_key']);
                            update_user_meta($user_id, 'stripe_user_id', $resp['stripe_user_id']);
                        }
                        if (isset($resp['access_token']) || get_user_meta($user_id, 'vendor_connected', true) == 1) {
                            update_user_meta($user_id, 'vendor_connected', 1);
                            ?>
                            <div class="form-group">
                                <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                                <div class="col-md-6 col-sm-9">
                                    <input type="submit" class="btn btn-default" name="disconnect_stripe" value="Disconnect Stripe Account" />
                                </div>
                            </div>
                            <?php
                        } else {
                            update_user_meta($user_id, 'vendor_connected', 0);
                            ?>
                            <div class="form-group">
                                <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                                <div class="col-md-6 col-sm-9">
                                    <b><?php _e('Please Retry!!!', 'marketplace-stripe-gateway'); ?></b>
                                </div>
                            </div>
                            <?php
                        }
                    } else if (isset($_GET['error'])) { // Error
                        update_user_meta($user_id, 'vendor_connected', 0);
                        ?>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                            <div class="col-md-6 col-sm-9">
                                <b><?php _e('Please Retry!!!', 'marketplace-stripe-gateway'); ?></b>
                            </div>
                        </div>
                        <?php
                    } else {
                        $vendor_connected = get_user_meta($user_id, 'vendor_connected', true);
                        $connected = true;

                        if (isset($vendor_connected) && $vendor_connected == 1) {
                            $admin_client_id = get_user_meta($user_id, 'admin_client_id', true);

                            if ($admin_client_id == $client_id) {
                                ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                                    <div class="col-md-6 col-sm-9">
                                        <input type="submit" class="btn btn-default" name="disconnect_stripe" value="Disconnect Stripe Account" />
                                    </div>
                                </div>
                                <?php
                            } else {
                                $connected = false;
                            }
                        } else {
                            $connected = false;
                        }

                        if (!$connected) {

                            $status = delete_user_meta($user->ID, 'vendor_connected');
                            $status = delete_user_meta($user->ID, 'admin_client_id');

                            // Show OAuth link
                            $authorize_request_body = array(
                                'response_type' => 'code',
                                'scope' => 'read_write',
                                'client_id' => $client_id,
                                'state' => $user->ID
                            );
                            $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($authorize_request_body);
                            $stripe_connect_url = $WCMp_Stripe_Gateway->plugin_url . 'assets/images/blue-on-light.png';

                            if (!$status) {
                                ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                                    <div class="col-md-6 col-sm-9">
                                        <a href=<?php echo $url; ?> target="_self"><img src="<?php echo $stripe_connect_url; ?>" /></a>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'marketplace-stripe-gateway'); ?></label>
                                    <div class="col-md-6 col-sm-9">
                                        <a href=<?php echo $url; ?> target="_self"><img src="<?php echo $stripe_connect_url; ?>" /></a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        } else {
            ?>
            <div><?php _e('You are not a Vendor. Please Login as a Vendor.', 'marketplace-stripe-gateway'); ?></div>
            <?php
        }
        echo '</div>';
    }

    public function disconnect_stripe_account() {
        global $WCMp_Stripe_Gateway;
        if (isset($_POST['disconnect_stripe'])) {
            $user = wp_get_current_user();
            $user_id = $user->ID;
            $vendor = get_wcmp_vendor($user_id);
            $stripe_settings = get_option('woocommerce_stripe_settings');
            $stripe_user_id = get_user_meta($user_id, 'stripe_user_id', true);
            if (isset($stripe_settings['enabled']) && $stripe_settings['enabled'] == 'no' && empty($stripe_user_id)) {
                return;
            }
            $testmode = $stripe_settings['testmode'] === "yes" ? true : false;
            $client_id = $testmode ? get_wcmp_stripe_gateway_settings('test_client_id', 'payment', 'stripe_gateway') : get_wcmp_stripe_gateway_settings('live_client_id', 'payment', 'stripe_gateway');
            $secret_key = $testmode ? $stripe_settings['test_secret_key'] : $stripe_settings['secret_key'];
            $token_request_body = array(
                'client_id' => $client_id,
                'stripe_user_id' => $stripe_user_id,
                'client_secret' => $secret_key
            );
            $req = curl_init('https://connect.stripe.com/oauth/deauthorize');
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true);
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
            curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($req, CURLOPT_VERBOSE, true);
            // TODO: Additional error handling
            $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            curl_close($req);
            if ($vendor && isset($resp['stripe_user_id'])) {
                delete_user_meta($user_id, 'vendor_connected');
                delete_user_meta($user_id, 'admin_client_id');
                delete_user_meta($user_id, 'access_token');
                delete_user_meta($user_id, 'refresh_token');
                delete_user_meta($user_id, 'stripe_publishable_key');
                delete_user_meta($user_id, 'stripe_user_id');
                wc_add_notice(__('Your account has been disconnected', 'marketplace-stripe-gateway'), 'success');
            } else {
                wc_add_notice(__('Unable to disconnect your account pleease try again', 'marketplace-stripe-gateway'), 'error');
            }
        }
    }

}
