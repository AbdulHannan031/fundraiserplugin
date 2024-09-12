<?php

class WF_Dashboard {
    
    public function __construct() {
        add_shortcode('wf_fundraiser_dashboard', array($this, 'wf_dashboard_shortcode'));
        add_action('admin_post_wf_request_withdrawal', array($this, 'wf_handle_withdrawal_request'));
    }

    // Dashboard content
    public function wf_dashboard_shortcode() {
        if (!is_user_logged_in()) {
            return 'Please log in to view your dashboard.';
        }
    
        $current_user = wp_get_current_user();
        if (in_array('fundraiser', (array)$current_user->roles)) {
            $total_earnings = get_user_meta($current_user->ID, 'total_earnings', true);
            $total_earnings = !empty($total_earnings) ? $total_earnings : 0;
            $threshold = 20; // Minimum amount for withdrawal
           
            ob_start();
            ?>
            <div class="wf-dashboard-container">
                <h2>Welcome, <?php echo $current_user->display_name; ?></h2>
    
                <div class="promo-code">
                    <h4>Your Promo Code</h4>
                    <p><?php echo get_user_meta($current_user->ID, 'promo_code', true); ?></p>
                </div>
    
                <div class="social-buttons">
                    <h4>Share your promo code on social media</h4>
                    <a href="https://twitter.com/share?url=<?php echo urlencode(home_url()); ?>&text=Use%20my%20promo%20code%20for%2020%%20off!">Share on Twitter</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(home_url()); ?>">Share on Facebook</a>
                </div>
    
                <div class="earnings">
                    <h4>Total Earnings</h4>
                    <p>$<?php echo $total_earnings; ?></p>
                </div>
    
                <?php if ($total_earnings >= $threshold) : ?>
                    <div class="withdrawal-request">
                        <h4>Request Withdrawal</h4>
                        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                            <input type="hidden" name="action" value="wf_request_withdrawal">
                            <input type="hidden" name="user_id" value="<?php echo esc_attr($current_user->ID); ?>">
                            
                            <label for="bank_account">Bank Account Number:</label>
                            <input type="text" name="bank_account" id="bank_account" required>
                            
                            <label for="bank_name">Bank Name:</label>
                            <input type="text" name="bank_name" id="bank_name" required>
                            
                            <button type="submit" class="withdrawal-button">Request Withdrawal</button>
                        </form>
                    </div>
                <?php else : ?>
                    <p class="alert">You need at least $<?php echo $threshold; ?> to request a withdrawal.</p>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        } else {
            return 'You are not authorized to view this dashboard.';
        }
    }
    
    // Handle withdrawal request
    public function wf_handle_withdrawal_request() {
        if (!isset($_POST['user_id']) || !isset($_POST['bank_account']) || !isset($_POST['bank_name'])) {
            wp_redirect(home_url('/fundraiser-dashboard?status=withdrawal_failed'));
            exit;
        }

        $user_id = intval($_POST['user_id']);
        $bank_account = sanitize_text_field($_POST['bank_account']);
        $bank_name = sanitize_text_field($_POST['bank_name']);
        $total_earnings = get_user_meta($user_id, 'total_earnings', true);
        $total_earnings = !empty($total_earnings) ? $total_earnings : 0;

        if ($total_earnings >= 20) {
            // Notify the admin
            $admin_email = get_option('admin_email');
            $admin_subject = 'Withdrawal Request Received';
            $admin_message = "A fundraiser has requested a withdrawal.\n\n";
            $admin_message .= "User ID: $user_id\n";
            $admin_message .= "Bank Account: $bank_account\n";
            $admin_message .= "Bank Name: $bank_name\n";
            wp_mail($admin_email, $admin_subject, $admin_message);

            // Notify the user
            $user = get_user_by('id', $user_id);
            $user_email = $user->user_email;
            $user_subject = 'Withdrawal Request Received';
            $user_message = "Dear {$user->display_name},\n\n";
            $user_message .= "We have received your withdrawal request. Your request will be processed and completed within 3 working days.\n\n";
            $user_message .= "Thank you for your patience.";
            wp_mail($user_email, $user_subject, $user_message);

            // Deduct the requested amount (here we're resetting it for simplicity)
            update_user_meta($user_id, 'total_earnings', 0);

            // Redirect to dashboard with a success message
            wp_redirect(home_url('/fundraiser-dashboard?status=withdrawal_success'));
            exit;
        } else {
            wp_redirect(home_url('/fundraiser-dashboard?status=withdrawal_failed'));
            exit;
        }
    }
}

new WF_Dashboard();
