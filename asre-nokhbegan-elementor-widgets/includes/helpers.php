<?php
/**
 * توابع کمکی مشترک بین ویجت‌ها.
 *
 * @package AsreNokhbeganWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'anw_validate_html_tag' ) ) {
	/**
	 * اعتبارسنجی تگ HTML برای جلوگیری از خروجی ناامن.
	 *
	 * @param string $tag     تگ ورودی کاربر.
	 * @param string $default تگ پیش‌فرض.
	 * @return string
	 */
	function anw_validate_html_tag( $tag, $default = 'div' ) {
		$allowed = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' ];
		$tag     = strtolower( (string) $tag );

		return in_array( $tag, $allowed, true ) ? $tag : $default;
	}
}

if ( ! function_exists( 'anw_allowed_inline_html' ) ) {
	/**
	 * تگ‌های مجاز درون عناوین (برای استایل‌دهی به بخشی از متن با <span>).
	 *
	 * @return array
	 */
	function anw_allowed_inline_html() {
		return [
			'span'   => [
				'class' => [],
				'style' => [],
			],
			'b'      => [],
			'strong' => [],
			'em'     => [],
			'i'      => [],
			'mark'   => [ 'class' => [] ],
			'br'     => [],
			'a'      => [
				'href'   => [],
				'title'  => [],
				'target' => [],
				'rel'    => [],
				'class'  => [],
			],
		];
	}
}

if ( ! function_exists( 'anw_get_media_icon_html' ) ) {
	/**
	 * رندر آیکون از کنترل MEDIA؛ SVG به‌صورت اینلاین (پاک‌سازی‌شده) و سایر
	 * فرمت‌ها به‌صورت تگ <img>.
	 *
	 * @param array $image مقدار کنترل MEDIA (شامل url و id).
	 * @return string
	 */
	function anw_get_media_icon_html( $image ) {
		if ( empty( $image ) || empty( $image['url'] ) ) {
			return '';
		}

		$id   = isset( $image['id'] ) ? absint( $image['id'] ) : 0;
		$mime = $id ? get_post_mime_type( $id ) : '';

		$is_svg = ( 'image/svg+xml' === $mime )
			|| ( ! $mime && preg_match( '/\.svg(\?.*)?$/i', $image['url'] ) );

		if ( $is_svg && $id && class_exists( '\Elementor\Core\Files\Assets\Svg\Svg_Handler' ) ) {
			$svg = \Elementor\Core\Files\Assets\Svg\Svg_Handler::get_inline_svg( $id );
			if ( ! empty( $svg ) ) {
				return $svg;
			}
		}

		$alt = $id ? get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';

		return sprintf(
			'<img src="%s" alt="%s" loading="lazy" />',
			esc_url( $image['url'] ),
			esc_attr( $alt )
		);
	}
}
