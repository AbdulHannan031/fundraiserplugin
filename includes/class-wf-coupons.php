<?php

class WF_Coupons {

    public function __construct() {
        add_action('user_register', array($this, 'wf_generate_fundraiser_promo_code'));
    }

    // Generate promo code for new fundraisers
    public function wf_generate_fundraiser_promo_code($user_id) {
        $user = get_userdata($user_id);
        if (in_array('fundraiser', (array)$user->roles)) {
            $promo_code = 'FUND-' . strtoupper(wp_generate_password(8, false));
            update_user_meta($user_id, 'promo_code', $promo_code);

            // Create WooCommerce coupon
            $this->wf_create_coupon($promo_code);
        }
    }

    // Create WooCommerce coupon
    public function wf_create_coupon($promo_code) {
        $coupon = array(
            'post_title' => $promo_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon',
        );

        $new_coupon_id = wp_insert_post($coupon);
        update_post_meta($new_coupon_id, 'discount_type', 'percent');
        update_post_meta($new_coupon_id, 'coupon_amount', '20');
        update_post_meta($new_coupon_id, 'individual_use', 'no');
        update_post_meta($new_coupon_id, 'usage_limit', '');
    }
}

new WF_Coupons();
