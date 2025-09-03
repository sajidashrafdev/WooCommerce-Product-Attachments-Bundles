<?php

namespace WA_Elementor;

if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Attachments extends Widget_Base
{
	public function get_name()
	{
		return 'wa_attachments';
	}
	public function get_title()
	{
		return __('Woo Attachments', 'woo-attachments');
	}
	public function get_icon()
	{
		return 'eicon-products-archive';
	}
	public function get_categories()
	{
		return ['general'];
	}

	protected function register_controls()
	{
		$this->start_controls_section('content', ['label' => __('Content', 'woo-attachments')]);
		$this->add_control('source', [
			'label'   => __('Source Product', 'woo-attachments'),
			'type'    => Controls_Manager::SELECT,
			'options' => ['current' => __('Current Product (single product pages)', 'woo-attachments'), 'custom' => __('Custom Product', 'woo-attachments')],
			'default' => 'current',
		]);
		$this->add_control('product_id', ['label' => __('Product ID', 'woo-attachments'), 'type' => Controls_Manager::NUMBER, 'condition' => ['source' => 'custom']]);
		$this->add_control('layout', ['label' => __('Layout', 'woo-attachments'), 'type' => Controls_Manager::SELECT, 'options' => ['grid' => 'Grid', 'list' => 'List'], 'default' => 'grid']);
		$this->add_control('columns', ['label' => __('Columns', 'woo-attachments'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 6, 'step' => 1, 'default' => 3, 'condition' => ['layout' => 'grid']]);
		$this->add_control('show_image', ['label' => __('Show Image', 'woo-attachments'), 'type' => Controls_Manager::SWITCHER, 'label_on' => __('Show', 'woo-attachments'), 'label_off' => __('Hide', 'woo-attachments'), 'default' => 'yes']);
		$this->add_control('show_title', ['label' => __('Show Title', 'woo-attachments'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('show_price', ['label' => __('Show Price', 'woo-attachments'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('show_cart', ['label' => __('Show Add to Cart/Quote', 'woo-attachments'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('gap', ['label' => __('Grid Gap (px)', 'woo-attachments'), 'type' => Controls_Manager::NUMBER, 'default' => 16]);
		$this->end_controls_section();

		$this->start_controls_section('style', ['label' => __('Style', 'woo-attachments'), 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typo', 'selector' => '{{WRAPPER}} .wa-title']);
		$this->add_control('title_color', ['label' => __('Title Color', 'woo-attachments'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .wa-title a' => 'color: {{VALUE}}']]);
		$this->add_control('price_color', ['label' => __('Price Color', 'woo-attachments'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .wa-price' => 'color: {{VALUE}}']]);
		$this->add_control('card_padding', ['label' => __('Card Padding', 'woo-attachments'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'selectors' => ['{{WRAPPER}} .wa-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
		$this->add_control('card_border_radius', ['label' => __('Card Radius', 'woo-attachments'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 40]], 'selectors' => ['{{WRAPPER}} .wa-card' => 'border-radius: {{SIZE}}{{UNIT}};']]);
		$this->add_control('card_border', ['label' => __('Card Border', 'woo-attachments'), 'type' => Controls_Manager::TEXT, 'default' => '1px solid #eee', 'selectors' => ['{{WRAPPER}} .wa-card' => 'border: {{VALUE}};']]);
		$this->add_control('thumb_radius', ['label' => __('Image Radius', 'woo-attachments'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 40]], 'selectors' => ['{{WRAPPER}} .wa-thumb img' => 'border-radius: {{SIZE}}{{UNIT}};']]);
		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$product_id = 0;
		if ('current' === $settings['source'] && function_exists('is_product') && is_product()) {
			$product_id = get_the_ID();
		} else {
			$product_id = absint($settings['product_id']);
		}

		if (! $product_id) {
			return;
		}

		$ids = \WA_Helpers::get_attached_ids($product_id);
		if (empty($ids)) {
			return;
		}

		echo \WA_Helpers::render_cards($ids, [
			'layout'     => 'carousel', // force carousel mode
			'card_width' => 240,        // px width per card
			'gap'        => 16,         // px gap between cards
			'arrows'     => true,       // show prev/next buttons

			// same common options:
			'show_image' => true,
			'show_title' => true,
			'show_price' => true,
			'show_cart'  => true,
		]);
	}
}
