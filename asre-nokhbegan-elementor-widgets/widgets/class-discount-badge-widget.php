<?php
/**
 * ابزارک «درصد تخفیف» (ووکامرس) — نمایش درصد تخفیف برای محصولات دارای فروش ویژه.
 * برای محصول متغیر، انتخاب بین «بیشترین»، «کمترین» یا «درصد قیمت فعلیِ نمایش‌داده‌شده».
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
 * Class ANW_Discount_Badge_Widget
 */
class ANW_Discount_Badge_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-discount-badge';
	}

	public function get_title(): string {
		return esc_html__( 'درصد تخفیف', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-price-table';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'discount', 'percent', 'sale', 'badge', 'woocommerce', 'تخفیف', 'درصد', 'فروش', 'محصول', 'ووکامرس' ];
	}

	public function get_style_depends(): array {
		return [ 'anw-widgets' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
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
			'product_id',
			[
				'label'       => esc_html__( 'شناسهٔ محصول', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'placeholder' => esc_html__( 'خالی = محصول جاری', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'اگر خالی بماند، محصولِ جاری (در صفحهٔ محصول یا قالب تکی) در نظر گرفته می‌شود.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'discount_type',
			[
				'label'       => esc_html__( 'کدام درصد نمایش داده شود؟', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current',
				'options'     => [
					'current' => esc_html__( 'درصد قیمتِ فعلیِ نمایش‌داده‌شده', 'asre-nokhbegan-widgets' ),
					'highest' => esc_html__( 'بیشترین درصد تخفیف', 'asre-nokhbegan-widgets' ),
					'lowest'  => esc_html__( 'کمترین درصد تخفیف', 'asre-nokhbegan-widgets' ),
				],
				'description' => esc_html__( 'این انتخاب فقط برای محصولات متغیر تفاوت ایجاد می‌کند؛ برای محصول ساده هر سه گزینه یکسان‌اند. «درصد قیمت فعلی» با همان قیمتی که ابزارک قیمت نمایش می‌دهد هماهنگ است.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'prefix',
			[
				'label'     => esc_html__( 'پیشوند', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'suffix',
			[
				'label'   => esc_html__( 'پسوند', 'asre-nokhbegan-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '٪',
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: نشان تخفیف ============================ */

	private function register_style_controls(): void {
		$badge = '{{WRAPPER}} .anw-disc';

		$this->start_controls_section(
			'badge_style_section',
			[
				'label' => esc_html__( 'نشان تخفیف', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'default'   => 'right',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => $badge,
			]
		);

		$this->add_responsive_control(
			'parts_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ پیشوند/عدد/پسوند', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 0 ],
				'selectors'  => [
					$badge => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$badge => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_min_width',
			[
				'label'      => esc_html__( 'حداقل عرض', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 300 ] ],
				'selectors'  => [
					$badge => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_min_height',
			[
				'label'      => esc_html__( 'حداقل ارتفاع', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 300 ] ],
				'description' => esc_html__( 'برای نشان دایره‌ای، حداقل عرض و ارتفاع را برابر و گردی گوشه را ۵۰٪ بگذارید.', 'asre-nokhbegan-widgets' ),
				'selectors'  => [
					$badge => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$badge => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_transition',
			[
				'label'     => esc_html__( 'مدت انیمیشن (ثانیه)', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'default'   => [ 'size' => 0.3 ],
				'selectors' => [
					$badge => 'transition: all {{SIZE}}s ease;',
				],
			]
		);

		$this->start_controls_tabs( 'badge_tabs' );

		$this->start_controls_tab( 'badge_normal_tab', [ 'label' => esc_html__( 'عادی', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'badge_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $badge => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'badge_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $badge,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'badge_border',
				'selector' => $badge,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => $badge,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'badge_hover_tab', [ 'label' => esc_html__( 'هاور', 'asre-nokhbegan-widgets' ) ] );

		$this->add_control(
			'badge_color_hover',
			[
				'label'     => esc_html__( 'رنگ متن', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $badge . ':hover' => 'color: {{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'badge_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $badge . ':hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'badge_border_hover',
				'selector' => $badge . ':hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow_hover',
				'selector' => $badge . ':hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'badge_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					$badge => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// استایل مستقل عدد (اختیاری).
		$this->add_control(
			'value_custom',
			[
				'label'        => esc_html__( 'استایل منحصربه‌فرد عدد', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'description'  => esc_html__( 'با فعال‌کردن، می‌توانید رنگ و تایپوگرافی عدد را جدا از پیشوند/پسوند تعیین کنید.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'value_typography',
				'selector'  => $badge . ' .anw-disc-value',
				'condition' => [ 'value_custom' => 'yes' ],
			]
		);

		$this->add_control(
			'value_color',
			[
				'label'     => esc_html__( 'رنگ عدد', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $badge . ' .anw-disc-value' => 'color: {{VALUE}};' ],
				'condition' => [ 'value_custom' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ منطق محصول ============================ */

	/**
	 * یافتن محصول هدف.
	 *
	 * @param array $settings تنظیمات ویجت.
	 * @return \WC_Product|false
	 */
	private function resolve_product( array $settings ) {
		if ( ! empty( $settings['product_id'] ) ) {
			$product = wc_get_product( absint( $settings['product_id'] ) );
			if ( $product instanceof \WC_Product ) {
				return $product;
			}
		}

		global $product;
		if ( $product instanceof \WC_Product ) {
			return $product;
		}

		$current = wc_get_product( get_the_ID() );

		return $current instanceof \WC_Product ? $current : false;
	}

	/**
	 * محاسبهٔ درصد تخفیف بر اساس نوع انتخابی، با پشتیبانی کامل از محصول متغیر.
	 *
	 * @param \WC_Product|false $product محصول.
	 * @param string            $mode    current | highest | lowest.
	 * @return float درصد تخفیف (۰ یعنی بدون تخفیف).
	 */
	private function get_discount_percent( $product, string $mode ): float {
		if ( ! $product instanceof \WC_Product ) {
			return 0.0;
		}

		if ( $product->is_type( 'variable' ) ) {
			if ( 'highest' === $mode || 'lowest' === $mode ) {
				$percents = [];

				foreach ( $product->get_children() as $variation_id ) {
					$variation = wc_get_product( $variation_id );
					if ( ! $variation instanceof \WC_Product || ! $variation->is_on_sale() ) {
						continue;
					}
					$regular = (float) $variation->get_regular_price();
					$active  = (float) $variation->get_price();
					if ( $regular > 0 && $active < $regular ) {
						$percents[] = ( $regular - $active ) / $regular * 100;
					}
				}

				if ( empty( $percents ) ) {
					return 0.0;
				}

				return ( 'highest' === $mode ) ? max( $percents ) : min( $percents );
			}

			// current: مطابق با قیمتی که ابزارک قیمت نمایش می‌دهد (کمترین قیمت).
			$regular_min = (float) $product->get_variation_regular_price( 'min', true );
			$active_min  = (float) $product->get_variation_price( 'min', true );

			if ( $regular_min > 0 && $active_min < $regular_min ) {
				return ( $regular_min - $active_min ) / $regular_min * 100;
			}

			return 0.0;
		}

		// محصول ساده و سایر انواع.
		if ( $product->is_on_sale() ) {
			$regular = (float) $product->get_regular_price();
			$active  = (float) $product->get_price();
			if ( $regular > 0 && $active < $regular ) {
				return ( $regular - $active ) / $regular * 100;
			}
		}

		return 0.0;
	}

	private function is_edit_mode(): bool {
		return class_exists( '\Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/* ============================ رندر ============================ */

	protected function render(): void {
		if ( ! function_exists( 'wc_get_product' ) ) {
			if ( $this->is_edit_mode() ) {
				echo '<div class="anw-pp-notice">' . esc_html__( 'برای این ابزارک باید افزونهٔ ووکامرس نصب و فعال باشد.', 'asre-nokhbegan-widgets' ) . '</div>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$product  = $this->resolve_product( $settings );
		$mode     = isset( $settings['discount_type'] ) ? $settings['discount_type'] : 'current';
		$percent  = (int) round( $this->get_discount_percent( $product, $mode ) );

		// در ویرایشگر، اگر تخفیفی نبود، نمونهٔ نمایشی نشان بده.
		if ( $percent <= 0 && $this->is_edit_mode() ) {
			$percent = 25;
		}

		// محصول بدون تخفیف → چیزی نمایش داده نمی‌شود.
		if ( $percent <= 0 ) {
			return;
		}

		$prefix = isset( $settings['prefix'] ) ? (string) $settings['prefix'] : '';
		$suffix = isset( $settings['suffix'] ) ? (string) $settings['suffix'] : '';
		?>
		<span class="anw-disc">
			<?php if ( '' !== $prefix ) : ?>
				<span class="anw-disc-prefix"><?php echo esc_html( $prefix ); ?></span>
			<?php endif; ?>
			<span class="anw-disc-value"><?php echo esc_html( (string) $percent ); ?></span>
			<?php if ( '' !== $suffix ) : ?>
				<span class="anw-disc-suffix"><?php echo esc_html( $suffix ); ?></span>
			<?php endif; ?>
		</span>
		<?php
	}
}
