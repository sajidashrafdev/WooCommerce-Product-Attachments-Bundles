<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WA_Core {
	const META_KEY = '_wa_attached_product_ids';

	public static function init() {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_front_assets' ] );
		add_action( 'elementor/widgets/register', [ __CLASS__, 'register_elementor_widget' ] );
		add_action( 'elementor/widgets/widgets_registered', [ __CLASS__, 'register_elementor_widget_legacy' ] );
	}

	public static function enqueue_front_assets() {
		wp_register_style( 'wa-frontend', WA_PLUGIN_URL . 'assets/css/frontend.css', [], '1.0.3' );
	}

	public static function register_elementor_widget( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) { return; }
		require_once WA_PLUGIN_DIR . 'includes/Elementor/class-widget-attachments.php';
		$widget = new \WA_Elementor\Widget_Attachments();
		if ( method_exists( $widgets_manager, 'register' ) ) {
			$widgets_manager->register( $widget );
		} else {
			$widgets_manager->register_widget_type( $widget );
		}
	}

	public static function register_elementor_widget_legacy() {
		if ( ! did_action( 'elementor/loaded' ) ) return;
		$plugin = \Elementor\Plugin::instance();
		self::register_elementor_widget( $plugin->widgets_manager );
	}
}
