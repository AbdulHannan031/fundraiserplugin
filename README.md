# Woo Fundraiser Plugin

Woo Fundraiser is a custom WordPress plugin designed for WooCommerce to enable fundraising functionality. It allows fundraisers to register, generate unique promo codes, track earnings, and request withdrawals. The plugin also integrates with WooCommerce to create and manage discount coupons.

## Features

- **Fundraiser Registration**: Users can register as fundraisers and receive unique promo codes.
- **Promo Code Generation**: Automatically generates unique promo codes for fundraisers.
- **WooCommerce Coupon Integration**: Creates WooCommerce coupons linked to fundraiser promo codes.
- **Earnings Tracking**: Tracks earnings for fundraisers based on coupon usage.
- **Fundraiser Dashboard**: Displays promo codes, earnings, and withdrawal options.
- **Withdrawal Requests**: Fundraisers can request withdrawals when earnings meet the threshold.
- **Social Sharing**: Fundraisers can share their promo codes on social media.

## Installation

1. Download the plugin files and place them in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin from the WordPress admin dashboard under **Plugins**.

## File Structure
woo-fundraiser.php assets/ css/ style.css includes/ class-wf-coupons.php class-wf-dashboard.php class-wf-earnings.php class-wf-fundraiser.php

### File Descriptions

#### [woo-fundraiser.php](woo-fundraiser.php)
- The main plugin file.
- Registers activation and deactivation hooks.
- Enqueues custom styles.
- Includes the necessary class files.

#### [assets/css/style.css](assets/css/style.css)
- Contains the styles for the plugin's forms and dashboard.

#### [includes/class-wf-fundraiser.php](includes/class-wf-fundraiser.php)
- Handles fundraiser registration and login.
- Generates unique promo codes for fundraisers.
- Creates WooCommerce coupons for promo codes.
- Redirects fundraisers to their dashboard after login.

#### [includes/class-wf-dashboard.php](includes/class-wf-dashboard.php)
- Provides a shortcode for the fundraiser dashboard.
- Displays promo codes, earnings, and withdrawal options.
- Handles withdrawal requests and notifies the admin and fundraiser.

#### [includes/class-wf-earnings.php](includes/class-wf-earnings.php)
- Tracks earnings for fundraisers based on WooCommerce orders.
- Assigns commissions to fundraisers when their promo codes are used.

#### [includes/class-wf-coupons.php](includes/class-wf-coupons.php)
- Generates promo codes for fundraisers upon registration.
- Creates WooCommerce coupons linked to the promo codes.

## Shortcodes

- `[wf_fundraiser_registration]`: Displays the fundraiser registration form.
- `[wf_fundraiser_login]`: Displays the fundraiser login form.
- `[wf_fundraiser_dashboard]`: Displays the fundraiser dashboard.

## Usage

1. Add the `[wf_fundraiser_registration]` shortcode to a page to allow fundraisers to register.
2. Add the `[wf_fundraiser_login]` shortcode to a page for fundraiser login.
3. Add the `[wf_fundraiser_dashboard]` shortcode to a page to display the fundraiser dashboard.

## Withdrawal Process

- Fundraisers can request withdrawals from their dashboard when their earnings meet the minimum threshold (default: $20).
- Admins are notified via email when a withdrawal request is made.

## Customization

- Modify the styles in `assets/css/style.css` to match your site's design.
- Adjust the commission amount or withdrawal threshold in the respective class files.

## License

This plugin is open-source and available under the [MIT License](LICENSE).

## Author

Developed by Abdul Hannan.
