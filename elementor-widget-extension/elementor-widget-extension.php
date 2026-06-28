<?php
/**
 * Plugin Name: Elementor Widget Extension
 * Description: افزودن ویجت‌های سفارشی به المنتور — مرجع آموزشی هم‌گام با المنتور ۲۰۲۶.
 * Version:     1.0.0
 * Author:      You
 * Text Domain: elementor-widget-extension
 * Requires Plugins: elementor
 * Elementor tested up to: 3.30.0
 * Requires PHP: 7.4
 *
 * @package ElementorWidgetExtension
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم.
}

define( 'EWE_VERSION', '1.0.0' );
define( 'EWE_MINIMUM_ELEMENTOR_VERSION', '3.25.0' );
define( 'EWE_MINIMUM_PHP_VERSION', '7.4' );
define( 'EWE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * بوت‌استرپ افزونه پس از بارگذاری همهٔ افزونه‌ها.
 */
function ewe_init() {

	// ۱) آیا المنتور بارگذاری شده است؟
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_missing_elementor' );
		return;
	}

	// ۲) حداقل نسخهٔ المنتور.
	if ( ! version_compare( ELEMENTOR_VERSION, EWE_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_minimum_elementor_version' );
		return;
	}

	// ۳) حداقل نسخهٔ PHP.
	if ( version_compare( PHP_VERSION, EWE_MINIMUM_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_minimum_php_version' );
		return;
	}

	// همه‌چیز آماده است.
	add_action( 'elementor/elements/categories_registered', 'ewe_add_category' );
	add_action( 'elementor/widgets/register', 'ewe_register_widgets' );
}
add_action( 'plugins_loaded', 'ewe_init' );

/**
 * ثبت دستهٔ سفارشی برای ویجت‌های افزونه.
 *
 * @param \Elementor\Elements_Manager $elements_manager مدیر عناصر المنتور.
 */
function ewe_add_category( $elements_manager ) {
	$elements_manager->add_category(
		'ewe-widgets',
		[
			'title' => esc_html__( 'ویجت‌های من', 'elementor-widget-extension' ),
			'icon'  => 'fa fa-plug',
		]
	);
}

/**
 * ثبت ویجت‌ها روی هوک پایدار المنتور.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌های المنتور.
 */
function ewe_register_widgets( $widgets_manager ) {
	require_once EWE_PATH . 'widgets/class-hello-world-widget.php';

	$widgets_manager->register( new \EWE_Hello_World_Widget() );
}

/* -------------------------------------------------------------------------
 * پیام‌های ادمین در صورت برآورده‌نشدن شرایط.
 * ---------------------------------------------------------------------- */

/**
 * هشدار نبودِ المنتور.
 */
function ewe_admin_notice_missing_elementor() {
	$message = esc_html__( 'برای کار افزونهٔ «Elementor Widget Extension» باید المنتور نصب و فعال باشد.', 'elementor-widget-extension' );
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * هشدار قدیمی‌بودن نسخهٔ المنتور.
 */
function ewe_admin_notice_minimum_elementor_version() {
	$message = sprintf(
		/* translators: %s: شمارهٔ نسخهٔ حداقلی المنتور. */
		esc_html__( 'افزونهٔ «Elementor Widget Extension» به المنتور نسخهٔ %s یا بالاتر نیاز دارد.', 'elementor-widget-extension' ),
		EWE_MINIMUM_ELEMENTOR_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * هشدار قدیمی‌بودن نسخهٔ PHP.
 */
function ewe_admin_notice_minimum_php_version() {
	$message = sprintf(
		/* translators: %s: شمارهٔ نسخهٔ حداقلی PHP. */
		esc_html__( 'افزونهٔ «Elementor Widget Extension» به PHP نسخهٔ %s یا بالاتر نیاز دارد.', 'elementor-widget-extension' ),
		EWE_MINIMUM_PHP_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}
