<?php
/**
 * ویجت نمونهٔ «سلام دنیا» — الگوی کامل ساخت ویجت سفارشی المنتور.
 *
 * @package ElementorWidgetExtension
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم.
}

/**
 * Class EWE_Hello_World_Widget
 */
class EWE_Hello_World_Widget extends \Elementor\Widget_Base {

	/**
	 * شناسهٔ یکتای ماشینی ویجت.
	 */
	public function get_name(): string {
		return 'ewe-hello-world';
	}

	/**
	 * عنوان نمایشی در پنل ویرایشگر.
	 */
	public function get_title(): string {
		return esc_html__( 'سلام دنیا', 'elementor-widget-extension' );
	}

	/**
	 * آیکن ویجت (از مجموعهٔ eicon-* المنتور).
	 */
	public function get_icon(): string {
		return 'eicon-code';
	}

	/**
	 * دستهٔ ویجت در پنل.
	 */
	public function get_categories(): array {
		return [ 'ewe-widgets' ];
	}

	/**
	 * کلیدواژه‌های جست‌وجو.
	 */
	public function get_keywords(): array {
		return [ 'hello', 'world', 'سلام', 'نمونه' ];
	}

	/**
	 * حذف لایهٔ اضافی DOM برای بهینه‌سازی.
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * ثبت کنترل‌های ویجت.
	 */
	protected function register_controls(): void {

		/* ---------------- بخش محتوا ---------------- */
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'محتوا', 'elementor-widget-extension' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'عنوان', 'elementor-widget-extension' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'سلام دنیا', 'elementor-widget-extension' ),
				'placeholder' => esc_html__( 'عنوان را وارد کنید', 'elementor-widget-extension' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => esc_html__( 'توضیحات', 'elementor-widget-extension' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			]
		);

		$this->end_controls_section();

		/* ---------------- بخش استایل ---------------- */
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'استایل', 'elementor-widget-extension' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'رنگ عنوان', 'elementor-widget-extension' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ewe-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => esc_html__( 'چینش', 'elementor-widget-extension' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'چپ', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'راست', 'elementor-widget-extension' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ewe-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ewe-title',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * رندر خروجی HTML در فرانت‌اند.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['title'] ) ) {
			return;
		}
		?>
		<div class="ewe-wrapper">
			<h2 class="ewe-title"><?php echo esc_html( $settings['title'] ); ?></h2>
			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="ewe-description"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * قالب پیش‌نمایش زنده در ویرایشگر (Underscore.js).
	 */
	protected function content_template(): void {
		?>
		<# if ( settings.title ) { #>
			<div class="ewe-wrapper">
				<h2 class="ewe-title">{{{ settings.title }}}</h2>
				<# if ( settings.description ) { #>
					<p class="ewe-description">{{{ settings.description }}}</p>
				<# } #>
			</div>
		<# } #>
		<?php
	}
}
