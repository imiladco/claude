#!/usr/bin/env bash
#
# انتشار کامل افزونه روی گیت‌هاب با یک دستور:
#   ۱) ساخت ریپوی Public
#   ۲) push کد افزونه (فقط فایل‌های افزونه، تمیز)
#   ۳) ساخت Release و پیوست فایل zip  → فعال‌شدن بروزرسانی خودکار
#
# پیش‌نیاز: یک Personal Access Token گیت‌هاب با اسکوپ "repo".
#
# روش استفاده:
#   GH_TOKEN=ghp_xxx bash publish.sh                # نسخه از روی هدر افزونه خوانده می‌شود
#   GH_TOKEN=ghp_xxx OWNER=imiladco bash publish.sh # تعیین دستی مالک
#   GH_TOKEN=ghp_xxx VERSION=1.2.0 bash publish.sh  # تعیین دستی نسخه
#
set -euo pipefail

SLUG="asre-nokhbegan-elementor-widgets"
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MAIN_FILE="$ROOT/$SLUG.php"

if [[ -z "${GH_TOKEN:-}" ]]; then
	echo "خطا: متغیر GH_TOKEN تنظیم نشده. یک توکن با اسکوپ repo بسازید." >&2
	exit 1
fi

API="https://api.github.com"
AUTH=(-H "Authorization: Bearer ${GH_TOKEN}" -H "Accept: application/vnd.github+json")

# --- تشخیص مالک (نام کاربری توکن) در صورت تعیین‌نشدن ---
if [[ -z "${OWNER:-}" ]]; then
	OWNER="$(curl -fsSL "${AUTH[@]}" "$API/user" | sed -n 's/.*"login": *"\([^"]*\)".*/\1/p' | head -n1)"
fi
[[ -n "$OWNER" ]] || { echo "خطا: تشخیص مالک ممکن نشد." >&2; exit 1; }

# --- استخراج نسخه از هدر افزونه ---
if [[ -z "${VERSION:-}" ]]; then
	VERSION="$(sed -n 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*\([0-9.]*\).*/\1/p' "$MAIN_FILE" | head -n1)"
fi
[[ -n "$VERSION" ]] || { echo "خطا: نسخه پیدا نشد." >&2; exit 1; }
TAG="v$VERSION"

echo "» مالک: $OWNER | ریپو: $SLUG | نسخه: $TAG"

# --- ۱) ساخت ریپو (اگر وجود نداشته باشد) ---
if curl -fsSL "${AUTH[@]}" "$API/repos/$OWNER/$SLUG" >/dev/null 2>&1; then
	echo "» ریپو از قبل وجود دارد؛ ادامه می‌دهیم."
else
	echo "» ساخت ریپوی Public..."
	curl -fsSL -X POST "${AUTH[@]}" "$API/user/repos" \
		-d "{\"name\":\"$SLUG\",\"description\":\"ابزارک‌های اختصاصی المنتور — عصر نخبگان\",\"private\":false}" >/dev/null
fi

# --- ۲) آماده‌سازی یک کپی تمیز و push ---
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT
cp -r "$ROOT"/. "$WORK/"
rm -rf "$WORK/.git" "$WORK/dist"

(
	cd "$WORK"
	git init -q
	git checkout -q -b main
	git add .
	git -c user.email="dev@asrenokhbegan.com" -c user.name="imiladco" commit -q -m "Release $TAG"
	git remote add origin "https://x-access-token:${GH_TOKEN}@github.com/$OWNER/$SLUG.git"
	git push -q -u origin main --force
)
echo "» کد push شد."

# --- ۳) ساخت zip ---
DIST="$ROOT/dist"
mkdir -p "$DIST"
PKG="$DIST/$SLUG.zip"
rm -f "$PKG"
TMPZIP="$(mktemp -d)"
mkdir -p "$TMPZIP/$SLUG"
cp -r "$WORK"/. "$TMPZIP/$SLUG/"
rm -rf "$TMPZIP/$SLUG/.git"
( cd "$TMPZIP" && zip -rq "$PKG" "$SLUG" )
rm -rf "$TMPZIP"
echo "» zip ساخته شد: $PKG"

# --- ۴) ساخت Release ---
echo "» ساخت Release $TAG..."
REL_JSON="$(curl -fsSL -X POST "${AUTH[@]}" "$API/repos/$OWNER/$SLUG/releases" \
	-d "{\"tag_name\":\"$TAG\",\"target_commitish\":\"main\",\"name\":\"$TAG\",\"body\":\"انتشار $TAG\"}")"
RELEASE_ID="$(printf '%s' "$REL_JSON" | sed -n 's/.*"id": *\([0-9]*\).*/\1/p' | head -n1)"
[[ -n "$RELEASE_ID" ]] || { echo "خطا: ساخت Release ناموفق بود." >&2; echo "$REL_JSON" >&2; exit 1; }

# --- ۵) آپلود فایل zip به‌عنوان asset ---
echo "» آپلود فایل zip..."
curl -fsSL -X POST \
	-H "Authorization: Bearer ${GH_TOKEN}" \
	-H "Content-Type: application/zip" \
	--data-binary @"$PKG" \
	"https://uploads.github.com/repos/$OWNER/$SLUG/releases/$RELEASE_ID/assets?name=$SLUG.zip" >/dev/null

echo "✓ تمام شد. ریپو و Release آماده‌اند:"
echo "  https://github.com/$OWNER/$SLUG/releases/tag/$TAG"
echo
echo "حالا در wp-config.php مطمئن شو که ANW_GITHUB_REPO روی \"$OWNER/$SLUG\" تنظیم است،"
echo "سپس در صفحهٔ افزونه‌ها روی «بررسی بروزرسانی» بزن."
