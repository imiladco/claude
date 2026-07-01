<?php
/**
 * ویجت «باکس عنوان آیکون‌دار» — یک آیکون (تصویر/SVG) به‌همراه دو عنوان.
 *
 * @package ElementorWidgetExtension
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
 * Class EWE_Icon_Heading_Box_Widget
 */
class EWE_Icon_Heading_Box_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'ewe-icon-heading-box';
	}

	public function get_title(): string {
		return esc_html__( 'باکس عنوان آیکون‌دار', 'elementor-widget-extension' );
	}

	public function get_icon(): string {
		return 'eicon-image-box';
	}

	public function get_categories(): array {
		return [ 'ewe-widgets' ];
	}

	public function get_keywords(): array {
		return [ 'icon', 'heading', 'title', 'box', 'آیکون', 'عنوان', 'تیتر' ];
	}

	public function get_style_depends(): array {
		return [ 'ewe-widgets' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * ثبت کنترل‌ها.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_gap_style_controls();
		$this->register_box_style_controls();
		$this->register_icon_style_controls();
		$this->register_title_style_controls( '1', esc_html__( 'عنوان اول', 'elementor-widget-extension' ) );
		$this->register_title_style_controls( '2', esc_html__( 'عنوان دوم', 'elementor-widget-extension' ) );
		$this->register_highlight_style_controls();
	}

	/* ============================ محتوا ============================ */

	private function register_content_controls(): void {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'محتوا', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'       => esc_html__( 'آیکون (تصویر / SVG)', 'elementor-widget-extension' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => [ 'image', 'svg' ],
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'title_1',
			[
				'label'       => esc_html__( 'عنوان اول', 'elementor-widget-extension' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'پرمخاطب‌ترین انتخاب هنرجوها', 'elementor-widget-extension' ),
				'description' => esc_html__( 'برای استایل‌دهی به بخشی از متن، آن را داخل <span> قرار دهید. مثال: متن <span>متمایز</span>', 'elementor-widget-extension' ),
			]
		);

		$this->add_control(
			'title_1_tag',
			[
				'label'   => esc_html__( 'تگ عنوان اول', 'elementor-widget-extension' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'p',
				'options' => $this->get_title_tags(),
			]
		);

		$this->add_control(
			'title_2',
			[
				'label'   => esc_html__( 'عنوان دوم', 'elementor-widget-extension' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'محبوب ترین دوره ها', 'elementor-widget-extension' ),
			]
		);

		$this->add_control(
			'title_2_tag',
			[
				'label'   => esc_html__( 'تگ عنوان دوم', 'elementor-widget-extension' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => $this->get_title_tags(),
			]
		);

		$this->add_control(
			'box_link',
			[
				'label'       => esc_html__( 'لینک باکس (اختیاری)', 'elementor-widget-extension' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-widget-extension' ),
			]
		);

		$this->end_controls_section();
	}

	private function register_layout_controls(): void {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'چیدمان', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		// در حالت RTL، آیتم اول در DOM (آیکون) در سمت راست قرار می‌گیرد.
		$this->add_control(
			'icon_position',
			[
				'label'                => esc_html__( 'موقعیت آیکون', 'elementor-widget-extension' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'row'         => [
						'title' => esc_html__( 'ابتدا', 'elementor-widget-extension' ),
						'icon'  => 'eicon-arrow-right',
					],
					'row-reverse' => [
						'title' => esc_html__( 'انتها', 'elementor-widget-extension' ),
						'icon'  => 'eicon-arrow-left',
					],
				],
				'default'              => 'row',
				'selectors_dictionary' => [
					'row'         => 'flex-direction: row;',
					'row-reverse' => 'flex-direction: row-reverse;',
				],
				'selectors'            => [
					'{{WRAPPER}} .ewe-ihb' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_align',
			[
				'label'     => esc_html__( 'تراز عمودی', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'بالا', 'elementor-widget-extension' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'پایین', 'elementor-widget-extension' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ewe-ihb' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'     => esc_html__( 'تراز متن', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'right'  => [
						'title' => esc_html__( 'راست', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-right',
					],
					'center' => [
						'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-center',
					],
					'left'   => [
						'title' => esc_html__( 'چپ', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-left',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ewe-ihb-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: فاصله‌ها ============================ */

	private function register_gap_style_controls(): void {
		$this->start_controls_section(
			'gaps_section',
			[
				'label' => esc_html__( 'فاصله‌ها', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ آیکون تا متن', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'titles_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ بین دو عنوان', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb-content' => 'gap: {{SIZE}}{{UNIT}};',
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
				'label' => esc_html__( 'باکس', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_transition',
			[
				'label'      => esc_html__( 'مدت انیمیشن (ثانیه)', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'default'    => [ 'size' => 0.3 ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb' => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		// --- عادی ---
		$this->start_controls_tab(
			'box_normal_tab',
			[ 'label' => esc_html__( 'عادی', 'elementor-widget-extension' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-ihb',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .ewe-ihb',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .ewe-ihb',
			]
		);

		$this->end_controls_tab();

		// --- هاور ---
		$this->start_controls_tab(
			'box_hover_tab',
			[ 'label' => esc_html__( 'هاور', 'elementor-widget-extension' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-ihb:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border_hover',
				'selector' => '{{WRAPPER}} .ewe-ihb:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .ewe-ihb:hover',
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
				'label' => esc_html__( 'آیکون', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکون', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 400 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .ewe-ihb-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => esc_html__( 'اندازهٔ کادر آیکون', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 500 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'پدینگ کادر آیکون', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'گردی گوشهٔ کادر آیکون', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-ihb-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		// --- عادی ---
		$this->start_controls_tab(
			'icon_normal_tab',
			[ 'label' => esc_html__( 'عادی', 'elementor-widget-extension' ) ]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ewe-ihb-icon svg, {{WRAPPER}} .ewe-ihb-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-ihb-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .ewe-ihb-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .ewe-ihb-icon',
			]
		);

		$this->end_controls_tab();

		// --- هاور ---
		$this->start_controls_tab(
			'icon_hover_tab',
			[ 'label' => esc_html__( 'هاور', 'elementor-widget-extension' ) ]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'رنگ آیکون (SVG)', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ewe-ihb:hover .ewe-ihb-icon svg, {{WRAPPER}} .ewe-ihb:hover .ewe-ihb-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-ihb:hover .ewe-ihb-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border_hover',
				'selector' => '{{WRAPPER}} .ewe-ihb:hover .ewe-ihb-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow_hover',
				'selector' => '{{WRAPPER}} .ewe-ihb:hover .ewe-ihb-icon',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: عناوین ============================ */

	private function register_title_style_controls( string $index, string $label ): void {
		$key      = 'title_' . $index;
		$selector = '{{WRAPPER}} .ewe-ihb-' . $key;

		$this->start_controls_section(
			$key . '_style_section',
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $key . '_typography',
				'selector' => $selector,
			]
		);

		$this->add_responsive_control(
			$key . '_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( $key . '_color_tabs' );

		$this->start_controls_tab(
			$key . '_color_normal_tab',
			[ 'label' => esc_html__( 'عادی', 'elementor-widget-extension' ) ]
		);
		$this->add_control(
			$key . '_color',
			[
				'label'     => esc_html__( 'رنگ', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $selector => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			$key . '_color_hover_tab',
			[ 'label' => esc_html__( 'هاور', 'elementor-widget-extension' ) ]
		);
		$this->add_control(
			$key . '_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .ewe-ihb:hover .ewe-ihb-' . $key => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: متن متمایز ============================ */

	private function register_highlight_style_controls(): void {
		$selector = '{{WRAPPER}} .ewe-ihb-title-1 span, {{WRAPPER}} .ewe-ihb-title-2 span';

		$this->start_controls_section(
			'highlight_style_section',
			[
				'label' => esc_html__( 'متن متمایز (داخل <span>)', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'highlight_notice',
			[
				'type'    => Controls_Manager::RAW_HTML,
				'raw'     => esc_html__( 'هر بخشی از متن عنوان را که داخل تگ <span> بگذارید، با تنظیمات این بخش استایل می‌گیرد.', 'elementor-widget-extension' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'highlight_color',
			[
				'label'     => esc_html__( 'رنگ', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $selector => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'highlight_typography',
				'selector' => $selector,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'highlight_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $selector,
			]
		);

		$this->end_controls_section();
	}

	/* ============================ کمکی ============================ */

	private function get_title_tags(): array {
		return [
			'h1'   => 'H1',
			'h2'   => 'H2',
			'h3'   => 'H3',
			'h4'   => 'H4',
			'h5'   => 'H5',
			'h6'   => 'H6',
			'p'    => 'p',
			'div'  => 'div',
			'span' => 'span',
		];
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$has_link = ! empty( $settings['box_link']['url'] );
		$tag      = $has_link ? 'a' : 'div';

		$this->add_render_attribute( 'wrapper', 'class', 'ewe-ihb' );
		if ( $has_link ) {
			$this->add_link_attributes( 'wrapper', $settings['box_link'] );
		}

		$icon_html    = ewe_get_media_icon_html( $settings['icon_image'] );
		$allowed_html = ewe_allowed_inline_html();
		$tag_1        = ewe_validate_html_tag( $settings['title_1_tag'], 'p' );
		$tag_2        = ewe_validate_html_tag( $settings['title_2_tag'], 'h2' );
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $icon_html ) : ?>
				<span class="ewe-ihb-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — خروجی SVG/IMG پیش‌تر پاک‌سازی شده. ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['title_1'] ) || ! empty( $settings['title_2'] ) ) : ?>
				<span class="ewe-ihb-content">
					<?php if ( ! empty( $settings['title_1'] ) ) : ?>
						<<?php echo esc_html( $tag_1 ); ?> class="ewe-ihb-title-1"><?php echo wp_kses( $settings['title_1'], $allowed_html ); ?></<?php echo esc_html( $tag_1 ); ?>>
					<?php endif; ?>
					<?php if ( ! empty( $settings['title_2'] ) ) : ?>
						<<?php echo esc_html( $tag_2 ); ?> class="ewe-ihb-title-2"><?php echo wp_kses( $settings['title_2'], $allowed_html ); ?></<?php echo esc_html( $tag_2 ); ?>>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
