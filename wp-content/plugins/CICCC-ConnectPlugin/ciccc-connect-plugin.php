<?php
/**
 * Plugin Name: CICCC Connect Plugin for Elementor
 * Description: A customizable event list plugin for CICCC with Elementor integration
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('CICCC_VERSION', '1.0');
define('CICCC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CICCC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once CICCC_PLUGIN_DIR . 'includes/rest-api.php';

// Initialize the plugin
function ciccc_init() {
    // Register REST API routes
    add_action('rest_api_init', 'ciccc_register_rest_routes');
    
    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'ciccc_enqueue_scripts');
}
add_action('plugins_loaded', 'ciccc_init');

// Enqueue scripts and styles
function ciccc_enqueue_scripts() {
    wp_enqueue_style('ciccc-styles', CICCC_PLUGIN_URL . 'assets/css/event-list-style.css', array(), CICCC_VERSION);
    wp_enqueue_script('ciccc-script', CICCC_PLUGIN_URL . 'assets/js/event-list-script.js', array('jquery'), CICCC_VERSION, true);
}

// Check if Elementor is active and load the widget
function ciccc_elementor_init() {
    // Load Elementor widget only if Elementor is active
    if (did_action('elementor/loaded')) {
        require_once CICCC_PLUGIN_DIR . 'includes/elementor-widget.php';
    }
}
add_action('plugins_loaded', 'ciccc_elementor_init');