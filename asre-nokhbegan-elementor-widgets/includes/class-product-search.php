<?php
/**
 * فیلترِ کوئریِ صفحهٔ نتایج بر پایهٔ ویژگی‌های محصول ووکامرس.
 *
 * منطق به‌صورت جنریک روی همهٔ ویژگی‌های ثبت‌شده (wc_get_attribute_taxonomies)
 * حلقه می‌زند؛ افزودن ویژگی جدید در آینده بدون تغییر کد کار می‌کند. پارامترها
 * از طریق GET با پیشوند «anwf_» خوانده می‌شوند (فرمِ ابزارک جستجوی هوشمند).
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ANW_Product_Search
 */
class ANW_Product_Search {

	const QUERY_PREFIX = 'anwf_';

	/**
	 * ثبت هوک‌ها.
	 */
	public function init_hooks(): void {
		add_action( 'pre_get_posts', [ $this, 'filter_query' ] );
	}

	/**
	 * اعمال فیلترِ ویژگی‌ها روی کوئریِ اصلیِ آرشیو محصولات.
	 *
	 * @param \WP_Query $query کوئری در حال اجرا.
	 */
	public function filter_query( $query ): void {
		if ( is_admin() || ! $query instanceof \WP_Query || ! $query->is_main_query() ) {
			return;
		}

		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		// فقط روی آرشیو محصولات (فروشگاه، آرشیو دستهٔ محصول یا ویژگی).
		$is_product_archive = ( function_exists( 'is_shop' ) && is_shop() )
			|| ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
			|| $query->is_post_type_archive( 'product' );

		if ( ! $is_product_archive ) {
			return;
		}

		$tax_query = $this->build_tax_query();

		if ( empty( $tax_query ) ) {
			return;
		}

		$existing = (array) $query->get( 'tax_query' );
		$merged   = array_merge( $existing, $tax_query );

		if ( count( $merged ) > 1 ) {
			$merged['relation'] = 'AND';
		}

		$query->set( 'tax_query', $merged );
	}

	/**
	 * ساخت آرایهٔ tax_query از روی پارامترهای GET برای هر ویژگیِ ثبت‌شده.
	 *
	 * @return array
	 */
	private function build_tax_query(): array {
		$tax_query = [];

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$name      = $attribute->attribute_name;
			$taxonomy  = wc_attribute_taxonomy_name( $name );
			$query_var = self::QUERY_PREFIX . $name;

			if ( empty( $_GET[ $query_var ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended — فیلترِ عمومی خواندنی از طریق GET نیاز به nonce ندارد.
				continue;
			}

			$raw   = wp_unslash( $_GET[ $query_var ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — در ادامه sanitize می‌شود.
			$terms = array_filter( array_map( 'sanitize_title', explode( ',', (string) $raw ) ) );

			if ( empty( $terms ) ) {
				continue;
			}

			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $terms,
				'operator' => 'IN',
			];
		}

		return $tax_query;
	}
}
