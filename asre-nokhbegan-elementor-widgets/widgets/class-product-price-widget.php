<?php
/**
 * ابزارک «قیمت محصول» (ووکامرس) — نمایش قیمت اصلی و قیمت فروش فوق‌العاده
 * با کنترل کامل فلکس، واحد پول مستقل برای هر قیمت و پشتیبانی از محصول متغیر
 * (نمایش کمترین قیمت ممکن).
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
 * Class ANW_Product_Price_Widget
 */
class ANW_Product_Price_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'anw-product-price';
	}

	public function get_title(): string {
		return esc_html__( 'قیمت محصول', 'asre-nokhbegan-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-product-price';
	}

	public function get_categories(): array {
		return [ 'asre-nokhbegan' ];
	}

	public function get_keywords(): array {
		return [ 'price', 'product', 'woocommerce', 'sale', 'قیمت', 'محصول', 'ووکامرس', 'فروش', 'تخفیف' ];
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
		$this->register_container_style_controls();
		$this->register_price_block_style_controls( 'regular', esc_html__( 'قیمت اصلی', 'asre-nokhbegan-widgets' ), true );
		$this->register_price_block_style_controls( 'sale', esc_html__( 'قیمت فروش فوق‌العاده', 'asre-nokhbegan-widgets' ), false );
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
				'description' => esc_html__( 'اگر خالی بماند، قیمتِ محصولِ جاری (در صفحهٔ محصول یا قالب تکی) نمایش داده می‌شود.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'show_regular',
			[
				'label'        => esc_html__( 'نمایش قیمت اصلی', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_sale',
			[
				'label'        => esc_html__( 'نمایش قیمت فروش فوق‌العاده', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'فقط در صورتی نمایش داده می‌شود که محصول فروش ویژه (تخفیف) داشته باشد.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'currency_heading',
			[
				'label'     => esc_html__( 'واحد پول', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'currency_text',
			[
				'label'       => esc_html__( 'متن واحد پول', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : esc_html__( 'تومان', 'asre-nokhbegan-widgets' ),
				'description' => esc_html__( 'خالی = نماد پیش‌فرض ووکامرس.', 'asre-nokhbegan-widgets' ),
			]
		);

		$this->add_control(
			'regular_currency_enable',
			[
				'label'        => esc_html__( 'واحد پول برای قیمت اصلی', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'sale_currency_enable',
			[
				'label'        => esc_html__( 'واحد پول برای قیمت فروش', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'free_text',
			[
				'label'       => esc_html__( 'متن «رایگان»', 'asre-nokhbegan-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'رایگان', 'asre-nokhbegan-widgets' ),
				'separator'   => 'before',
				'description' => esc_html__( 'وقتی قیمت صفر باشد، این متن به‌جای عدد و واحد پول نمایش داده می‌شود. برای نمایش خودِ صفر، خالی بگذارید.', 'asre-nokhbegan-widgets' ),
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
			'price_direction',
			[
				'label'                => esc_html__( 'جهت چیدمان دو قیمت', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'row'    => [
						'title' => esc_html__( 'افقی', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'column' => [
						'title' => esc_html__( 'عمودی', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-ellipsis-v',
					],
				],
				'default'              => 'row',
				'selectors_dictionary' => [
					'row'    => 'flex-direction: row;',
					'column' => 'flex-direction: column;',
				],
				'selectors'            => [
					'{{WRAPPER}} .anw-pp' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sale_first',
			[
				'label'        => esc_html__( 'نمایش قیمت فروش پیش از قیمت اصلی', 'asre-nokhbegan-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'selectors'    => [
					'{{WRAPPER}} .anw-pp-sale' => 'order: -1;',
				],
			]
		);

		$this->add_responsive_control(
			'price_justify',
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
				],
				'selectors' => [
					'{{WRAPPER}} .anw-pp' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'price_align',
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
					'baseline'   => [
						'title' => esc_html__( 'خط پایه', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .anw-pp' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'price_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ بین دو قیمت', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'size' => 8 ],
				'selectors'  => [
					'{{WRAPPER}} .anw-pp' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: کانتینر ============================ */

	private function register_container_style_controls(): void {
		$this->start_controls_section(
			'container_style_section',
			[
				'label' => esc_html__( 'کادر کلی', 'asre-nokhbegan-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label'      => esc_html__( 'پدینگ', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .anw-pp' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .anw-pp' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'container_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .anw-pp',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .anw-pp',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'container_shadow',
				'selector' => '{{WRAPPER}} .anw-pp',
			]
		);

		$this->end_controls_section();
	}

	/* ============================ استایل: هر بلوک قیمت ============================ */

	/**
	 * ثبت کنترل‌های استایل یک بلوک قیمت (اصلی یا فروش).
	 *
	 * @param string $key       کلید بلوک: regular یا sale.
	 * @param string $label     عنوان بخش.
	 * @param bool   $is_regular آیا این بلوک قیمت اصلی است (برای گزینهٔ خط‌خوردگی).
	 */
	private function register_price_block_style_controls( string $key, string $label, bool $is_regular ): void {
		$block    = '{{WRAPPER}} .anw-pp-' . $key;
		$amount   = $block . ' .anw-pp-amount';
		$currency = $block . ' .anw-pp-currency';

		$this->start_controls_section(
			$key . '_style_section',
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		/* --- چیدمان داخلی: عدد و واحد پول --- */
		$this->add_control(
			$key . '_inner_heading',
			[
				'label' => esc_html__( 'چیدمان عدد و واحد پول', 'asre-nokhbegan-widgets' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			$key . '_currency_position',
			[
				'label'                => esc_html__( 'جایگاه واحد پول', 'asre-nokhbegan-widgets' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'after',
				'options'              => [
					'before' => [
						'title' => esc_html__( 'قبل از عدد', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-right',
					],
					'after'  => [
						'title' => esc_html__( 'بعد از عدد', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-h-align-left',
					],
				],
				'selectors_dictionary' => [
					'before' => 'order: -1;',
					'after'  => 'order: 2;',
				],
				'selectors'            => [
					$currency => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			$key . '_inner_align',
			[
				'label'     => esc_html__( 'تراز عمودی', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
					'baseline'   => [
						'title' => esc_html__( 'خط پایه', 'asre-nokhbegan-widgets' ),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					$block => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			$key . '_inner_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ عدد تا واحد پول', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 4 ],
				'selectors'  => [
					$block => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/* --- کادر بلوک قیمت --- */
		$this->add_responsive_control(
			$key . '_padding',
			[
				'label'      => esc_html__( 'پدینگ بلوک', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					$block => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$key . '_radius',
			[
				'label'      => esc_html__( 'گردی گوشهٔ بلوک', 'asre-nokhbegan-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					$block => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => $key . '_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $block,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $key . '_border',
				'selector' => $block,
			]
		);

		if ( $is_regular ) {
			$this->add_control(
				'regular_strike',
				[
					'label'        => esc_html__( 'خط‌خورده هنگام فروش ویژه', 'asre-nokhbegan-widgets' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
					'description'  => esc_html__( 'وقتی محصول فروش ویژه دارد، روی قیمت اصلی خط کشیده می‌شود.', 'asre-nokhbegan-widgets' ),
					'selectors'    => [
						'{{WRAPPER}} .anw-pp--on-sale .anw-pp-regular .anw-pp-amount' => 'text-decoration: line-through;',
					],
				]
			);
		}

		/* --- استایل عدد --- */
		$this->add_control(
			$key . '_amount_heading',
			[
				'label'     => esc_html__( 'عدد قیمت', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $key . '_amount_typography',
				'selector' => $amount,
			]
		);

		$this->add_control(
			$key . '_amount_color',
			[
				'label'     => esc_html__( 'رنگ عدد', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $amount => 'color: {{VALUE}};' ],
			]
		);

		/* --- استایل واحد پول --- */
		$this->add_control(
			$key . '_currency_style_heading',
			[
				'label'     => esc_html__( 'واحد پول', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $key . '_currency_typography',
				'selector' => $currency,
			]
		);

		$this->add_control(
			$key . '_currency_color',
			[
				'label'     => esc_html__( 'رنگ واحد پول', 'asre-nokhbegan-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $currency => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ============================ منطق محصول ============================ */

	/**
	 * یافتن محصول هدف: شناسهٔ دستی، سپس محصول سراسری/جاری.
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
	 * استخراج قیمت اصلی و فروش با پشتیبانی از محصول متغیر (کمترین قیمت).
	 *
	 * @param \WC_Product|false $product محصول.
	 * @return array{regular:string,sale:string,on_sale:bool,has_price:bool}
	 */
	private function get_price_data( $product ): array {
		$data = [
			'regular'   => '',
			'sale'      => '',
			'on_sale'   => false,
			'has_price' => false,
		];

		if ( ! $product instanceof \WC_Product ) {
			return $data;
		}

		if ( $product->is_type( 'variable' ) ) {
			// محصول متغیر: کمترین قیمت ممکن.
			$regular = $product->get_variation_regular_price( 'min', true );
			$active  = $product->get_variation_price( 'min', true );

			$data['regular'] = ( '' === $regular ) ? '' : (string) $regular;

			if ( '' !== $regular && '' !== $active && (float) $active < (float) $regular ) {
				$data['sale']    = (string) $active;
				$data['on_sale'] = true;
			}

			$data['has_price'] = ( '' !== $data['regular'] );

			return $data;
		}

		// محصول ساده و سایر انواع.
		$regular = $product->get_regular_price();
		if ( '' === $regular ) {
			// برخی انواع (یا قیمت محاسبه‌شده) قیمت اصلی مستقیم ندارند.
			$regular = $product->get_price();
		}
		$data['regular'] = ( '' === $regular || null === $regular ) ? '' : (string) $regular;

		if ( $product->is_on_sale() ) {
			$sale = $product->get_sale_price();
			if ( '' !== $sale && null !== $sale ) {
				$data['sale']    = (string) $sale;
				$data['on_sale'] = true;
			}
		}

		$data['has_price'] = ( '' !== $data['regular'] || '' !== $data['sale'] );

		return $data;
	}

	/**
	 * قالب‌بندی عدد قیمت بر اساس تنظیمات ووکامرس.
	 *
	 * @param string $value مقدار خام.
	 * @return string
	 */
	private function format_amount( string $value ): string {
		if ( '' === $value ) {
			return '';
		}

		$decimals = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 0;
		$dec_sep  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
		$thou_sep = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';

		return number_format( (float) $value, $decimals, $dec_sep, $thou_sep );
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
		$price    = $this->get_price_data( $product );

		// در ویرایشگر، اگر محصولی در دسترس نیست، دادهٔ نمونه نمایش بده.
		if ( ! $price['has_price'] && $this->is_edit_mode() ) {
			$price = [
				'regular'   => '250000',
				'sale'      => '199000',
				'on_sale'   => true,
				'has_price' => true,
			];
		}

		if ( ! $price['has_price'] ) {
			return;
		}

		$currency = isset( $settings['currency_text'] ) ? trim( (string) $settings['currency_text'] ) : '';
		if ( '' === $currency ) {
			$currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
		}

		$free_text    = isset( $settings['free_text'] ) ? (string) $settings['free_text'] : '';
		$show_regular = ( 'yes' === $settings['show_regular'] ) && ( '' !== $price['regular'] );
		$show_sale    = ( 'yes' === $settings['show_sale'] ) && $price['on_sale'] && ( '' !== $price['sale'] );

		$classes = 'anw-pp';
		if ( $price['on_sale'] ) {
			$classes .= ' anw-pp--on-sale';
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php
			if ( $show_regular ) {
				$this->render_price_block( 'regular', $price['regular'], $currency, ( 'yes' === $settings['regular_currency_enable'] ), $free_text );
			}
			if ( $show_sale ) {
				$this->render_price_block( 'sale', $price['sale'], $currency, ( 'yes' === $settings['sale_currency_enable'] ), $free_text );
			}
			?>
		</div>
		<?php
	}

	/**
	 * رندر یک بلوک قیمت.
	 *
	 * @param string $type          regular یا sale.
	 * @param string $amount_raw    مقدار خام قیمت.
	 * @param string $currency      متن واحد پول.
	 * @param bool   $show_currency نمایش واحد پول.
	 * @param string $free_text     متن جایگزین برای قیمت صفر.
	 */
	private function render_price_block( string $type, string $amount_raw, string $currency, bool $show_currency, string $free_text ): void {
		$is_free = ( 0.0 === (float) $amount_raw ) && ( '' !== $free_text );
		$text    = $is_free ? $free_text : $this->format_amount( $amount_raw );

		if ( '' === $text ) {
			return;
		}
		?>
		<span class="anw-pp-price anw-pp-<?php echo esc_attr( $type ); ?>">
			<span class="anw-pp-amount"><?php echo esc_html( $text ); ?></span>
			<?php if ( ! $is_free && $show_currency && '' !== $currency ) : ?>
				<span class="anw-pp-currency"><?php echo esc_html( $currency ); ?></span>
			<?php endif; ?>
		</span>
		<?php
	}
}
