#!/usr/bin/env bash
# Generate WebP variants for JPG/PNG under public/media and public/img.
# Nginx will serve these when the browser sends Accept: image/webp (see nginx.conf).
# Requires: cwebp (libwebp) or ImageMagick 'convert'. Install: apt install webp / brew install webp

set -e
ROOT="${1:-$(cd "$(dirname "$0")/.." && pwd)}"
PUBLIC="${ROOT}/public"
QUALITY="${WEBP_QUALITY:-82}"
DRY_RUN="${DRY_RUN:-0}"

if [[ ! -d "$PUBLIC" ]]; then
  echo "Not found: $PUBLIC (run from repo root or pass path as first arg)"
  exit 1
fi

if command -v cwebp >/dev/null 2>&1; then
  CONVERT="cwebp"
elif command -v convert >/dev/null 2>&1; then
  CONVERT="convert"
else
  echo "Install cwebp (webp) or ImageMagick (convert)."
  exit 1
fi

count=0
while IFS= read -r -d '' f; do
  base="${f%.*}"
  ext="${f##*.}"
  out="${base}.${ext}.webp"
  if [[ -f "$out" && "$out" -nt "$f" ]]; then
    continue
  fi
  if [[ "$DRY_RUN" = "1" ]]; then
    echo "Would create: $out"
  else
    mkdir -p "$(dirname "$out")"
    if [[ "$CONVERT" = "cwebp" ]]; then
      cwebp -q "$QUALITY" "$f" -o "$out"
    else
      convert "$f" -quality "$QUALITY" "$out"
    fi
    echo "Created: $out"
  fi
  (( count++ )) || true
done < <(find "$PUBLIC/media" "$PUBLIC/img" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) -print0 2>/dev/null || true)

echo "Done. WebP variants: $count (nginx will serve them when Accept: image/webp)."
