<?php
/**
 * Plugin Name: Asre Nokhbegan – Elementor Widgets
 * Plugin URI:  https://asrenokhbegan.com
 * Description: ابزارک‌های اختصاصی المنتور برای وب‌سایت عصر نخبگان.
 * Version:     1.0.0
 * Author:      imiladco
 * Author URI:  https://asrenokhbegan.com
 * Text Domain: asre-nokhbegan-widgets
 * Requires Plugins: elementor
 * Elementor tested up to: 3.30.0
 * Requires PHP: 7.4
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم.
}

define( 'ANW_VERSION', '1.0.0' );
define( 'ANW_MINIMUM_ELEMENTOR_VERSION', '3.25.0' );
define( 'ANW_MINIMUM_PHP_VERSION', '7.4' );
define( 'ANW_FILE', __FILE__ );
define( 'ANW_PATH', plugin_dir_path( __FILE__ ) );

/**
 * مخزن گیت‌هاب برای بروزرسانی خودکار (owner/repo).
 * هر زمان نسخهٔ جدیدی به‌صورت Release روی این مخزن منتشر شود،
 * وردپرس به‌صورت خودکار بروزرسانی را پیشنهاد می‌دهد.
 */
if ( ! defined( 'ANW_GITHUB_REPO' ) ) {
	define( 'ANW_GITHUB_REPO', 'imiladco/asre-nokhbegan-elementor-widgets' );
}
// برای مخزن خصوصی، یک توکن دسترسی در wp-config.php تعریف کنید:
// define( 'ANW_GITHUB_TOKEN', 'xxxx' );

/**
 * راه‌اندازی بروزرسانی خودکار از گیت‌هاب (مستقل از المنتور، فقط در ادمین/کرون).
 */
function anw_setup_updater() {
	if ( ! is_admin() && ! wp_doing_cron() ) {
		return;
	}

	require_once ANW_PATH . 'includes/class-github-updater.php';

	$token   = defined( 'ANW_GITHUB_TOKEN' ) ? ANW_GITHUB_TOKEN : '';
	$updater = new ANW_Github_Updater( ANW_FILE, ANW_GITHUB_REPO, $token );
	$updater->init_hooks();
}
add_action( 'init', 'anw_setup_updater' );

/**
 * بوت‌استرپ بخش المنتوری افزونه.
 */
function anw_init() {

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'anw_admin_notice_missing_elementor' );
		return;
	}

	if ( ! version_compare( ELEMENTOR_VERSION, ANW_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
		add_action( 'admin_notices', 'anw_admin_notice_minimum_elementor_version' );
		return;
	}

	if ( version_compare( PHP_VERSION, ANW_MINIMUM_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'anw_admin_notice_minimum_php_version' );
		return;
	}

	require_once ANW_PATH . 'includes/helpers.php';

	add_action( 'elementor/elements/categories_registered', 'anw_add_category' );
	add_action( 'elementor/widgets/register', 'anw_register_widgets' );
	add_action( 'elementor/frontend/after_register_styles', 'anw_register_assets' );
}
add_action( 'plugins_loaded', 'anw_init' );

/**
 * افزودن دستهٔ اختصاصی و قرار دادن آن در ابتدای لیست دسته‌های المنتور.
 *
 * @param \Elementor\Elements_Manager $elements_manager مدیر عناصر المنتور.
 */
function anw_add_category( $elements_manager ) {
	$slug = 'asre-nokhbegan';

	$elements_manager->add_category(
		$slug,
		[
			'title' => esc_html__( 'عصر نخبگان', 'asre-nokhbegan-widgets' ),
			'icon'  => 'eicon-crown',
		]
	);

	// انتقال دستهٔ ما به ابتدای لیست (add_category به‌صورت پیش‌فرض در انتها اضافه می‌کند).
	try {
		$reflection = new ReflectionObject( $elements_manager );
		$property   = $reflection->getProperty( 'categories' );
		$property->setAccessible( true );

		$categories = $property->getValue( $elements_manager );

		if ( is_array( $categories ) && isset( $categories[ $slug ] ) ) {
			$ours = [ $slug => $categories[ $slug ] ];
			unset( $categories[ $slug ] );
			$property->setValue( $elements_manager, array_merge( $ours, $categories ) );
		}
	} catch ( \Exception $e ) {
		// اگر ساختار داخلی المنتور تغییر کرده باشد، دسته در انتها باقی می‌ماند (بدون خطا).
		unset( $e );
	}
}

/**
 * ثبت ویجت‌ها.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager مدیر ویجت‌های المنتور.
 */
function anw_register_widgets( $widgets_manager ) {
	require_once ANW_PATH . 'widgets/class-icon-heading-box-widget.php';
	require_once ANW_PATH . 'widgets/class-title-list-widget.php';

	$widgets_manager->register( new \ANW_Icon_Heading_Box_Widget() );
	$widgets_manager->register( new \ANW_Title_List_Widget() );
}

/**
 * ثبت دارایی‌های مشترک (CSS) — به‌صورت مشروط بارگذاری می‌شوند.
 */
function anw_register_assets() {
	wp_register_style(
		'anw-widgets',
		plugins_url( 'assets/css/widgets.css', __FILE__ ),
		[],
		ANW_VERSION
	);
}

/* ----------------------------- اعلان‌های ادمین ----------------------------- */

function anw_admin_notice_missing_elementor() {
	$message = esc_html__( 'افزونهٔ «عصر نخبگان» برای کار به المنتور نیاز دارد؛ لطفاً المنتور را نصب و فعال کنید.', 'asre-nokhbegan-widgets' );
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}

function anw_admin_notice_minimum_elementor_version() {
	$message = sprintf(
		/* translators: %s: شمارهٔ نسخهٔ حداقلی المنتور. */
		esc_html__( 'افزونهٔ «عصر نخبگان» به المنتور نسخهٔ %s یا بالاتر نیاز دارد.', 'asre-nokhbegan-widgets' ),
		ANW_MINIMUM_ELEMENTOR_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}

function anw_admin_notice_minimum_php_version() {
	$message = sprintf(
		/* translators: %s: شمارهٔ نسخهٔ حداقلی PHP. */
		esc_html__( 'افزونهٔ «عصر نخبگان» به PHP نسخهٔ %s یا بالاتر نیاز دارد.', 'asre-nokhbegan-widgets' ),
		ANW_MINIMUM_PHP_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html( $message ) );
}
