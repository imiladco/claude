<?php
/**
 * ابزارک «دکمهٔ سبد خرید» (ووکامرس) — آیکون + متن (اختیاری) + شمارشگر تعداد اقلام
 * سبد، لینک‌دار و کاملاً قابل استایل‌دهی. شمارشگر با موقعیت‌دهی مطلق و کنترل کامل
 * می‌تواند روی گوشهٔ دکمه قرار گیرد و هنگام افزودن محصول به‌صورت زنده بروزرسانی می‌شود.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Class ANW_Cart_Button_Widget
 */
class ANW_Cart_Button_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-cart-button';
	}

	public function get_title(): string {
		return esc_html__( 'دکمهٔ سبد خرید', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-cart-medium';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'cart', 'basket', 'count', 'woocommerce', 'سبد', 'خرید', 'شمارشگر', 'ووکامرس' ];
	}

	public function get_style_depends(): array {
		return [ 'anw-widgets' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_box_style_controls();
		$this->register_icon_style_controls();
		$this->register_text_style_controls();
		$this->register_count_style_controls();
	}

	/* ============================ محتوا ============================ */

	private function register_content_controls(): void {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'محتوا', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'        => esc_html__( 'نمایش آیکون', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'       => esc_html__( 'آیکون (تصویر / SVG)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => [ 'image', 'svg' ],
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition'   => [ 'show_icon' => 'yes' ],
			]
		);

		$this->add_control(
			'text',
			[
				'label'       => esc_html__( 'متن (اختیاری)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'مثلاً: سبد خرید', 'asre-nokhbegan-widgets' ),
				'dynamic'     => [ 'active' => true ],
				'description' => esc_html__( 'اگر خالی باشد، فقط آیکون و شمارشگر نمایش داده می‌شوند.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__( 'لینک', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'خالی = صفحهٔ سبد خرید', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'اگر خالی بماند، به‌صورت خودکار به صفحهٔ سبد خرید ووکامرس لینک می‌شود.', 'asre-nokhbegan-widgets' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'        => esc_html__( 'نمایش شمارشگر', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'hide_when_empty',
			[
				'label'        => esc_html__( 'پنهان‌کردن شمارشگر وقتی سبد خالی است', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'show_count' => 'yes' ],
				'selectors'    => [
					'{{WRAPPER}} .anw-cart-count--empty' => 'display: none;',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ چیدمان (آیکون و متن) ============================ */

	private function register_layout_controls(): void {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'چیدمان آیکون و متن', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'                => esc_html__( 'موقعیت آیکون', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'row'            => [
						'title' => esc_html__( 'ابتدا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
					'row-reverse'    => [
						'title' => esc_html__( 'انتها', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
					'column'         => [
						'title' => esc_html__( 'بالا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-top',
					],
					'column-reverse' => [
						'title' => esc_html__( 'پایین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'row',
				'selectors_dictionary' => [
					'row'            => 'flex-direction: row;',
					'row-reverse'    => 'flex-direction: row-reverse;',
					'column'         => 'flex-direction: column;',
					'column-reverse' => 'flex-direction: column-reverse;',
				],
				'selectors'            => [
					'{{WRAPPER}} .anw-cart' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_justify',
			[
				'label'     => esc_html__( 'توزیع افقی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'flex-start'    => [
						'title' => esc_html__( 'شروع', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-start-h',
					],
					'center'        => [
						'title' => esc_html__( 'وسط', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end'      => [
						'title' => esc_html__( 'پایان', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html__( 'دو سرِ کادر', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-space-between-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-cart' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'     => esc_html__( 'تراز عمودی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'شروع', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'وسط', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'پایان', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-end-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-cart' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ آیکون تا متن', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-cart' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: کادر دکمه ============================ */

	private function register_box_style_controls(): void {
		$box = '{{WRAPPER}} .anw-cart';

		$this->start_controls_section(
			'box_style_section',
			[
				'label' => esc_html__( 'کادر دکمه', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_width',
			[
				'label'      => esc_html__( 'عرض', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 600 ],
					'%'  => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors'  => [ $box => 'width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'box_height',
			[
				'label'      => esc_html__( 'ارتفاع', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 600 ] ],
				'selectors'  => [ $box => 'height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$box => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$box => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$box => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_transition',
			[
				'label'     => esc_html__( 'مدت انیمیشن (ثانیه)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'default'   => [ 'size' => 0.3 ],
				'selectors' => [ $box => 'transition: all {{SIZE}}s ease;' ],
			]
		);

		$this->start_controls_tabs( 'box_tabs' );

		$this->start_controls_tab( 'box_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $box,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => $box,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => $box,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'box_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $box . ':hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border_hover',
				'selector' => $box . ':hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => $box . ':hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: آیکون ============================ */

	private function register_icon_style_controls(): void {
		$this->start_controls_section(
			'icon_style_section',
			[
				'label'     => esc_html__( 'آیکون', 'asre-nokhbegan-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_icon' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 300 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-cart-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .anw-cart-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab( 'icon_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-cart-icon svg, {{WRAPPER}} .anw-cart-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-cart:hover .anw-cart-icon svg, {{WRAPPER}} .anw-cart:hover .anw-cart-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: متن ============================ */

	private function register_text_style_controls(): void {
		$text = '{{WRAPPER}} .anw-cart-text';

		$this->start_controls_section(
			'text_style_section',
			[
				'label' => esc_html__( 'متن', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => $text,
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$text => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'text_tabs' );

		$this->start_controls_tab( 'text_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $text => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'text_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'text_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .anw-cart:hover .anw-cart-text' => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: شمارشگر ============================ */

	private function register_count_style_controls(): void {
		$count = '{{WRAPPER}} .anw-cart-count';

		$this->start_controls_section(
			'count_style_section',
			[
				'label'     => esc_html__( 'شمارشگر', 'asre-nokhbegan-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_count' => 'yes' ],
			]
		);

		/* --- موقعیت --- */
		$this->add_control(
			'count_position_heading',
			[
				'label' => esc_html__( 'موقعیت (نسبت به دکمه)', 'asre-nokhbegan-widgets' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'count_h_anchor',
			[
				'label'   => esc_html__( 'لنگرِ افقی', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'  => [
						'title' => esc_html__( 'چپ', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'راست', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'count_offset_left',
			[
				'label'      => esc_html__( 'فاصله از چپ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => -200, 'max' => 400 ] ],
				'default'    => [ 'size' => -8, 'unit' => 'px' ],
				'condition'  => [ 'count_h_anchor' => 'left' ],
				'selectors'  => [ $count => 'left: {{SIZE}}{{UNIT}}; right: auto;' ],
			]
		);

		$this->add_responsive_control(
			'count_offset_right',
			[
				'label'      => esc_html__( 'فاصله از راست', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => -200, 'max' => 400 ] ],
				'default'    => [ 'size' => -8, 'unit' => 'px' ],
				'condition'  => [ 'count_h_anchor' => 'right' ],
				'selectors'  => [ $count => 'right: {{SIZE}}{{UNIT}}; left: auto;' ],
			]
		);

		$this->add_control(
			'count_v_anchor',
			[
				'label'   => esc_html__( 'لنگرِ عمودی', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'top'    => [
						'title' => esc_html__( 'بالا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'پایین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
			]
		);

		$this->add_responsive_control(
			'count_offset_top',
			[
				'label'      => esc_html__( 'فاصله از بالا', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => -200, 'max' => 400 ] ],
				'default'    => [ 'size' => -8, 'unit' => 'px' ],
				'condition'  => [ 'count_v_anchor' => 'top' ],
				'selectors'  => [ $count => 'top: {{SIZE}}{{UNIT}}; bottom: auto;' ],
			]
		);

		$this->add_responsive_control(
			'count_offset_bottom',
			[
				'label'      => esc_html__( 'فاصله از پایین', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => -200, 'max' => 400 ] ],
				'default'    => [ 'size' => -8, 'unit' => 'px' ],
				'condition'  => [ 'count_v_anchor' => 'bottom' ],
				'selectors'  => [ $count => 'bottom: {{SIZE}}{{UNIT}}; top: auto;' ],
			]
		);

		/* --- ابعاد و کادر --- */
		$this->add_responsive_control(
			'count_min_size',
			[
				'label'       => esc_html__( 'حداقل عرض و ارتفاع', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'range'       => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
				'default'     => [ 'size' => 28, 'unit' => 'px' ],
				'separator'   => 'before',
				'description' => esc_html__( 'برای شمارشگر دایره‌ای این مقدار را تنظیم و گردی گوشه را ۵۰٪ بگذارید.', 'asre-nokhbegan-widgets' ),
				'selectors'   => [
					$count => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					$count => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => '%',
					'isLinked' => true,
				],
				'selectors'  => [
					$count => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		/* --- تایپوگرافی و رنگ --- */
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'selector' => $count,
			]
		);

		$this->start_controls_tabs( 'count_tabs' );

		$this->start_controls_tab( 'count_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ $count => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'count_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $count,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'count_border',
				'selector' => $count,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'count_shadow',
				'selector' => $count,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'count_hover_tab', [ 'label' => esc_html__( 'هاور دکمه', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .anw-cart:hover .anw-cart-count' => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'count_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-cart:hover .anw-cart-count',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ منطق ============================ */

	private function get_cart_count(): int {
		if ( function_exists( 'WC' ) && WC()->cart ) {
			return (int) WC()->cart->get_cart_contents_count();
		}
		return 0;
	}

	private function is_edit_mode(): bool {
		return class_exists( '\Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		// مقصد لینک: مقدار دستی، در غیر این صورت صفحهٔ سبد خرید.
		$has_link = ! empty( $settings['link']['url'] );
		$tag      = 'a';

		$this->add_render_attribute( 'wrapper', 'class', 'anw-cart' );
		if ( $has_link ) {
			$this->add_link_attributes( 'wrapper', $settings['link'] );
		} else {
			$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
			$this->add_render_attribute( 'wrapper', 'href', esc_url( $cart_url ) );
		}
		$this->add_render_attribute( 'wrapper', 'aria-label', esc_attr__( 'سبد خرید', 'asre-nokhbegan-widgets' ) );

		$show_icon = ( 'yes' === $settings['show_icon'] );
		$icon_html = $show_icon ? anw_get_media_icon_html( $settings['icon_image'] ) : '';
		$text      = isset( $settings['text'] ) ? trim( (string) $settings['text'] ) : '';

		$show_count = ( 'yes' === $settings['show_count'] );
		$count      = $this->get_cart_count();
		if ( 0 === $count && $this->is_edit_mode() ) {
			$count = 2; // نمونهٔ نمایشی در ویرایشگر.
		}
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $icon_html ) : ?>
				<span class="anw-cart-icon" aria-hidden="true"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — خروجی SVG/IMG پیش‌تر پاک‌سازی شده. ?></span>
			<?php endif; ?>

			<?php if ( '' !== $text ) : ?>
				<span class="anw-cart-text"><?php echo esc_html( $text ); ?></span>
			<?php endif; ?>

			<?php
			if ( $show_count ) {
				echo anw_cart_count_badge_html( $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup امن و پاک‌سازی‌شده در helper.
			}
			?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
