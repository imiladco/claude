<?php
/**
 * ویجت «لیست عنوان‌ها» — لیست فلکس‌باکس از آیتم‌ها که هر آیتم یک عنوان دارد.
 *
 * @package ElementorWidgetExtension
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
 * Class EWE_Title_List_Widget
 */
class EWE_Title_List_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'ewe-title-list';
	}

	public function get_title(): string {
		return esc_html__( 'لیست عنوان‌ها', 'elementor-widget-extension' );
	}

	public function get_icon(): string {
		return 'eicon-bullet-list';
	}

	public function get_categories(): array {
		return [ 'ewe-widgets' ];
	}

	public function get_keywords(): array {
		return [ 'list', 'items', 'tags', 'pills', 'لیست', 'برچسب', 'تگ' ];
	}

	public function get_style_depends(): array {
		return [ 'ewe-widgets' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_container_style_controls();
		$this->register_item_style_controls();
	}

	/* ============================ محتوا ============================ */

	private function register_content_controls(): void {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'آیتم‌ها', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'عنوان', 'elementor-widget-extension' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'آیتم جدید', 'elementor-widget-extension' ),
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'       => esc_html__( 'لینک (اختیاری)', 'elementor-widget-extension' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-widget-extension' ),
			]
		);

		$this->add_control(
			'list',
			[
				'label'       => esc_html__( 'لیست', 'elementor-widget-extension' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => [
					[ 'item_title' => esc_html__( 'پک طلایی گرافیک', 'elementor-widget-extension' ) ],
					[ 'item_title' => esc_html__( 'فتوشاپ Photoshop', 'elementor-widget-extension' ) ],
					[ 'item_title' => esc_html__( 'ایلوستریتور Illustrator', 'elementor-widget-extension' ) ],
					[ 'item_title' => esc_html__( 'کورل Corel', 'elementor-widget-extension' ) ],
					[ 'item_title' => esc_html__( 'سئو (بهینه سازی) سایت SEO', 'elementor-widget-extension' ) ],
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: لیست (فلکس) ============================ */

	private function register_container_style_controls(): void {
		$this->start_controls_section(
			'container_style_section',
			[
				'label' => esc_html__( 'چیدمان لیست', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'direction',
			[
				'label'     => esc_html__( 'جهت', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => [
					'row'    => esc_html__( 'افقی', 'elementor-widget-extension' ),
					'column' => esc_html__( 'عمودی', 'elementor-widget-extension' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ewe-list' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'justify',
			[
				'label'     => esc_html__( 'تراز افقی', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start'    => [
						'title' => esc_html__( 'شروع', 'elementor-widget-extension' ),
						'icon'  => 'eicon-flex eicon-justify-start-h',
					],
					'center'        => [
						'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),
						'icon'  => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end'      => [
						'title' => esc_html__( 'پایان', 'elementor-widget-extension' ),
						'icon'  => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html__( 'بین', 'elementor-widget-extension' ),
						'icon'  => 'eicon-flex eicon-justify-space-between-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ewe-list' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'align_items',
			[
				'label'     => esc_html__( 'تراز عمودی', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'شروع', 'elementor-widget-extension' ),
						'icon'  => 'eicon-align-start-v',
					],
					'center'     => [
						'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),
						'icon'  => 'eicon-align-center-v',
					],
					'flex-end'   => [
						'title' => esc_html__( 'پایان', 'elementor-widget-extension' ),
						'icon'  => 'eicon-align-end-v',
					],
					'stretch'    => [
						'title' => esc_html__( 'کشیده', 'elementor-widget-extension' ),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ewe-list' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wrap',
			[
				'label'        => esc_html__( 'شکستن خط (Wrap)', 'elementor-widget-extension' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'elementor-widget-extension' ),
				'label_off'    => esc_html__( 'خیر', 'elementor-widget-extension' ),
				'return_value' => 'wrap',
				'default'      => 'wrap',
				'selectors'    => [
					'{{WRAPPER}} .ewe-list' => 'flex-wrap: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ افقی', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-list' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ عمودی', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-list' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: آیتم ============================ */

	private function register_item_style_controls(): void {
		$this->start_controls_section(
			'item_style_section',
			[
				'label' => esc_html__( 'آیتم', 'elementor-widget-extension' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .ewe-list-item-title',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'elementor-widget-extension' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ewe-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_transition',
			[
				'label'     => esc_html__( 'مدت انیمیشن (ثانیه)', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'default'   => [ 'size' => 0.3 ],
				'selectors' => [
					'{{WRAPPER}} .ewe-list-item' => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->start_controls_tabs( 'item_style_tabs' );

		// --- عادی ---
		$this->start_controls_tab(
			'item_normal_tab',
			[ 'label' => esc_html__( 'عادی', 'elementor-widget-extension' ) ]
		);

		$this->add_control(
			'item_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ewe-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .ewe-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .ewe-list-item',
			]
		);

		$this->end_controls_tab();

		// --- هاور ---
		$this->start_controls_tab(
			'item_hover_tab',
			[ 'label' => esc_html__( 'هاور', 'elementor-widget-extension' ) ]
		);

		$this->add_control(
			'item_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن', 'elementor-widget-extension' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ewe-list-item:hover .ewe-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ewe-list-item:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border_hover',
				'selector' => '{{WRAPPER}} .ewe-list-item:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow_hover',
				'selector' => '{{WRAPPER}} .ewe-list-item:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['list'] ) ) {
			return;
		}
		?>
		<div class="ewe-list">
			<?php
			foreach ( $settings['list'] as $index => $item ) {
				if ( empty( $item['item_title'] ) ) {
					continue;
				}

				$repeater_key = $this->get_repeater_setting_key( 'item_title', 'list', $index );
				$this->add_inline_editing_attributes( $repeater_key, 'none' );

				$has_link = ! empty( $item['item_link']['url'] );
				$tag      = $has_link ? 'a' : 'div';

				$item_attr = 'item_' . $index;
				$this->add_render_attribute( $item_attr, 'class', 'ewe-list-item' );
				if ( $has_link ) {
					$this->add_link_attributes( $item_attr, $item['item_link'] );
				}
				?>
				<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( $item_attr ); ?>>
					<span class="ewe-list-item-title" <?php $this->print_render_attribute_string( $repeater_key ); ?>><?php echo esc_html( $item['item_title'] ); ?></span>
				</<?php echo esc_html( $tag ); ?>>
				<?php
			}
			?>
		</div>
		<?php
	}
}
