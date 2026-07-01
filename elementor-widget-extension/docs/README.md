# راهنمای توسعه افزونه ویجت المنتور (به‌روزرسانی ۲۰۲۶)

این سند، مرجع کامل و **هم‌گام با آخرین وضعیت المنتور (نسخهٔ ۴ / Atomic Editor)** برای ساخت
یک افزونهٔ وردپرسی است که ویجت‌های سفارشی به المنتور اضافه می‌کند.

> منبع رسمی: [Elementor Developers](https://developers.elementor.com/) — همهٔ کدها و
> امضای متدها مطابق مستندات رسمی فعلی (سینتکس Typed PHP) بازنویسی شده‌اند.

---

## ۱. وضعیت فعلی المنتور (چرا این مهم است)

از **آوریل ۲۰۲۶** همهٔ سایت‌های جدید المنتور به‌صورت پیش‌فرض روی **نسخهٔ ۴ (Atomic Editor)**
اجرا می‌شوند. نکات کلیدی برای توسعه‌دهنده:

- **معماری اتمی (Atomic):** سلسله‌مراتب قدیمی Section → Column → Widget کنار رفته و جای خود را به
  یک سیستم مدرن مبتنی بر React با عناصر مستقل (Atomic Elements)، **Classes** و **Variables** داده است.
- **سازگاری به‌عقب (Hybrid):** ویجت‌های کلاسیک نسخهٔ ۳ که با `\Elementor\Widget_Base` ساخته
  می‌شوند **همچنان کار می‌کنند** و می‌توانند کنار عناصر اتمی در یک صفحه استفاده شوند — نیازی به بازنویسی نیست.
- **انتخاب مسیر:** برای افزونهٔ ویجت سفارشی، روش پایدار و مستندشدهٔ فعلی همچنان
  **`Widget_Base`** است؛ این راهنما بر همین مبنا نوشته شده و سپس ملاحظات نسخهٔ ۴ را پوشش می‌دهد.

منابع: [معرفی نسخهٔ ۴.۰](https://elementor.com/blog/editor-40-atomic-forms-pro-interactions/) ·
[به‌روزرسانی توسعه‌دهندگان ۴.۰](https://developers.elementor.com/elementor-editor-4-0-developers-update/)

---

## ۲. پیش‌نیازها

| مورد | توضیح |
|------|-------|
| WordPress | نصب تازه و سالم، ترجیحاً با تم **Hello Elementor** |
| Elementor | افزونهٔ المنتور (و در صورت نیاز Elementor Pro برای قابلیت‌های Pro) |
| PHP | نسخه‌ای که حداقل موردنیاز المنتور را برآورده کند (در بوت‌استرپ بررسی می‌کنیم) |
| دانش | توسعهٔ افزونهٔ وردپرس + HTML/CSS/JS/PHP |

منبع: [Requirements](https://developers.elementor.com/docs/getting-started/requirements/)

---

## ۳. ساختار پوشهٔ افزونه

```
elementor-widget-extension/
├─ elementor-widget-extension.php   ← فایل اصلی افزونه (بوت‌استرپ + بررسی نسخه‌ها)
└─ widgets/
   └─ class-hello-world-widget.php  ← کلاس ویجت
```

برای افزونه‌های بزرگ‌تر می‌توان `assets/` (css/js)، `includes/` و `controls/` را هم اضافه کرد.

---

## ۴. فایل اصلی افزونه و بررسی نسخه‌ها

فایل اصلی باید سه چیز را تضمین کند: **خود المنتور فعال است**، **حداقل نسخهٔ المنتور** و
**حداقل نسخهٔ PHP** برآورده شده‌اند؛ سپس ویجت‌ها را روی هوک ثبت می‌کند.

```php
<?php
/**
 * Plugin Name: Elementor Widget Extension
 * Description: افزودن ویجت‌های سفارشی به المنتور.
 * Version:     1.0.0
 * Author:      You
 * Text Domain: elementor-widget-extension
 * Requires Plugins: elementor
 * Elementor tested up to: 3.30.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم
}

define( 'EWE_VERSION', '1.0.0' );
define( 'EWE_MINIMUM_ELEMENTOR_VERSION', '3.25.0' );
define( 'EWE_MINIMUM_PHP_VERSION', '7.4' );

/**
 * بوت‌استرپ افزونه پس از بارگذاری همهٔ افزونه‌ها.
 */
function ewe_init() {

	// ۱) آیا المنتور نصب و فعال است؟
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_missing_elementor' );
		return;
	}

	// ۲) حداقل نسخهٔ المنتور
	if ( ! version_compare( ELEMENTOR_VERSION, EWE_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_minimum_elementor_version' );
		return;
	}

	// ۳) حداقل نسخهٔ PHP
	if ( version_compare( PHP_VERSION, EWE_MINIMUM_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'ewe_admin_notice_minimum_php_version' );
		return;
	}

	// همه‌چیز آماده است → ثبت ویجت‌ها
	add_action( 'elementor/widgets/register', 'ewe_register_widgets' );
}
add_action( 'plugins_loaded', 'ewe_init' );

/**
 * ثبت ویجت‌ها روی هوک مخصوص المنتور.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager
 */
function ewe_register_widgets( $widgets_manager ) {
	require_once __DIR__ . '/widgets/class-hello-world-widget.php';

	$widgets_manager->register( new \EWE_Hello_World_Widget() );
}

/* --- پیام‌های ادمین در صورت برآورده‌نشدن شرایط --- */

function ewe_admin_notice_missing_elementor() {
	$message = esc_html__( 'برای کار افزونهٔ "Elementor Widget Extension" باید المنتور نصب و فعال باشد.', 'elementor-widget-extension' );
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}

function ewe_admin_notice_minimum_elementor_version() {
	$message = sprintf(
		esc_html__( 'این افزونه به المنتور نسخهٔ %s یا بالاتر نیاز دارد.', 'elementor-widget-extension' ),
		EWE_MINIMUM_ELEMENTOR_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}

function ewe_admin_notice_minimum_php_version() {
	$message = sprintf(
		esc_html__( 'این افزونه به PHP نسخهٔ %s یا بالاتر نیاز دارد.', 'elementor-widget-extension' ),
		EWE_MINIMUM_PHP_VERSION
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}
```

> نکتهٔ مهم: هدر `Requires Plugins: elementor` از وردپرس ۶.۵ به بعد وابستگی را رسمی می‌کند و
> هوک پایدار ثبت ویجت **`elementor/widgets/register`** است (هوک قدیمی `widgets_registered` منسوخ شده).

منابع: [اولین افزونه](https://developers.elementor.com/docs/getting-started/first-addon/) ·
[افزودن ویجت جدید](https://developers.elementor.com/docs/widgets/add-new-widget/)

---

## ۵. ساختار کلاس ویجت

هر ویجت از `\Elementor\Widget_Base` ارث می‌برد. اسکلت کامل با امضای تایپ‌دار فعلی:

```php
class Elementor_Test_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {}              // شناسهٔ یکتا در کد
	public function get_title(): string {}             // عنوان نمایشی در ویرایشگر
	public function get_icon(): string {}              // آیکن (کلاس eicon-*)
	public function get_categories(): array {}         // دستهٔ ویجت در پنل
	public function get_keywords(): array {}           // کلیدواژه‌های جست‌وجو
	public function get_custom_help_url(): string {}   // لینک راهنما

	protected function get_upsale_data(): array {}     // داده‌های تبلیغ نسخهٔ Pro
	public function get_script_depends(): array {}     // اسکریپت‌های وابسته
	public function get_style_depends(): array {}      // استایل‌های وابسته

	public function has_widget_inner_wrapper(): bool {} // بهینه‌سازی DOM
	protected function is_dynamic_content(): bool {}    // محتوای پویا (کش)

	protected function register_controls(): void {}    // تعریف کنترل‌ها
	protected function render(): void {}               // خروجی HTML نهایی (PHP)
	protected function content_template(): void {}     // قالب پیش‌نمایش زنده (JS)
}
```

### نقش هر متد

- **`get_name()`** — شناسهٔ یکتای ماشینی ویجت.
- **`get_title()`** — برچسبی که کاربر در پنل می‌بیند (با `esc_html__`).
- **`get_icon()`** — یکی از آیکن‌های `eicon-*` المنتور (مثلاً `eicon-code`).
- **`get_categories()`** — آرایه‌ای از دسته‌ها؛ پیش‌فرض `['general']` (ساخت دستهٔ سفارشی در بخش ۸).
- **`get_keywords()`** — بهبود جست‌وجوی ویجت در پنل.
- **`has_widget_inner_wrapper()`** — با `return false` لایهٔ اضافی DOM حذف می‌شود (بهینه‌سازی، توصیه‌شده برای ویجت‌های جدید).
- **`is_dynamic_content()`** — اگر خروجی پویاست `true`، تا کش به‌درستی مدیریت شود.
- **`register_controls()`** — تعریف فیلدهای ورودی کاربر.
- **`render()`** — تولید HTML نهایی در فرانت‌اند با PHP.
- **`content_template()`** — همان خروجی به‌صورت قالب Underscore.js برای پیش‌نمایش زندهٔ ویرایشگر (اختیاری ولی توصیه‌شده).

منبع: [ساختار ویجت](https://developers.elementor.com/docs/widgets/widget-structure/)

---

## ۶. کنترل‌ها (Controls)

کنترل‌ها فیلدهای ورودی کاربر هستند و درون **بخش‌ها (sections)** گروه‌بندی می‌شوند.

```php
protected function register_controls(): void {

	// --- شروع بخش محتوا ---
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

	// --- شروع بخش استایل ---
	$this->start_controls_section(
		'style_section',
		[
			'label' => esc_html__( 'استایل', 'elementor-widget-extension' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		]
	);

	// کنترل رنگ که مستقیماً به CSS وصل می‌شود (selectors)
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

	// کنترل واکنش‌گرا (مقدار متفاوت برای دسکتاپ/تبلت/موبایل)
	$this->add_responsive_control(
		'alignment',
		[
			'label'     => esc_html__( 'چینش', 'elementor-widget-extension' ),
			'type'      => \Elementor\Controls_Manager::CHOOSE,
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'چپ', 'elementor-widget-extension' ),   'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'وسط', 'elementor-widget-extension' ),  'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => esc_html__( 'راست', 'elementor-widget-extension' ), 'icon' => 'eicon-text-align-right' ],
			],
			'selectors' => [
				'{{WRAPPER}} .ewe-title' => 'text-align: {{VALUE}};',
			],
		]
	);

	// کنترل گروهی تایپوگرافی
	$this->add_group_control(
		\Elementor\Group_Control_Typography::get_type(),
		[
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .ewe-title',
		]
	);

	$this->end_controls_section();
}
```

### انواع پرکاربرد کنترل (`Controls_Manager::`)

| ثابت | کاربرد |
|------|--------|
| `TEXT` | متن یک‌خطی |
| `TEXTAREA` | متن چندخطی |
| `WYSIWYG` | ویرایشگر متن غنی |
| `NUMBER` | عدد |
| `SELECT` | فهرست کشویی |
| `CHOOSE` | انتخاب آیکنی (مثل چینش) |
| `SWITCHER` | کلید روشن/خاموش |
| `COLOR` | انتخاب رنگ |
| `MEDIA` | انتخاب تصویر/رسانه |
| `URL` | لینک (با گزینهٔ nofollow و …) |
| `SLIDER` | اسلایدر عددی با واحد |
| `DIMENSIONS` | حاشیه/پدینگ چهارطرفه |
| `ICONS` | کتابخانهٔ آیکن |
| `REPEATER` | فیلد تکرارشونده (آیتم‌های متعدد) |

### کنترل‌های گروهی (`Group_Control_*::get_type()`)

- `Group_Control_Typography` — تایپوگرافی کامل
- `Group_Control_Background` — پس‌زمینه (رنگ/گرادیان/تصویر)
- `Group_Control_Border` — حاشیه و گردی گوشه
- `Group_Control_Box_Shadow` — سایه
- `Group_Control_Text_Shadow` — سایهٔ متن

### نکات کلیدی

- **`selectors`** قدرتمندترین ابزار است: مقدار کنترل را بدون نوشتن CSS دستی مستقیم به DOM وصل می‌کند.
  `{{WRAPPER}}` به ریشهٔ همان ویجت اشاره دارد و `{{VALUE}}` مقدار انتخابی کاربر است.
- برای هر کنترلی که باید روی موبایل/تبلت فرق کند از **`add_responsive_control`** استفاده کنید.

منابع: [کنترل‌ها](https://developers.elementor.com/docs/controls/) ·
[تنظیمات کنترل](https://developers.elementor.com/docs/controls/control-settings/) ·
[رندر استایل](https://developers.elementor.com/docs/widgets/rendering-styles/)

---

## ۷. رندر کردن خروجی

### `render()` — خروجی PHP در فرانت‌اند

```php
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
```

- همیشه از **`get_settings_for_display()`** استفاده کنید (نه `get_settings()`) تا تگ‌های پویا پردازش شوند.
- خروجی را **escape** کنید (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).

### `content_template()` — پیش‌نمایش زنده در ویرایشگر

```php
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
```

این متد قالب Underscore.js است: `<# ... #>` برای منطق و `{{{ ... }}}` برای چاپ مقدار.

---

## ۸. دستهٔ سفارشی (اختیاری)

برای گروه‌بندی ویجت‌های افزونه زیر یک دستهٔ اختصاصی:

```php
function ewe_add_category( $elements_manager ) {
	$elements_manager->add_category(
		'ewe-widgets',
		[
			'title' => esc_html__( 'ویجت‌های من', 'elementor-widget-extension' ),
			'icon'  => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'ewe_add_category' );
```

سپس در ویجت: `public function get_categories(): array { return [ 'ewe-widgets' ]; }`

---

## ۹. بارگذاری دارایی‌ها (CSS/JS)

```php
// در ویجت
public function get_style_depends(): array { return [ 'ewe-style' ]; }
public function get_script_depends(): array { return [ 'ewe-script' ]; }

// ثبت دارایی‌ها
function ewe_register_assets() {
	wp_register_style( 'ewe-style', plugins_url( 'assets/css/widget.css', __FILE__ ), [], EWE_VERSION );
	wp_register_script( 'ewe-script', plugins_url( 'assets/js/widget.js', __FILE__ ), [ 'jquery' ], EWE_VERSION, true );
}
add_action( 'elementor/frontend/after_register_styles', 'ewe_register_assets' );
add_action( 'elementor/frontend/after_register_scripts', 'ewe_register_assets' );
```

با `get_style_depends`/`get_script_depends` دارایی‌ها فقط زمانی بارگذاری می‌شوند که ویجت در صفحه استفاده شده باشد (بهینه برای کارایی).

---

## ۱۰. بهترین شیوه‌ها (Best Practices)

1. **پیشوند یکتا** برای همهٔ توابع/کلاس‌ها/ثابت‌ها (`ewe_`, `EWE_`) تا تداخل ایجاد نشود.
2. همیشه **خروجی را escape و ورودی را sanitize** کنید.
3. از **`selectors`** به‌جای CSS دستی استفاده کنید تا استایل واکنش‌گرا و قابل‌ویرایش بماند.
4. **`has_widget_inner_wrapper(): false`** برای کاهش DOM در ویجت‌های جدید.
5. دارایی‌ها را **مشروط** (`get_*_depends`) بارگذاری کنید، نه سراسری.
6. متن‌ها را با **i18n** (`esc_html__`, دامنهٔ ترجمهٔ ثابت) قابل‌ترجمه کنید.
7. **بررسی نسخه‌ها** در بوت‌استرپ تا افزونه روی محیط ناسازگار خطا ندهد.
8. برای نسخهٔ ۴: ویجت کلاسیک شما همچنان کار می‌کند؛ اگر خواستید از مزایای **Classes/Variables و
   عناصر اتمی** بهره ببرید، در روادمپ مهاجرت تدریجی برنامه‌ریزی کنید (نه بازنویسی یک‌بارهٔ اجباری).

---

## ۱۱. چک‌لیست راه‌اندازی سریع

- [ ] پوشهٔ افزونه را در `wp-content/plugins/` بسازید.
- [ ] فایل اصلی با هدر و بررسی نسخه‌ها را اضافه کنید (بخش ۴).
- [ ] کلاس ویجت را در `widgets/` بسازید (بخش ۵–۷).
- [ ] ویجت را روی `elementor/widgets/register` ثبت کنید.
- [ ] افزونه را در پیشخوان وردپرس فعال کنید.
- [ ] در ویرایشگر المنتور، ویجت را زیر دستهٔ موردنظر پیدا و تست کنید.

---

## منابع رسمی

- [Elementor Developers — صفحهٔ اصلی](https://developers.elementor.com/)
- [ویجت‌ها](https://developers.elementor.com/docs/widgets/)
- [ساختار ویجت](https://developers.elementor.com/docs/widgets/widget-structure/)
- [اولین افزونه](https://developers.elementor.com/docs/getting-started/first-addon/)
- [افزودن ویجت جدید](https://developers.elementor.com/docs/widgets/add-new-widget/)
- [مثال ساده](https://developers.elementor.com/docs/widgets/simple-example/)
- [کنترل‌ها](https://developers.elementor.com/docs/controls/)
- [به‌روزرسانی توسعه‌دهندگان المنتور ۴.۰](https://developers.elementor.com/elementor-editor-4-0-developers-update/)
- [معرفی نسخهٔ ۴.۰ (Atomic Editor)](https://elementor.com/blog/editor-40-atomic-forms-pro-interactions/)
