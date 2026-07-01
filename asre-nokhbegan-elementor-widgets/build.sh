#!/usr/bin/env bash
# ساخت فایل zip قابل‌نصب افزونه (با پوشهٔ صحیح در ریشهٔ zip).
set -euo pipefail

SLUG="asre-nokhbegan-elementor-widgets"
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BUILD_DIR="$(mktemp -d)"
DIST_DIR="$ROOT/dist"

mkdir -p "$DIST_DIR" "$BUILD_DIR/$SLUG"

# کپی فایل‌های افزونه (بدون موارد توسعه/سورس‌کنترل).
rsync -a --exclude='.git' --exclude='dist' --exclude='build.sh' \
	"$ROOT"/ "$BUILD_DIR/$SLUG"/

rm -f "$DIST_DIR/$SLUG.zip"
( cd "$BUILD_DIR" && zip -rq "$DIST_DIR/$SLUG.zip" "$SLUG" )
rm -rf "$BUILD_DIR"

echo "ساخته شد: $DIST_DIR/$SLUG.zip"
