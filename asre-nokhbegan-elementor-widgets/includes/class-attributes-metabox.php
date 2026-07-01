<?php
/**
 * متاباکسِ انتخاب سریع ویژگی‌های محصول در صفحهٔ ویرایش محصول، به‌همراه مخفی‌کردن
 * تب پیش‌فرض «Attributes» برای نقش‌های غیرمدیر (بدون حذف کامل، تا مدیر بتواند
 * ویژگی/Term جدید بسازد).
 *
 * ذخیره‌سازی از طریق WooCommerce Attribute API انجام می‌شود
 * ($product->set_attributes() + save())، نه دستکاری مستقیم wp_set_object_terms.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ANW_Attributes_Metabox
 */
class ANW_Attributes_Metabox {

	const NONCE_ACTION = 'anw_attr_metabox';
	const NONCE_NAME   = 'anw_attr_metabox_nonce';
	const FIELD_PREFIX = 'anw_attr_';

	/**
	 * ثبت هوک‌ها.
	 */
	public function init_hooks(): void {
		add_action( 'add_meta_boxes', [ $this, 'register_metabox' ] );
		add_action( 'save_post_product', [ $this, 'save' ], 10, 2 );
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'maybe_hide_attributes_tab' ] );
	}

	/**
	 * افزودن متاباکس به صفحهٔ ویرایش محصول.
	 */
	public function register_metabox(): void {
		add_meta_box(
			'anw-attributes-metabox',
			esc_html__( 'ویژگی‌های محصول (انتخاب سریع)', 'asre-nokhbegan-widgets' ),
			[ $this, 'render' ],
			'product',
			'side',
			'default'
		);
	}

	/**
	 * رندر محتوای متاباکس.
	 *
	 * @param \WP_Post $post پستِ محصول.
	 */
	public function render( $post ): void {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			echo '<p>' . esc_html__( 'ووکامرس فعال نیست.', 'asre-nokhbegan-widgets' ) . '</p>';
			return;
		}

		$taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $taxonomies ) ) {
			echo '<p>' . esc_html__( 'هیچ ویژگی‌ای تعریف نشده است.', 'asre-nokhbegan-widgets' ) . '</p>';
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		echo '<div class="anw-attr-metabox">';

		foreach ( $taxonomies as $attribute ) {
			$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
			$terms    = get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				]
			);

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			$assigned = wp_get_object_terms( $post->ID, $taxonomy, [ 'fields' => 'ids' ] );
			$assigned = is_wp_error( $assigned ) ? [] : $assigned;
			$field    = self::FIELD_PREFIX . $attribute->attribute_name;
			$label    = $attribute->attribute_label ? $attribute->attribute_label : $attribute->attribute_name;

			printf(
				'<p style="margin:0 0 4px;font-weight:600;">%s</p>',
				esc_html( $label )
			);
			printf(
				'<select name="%s[]" multiple size="4" style="width:100%%;margin-bottom:12px;">',
				esc_attr( $field )
			);

			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$d" %2$s>%3$s</option>',
					(int) $term->term_id,
					in_array( (int) $term->term_id, array_map( 'intval', $assigned ), true ) ? 'selected' : '',
					esc_html( $term->name )
				);
			}

			echo '</select>';
		}

		echo '<p class="description">' . esc_html__( 'برای انتخاب چند مورد، کلید Ctrl (یا Cmd) را نگه دارید.', 'asre-nokhbegan-widgets' ) . '</p>';
		echo '</div>';
	}

	/**
	 * ذخیرهٔ ویژگی‌های انتخاب‌شده با WooCommerce Attribute API.
	 *
	 * @param int      $post_id شناسهٔ محصول.
	 * @param \WP_Post $post    پست.
	 */
	public function save( $post_id, $post ): void {
		// بررسی nonce.
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			return;
		}

		// جلوگیری از اجرا در autosave/revision.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// بررسی سطح دسترسی.
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}

		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$attributes = $product->get_attributes();

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$name     = $attribute->attribute_name;
			$taxonomy = wc_attribute_taxonomy_name( $name );
			$field    = self::FIELD_PREFIX . $name;

			$selected = isset( $_POST[ $field ] ) ? array_map( 'absint', (array) wp_unslash( $_POST[ $field ] ) ) : [];
			$selected = array_filter( $selected );

			if ( ! empty( $selected ) ) {
				$attr_obj = isset( $attributes[ $taxonomy ] ) && $attributes[ $taxonomy ] instanceof \WC_Product_Attribute
					? $attributes[ $taxonomy ]
					: new \WC_Product_Attribute();

				$attr_obj->set_id( wc_attribute_taxonomy_id_by_name( $name ) );
				$attr_obj->set_name( $taxonomy );
				$attr_obj->set_options( $selected );
				$attr_obj->set_visible( true );

				$attributes[ $taxonomy ] = $attr_obj;
			} else {
				unset( $attributes[ $taxonomy ] );
			}
		}

		$product->set_attributes( $attributes );
		$product->save();
	}

	/**
	 * مخفی‌کردن تب پیش‌فرض «Attributes» برای نقش‌های غیرمدیر.
	 *
	 * @param array $tabs تب‌های دادهٔ محصول.
	 * @return array
	 */
	public function maybe_hide_attributes_tab( $tabs ) {
		if ( ! current_user_can( 'manage_options' ) && isset( $tabs['attribute'] ) ) {
			unset( $tabs['attribute'] );
		}

		return $tabs;
	}
}
