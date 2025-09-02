<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WA_Admin_Attachments {

	public static function init() {
		add_filter( 'woocommerce_product_data_tabs',   [ __CLASS__, 'add_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ __CLASS__, 'add_panel' ] );
		add_action( 'woocommerce_admin_process_product_object', [ __CLASS__, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_scripts' ] );
	}

	public static function add_tab( $tabs ) {
		$tabs['wa_attachments'] = [
			'label'    => __( 'Attachments', 'woo-attachments' ),
			'target'   => 'wa_attachments_data',
			'class'    => ['show_if_simple','show_if_variable','show_if_grouped','show_if_external'],
			'priority' => 80,
		];
		return $tabs;
	}

	public static function add_panel() {
		global $post;
		$product_id = $post ? $post->ID : 0;
		$attached = WA_Helpers::get_attached_ids( $product_id );

		echo '<div id="wa_attachments_data" class="panel woocommerce_options_panel">';
		echo '<div class="options_group">';

		echo '<p class="form-field">';
		echo '<label for="wa_attached_ids">' . esc_html__( 'Attach Products', 'woo-attachments' ) . '</label>';
		echo '<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="wa_attached_ids" name="wa_attached_ids[]" data-placeholder="' . esc_attr__( 'Search for products…', 'woo-attachments' ) . '" data-action="woocommerce_json_search_products_and_variations">';

		if ( ! empty( $attached ) ) {
			foreach ( $attached as $pid ) {
				$product = wc_get_product( $pid );
				if ( ! $product ) continue;
				echo '<option value="' . esc_attr( $pid ) . '" selected="selected">' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
			}
		}

		echo '</select>';
		echo '<span class="description">' . esc_html__( 'Displayed via the Elementor widget or [wc_attachments] shortcode.', 'woo-attachments' ) . '</span>';
		echo '</p>';

		echo '</div></div>';
	}

	public static function save( $product ) {
		if ( ! current_user_can( 'edit_product', $product->get_id() ) ) return;

		$ids = isset( $_POST['wa_attached_ids'] ) ? (array) $_POST['wa_attached_ids'] : [];
		$ids = array_map( 'absint', array_filter( $ids ) );
		WA_Helpers::save_attached_ids( $product->get_id(), $ids );
	}

	public static function admin_scripts( $hook ) {
		if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			wp_enqueue_script( 'wc-enhanced-select' );
			wp_enqueue_style( 'woocommerce_admin_styles' );
		}
	}
}
