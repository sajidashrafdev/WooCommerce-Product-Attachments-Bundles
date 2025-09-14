<?php

namespace WA_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Attachments extends Widget_Base {
	public function get_name() {
		return 'wa_attachments';
	}
	public function get_title() {
		return __( 'Woo Attachments', 'woo-attachments' );
	}
	public function get_icon() {
		return 'eicon-products-archive';
	}
	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', [ 'label' => __( 'Content', 'woo-attachments' ) ] );

		$this->add_control( 'source', [
			'label'   => __( 'Source Product', 'woo-attachments' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'current' => __( 'Current Product (single product pages)', 'woo-attachments' ),
				'custom'  => __( 'Custom Product', 'woo-attachments' ),
			],
			'default' => 'current',
		] );

		$this->add_control( 'product_id', [
			'label'     => __( 'Product ID', 'woo-attachments' ),
			'type'      => Controls_Manager::NUMBER,
			'condition' => [ 'source' => 'custom' ],
		] );

		// Add Carousel to layout options
		$this->add_control( 'layout', [
			'label'   => __( 'Layout', 'woo-attachments' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'grid'     => __( 'Grid', 'woo-attachments' ),
				'list'     => __( 'List', 'woo-attachments' ),
				'carousel' => __( 'Carousel', 'woo-attachments' ),
			],
			'default' => 'carousel',
		] );

		$this->add_control( 'columns', [
			'label'     => __( 'Columns', 'woo-attachments' ),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 1,
			'max'       => 6,
			'step'      => 1,
			'default'   => 3,
			'condition' => [ 'layout' => 'grid' ],
		] );

		$this->add_control( 'show_image', [
			'label'        => __( 'Show Image', 'woo-attachments' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'woo-attachments' ),
			'label_off'    => __( 'Hide', 'woo-attachments' ),
			'default'      => 'yes',
		] );

		$this->add_control( 'show_title', [
			'label'   => __( 'Show Title', 'woo-attachments' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'show_price', [
			'label'   => __( 'Show Price', 'woo-attachments' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'show_cart', [
			'label'   => __( 'Show Add to Cart/Quote', 'woo-attachments' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'gap', [
			'label'   => __( 'Gap (px)', 'woo-attachments' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 16,
		] );

		// ---- Carousel settings (visible only when layout = carousel) ----
		$this->add_control( 'card_width', [
			'label'     => __( 'Card Width (px)', 'woo-attachments' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 240,
			'min'       => 120,
			'max'       => 600,
			'step'      => 1,
			'condition' => [ 'layout' => 'carousel' ],
		] );

		$this->add_control( 'arrows', [
			'label'     => __( 'Show Arrows', 'woo-attachments' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'layout' => 'carousel' ],
		] );

		$this->add_control( 'snap', [
			'label'     => __( 'Snap to Cards', 'woo-attachments' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'layout' => 'carousel' ],
		] );

		$this->add_control( 'auto_scroll', [
			'label'     => __( 'Auto-Scroll', 'woo-attachments' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'layout' => 'carousel' ],
		] );

		$this->add_control( 'scroll_mode', [
			'label'     => __( 'Scroll Mode', 'woo-attachments' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'lazy' => __( 'Continuous (lazy drift)', 'woo-attachments' ),
				'step' => __( 'Step by card (interval)', 'woo-attachments' ),
			],
			'default'   => 'lazy',
			'condition' => [ 'layout' => 'carousel', 'auto_scroll' => 'yes' ],
		] );

		$this->add_control( 'speed', [
			'label'       => __( 'Speed', 'woo-attachments' ),
			'description' => __( 'For lazy drift: pixels per second. For step mode: ignored.', 'woo-attachments' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 30,
			'min'         => 5,
			'max'         => 200,
			'step'        => 1,
			'condition'   => [ 'layout' => 'carousel', 'auto_scroll' => 'yes', 'scroll_mode' => 'lazy' ],
		] );

		$this->add_control( 'resume_delay', [
			'label'       => __( 'Resume Delay (ms)', 'woo-attachments' ),
			'description' => __( 'Idle time before auto-scroll resumes after user interaction/hover.', 'woo-attachments' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 1800,
			'min'         => 0,
			'max'         => 10000,
			'step'        => 100,
			'condition'   => [ 'layout' => 'carousel', 'auto_scroll' => 'yes' ],
		] );

		$this->add_control( 'pause_on_hover', [
			'label'     => __( 'Pause on Hover/Interaction', 'woo-attachments' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'layout' => 'carousel', 'auto_scroll' => 'yes' ],
		] );

		$this->end_controls_section();

		// ---- Style tab ----
		$this->start_controls_section( 'style', [ 'label' => __( 'Style', 'woo-attachments' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .wa-title',
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'woo-attachments' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .wa-title a' => 'color: {{VALUE}}' ],
		] );

		$this->add_control( 'price_color', [
			'label'     => __( 'Price Color', 'woo-attachments' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .wa-price' => 'color: {{VALUE}}' ],
		] );

		$this->add_control( 'card_padding', [
			'label'     => __( 'Card Padding', 'woo-attachments' ),
			'type'      => Controls_Manager::DIMENSIONS,
			'size_units'=> [ 'px' ],
			'selectors' => [ '{{WRAPPER}} .wa-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'card_border_radius', [
			'label'     => __( 'Card Radius', 'woo-attachments' ),
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'selectors' => [ '{{WRAPPER}} .wa-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'card_border', [
			'label'     => __( 'Card Border', 'woo-attachments' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '1px solid #eee',
			'selectors' => [ '{{WRAPPER}} .wa-card' => 'border: {{VALUE}};' ],
		] );

		$this->add_control( 'thumb_radius', [
			'label'     => __( 'Image Radius', 'woo-attachments' ),
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'selectors' => [ '{{WRAPPER}} .wa-thumb img' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_id = 0;
		if ( 'current' === $settings['source'] && function_exists( 'is_product' ) && is_product() ) {
			$product_id = get_the_ID();
		} else {
			$product_id = absint( $settings['product_id'] );
		}
		if ( ! $product_id ) {
			return;
		}

		$ids = \WA_Helpers::get_attached_ids( $product_id );
		if ( empty( $ids ) ) {
			return;
		}

		// Normalize booleans from switchers (Elementor returns 'yes'/'')
		$show_image     = ! empty( $settings['show_image'] ) && 'yes' === $settings['show_image'];
		$show_title     = ! empty( $settings['show_title'] ) && 'yes' === $settings['show_title'];
		$show_price     = ! empty( $settings['show_price'] ) && 'yes' === $settings['show_price'];
		$show_cart      = ! empty( $settings['show_cart'] ) && 'yes' === $settings['show_cart'];

		$arrows         = ! empty( $settings['arrows'] ) && 'yes' === $settings['arrows'];
		$snap           = ! empty( $settings['snap'] ) && 'yes' === $settings['snap'];
		$auto_scroll    = ! empty( $settings['auto_scroll'] ) && 'yes' === $settings['auto_scroll'];
		$pause_on_hover = ! empty( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'];

		$layout         = ! empty( $settings['layout'] ) ? $settings['layout'] : 'carousel';
		$columns        = isset( $settings['columns'] ) ? (int) $settings['columns'] : 3;
		$gap            = isset( $settings['gap'] ) ? (int) $settings['gap'] : 16;
		$card_width     = isset( $settings['card_width'] ) ? (int) $settings['card_width'] : 240;
		$speed          = isset( $settings['speed'] ) ? (int) $settings['speed'] : 30;
		$resume_delay   = isset( $settings['resume_delay'] ) ? (int) $settings['resume_delay'] : 1800;
		$scroll_mode    = ! empty( $settings['scroll_mode'] ) ? $settings['scroll_mode'] : 'lazy';

		echo \WA_Helpers::render_cards( $ids, [
			'layout'         => $layout,
			'columns'        => $columns,
			'show_image'     => $show_image,
			'show_title'     => $show_title,
			'show_price'     => $show_price,
			'show_cart'      => $show_cart,
			'gap'            => $gap,

			// Carousel-related:
			'card_width'     => $card_width,
			'arrows'         => $arrows,
			'snap'           => $snap,

			'auto_scroll'    => $auto_scroll,
			'scroll_mode'    => $scroll_mode,   // 'lazy' | 'step'
			'speed'          => $speed,         // px/sec for lazy
			'resume_delay'   => $resume_delay,  // ms
			'pause_on_hover' => $pause_on_hover,
		] );
	}
}
