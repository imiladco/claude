<?php
/**
 * ابزارک «جستجوی هوشمند محصولات» (ووکامرس) — فرمِ فیلترِ محصولات بر پایهٔ
 * ویژگی‌های محصول (Attribute / pa_...). ارسال با <form method="get"> استاندارد
 * است (بدون جاوااسکریپت هم کار می‌کند) و JS فقط برای بهبود تجربه افزوده می‌شود.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Class ANW_Smart_Search_Widget
 */
class ANW_Smart_Search_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-smart-search';
	}

	public function get_title(): string {
		return esc_html__( 'جستجوی هوشمند محصولات', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-site-search';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'search', 'filter', 'product', 'attribute', 'woocommerce', 'جستجو', 'فیلتر', 'محصول', 'ویژگی', 'ووکامرس' ];
	}

	public function get_style_depends(): array {
		return [ 'anw-widgets' ];
	}

	public function get_script_depends(): array {
		return [ 'anw-smart-search' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * فهرست ویژگی‌های محصول ووکامرس برای انتخاب در کنترل‌ها.
	 *
	 * @return array<string,string> نام ویژگی (بدون pa_) => برچسب.
	 */
	private function get_attribute_options(): array {
		$options = [ '' => esc_html__( '— یک ویژگی انتخاب کنید —', 'asre-nokhbegan-widgets' ) ];

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			foreach ( wc_get_attribute_taxonomies() as $tax ) {
				$label                              = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
				$options[ $tax->attribute_name ] = $label;
			}
		}

		return $options;
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_button_controls();
		$this->register_container_style_controls();
		$this->register_field_style_controls();
		$this->register_label_style_controls();
		$this->register_divider_style_controls();
		$this->register_button_style_controls();
	}

	/* ============================ محتوا: فیلترها ============================ */

	private function register_content_controls(): void {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'فیلترها', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'filter_label',
			[
				'label'   => esc_html__( 'عنوان فیلتر', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'فیلتر', 'asre-nokhbegan-widgets' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'attribute',
			[
				'label'   => esc_html__( 'ویژگی محصول', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_attribute_options(),
				'default' => '',
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label'   => esc_html__( 'متن پیش‌فرض (Placeholder)', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'انتخاب کنید', 'asre-nokhbegan-widgets' ),
			]
		);

		$repeater->add_control(
			'required',
			[
				'label'        => esc_html__( 'اجباری', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'filters',
			[
				'label'       => esc_html__( 'ردیف‌های فیلتر', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ filter_label }}}',
				'default'     => [
					[
						'filter_label' => esc_html__( 'هدف دوره', 'asre-nokhbegan-widgets' ),
						'placeholder'  => esc_html__( 'انتخاب کنید', 'asre-nokhbegan-widgets' ),
					],
					[
						'filter_label' => esc_html__( 'حوزهٔ علاقه', 'asre-nokhbegan-widgets' ),
						'placeholder'  => esc_html__( 'انتخاب کنید', 'asre-nokhbegan-widgets' ),
					],
					[
						'filter_label' => esc_html__( 'سطح', 'asre-nokhbegan-widgets' ),
						'placeholder'  => esc_html__( 'انتخاب کنید', 'asre-nokhbegan-widgets' ),
					],
				],
			]
		);

		$this->add_control(
			'results_url',
			[
				'label'       => esc_html__( 'آدرس صفحهٔ نتایج', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::URL,
				'options'     => false,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'خالی = صفحهٔ فروشگاه ووکامرس', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'فرم به این آدرس (با متد GET) ارسال می‌شود. برای فیلترِ آمادهٔ افزونه، صفحهٔ فروشگاه یا یک آرشیو محصول را انتخاب کنید.', 'asre-nokhbegan-widgets' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'error_message',
			[
				'label'   => esc_html__( 'پیام خطای فیلد اجباری', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'لطفاً این مورد را انتخاب کنید.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	/* ============================ محتوا: دکمه ============================ */

	private function register_button_controls(): void {
		$this->start_controls_section(
			'button_section',
			[
				'label' => esc_html__( 'دکمهٔ جستجو', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'button_show_text',
			[
				'label'        => esc_html__( 'نمایش متن دکمه', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => esc_html__( 'متن دکمه', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'جستجو', 'asre-nokhbegan-widgets' ),
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 'button_show_text' => 'yes' ],
			]
		);

		$this->add_control(
			'button_aria_label',
			[
				'label'       => esc_html__( 'برچسب دسترس‌پذیری (aria-label)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'جستجو', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'وقتی متن دکمه نمایش داده نمی‌شود، این برچسب برای صفحه‌خوان‌ها استفاده می‌شود.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label'   => esc_html__( 'آیکون', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'eicon-search',
					'library' => 'eicons',
				],
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label'                => esc_html__( 'جای آیکون', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'row',
				'options'              => [
					'row'         => [
						'title' => esc_html__( 'ابتدا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
					'row-reverse' => [
						'title' => esc_html__( 'انتها', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
				],
				'selectors_dictionary' => [
					'row'         => 'flex-direction: row;',
					'row-reverse' => 'flex-direction: row-reverse;',
				],
				'condition'            => [ 'button_show_text' => 'yes' ],
				'selectors'            => [
					'{{WRAPPER}} .anw-ss-button' => '{{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: کانتینر ============================ */

	private function register_container_style_controls(): void {
		$form = '{{WRAPPER}} .anw-ss';

		$this->start_controls_section(
			'container_style_section',
			[
				'label' => esc_html__( 'کانتینر', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'fields_direction',
			[
				'label'                => esc_html__( 'چیدمان فیلدها', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'row'    => [
						'title' => esc_html__( 'ردیفی', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'column' => [
						'title' => esc_html__( 'ستونی', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-ellipsis-v',
					],
				],
				'selectors_dictionary' => [
					'row'    => 'flex-direction: row;',
					'column' => 'flex-direction: column;',
				],
				'selectors'            => [
					'{{WRAPPER}} .anw-ss-fields' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'container_align',
			[
				'label'     => esc_html__( 'تراز عمودی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-end',
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'بالا', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'وسط', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'پایین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-end-v',
					],
					'stretch'    => [
						'title' => esc_html__( 'کشیده', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					$form => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'fields_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ بین فیلدها', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ss-fields' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'group_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ فیلدها تا دکمه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					$form => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$form => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$form => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$form => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'container_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $form,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'selector' => $form,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'container_shadow',
				'selector' => $form,
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: فیلد (Select) ============================ */

	private function register_field_style_controls(): void {
		$select = '{{WRAPPER}} .anw-ss-select';

		$this->start_controls_section(
			'field_style_section',
			[
				'label' => esc_html__( 'فیلد (منوی کشویی)', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_typography',
				'selector' => $select,
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					$select => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$select => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_min_height',
			[
				'label'      => esc_html__( 'حداقل ارتفاع', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					$select => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'field_tabs' );

		$this->start_controls_tab( 'field_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'field_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $select => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'field_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $select,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'field_border',
				'selector' => $select,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'field_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'field_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $select . ':hover' => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'field_border_hover',
				'selector' => $select . ':hover',
			]
		);
		$this->add_control(
			'field_bg_hover',
			[
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $select . ':hover' => 'background-color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'field_focus_tab', [ 'label' => esc_html__( 'فوکوس', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'field_border_color_focus',
			[
				'label'     => esc_html__( 'رنگ بوردر', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $select . ':focus' => 'border-color: {{VALUE}};' ],
			]
		);
		$this->add_control(
			'field_shadow_focus',
			[
				'label'     => esc_html__( 'هالهٔ فوکوس', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(37,99,235,0.25)',
				'selectors' => [ $select . ':focus' => 'box-shadow: 0 0 0 3px {{VALUE}}; outline: none;' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ استایل: برچسب ============================ */

	private function register_label_style_controls(): void {
		$label = '{{WRAPPER}} .anw-ss-label';

		$this->start_controls_section(
			'label_style_section',
			[
				'label' => esc_html__( 'برچسب', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'        => esc_html__( 'نمایش برچسب‌ها', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'در صورت خاموش‌بودن، برچسب‌ها به‌صورت بصری پنهان می‌شوند ولی برای صفحه‌خوان‌ها باقی می‌مانند.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'label_typography',
				'selector'  => $label,
				'condition' => [ 'show_labels' => 'yes' ],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'show_labels' => 'yes' ],
				'selectors' => [ $label => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'label_gap',
			[
				'label'      => esc_html__( 'فاصله تا فیلد', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'condition'  => [ 'show_labels' => 'yes' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-ss-field' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: جداکننده ============================ */

	private function register_divider_style_controls(): void {
		$divider = '{{WRAPPER}} .anw-ss-divider';

		$this->start_controls_section(
			'divider_style_section',
			[
				'label' => esc_html__( 'جداکننده', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_divider',
			[
				'label'        => esc_html__( 'نمایش خط جداکننده بین فیلدها', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'در چیدمان ردیفی (دسکتاپ) بین فیلدها یک خط عمودی نمایش می‌دهد.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'رنگ', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'condition' => [ 'show_divider' => 'yes' ],
				'selectors' => [ $divider => 'border-inline-start-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label'      => esc_html__( 'ضخامت', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
				'default'    => [ 'size' => 1, 'unit' => 'px' ],
				'condition'  => [ 'show_divider' => 'yes' ],
				'selectors'  => [ $divider => 'border-inline-start-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => esc_html__( 'سبک', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => esc_html__( 'ممتد', 'asre-nokhbegan-widgets' ),
					'dashed' => esc_html__( 'خط‌چین', 'asre-nokhbegan-widgets' ),
					'dotted' => esc_html__( 'نقطه‌چین', 'asre-nokhbegan-widgets' ),
				],
				'condition' => [ 'show_divider' => 'yes' ],
				'selectors' => [ $divider => 'border-inline-start-style: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: دکمه ============================ */

	private function register_button_style_controls(): void {
		$button = '{{WRAPPER}} .anw-ss-button';

		$this->start_controls_section(
			'button_style_section',
			[
				'label' => esc_html__( 'دکمهٔ جستجو', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => $button,
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					$button => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$button => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکون', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 80 ] ],
				'selectors'  => [
					$button . ' .anw-ss-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					$button . ' .anw-ss-button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ آیکون تا متن', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'condition'  => [ 'button_show_text' => 'yes' ],
				'selectors'  => [ $button => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'رنگ متن و آیکون', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					$button                          => 'color: {{VALUE}};',
					$button . ' .anw-ss-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $button,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border',
				'selector' => $button,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => $button,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'button_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن و آیکون', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$button . ':hover'                          => 'color: {{VALUE}};',
					$button . ':hover .anw-ss-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $button . ':hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border_hover',
				'selector' => $button . ':hover',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_active_tab', [ 'label' => esc_html__( 'کلیک/فعال', 'asre-nokhbegan-widgets' ) ] );
		$this->add_control(
			'button_color_active',
			[
				'label'     => esc_html__( 'رنگ متن و آیکون', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$button . ':active'                          => 'color: {{VALUE}};',
					$button . ':active .anw-ss-button-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'button_bg_active',
			[
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $button . ':active' => 'background-color: {{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$filters  = ! empty( $settings['filters'] ) ? $settings['filters'] : [];

		// آدرس مقصد فرم: مقدار دستی، در غیر این صورت صفحهٔ فروشگاه.
		$action = '';
		if ( ! empty( $settings['results_url']['url'] ) ) {
			$action = $settings['results_url']['url'];
		} elseif ( function_exists( 'wc_get_page_permalink' ) ) {
			$action = wc_get_page_permalink( 'shop' );
		}

		$show_labels  = ( 'yes' === $settings['show_labels'] );
		$show_divider = ( 'yes' === $settings['show_divider'] );
		$error_msg    = isset( $settings['error_message'] ) ? $settings['error_message'] : '';
		$uid          = $this->get_id();

		$this->add_render_attribute(
			'form',
			[
				'class'      => 'anw-ss',
				'method'     => 'get',
				'action'     => esc_url( $action ),
				'role'       => 'search',
				'aria-label' => esc_attr__( 'جستجوی هوشمند محصولات', 'asre-nokhbegan-widgets' ),
				'novalidate' => 'novalidate',
			]
		);
		?>
		<form <?php $this->print_render_attribute_string( 'form' ); ?>>
			<div class="anw-ss-fields">
				<?php
				$printed = 0;
				foreach ( $filters as $index => $filter ) {
					$attr_name = isset( $filter['attribute'] ) ? sanitize_key( $filter['attribute'] ) : '';
					if ( '' === $attr_name ) {
						continue;
					}

					if ( $show_divider && $printed > 0 ) {
						echo '<span class="anw-ss-divider" aria-hidden="true"></span>';
					}
					$printed++;

					$taxonomy   = wc_attribute_taxonomy_name( $attr_name );
					$query_var  = 'anwf_' . $attr_name;
					$field_id   = 'anw-ss-' . $uid . '-' . $index;
					$err_id     = $field_id . '-error';
					$is_required = ( isset( $filter['required'] ) && 'yes' === $filter['required'] );
					$selected   = isset( $_GET[ $query_var ] ) ? sanitize_title( wp_unslash( $_GET[ $query_var ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended — فیلترِ عمومی از طریق GET نیاز به nonce ندارد.

					$terms = get_terms(
						[
							'taxonomy'   => $taxonomy,
							'hide_empty' => false,
						]
					);
					?>
					<div class="anw-ss-field">
						<label class="anw-ss-label<?php echo $show_labels ? '' : ' anw-ss-sr-only'; ?>" for="<?php echo esc_attr( $field_id ); ?>">
							<?php echo esc_html( isset( $filter['filter_label'] ) ? $filter['filter_label'] : '' ); ?>
						</label>
						<select
							class="anw-ss-select"
							id="<?php echo esc_attr( $field_id ); ?>"
							name="<?php echo esc_attr( $query_var ); ?>"
							<?php echo $is_required ? 'data-required="1" aria-describedby="' . esc_attr( $err_id ) . '"' : ''; ?>
						>
							<option value=""><?php echo esc_html( isset( $filter['placeholder'] ) ? $filter['placeholder'] : '' ); ?></option>
							<?php
							if ( ! is_wp_error( $terms ) ) {
								foreach ( $terms as $term ) {
									printf(
										'<option value="%1$s" %2$s>%3$s</option>',
										esc_attr( $term->slug ),
										selected( $selected, $term->slug, false ),
										esc_html( $term->name )
									);
								}
							}
							?>
						</select>
						<?php if ( $is_required ) : ?>
							<span class="anw-ss-error" id="<?php echo esc_attr( $err_id ); ?>" role="alert" hidden><?php echo esc_html( $error_msg ); ?></span>
						<?php endif; ?>
					</div>
					<?php
				}
				?>
			</div>

			<?php
			$show_text = ( 'yes' === $settings['button_show_text'] );
			$this->add_render_attribute( 'button', 'class', 'anw-ss-button' );
			$this->add_render_attribute( 'button', 'type', 'submit' );
			if ( ! $show_text && ! empty( $settings['button_aria_label'] ) ) {
				$this->add_render_attribute( 'button', 'aria-label', $settings['button_aria_label'] );
			}
			?>
			<button <?php $this->print_render_attribute_string( 'button' ); ?>>
				<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
					<span class="anw-ss-button-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
				<?php endif; ?>
				<?php if ( $show_text && ! empty( $settings['button_text'] ) ) : ?>
					<span class="anw-ss-button-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
				<?php endif; ?>
			</button>
		</form>
		<?php
	}
}
