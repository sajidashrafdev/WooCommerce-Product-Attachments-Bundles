<?php
/**
 * Plugin Name: WooCommerce Product Attachments & Bundles
 * Description: Add "Attachments" (other WooCommerce products) to a product and display them anywhere via Elementor widget or shortcode.
 * Author: Ahsan Riaz & Sajid Ashraf
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: woo-attachments
 *
 * @package Woo_Attachments
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WA_PLUGIN_FILE', __FILE__ );
define( 'WA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function(){
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Woo Attachments requires WooCommerce to be active.', 'woo-attachments' ) . '</p></div>';
		});
		return;
	}

	load_plugin_textdomain( 'woo-attachments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	require_once WA_PLUGIN_DIR . 'includes/class-wa-helpers.php';
	require_once WA_PLUGIN_DIR . 'includes/class-wa-core.php';
	require_once WA_PLUGIN_DIR . 'includes/class-wa-admin-attachments.php';
	require_once WA_PLUGIN_DIR . 'includes/class-wa-shortcodes.php';

	WA_Core::init();
	WA_Admin_Attachments::init();
	WA_Shortcodes::init();
});
