<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WA_Shortcodes {
	public static function init() { add_shortcode( 'wc_attachments', [ __CLASS__, 'shortcode' ] ); }
	public static function shortcode( $atts ) {
		$atts = shortcode_atts([
			'product_id'  => 0,
			'layout'      => 'grid',
			'columns'     => 3,
			'show_image'  => 'yes',
			'show_title'  => 'yes',
			'show_price'  => 'yes',
			'show_cart'   => 'yes',
			'image_size'  => 'woocommerce_thumbnail',
			'gap'         => 16,
			'class'       => '',
		], $atts, 'wc_attachments' );

		$product_id = absint( $atts['product_id'] );
		if ( ! $product_id && function_exists( 'is_product' ) && is_product() ) {
			$product_id = get_the_ID();
		}
		if ( ! $product_id ) return '';

		$ids = WA_Helpers::get_attached_ids( $product_id );
		if ( empty( $ids ) ) return '';

		$settings = [
			'layout'     => sanitize_key( $atts['layout'] ),
			'columns'    => absint( $atts['columns'] ),
			'show_image' => ( $atts['show_image'] === 'yes' ),
			'show_title' => ( $atts['show_title'] === 'yes' ),
			'show_price' => ( $atts['show_price'] === 'yes' ),
			'show_cart'  => ( $atts['show_cart'] === 'yes' ),
			'image_size' => sanitize_key( $atts['image_size'] ),
			'gap'        => absint( $atts['gap'] ),
			'class'      => sanitize_html_class( $atts['class'] ),
		];

		return WA_Helpers::render_cards( $ids, $settings );
	}
}
