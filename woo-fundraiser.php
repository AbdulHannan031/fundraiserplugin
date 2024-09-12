<?php
/*
Plugin Name: Woo Fundraiser
Description: Custom plugin for WooCommerce fundraising with promo codes, dashboards, and earnings tracking.
Version: 1.0
Author: Abdul Hannan
*/
// Enqueue custom styles
add_action('wp_enqueue_scripts', 'wf_enqueue_styles');
function wf_enqueue_styles() {
    wp_enqueue_style('wf-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-wf-fundraiser.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wf-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wf-coupons.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wf-earnings.php';

// Activation Hook
register_activation_hook(__FILE__, 'wf_activate_plugin');

function wf_activate_plugin() {
    // Add custom fundraiser role
    add_role(
        'fundraiser',
        __('Fundraiser'),
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        )
    );
}

function wf_deactivate_plugin() {
    // Cleanup if necessary (optional)
    remove_role('fundraiser');
}

register_deactivation_hook(__FILE__, 'wf_deactivate_plugin');
