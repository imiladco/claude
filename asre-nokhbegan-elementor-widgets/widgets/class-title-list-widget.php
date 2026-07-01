<?php
/**
 * ابزارک «لیست عنوان‌ها» — لیست فلکس‌باکس از آیتم‌ها که هر آیتم یک عنوان دارد.
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
 * Class ANW_Title_List_Widget
 */
class ANW_Title_List_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-title-list';
	}

	public function get_title(): string {
		return esc_html__( 'لیست عنوان‌ها', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-bullet-list';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'list', 'items', 'tags', 'pills', 'لیست', 'برچسب', 'تگ' ];
	}

	public function get_style_depends(): array {
		return [ 'anw-widgets' ];
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
				'label' => esc_html__( 'آیتم‌ها', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label'   => esc_html__( 'عنوان', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'آیتم جدید', 'asre-nokhbegan-widgets' ),
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'       => esc_html__( 'لینک (اختیاری)', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://your-link.com', 'asre-nokhbegan-widgets' ),
			]
		);

		$repeater->add_control(
			'item_active',
			[
				'label'        => esc_html__( 'آیتم فعال', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'asre-nokhbegan-widgets' ),
				'label_off'    => esc_html__( 'خیر', 'asre-nokhbegan-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'این آیتم با استایلِ تب «فعال» نمایش داده می‌شود.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'list',
			[
				'label'       => esc_html__( 'لیست', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_title }}}',
				'default'     => [
					[ 'item_title' => esc_html__( 'پک طلایی گرافیک', 'asre-nokhbegan-widgets' ) ],
					[ 'item_title' => esc_html__( 'فتوشاپ Photoshop', 'asre-nokhbegan-widgets' ) ],
					[ 'item_title' => esc_html__( 'ایلوستریتور Illustrator', 'asre-nokhbegan-widgets' ) ],
					[ 'item_title' => esc_html__( 'کورل Corel', 'asre-nokhbegan-widgets' ) ],
					[ 'item_title' => esc_html__( 'سئو (بهینه سازی) سایت SEO', 'asre-nokhbegan-widgets' ) ],
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
				'label' => esc_html__( 'چیدمان لیست', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'direction',
			[
				'label'     => esc_html__( 'جهت', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => [
					'row'    => esc_html__( 'افقی', 'asre-nokhbegan-widgets' ),
					'column' => esc_html__( 'عمودی', 'asre-nokhbegan-widgets' ),
				],
				'selectors' => [
					'{{WRAPPER}} .anw-list' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'justify',
			[
				'label'     => esc_html__( 'تراز افقی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
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
						'title' => esc_html__( 'بین', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-flex eicon-justify-space-between-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-list' => 'justify-content: {{VALUE}};',
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
					'stretch'    => [
						'title' => esc_html__( 'کشیده', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-list' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'wrap',
			[
				'label'        => esc_html__( 'شکستن خط (Wrap)', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'asre-nokhbegan-widgets' ),
				'label_off'    => esc_html__( 'خیر', 'asre-nokhbegan-widgets' ),
				'return_value' => 'wrap',
				'default'      => 'wrap',
				'selectors'    => [
					'{{WRAPPER}} .anw-list' => 'flex-wrap: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ افقی', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-list' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ عمودی', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .anw-list' => 'row-gap: {{SIZE}}{{UNIT}};',
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
				'label' => esc_html__( 'آیتم', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .anw-list-item-title',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_transition',
			[
				'label'     => esc_html__( 'مدت انیمیشن (ثانیه)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'default'   => [ 'size' => 0.3 ],
				'selectors' => [
					'{{WRAPPER}} .anw-list-item' => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->start_controls_tabs( 'item_style_tabs' );

		$this->start_controls_tab( 'item_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'item_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-list-item',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .anw-list-item',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .anw-list-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'item_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'item_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-list-item:hover .anw-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-list-item:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border_hover',
				'selector' => '{{WRAPPER}} .anw-list-item:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow_hover',
				'selector' => '{{WRAPPER}} .anw-list-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'item_active_tab', [ 'label' => esc_html__( 'فعال', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'item_color_active',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .anw-list-item--active .anw-list-item-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg_active',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-list-item--active',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border_active',
				'selector' => '{{WRAPPER}} .anw-list-item--active',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow_active',
				'selector' => '{{WRAPPER}} .anw-list-item--active',
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
		<ul class="anw-list">
			<?php
			foreach ( $settings['list'] as $index => $item ) {
				if ( empty( $item['item_title'] ) ) {
					continue;
				}

				$repeater_key = $this->get_repeater_setting_key( 'item_title', 'list', $index );
				$this->add_inline_editing_attributes( $repeater_key, 'none' );

				$has_link  = ! empty( $item['item_link']['url'] );
				$is_active = ! empty( $item['item_active'] ) && 'yes' === $item['item_active'];
				$tag       = $has_link ? 'a' : 'span';

				$item_attr = 'item_' . $index;
				$this->add_render_attribute( $item_attr, 'class', 'anw-list-item' );
				if ( $is_active ) {
					$this->add_render_attribute( $item_attr, 'class', 'anw-list-item--active' );
					$this->add_render_attribute( $item_attr, 'aria-current', 'true' );
				}
				if ( $has_link ) {
					$this->add_link_attributes( $item_attr, $item['item_link'] );
				}
				?>
				<li class="anw-list-li">
					<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( $item_attr ); ?>>
						<span class="anw-list-item-title" <?php $this->print_render_attribute_string( $repeater_key ); ?>><?php echo esc_html( $item['item_title'] ); ?></span>
					</<?php echo esc_html( $tag ); ?>>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
}
