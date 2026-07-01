<?php
/**
 * ابزارک «کارت آیکون و عنوان» — یک آیکون و یک عنوان، لینک‌دار با استایل کامل.
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
 * Class ANW_Icon_Title_Widget
 */
class ANW_Icon_Title_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-icon-title';
	}

	public function get_title(): string {
		return esc_html__( 'کارت آیکون و عنوان', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-icon-box';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'icon', 'title', 'card', 'button', 'link', 'آیکون', 'عنوان', 'کارت', 'دکمه' ];
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
		$this->register_title_style_controls();
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
				'label_on'     => esc_html__( 'بله', 'asre-nokhbegan-widgets' ),
				'label_off'    => esc_html__( 'خیر', 'asre-nokhbegan-widgets' ),
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
			'title',
			[
				'label'       => esc_html__( 'عنوان', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'عنوان نمونه', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'برای استایل‌دهی به بخشی از متن، آن را داخل <span> قرار دهید.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'تگ عنوان', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'p'    => 'p',
					'div'  => 'div',
					'span' => 'span',
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
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();
	}

	/* ============================ چیدمان ============================ */

	private function register_layout_controls(): void {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'چیدمان', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'                => esc_html__( 'موقعیت آیکون', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'column'         => [
						'title' => esc_html__( 'بالا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-top',
					],
					'column-reverse' => [
						'title' => esc_html__( 'پایین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'row'            => [
						'title' => esc_html__( 'ابتدا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
					'row-reverse'    => [
						'title' => esc_html__( 'انتها', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
				],
				'default'              => 'row',
				'selectors_dictionary' => [
					'column'         => 'flex-direction: column;',
					'column-reverse' => 'flex-direction: column-reverse;',
					'row'            => 'flex-direction: row;',
					'row-reverse'    => 'flex-direction: row-reverse;',
				],
				'selectors'            => [
					'{{WRAPPER}} .anw-it' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_justify',
			[
				'label'     => esc_html__( 'توزیع افقی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
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
					'space-around'  => [
						'title' => esc_html__( 'با فاصلهٔ اطراف', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-space-around-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-it' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'align_items',
			[
				'label'     => esc_html__( 'تراز عمودی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
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
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .anw-it' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'     => esc_html__( 'تراز متن', 'asre-nokhbegan-widgets' ),
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
				'selectors' => [
					'{{WRAPPER}} .anw-it-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'فاصلهٔ آیکون تا عنوان', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: باکس ============================ */

	private function register_box_style_controls(): void {
		$this->start_controls_section(
			'box_style_section',
			[
				'label' => esc_html__( 'باکس', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-it' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-it' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_width',
			[
				'label'      => esc_html__( 'عرض', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 1000 ],
					'%'  => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .anw-it' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-it' => 'transition: all {{SIZE}}s ease;',
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
				'selector' => '{{WRAPPER}} .anw-it',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .anw-it',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .anw-it',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'box_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-it:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border_hover',
				'selector' => '{{WRAPPER}} .anw-it:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-it:hover',
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
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 400 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .anw-it-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => esc_html__( 'اندازهٔ کادر آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 500 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'پدینگ کادر آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'گردی گوشهٔ کادر آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-it-icon svg, {{WRAPPER}} .anw-it-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-it-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .anw-it-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .anw-it-icon',
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
					'{{WRAPPER}} .anw-it:hover .anw-it-icon svg, {{WRAPPER}} .anw-it:hover .anw-it-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-it:hover .anw-it-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border_hover',
				'selector' => '{{WRAPPER}} .anw-it:hover .anw-it-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-it:hover .anw-it-icon',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: عنوان ============================ */

	private function register_title_style_controls(): void {
		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'عنوان', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .anw-it-title',
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-it-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'title_color_tabs' );

		$this->start_controls_tab( 'title_color_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .anw-it-title' => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'title_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-it-title',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'title_color_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .anw-it:hover .anw-it-title' => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		// متن متمایز داخل <span>.
		$highlight = '{{WRAPPER}} .anw-it-title span';

		$this->add_control(
			'highlight_heading',
			[
				'label'     => esc_html__( 'متن متمایز (داخل <span>)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'highlight_color',
			[
				'label'     => esc_html__( 'رنگ متن متمایز', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $highlight => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'highlight_typography',
				'selector' => $highlight,
			]
		);

		$this->end_controls_section();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$has_link = ! empty( $settings['link']['url'] );
		$tag      = $has_link ? 'a' : 'div';

		$this->add_render_attribute( 'wrapper', 'class', 'anw-it' );
		if ( $has_link ) {
			$this->add_link_attributes( 'wrapper', $settings['link'] );
		}

		$show_icon    = ( 'yes' === $settings['show_icon'] );
		$icon_html    = $show_icon ? anw_get_media_icon_html( $settings['icon_image'] ) : '';
		$allowed_html = anw_allowed_inline_html();
		$title_tag    = anw_validate_html_tag( $settings['title_tag'], 'h3' );
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $icon_html ) : ?>
				<span class="anw-it-icon" aria-hidden="true"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — خروجی SVG/IMG پیش‌تر پاک‌سازی شده. ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="anw-it-title"><?php echo wp_kses( $settings['title'], $allowed_html ); ?></<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
