<?php
if (! defined('ABSPATH')) {
	exit;
}

class WA_Helpers
{

	public static function get_attached_ids($product_id)
	{
		$ids = get_post_meta($product_id, WA_Core::META_KEY, true);
		$ids = is_array($ids) ? array_map('absint', $ids) : [];
		return array_values(array_filter(array_unique($ids)));
	}

	public static function save_attached_ids($product_id, $ids)
	{
		$ids = array_map('absint', (array) $ids);
		update_post_meta($product_id, WA_Core::META_KEY, $ids);
	}

	public static function render_cards($product_ids, $settings = [])
	{
		if (empty($product_ids)) return '';

		$defaults = [
			'layout'      => 'grid', // 'grid' | 'list' | 'carousel'
			'columns'     => 3,
			'show_image'  => true,
			'show_title'  => true,
			'show_price'  => true,
			'show_cart'   => true,
			'image_size'  => 'woocommerce_thumbnail',
			'gap'         => 16,
			'class'       => '',
			// Carousel-only:
			'arrows'      => true,
			'card_width'  => 220,   // px
			'snap'        => true,
		];
		$settings = wp_parse_args($settings, $defaults);

		wp_enqueue_style('wa-frontend');

		$columns = max(1, (int) $settings['columns']);
		$gap     = max(0, (int) $settings['gap']);

		$wrapper_classes = [
			'wa-attachments',
			'wa-layout-' . $settings['layout'],
			$settings['layout'] === 'grid' ? 'wa-cols-' . $columns : '',
			sanitize_html_class($settings['class']),
		];

		$is_carousel = ($settings['layout'] === 'carousel');
		$uid         = uniqid('wa_');

		// Inline layout styles (avoid theme overrides)
		if ($is_carousel) {
			$style = 'display:flex;overflow-x:auto;gap:' . intval($gap) . 'px;'
				. ($settings['snap'] ? 'scroll-snap-type:x mandatory;' : '')
				. '-webkit-overflow-scrolling:touch;';
		} else {
			$style = ($settings['layout'] === 'grid')
				? 'display:grid;grid-template-columns:repeat(' . intval($columns) . ',minmax(0,1fr));gap:' . intval($gap) . 'px'
				: '';
		}

		ob_start();

		// For carousel, wrap for arrow positioning
		if ($is_carousel) {
			echo '<div class="wa-carousel-wrap" id="' . esc_attr($uid) . '">';
			if ($settings['arrows']) {
				echo '<button class="wa-nav wa-prev" type="button" aria-label="Previous" data-target="#' . esc_attr($uid) . '">‹</button>';
			}
		}

		echo '<div class="' . esc_attr(trim(implode(' ', array_filter($wrapper_classes)))) . '"'
			. ' style="' . esc_attr($style) . '"'
			. ($is_carousel ? ' tabindex="0" role="region" aria-label="Attachments carousel"' : '')
			. '>';

		$loop = new WP_Query([
			'post_type'           => 'product',
			'post__in'            => $product_ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => true,
		]);

		if ($loop->have_posts()) :
			while ($loop->have_posts()) : $loop->the_post();
				$product = wc_get_product(get_the_ID());
				if (! $product) continue;

				$card_style = '';
				if ($is_carousel) {
					$w = max(120, (int) $settings['card_width']);
					$card_style = 'flex:0 0 auto;width:' . $w . 'px;'
						. ($settings['snap'] ? 'scroll-snap-align:start;' : '');
				}

				echo '<div class="wa-card" style="' . esc_attr($card_style) . '">';

				if ($settings['show_image']) {
					echo '<a class="wa-thumb" href="' . esc_url(get_permalink()) . '">';
					echo $product->get_image($settings['image_size']);
					echo '</a>';
				}

				if ($settings['show_title']) {
					echo '<h3 class="wa-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
				}

				if ($settings['show_price']) {
					echo '<div class="wa-price">' . wp_kses_post($product->get_price_html()) . '</div>';
				}

				if ($settings['show_cart']) {
					ob_start();
					do_action('woocommerce_after_shop_loop_item');
					$wa_button_html = ob_get_clean();
					if (! trim($wa_button_html)) {
						ob_start();
						woocommerce_template_loop_add_to_cart();
						$wa_button_html = ob_get_clean();
					}
					$wa_button_html = apply_filters('wa_button_html', $wa_button_html, $product, $settings);
					echo $wa_button_html;
				}

				echo '</div>';
			endwhile;
			wp_reset_postdata();
		endif;

		echo '</div>'; // .wa-attachments

		if ($is_carousel) {
			if ($settings['arrows']) {
				echo '<button class="wa-nav wa-next" type="button" aria-label="Next" data-target="#' . esc_attr($uid) . '">›</button>';
			}
			echo '</div>'; // .wa-carousel-wrap
		}

		$html = ob_get_clean();

		// Minimal inline JS for arrow controls (once per request)
		static $did_inline = false;
		if ($is_carousel && $settings['arrows'] && ! $did_inline) {
			$did_inline = true;
			wp_register_script('wa-carousel-js', '', [], null, true);
			wp_enqueue_script('wa-carousel-js');
			wp_add_inline_script('wa-carousel-js', "
		(function(){
			function scrollByCard(container, dir){
				if(!container) return;
				var card = container.querySelector('.wa-card');
				var delta = 300, gap = " . (int) $gap . ";
				if(card){
					var rect = card.getBoundingClientRect();
					delta = rect.width + gap;
				}
				container.scrollBy({left: dir * delta, behavior:'smooth'});
			}
			document.addEventListener('click', function(e){
				var btn = e.target.closest('.wa-nav');
				if(!btn) return;
				var wrap = document.querySelector(btn.getAttribute('data-target'));
				if(!wrap) return;
				var scroller = wrap.querySelector('.wa-attachments');
				if(!scroller) return;
				if(btn.classList.contains('wa-prev')) scrollByCard(scroller, -1);
				if(btn.classList.contains('wa-next')) scrollByCard(scroller,  1);
			});
		})();");
		}

		return $html;
	}
}
