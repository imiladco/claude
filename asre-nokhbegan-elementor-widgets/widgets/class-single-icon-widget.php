<?php
/**
 * ابزارک «آیکون تکی» — فقط یک آیکون (تصویر/SVG) با استایل کامل کادر و آیکون.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Class ANW_Single_Icon_Widget
 */
class ANW_Single_Icon_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-single-icon';
	}

	public function get_title(): string {
		return esc_html__( 'آیکون تکی', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-favorite';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'icon', 'image', 'svg', 'single', 'آیکون', 'تصویر', 'تکی' ];
	}

	public function get_style_depends(): array {
		return [ 'anw-widgets' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_box_style_controls();
		$this->register_icon_style_controls();
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
			'icon_image',
			[
				'label'       => esc_html__( 'آیکون (تصویر / SVG)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => [ 'image', 'svg' ],
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__( 'لینک (اختیاری)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'چینش', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'right'  => [
						'title' => esc_html__( 'راست', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-text-align-right',
					],
					'center' => [
						'title' => esc_html__( 'وسط', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-text-align-center',
					],
					'left'   => [
						'title' => esc_html__( 'چپ', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-text-align-left',
					],
				],
				'default'   => 'center',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: کادر (باکس) ============================ */

	private function register_box_style_controls(): void {
		$this->start_controls_section(
			'box_style_section',
			[
				'label' => esc_html__( 'کادر (باکس)', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_width',
			[
				'label'      => esc_html__( 'عرض کادر', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 600 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ico' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_height',
			[
				'label'      => esc_html__( 'ارتفاع کادر', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 600 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ico' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ico' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ico' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ico' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selectors' => [
					'{{WRAPPER}} .anw-ico, {{WRAPPER}} .anw-ico-icon img, {{WRAPPER}} .anw-ico-icon svg' => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab( 'box_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-ico',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .anw-ico',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .anw-ico',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'box_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-ico:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border_hover',
				'selector' => '{{WRAPPER}} .anw-ico:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-ico:hover',
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
				'label' => esc_html__( 'آیکون', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 8, 'max' => 600 ],
					'%'  => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .anw-ico-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .anw-ico-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'پدینگ آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ico-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab( 'icon_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-ico-icon svg, {{WRAPPER}} .anw-ico-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_opacity',
			[
				'label'     => esc_html__( 'شفافیت', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'selectors' => [
					'{{WRAPPER}} .anw-ico-icon img, {{WRAPPER}} .anw-ico-icon svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_icon_shadow_controls( 'icon_shadow', '{{WRAPPER}} .anw-ico-icon img, {{WRAPPER}} .anw-ico-icon svg' );

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-ico:hover .anw-ico-icon svg, {{WRAPPER}} .anw-ico:hover .anw-ico-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_opacity_hover',
			[
				'label'     => esc_html__( 'شفافیت', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'selectors' => [
					'{{WRAPPER}} .anw-ico:hover .anw-ico-icon img, {{WRAPPER}} .anw-ico:hover .anw-ico-icon svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_icon_shadow_controls( 'icon_shadow_hover', '{{WRAPPER}} .anw-ico:hover .anw-ico-icon img, {{WRAPPER}} .anw-ico:hover .anw-ico-icon svg' );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * مجموعه کنترل‌های «سایهٔ آیکون» به‌صورت پاپ‌اور (مانند سایهٔ جعبه)؛ از نوع
	 * drop-shadow که از شکل خود آیکون پیروی می‌کند، با مقادیر پیش‌فرض نرم و حرفه‌ای.
	 *
	 * @param string $prefix   پیشوند نام کنترل‌ها (برای تفکیک عادی/هاور).
	 * @param string $selector سلکتور هدف.
	 */
	private function add_icon_shadow_controls( string $prefix, string $selector ): void {
		$this->add_control(
			$prefix . '_toggle',
			[
				'label'        => esc_html__( 'سایهٔ آیکون', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'بدون', 'asre-nokhbegan-widgets' ),
				'label_on'     => esc_html__( 'سفارشی', 'asre-nokhbegan-widgets' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->start_popover();

		$this->add_control(
			$prefix . '_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.45)',
				'condition' => [ $prefix . '_toggle' => 'yes' ],
				'selectors' => [
					$selector => 'filter: drop-shadow({{' . $prefix . '_h.SIZE}}{{' . $prefix . '_h.UNIT}} {{' . $prefix . '_v.SIZE}}{{' . $prefix . '_v.UNIT}} {{' . $prefix . '_blur.SIZE}}{{' . $prefix . '_blur.UNIT}} {{VALUE}});',
				],
			]
		);

		$this->add_control(
			$prefix . '_h',
			[
				'label'      => esc_html__( 'افقی', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'default'    => [ 'size' => 0, 'unit' => 'px' ],
				'condition'  => [ $prefix . '_toggle' => 'yes' ],
			]
		);

		$this->add_control(
			$prefix . '_v',
			[
				'label'      => esc_html__( 'عمودی', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'condition'  => [ $prefix . '_toggle' => 'yes' ],
			]
		);

		$this->add_control(
			$prefix . '_blur',
			[
				'label'      => esc_html__( 'محو شدن', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'condition'  => [ $prefix . '_toggle' => 'yes' ],
			]
		);

		$this->end_popover();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$has_link = ! empty( $settings['link']['url'] );
		$tag      = $has_link ? 'a' : 'div';

		$this->add_render_attribute( 'wrapper', 'class', 'anw-ico' );
		if ( $has_link ) {
			$this->add_link_attributes( 'wrapper', $settings['link'] );
		}

		$icon_html = anw_get_media_icon_html( $settings['icon_image'] );

		if ( ! $icon_html ) {
			return;
		}
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<span class="anw-ico-icon" aria-hidden="true"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — خروجی SVG/IMG پیش‌تر پاک‌سازی شده. ?></span>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
