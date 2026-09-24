#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Docker-free local preview: WordPress + SQLite + PHP's built-in server.
#
#   ./tools/local-preview.sh            # install (first run) and serve
#   ./tools/local-preview.sh --reset    # start again from an empty site
#   PORT=8890 ./tools/local-preview.sh  # another port
#
# Requires: PHP 8.2+ (pdo_sqlite, gd), Composer. Everything is created in
# .local-wp/ (git-ignored). The theme and plugin are symlinked, so edits in
# wordpress/wp-content/ are visible immediately.
#
# For development only — the SQLite drop-in and PHP's built-in server are
# NOT a production setup. For a Docker-based setup, see `npm run env:start`.
# ---------------------------------------------------------------------------
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SITE="$ROOT/.local-wp"
PORT="${PORT:-8888}"
URL="http://localhost:$PORT"
WP_VERSION="${WP_VERSION:-7.1.2}"
export COMPOSER_ALLOW_SUPERUSER=1

if [[ "${1:-}" == "--reset" ]]; then
	rm -rf "$SITE"
fi

if [[ ! -f "$SITE/public/wp-load.php" ]]; then
	echo "→ Téléchargement de WordPress $WP_VERSION, WP-CLI et du connecteur SQLite…"
	mkdir -p "$SITE/tools"
	composer create-project --no-interaction --no-scripts --quiet \
		johnpbloch/wordpress-core "$SITE/public" "$WP_VERSION"
	rm -rf "$SITE/public/vendor" "$SITE/public/composer.json" "$SITE/public/composer.lock"
	(
		cd "$SITE/tools"
		echo '{}' > composer.json
		composer config --no-plugins allow-plugins.composer/installers false
		composer require --no-interaction --quiet wp-cli/wp-cli-bundle aaemnnosttv/wp-sqlite-db:dev-master
	)
	cp "$SITE/tools/vendor/aaemnnosttv/wp-sqlite-db/src/db.php" "$SITE/public/wp-content/db.php"
	ln -sfn "$ROOT/wordpress/wp-content/themes/yoga-et-vie" "$SITE/public/wp-content/themes/yoga-et-vie"
	ln -sfn "$ROOT/wordpress/wp-content/plugins/yoga-et-vie-core" "$SITE/public/wp-content/plugins/yoga-et-vie-core"
fi

WP="$SITE/tools/vendor/bin/wp --path=$SITE/public --allow-root"

if ! $WP core is-installed 2>/dev/null; then
	echo "→ Installation du site de démonstration…"
	$WP config create --dbname=wp --dbuser=local --dbpass=local --skip-check --force --extra-php <<'PHP'
define( 'DB_DIR', __DIR__ . '/wp-content/database/' );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISABLE_WP_CRON', true ); // PHP's built-in server cannot answer WP-Cron's loopback call.
PHP
	$WP core install --url="$URL" --title="Yoga et Vie Albertville" \
		--admin_user=admin --admin_password=admin --admin_email=admin@example.test --skip-email
	$WP language core install fr_FR --activate 2>/dev/null || echo "   (pack de langue fr_FR indisponible hors ligne — administration en anglais)"
	$WP theme activate yoga-et-vie
	$WP plugin activate yoga-et-vie-core
	$WP eval-file "$ROOT/tools/seed-demo-content.php"
	$WP rewrite structure '/%postname%/' --hard 2>/dev/null || true
fi

echo
echo "✔ Site : $URL   —   Administration : $URL/wp-admin (admin / admin)"
echo "  Ctrl+C pour arrêter."
cd "$SITE/public"
PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}" exec php -S "localhost:$PORT" "$ROOT/tools/router.php"
