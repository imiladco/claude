<?php
/**
 * ابزارک «سرتیتر آیکون‌دار» — یک آیکون (تصویر/SVG) به‌همراه دو عنوان.
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
 * Class ANW_Icon_Heading_Box_Widget
 */
class ANW_Icon_Heading_Box_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-icon-heading-box';
	}

	public function get_title(): string {
		return esc_html__( 'سرتیتر آیکون‌دار', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-image-box';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'icon', 'heading', 'title', 'box', 'آیکون', 'عنوان', 'تیتر', 'سرتیتر' ];
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
		$this->register_gap_style_controls();
		$this->register_box_style_controls();
		$this->register_icon_style_controls();
		$this->register_title_style_controls( '1', esc_html__( 'عنوان اول', 'asre-nokhbegan-widgets' ) );
		$this->register_title_style_controls( '2', esc_html__( 'عنوان دوم', 'asre-nokhbegan-widgets' ) );
		$this->register_highlight_style_controls();
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
			'title_1',
			[
				'label'       => esc_html__( 'عنوان اول', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'پرمخاطب‌ترین انتخاب هنرجوها', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'برای استایل‌دهی به بخشی از متن، آن را داخل <span> قرار دهید. مثال: متن <span>متمایز</span>', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'title_1_tag',
			[
				'label'   => esc_html__( 'تگ عنوان اول', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'p',
				'options' => $this->get_title_tags(),
			]
		);

		$this->add_control(
			'title_2',
			[
				'label'   => esc_html__( 'عنوان دوم', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'محبوب ترین دوره ها', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'title_2_tag',
			[
				'label'   => esc_html__( 'تگ عنوان دوم', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => $this->get_title_tags(),
			]
		);

		$this->add_control(
			'box_link',
			[
				'label'       => esc_html__( 'لینک باکس (اختیاری)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->end_controls_section();
	}

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
					'row'         => [
						'title' => esc_html__( 'ابتدا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-arrow-right',
					],
					'row-reverse' => [
						'title' => esc_html__( 'انتها', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-arrow-left',
					],
				],
				'default'              => 'row',
				'selectors_dictionary' => [
					'row'         => 'flex-direction: row;',
					'row-reverse' => 'flex-direction: row-reverse;',
				],
				'selectors'            => [
					'{{WRAPPER}} .anw-ihb' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_align',
			[
				'label'     => esc_html__( 'تراز عمودی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'بالا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'وسط', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'پایین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .anw-ihb' => 'align-items: {{VALUE}};',
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
					'{{WRAPPER}} .anw-ihb-content' => 'text-align: {{VALUE}};',
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
				'label' => esc_html__( 'فاصله‌ها', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ آیکون تا متن', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ihb' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'titles_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ بین دو عنوان', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ihb-content' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb' => 'transition: all {{SIZE}}s ease;',
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
				'selector' => '{{WRAPPER}} .anw-ihb',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .anw-ihb',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .anw-ihb',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'box_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'box_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-ihb:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border_hover',
				'selector' => '{{WRAPPER}} .anw-ihb:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-ihb:hover',
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
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 400 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ihb-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
					'{{WRAPPER}} .anw-ihb-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-ihb-icon svg, {{WRAPPER}} .anw-ihb-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-ihb-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .anw-ihb-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .anw-ihb-icon',
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
					'{{WRAPPER}} .anw-ihb:hover .anw-ihb-icon svg, {{WRAPPER}} .anw-ihb:hover .anw-ihb-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-ihb:hover .anw-ihb-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border_hover',
				'selector' => '{{WRAPPER}} .anw-ihb:hover .anw-ihb-icon',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-ihb:hover .anw-ihb-icon',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: عناوین ============================ */

	private function register_title_style_controls( string $index, string $label ): void {
		$key      = 'title_' . $index;
		$selector = '{{WRAPPER}} .anw-ihb-' . $key;

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
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( $key . '_color_tabs' );

		$this->start_controls_tab( $key . '_color_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			$key . '_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $selector => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( $key . '_color_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			$key . '_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .anw-ihb:hover .anw-ihb-' . $key => 'color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: متن متمایز ============================ */

	private function register_highlight_style_controls(): void {
		$selector = '{{WRAPPER}} .anw-ihb-title-1 span, {{WRAPPER}} .anw-ihb-title-2 span';

		$this->start_controls_section(
			'highlight_style_section',
			[
				'label' => esc_html__( 'متن متمایز (داخل <span>)', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'highlight_notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'هر بخشی از متن عنوان را که داخل تگ <span> بگذارید، با تنظیمات این بخش استایل می‌گیرد.', 'asre-nokhbegan-widgets' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'highlight_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
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

		$this->add_render_attribute( 'wrapper', 'class', 'anw-ihb' );
		if ( $has_link ) {
			$this->add_link_attributes( 'wrapper', $settings['box_link'] );
		}

		$icon_html    = anw_get_media_icon_html( $settings['icon_image'] );
		$allowed_html = anw_allowed_inline_html();
		$tag_1        = anw_validate_html_tag( $settings['title_1_tag'], 'p' );
		$tag_2        = anw_validate_html_tag( $settings['title_2_tag'], 'h2' );
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $icon_html ) : ?>
				<span class="anw-ihb-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — خروجی SVG/IMG پیش‌تر پاک‌سازی شده. ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['title_1'] ) || ! empty( $settings['title_2'] ) ) : ?>
				<span class="anw-ihb-content">
					<?php if ( ! empty( $settings['title_1'] ) ) : ?>
						<<?php echo esc_html( $tag_1 ); ?> class="anw-ihb-title-1"><?php echo wp_kses( $settings['title_1'], $allowed_html ); ?></<?php echo esc_html( $tag_1 ); ?>>
					<?php endif; ?>
					<?php if ( ! empty( $settings['title_2'] ) ) : ?>
						<<?php echo esc_html( $tag_2 ); ?> class="anw-ihb-title-2"><?php echo wp_kses( $settings['title_2'], $allowed_html ); ?></<?php echo esc_html( $tag_2 ); ?>>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}
