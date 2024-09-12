<?php

class WF_Fundraiser {
    
    public function __construct() {
        add_action('init', array($this, 'wf_custom_registration_form'));
        add_shortcode('wf_fundraiser_registration', array($this, 'wf_registration_shortcode'));
        add_shortcode('wf_fundraiser_login', array($this, 'wf_login_shortcode'));

        // Handle login redirection
        add_action('wp_login', array($this, 'wf_redirect_after_login'), 10, 2);
    }

    // Fundraiser registration form
    public function wf_custom_registration_form() {
        if (isset($_POST['wf_register_fundraiser'])) {
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = esc_attr($_POST['password']);
            $errors = new WP_Error();

            if (empty($username) || empty($email) || empty($password)) {
                $errors->add('field', 'Required form field is missing.');
            }

            if (!is_email($email)) {
                $errors->add('email_invalid', 'Invalid email.');
            }

            if (username_exists($username)) {
                $errors->add('username_exists', 'Username already exists.');
            }

            if (email_exists($email)) {
                $errors->add('email_exists', 'Email already in use.');
            }

            if (empty($errors->get_error_codes())) {
                $user_id = wp_create_user($username, $password, $email);
                wp_update_user(array('ID' => $user_id, 'role' => 'fundraiser'));
				$promo_code = $this->generate_unique_promo_code();
                update_user_meta($user_id, 'promo_code', $promo_code);
                $this->wf_create_woocommerce_coupon($promo_code);
                // Auto login after registration
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                wp_redirect(home_url('/fundraiser-dashboard')); // Redirect to dashboard
                exit;
            }
        }
    }
	private function wf_create_woocommerce_coupon($promo_code) {
        
        $coupon = array(
            'post_title'   => $promo_code,
            'post_content' => 'Discount code for fundraisers.',
            'post_status'  => 'publish',
            'post_author'  => 1,
            'post_type'    => 'shop_coupon'
        );

        // Insert the coupon into the database
        $coupon_id = wp_insert_post($coupon);

        // Set coupon meta
        update_post_meta($coupon_id, 'discount_type', 'percent'); // Percentage discount
        update_post_meta($coupon_id, 'coupon_amount', '20'); // 20% discount
        update_post_meta($coupon_id, 'individual_use', 'no'); // Allow use with other coupons
        update_post_meta($coupon_id, 'exclude_sale_items', 'no'); // Apply to sale items
        update_post_meta($coupon_id, 'usage_limit', ''); // Unlimited usage
        update_post_meta($coupon_id, 'usage_limit_per_user', ''); // Unlimited per user
        update_post_meta($coupon_id, 'limit_usage_to_x_items', ''); // Unlimited per item
    }


private function generate_unique_promo_code() {
        $prefix = 'PROMO_';
        $unique_code = '';
        $is_unique = false;
        
        while (!$is_unique) {
            $unique_code = $prefix . strtoupper(bin2hex(random_bytes(4))); // Generates a unique code

            // Check if the promo code is already in use
            $args = array(
                'meta_key' => 'promo_code',
                'meta_value' => $unique_code,
                'meta_compare' => '=',
                'fields' => 'ID'
            );

            $existing_users = get_users($args);
            
            if (empty($existing_users)) {
                // Promo code is unique
                $is_unique = true;
            }
        }

        return $unique_code;
    }

    // Registration form shortcode
    public function wf_registration_shortcode() {
        ob_start();
        ?>
        <div class="wf-form-container">
            <h2>Register as Fundraiser</h2>
            <form method="POST">
                <p>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required />
                </p>
                <p>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required />
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required />
                </p>
                <p>
                    <input type="submit" name="wf_register_fundraiser" value="Register" />
                </p>
				<p>
                   Already have an account? <a href="<?php echo esc_url(home_url('/fundraiser-login')); ?>" style="Color:blue;"> Login now</a>
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Login form shortcode
    public function wf_login_shortcode() {
        ob_start();
        ?>
        <div class="wf-form-container">
            <h2>Fundraiser Login</h2>
            <?php wp_login_form(); ?>
			 <p>
                Don't have an account?<a href="<?php echo esc_url(home_url('/fundraiser-register')); ?>"style="Color:blue;"> Register now</a>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    // Redirect after login
    public function wf_redirect_after_login($user_login, $user) {
        if (in_array('fundraiser', (array)$user->roles)) {
            wp_redirect(home_url('/fundraiser-dashboard'));
            exit;
        }
    }
}

new WF_Fundraiser();
