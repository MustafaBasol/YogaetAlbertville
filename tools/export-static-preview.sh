#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Static client-preview export.
#
# Renders the CURRENT demo WordPress site (see tools/local-preview.sh) to a
# self-contained, disposable static snapshot in preview-dist/, suitable for
# a temporary review link (e.g. on Vercel). It is presentation-only: no PHP,
# no database, no working wp-admin, no real form submission.
#
# The real source of truth stays:
#   wordpress/wp-content/themes/yoga-et-vie
#   wordpress/wp-content/plugins/yoga-et-vie-core
# See docs/client-preview.md.
#
#   ./tools/export-static-preview.sh
#   PORT=8890 ./tools/export-static-preview.sh
# ---------------------------------------------------------------------------
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PORT="${PORT:-8888}"
BASE="http://localhost:$PORT"
OUT="$ROOT/preview-dist"
RAW="$ROOT/.preview-raw"
export COMPOSER_ALLOW_SUPERUSER=1

command -v wget >/dev/null || { echo "wget est requis." >&2; exit 1; }

# --- 1. Make sure a local preview is running -------------------------------
STARTED_SERVER=0
if ! curl -fsS -o /dev/null "$BASE/" 2>/dev/null; then
	echo "→ Aucun aperçu local détecté sur $BASE : démarrage…"
	PORT="$PORT" "$ROOT/tools/local-preview.sh" > "$ROOT/.preview-raw.log" 2>&1 &
	STARTED_SERVER=1
	for _ in $(seq 1 60); do
		curl -fsS -o /dev/null "$BASE/" 2>/dev/null && break
		sleep 2
	done
	curl -fsS -o /dev/null "$BASE/" 2>/dev/null || { echo "Le serveur local n'a pas démarré à temps." >&2; exit 1; }
fi

WP="$ROOT/.local-wp/tools/vendor/bin/wp --path=$ROOT/.local-wp/public --allow-root"

# --- 2. Build the list of URLs to export -----------------------------------
urls_file="$(mktemp)"
{
	echo "$BASE/"
	$WP post list --post_type=page --post_status=publish --field=post_name | while read -r slug; do
		[ "$slug" = "accueil" ] && continue # front page, already added as "/"
		echo "$BASE/$slug/"
	done
	$WP post list --post_type=post --post_status=publish --field=post_name | while read -r slug; do
		echo "$BASE/$slug/"
	done
	$WP post list --post_type=yev_cours --post_status=publish --field=post_name | while read -r slug; do
		echo "$BASE/cours/$slug/"
	done
	echo "$BASE/category/evenements/"
	echo "$BASE/category/actualites/"
	echo "$BASE/page-inexistante-apercu/" # rendered and saved as 404.html below

	# The header's Navigation block loads its interactivity behaviour (the
	# mobile menu open/close) as a JS module resolved through an import map
	# (<script type="importmap">) rather than a plain <script src>, which
	# wget's page-requisites cannot follow. Read the homepage once to find
	# every such module URL (import map values + <link rel="modulepreload">)
	# and fetch them explicitly so the exported menu keeps working.
	home_html="$(curl -fsS "$BASE/")"
	{
		echo "$home_html" | grep -oE '"@wordpress/[a-z-]+":"[^"]+"' | sed -E 's/^"[^"]+":"([^"]+)"$/\1/'
		echo "$home_html" | grep -oE 'rel=["'"'"']modulepreload["'"'"'][^>]*href=["'"'"'][^"'"'"']+' | grep -oE 'href=["'"'"'][^"'"'"']+' | sed -E 's/^href=["'"'"']//'
	} | sed -E 's#^/#'"$BASE"'/#' | sort -u
} > "$urls_file"

echo "→ $(wc -l < "$urls_file") URL à exporter."

# --- 3. Mirror those URLs (and their CSS/JS/font/image requisites) --------
rm -rf "$RAW" "$OUT"
mkdir -p "$RAW"
wget \
	--directory-prefix="$RAW" \
	--no-host-directories \
	--page-requisites \
	--convert-links \
	--adjust-extension \
	--no-parent \
	-e robots=off \
	--user-agent="YogaEtVie-StaticPreviewExport/1.0" \
	--input-file="$urls_file" \
	--wait=0 \
	|| true # wget exits non-zero on the deliberate 404 page; that page is still saved.

rm -f "$urls_file"

# The deliberately-nonexistent URL renders WordPress's real 404 template;
# Vercel serves a top-level 404.html automatically, so promote it there.
if [ -f "$RAW/page-inexistante-apercu/index.html" ]; then
	cp "$RAW/page-inexistante-apercu/index.html" "$RAW/404.html"
	rm -rf "$RAW/page-inexistante-apercu"
fi

mv "$RAW" "$OUT"

# --- 4. Post-process: noindex, strip WP-only head noise, kill origin ------
#        strings, and disable the contact form's real submission.
node "$ROOT/tools/qa/postprocess-static-preview.mjs" "$OUT" "$BASE"

# --- 5. Privacy: robots.txt disallows everything ---------------------------
cat > "$OUT/robots.txt" <<'EOF'
User-agent: *
Disallow: /
EOF

# --- 6. Minimal Vercel config (static output, no build) -------------------
cat > "$OUT/vercel.json" <<'EOF'
{
  "$schema": "https://openapi.vercel.sh/vercel.json",
  "cleanUrls": false,
  "trailingSlash": true,
  "headers": [
    {
      "source": "/(.*)",
      "headers": [
        { "key": "X-Robots-Tag", "value": "noindex, nofollow" }
      ]
    }
  ]
}
EOF

if [ "$STARTED_SERVER" = "1" ]; then
	pkill -f "php -S localhost:$PORT" 2>/dev/null || true
fi

pages=$(find "$OUT" -name "index.html" | wc -l | tr -d ' ')
size=$(du -sh "$OUT" | cut -f1)
echo
echo "✔ Aperçu statique exporté dans preview-dist/ ($pages pages, $size)."
echo "  Aperçu local : cd preview-dist && python3 -m http.server 4000"
