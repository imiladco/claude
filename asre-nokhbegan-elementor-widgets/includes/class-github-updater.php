<?php
/**
 * بروزرسانی خودکار افزونه از روی Releaseهای گیت‌هاب.
 *
 * با انتشار هر Release جدید روی مخزن (با تگی مثل v1.0.1)، وردپرس به‌صورت
 * خودکار بروزرسانی را در صفحهٔ افزونه‌ها نمایش می‌دهد — بدون آپلود دستی.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'ANW_Github_Updater' ) ) {
	return;
}

/**
 * Class ANW_Github_Updater
 */
class ANW_Github_Updater {

	/** @var string مسیر فایل اصلی افزونه. */
	private $file;

	/** @var string مخزن گیت‌هاب به‌شکل owner/repo. */
	private $repo;

	/** @var string توکن دسترسی (برای مخزن خصوصی). */
	private $token;

	/** @var array|null دادهٔ افزونه. */
	private $plugin_data;

	/** @var string basename افزونه. */
	private $basename;

	/** @var array|null پاسخ کش‌شدهٔ گیت‌هاب. */
	private $remote;

	/**
	 * @param string $file  مسیر فایل اصلی افزونه.
	 * @param string $repo  مخزن گیت‌هاب owner/repo.
	 * @param string $token توکن اختیاری.
	 */
	public function __construct( $file, $repo, $token = '' ) {
		$this->file  = $file;
		$this->repo  = $repo;
		$this->token = $token;
	}

	/**
	 * ثبت هوک‌ها.
	 */
	public function init_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
		add_filter( 'plugins_api', [ $this, 'plugin_info' ], 10, 3 );
		add_filter( 'upgrader_post_install', [ $this, 'after_install' ], 10, 3 );

		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), [ $this, 'action_links' ] );
			add_action( 'admin_init', [ $this, 'handle_force_check' ] );
			add_action( 'admin_notices', [ $this, 'force_check_notice' ] );
		}
	}

	/**
	 * افزودن لینک «بررسی بروزرسانی» در ردیف افزونه.
	 *
	 * @param array $links
	 * @return array
	 */
	public function action_links( $links ) {
		$url = wp_nonce_url(
			self_admin_url( 'plugins.php?anw_force_check=' . rawurlencode( plugin_basename( $this->file ) ) ),
			'anw_force_check'
		);

		$links['anw_check'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html__( 'بررسی بروزرسانی', 'asre-nokhbegan-widgets' )
		);

		return $links;
	}

	/**
	 * پاک‌سازی کش و واداشتن وردپرس به بررسی فوری بروزرسانی.
	 */
	public function handle_force_check() {
		if ( empty( $_GET['anw_force_check'] ) || plugin_basename( $this->file ) !== sanitize_text_field( wp_unslash( $_GET['anw_force_check'] ) ) ) {
			return;
		}
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}
		check_admin_referer( 'anw_force_check' );

		delete_transient( 'anw_gh_release_' . md5( $this->repo ) );
		delete_site_transient( 'update_plugins' );
		$this->remote = null;
		wp_update_plugins();

		wp_safe_redirect( self_admin_url( 'plugins.php?anw_checked=1' ) );
		exit;
	}

	/**
	 * نمایش پیام پس از بررسی دستی.
	 */
	public function force_check_notice() {
		if ( empty( $_GET['anw_checked'] ) ) {
			return;
		}

		$this->ensure_plugin_data();
		$version = $this->remote_version();
		$current = $this->plugin_data['Version'];

		if ( $version && version_compare( $version, $current, '>' ) ) {
			$message = sprintf(
				/* translators: %s: نسخهٔ جدید. */
				esc_html__( 'نسخهٔ جدید (%s) موجود است؛ از همین صفحه می‌توانید بروزرسانی کنید.', 'asre-nokhbegan-widgets' ),
				$version
			);
			$class = 'notice-warning';
		} elseif ( $version ) {
			$message = esc_html__( 'افزونه به‌روز است. (آخرین نسخهٔ منتشرشده روی گیت‌هاب بررسی شد.)', 'asre-nokhbegan-widgets' );
			$class   = 'notice-success';
		} else {
			$message = esc_html__( 'بررسی انجام شد، اما هیچ Releaseای روی مخزن گیت‌هاب پیدا نشد. ابتدا یک Release منتشر کنید.', 'asre-nokhbegan-widgets' );
			$class   = 'notice-info';
		}

		printf( '<div class="notice %s is-dismissible"><p>%s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	/**
	 * بارگذاری دادهٔ افزونه (در صورت نیاز).
	 */
	private function ensure_plugin_data() {
		if ( null !== $this->plugin_data ) {
			return;
		}
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin_data = get_plugin_data( $this->file, false, false );
		$this->basename    = plugin_basename( $this->file );
	}

	/**
	 * دریافت آخرین Release از گیت‌هاب (با کش ۶ ساعته).
	 *
	 * @return array|null
	 */
	private function get_remote() {
		if ( null !== $this->remote ) {
			return $this->remote;
		}

		$transient_key = 'anw_gh_release_' . md5( $this->repo );
		$cached        = get_transient( $transient_key );
		if ( false !== $cached ) {
			$this->remote = $cached;
			return $this->remote;
		}

		$url  = sprintf( 'https://api.github.com/repos/%s/releases/latest', $this->repo );
		$args = [
			'timeout' => 15,
			'headers' => [
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'WordPress/AsreNokhbeganWidgets',
			],
		];
		if ( ! empty( $this->token ) ) {
			$args['headers']['Authorization'] = 'Bearer ' . $this->token;
		}

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			set_transient( $transient_key, [], HOUR_IN_SECONDS );
			$this->remote = [];
			return $this->remote;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body ) || ! is_array( $body ) ) {
			$body = [];
		}

		set_transient( $transient_key, $body, 6 * HOUR_IN_SECONDS );
		$this->remote = $body;

		return $this->remote;
	}

	/**
	 * نسخهٔ Release (بدون پیشوند v).
	 *
	 * @return string
	 */
	private function remote_version() {
		$remote = $this->get_remote();
		if ( empty( $remote['tag_name'] ) ) {
			return '';
		}
		return ltrim( $remote['tag_name'], 'vV' );
	}

	/**
	 * آدرس بستهٔ نصب (ترجیحاً فایل zip پیوست‌شده، در غیر این صورت zipball).
	 *
	 * @return string
	 */
	private function package_url() {
		$remote = $this->get_remote();

		if ( ! empty( $remote['assets'] ) && is_array( $remote['assets'] ) ) {
			foreach ( $remote['assets'] as $asset ) {
				if ( isset( $asset['name'], $asset['browser_download_url'] )
					&& '.zip' === strtolower( substr( $asset['name'], -4 ) ) ) {
					return $asset['browser_download_url'];
				}
			}
		}

		return isset( $remote['zipball_url'] ) ? $remote['zipball_url'] : '';
	}

	/**
	 * تزریق اطلاعات بروزرسانی به ترنزینت وردپرس.
	 *
	 * @param mixed $transient
	 * @return mixed
	 */
	public function check_update( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$this->ensure_plugin_data();

		$remote_version = $this->remote_version();
		if ( empty( $remote_version ) ) {
			return $transient;
		}

		$current = isset( $transient->checked[ $this->basename ] )
			? $transient->checked[ $this->basename ]
			: $this->plugin_data['Version'];

		if ( version_compare( $remote_version, $current, '>' ) ) {
			$package = $this->package_url();

			$item               = new stdClass();
			$item->slug         = dirname( $this->basename );
			$item->plugin       = $this->basename;
			$item->new_version  = $remote_version;
			$item->url          = ! empty( $this->plugin_data['PluginURI'] ) ? $this->plugin_data['PluginURI'] : '';
			$item->package      = $package;
			$item->tested       = isset( $this->plugin_data['Elementor tested up to'] ) ? '' : '';
			$item->requires_php = ! empty( $this->plugin_data['RequiresPHP'] ) ? $this->plugin_data['RequiresPHP'] : '';

			$transient->response[ $this->basename ] = $item;
		} else {
			$item               = new stdClass();
			$item->slug         = dirname( $this->basename );
			$item->plugin       = $this->basename;
			$item->new_version  = $current;
			$item->package      = '';
			$transient->no_update[ $this->basename ] = $item;
		}

		return $transient;
	}

	/**
	 * اطلاعات نمایش‌داده‌شده در پنجرهٔ «جزئیات نسخه».
	 *
	 * @param mixed  $result
	 * @param string $action
	 * @param object $args
	 * @return mixed
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		$this->ensure_plugin_data();

		if ( empty( $args->slug ) || dirname( $this->basename ) !== $args->slug ) {
			return $result;
		}

		$remote = $this->get_remote();
		if ( empty( $remote ) ) {
			return $result;
		}

		$info               = new stdClass();
		$info->name         = $this->plugin_data['Name'];
		$info->slug         = dirname( $this->basename );
		$info->version      = $this->remote_version();
		$info->author       = $this->plugin_data['Author'];
		$info->homepage     = ! empty( $this->plugin_data['PluginURI'] ) ? $this->plugin_data['PluginURI'] : '';
		$info->requires_php = ! empty( $this->plugin_data['RequiresPHP'] ) ? $this->plugin_data['RequiresPHP'] : '';
		$info->download_link = $this->package_url();
		$info->sections     = [
			'description' => ! empty( $this->plugin_data['Description'] ) ? $this->plugin_data['Description'] : '',
			'changelog'   => ! empty( $remote['body'] ) ? wpautop( wp_kses_post( $remote['body'] ) ) : '',
		];

		if ( ! empty( $remote['published_at'] ) ) {
			$info->last_updated = $remote['published_at'];
		}

		return $info;
	}

	/**
	 * جابه‌جایی پوشهٔ استخراج‌شده به نام صحیح افزونه پس از نصب.
	 *
	 * @param mixed $response
	 * @param array $hook_extra
	 * @param array $result
	 * @return mixed
	 */
	public function after_install( $response, $hook_extra, $result ) {
		global $wp_filesystem;

		$this->ensure_plugin_data();

		if ( empty( $hook_extra['plugin'] ) || $this->basename !== $hook_extra['plugin'] ) {
			return $result;
		}

		$desired_dir = ANW_PATH;

		if ( isset( $result['destination'] ) && $result['destination'] !== untrailingslashit( $desired_dir ) ) {
			$wp_filesystem->move( $result['destination'], untrailingslashit( $desired_dir ) );
			$result['destination'] = untrailingslashit( $desired_dir );
		}

		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $this->basename ) ) {
			activate_plugin( $this->basename );
		}

		return $result;
	}
}
