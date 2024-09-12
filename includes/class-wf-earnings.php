<?php

class WF_Earnings {

    public function __construct() {
        // Hook into the order completion process to track earnings
        add_action('woocommerce_thankyou', array($this, 'wf_track_order_and_earnings'));
    }

    // Track orders and assign earnings to fundraisers
    public function wf_track_order_and_earnings($order_id) {
        // Get the order object
        $order = wc_get_order($order_id);
        
        // Get used coupons from the order
        $used_coupons = $order->get_used_coupons();

        foreach ($used_coupons as $coupon_code) {
            // Find the fundraiser associated with the coupon
            $fundraiser_id = $this->wf_get_fundraiser_by_coupon($coupon_code);
            if ($fundraiser_id) {
                // Get total number of products purchased
                $total_items = $order->get_item_count();
                
                // Commission is $5 per product sold
                $commission = $total_items * 5;
                
                // Update fundraiser earnings
                $this->wf_add_fundraiser_earnings($fundraiser_id, $commission);
            }
        }
    }

    // Get fundraiser by promo code
    private function wf_get_fundraiser_by_coupon($coupon_code) {
        // Query for users with the given promo code
        $args = array(
            'meta_key'   => 'promo_code',
            'meta_value' => $coupon_code,
            'number'     => 1,
            'fields'     => 'ID'
        );
        $users = get_users($args);
        
        if (!empty($users)) {
            return $users[0]; // Return the ID of the first user found
        }
        return false;
    }

    // Add earnings to fundraiser's total
    private function wf_add_fundraiser_earnings($fundraiser_id, $commission) {
        // Retrieve current earnings
        $total_earnings = get_user_meta($fundraiser_id, 'total_earnings', true);
        $total_earnings = empty($total_earnings) ? 0 : $total_earnings;
        
        // Add commission to total earnings
        $total_earnings += $commission;
        
        // Update user meta with new earnings
        update_user_meta($fundraiser_id, 'total_earnings', $total_earnings);
    }
}

new WF_Earnings();
