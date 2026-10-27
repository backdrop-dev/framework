#!/usr/bin/env bash
#
# Sets up WordPress with SQLite for Backdrop's integration tests.
#
# Usage: tests/integration/setup.sh /path/to/wordpress
#
# Environment:
#   WP_VERSION      "latest" (default), a major version such as "6.6" (uses
#                   the newest 6.6.x release), or an exact version.
#   SQLITE_VERSION  Tag of WordPress/sqlite-database-integration to use.

set -euo pipefail

WP_DIR="${1:?Usage: setup.sh /path/to/wordpress}"
WP_VERSION="${WP_VERSION:-latest}"
SQLITE_VERSION="${SQLITE_VERSION:-v3.1.1}"
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [ -e "$WP_DIR" ]; then
	echo "$WP_DIR already exists. Remove it first." >&2
	exit 1
fi

# Resolve the WordPress version to a release tag.
TAGS="$(git ls-remote --tags --refs https://github.com/WordPress/WordPress | awk -F/ '{print $3}' | grep -E '^[0-9]+\.[0-9]+(\.[0-9]+)?$' | sort -V)"

if [ "$WP_VERSION" = "latest" ]; then
	TAG="$(echo "$TAGS" | tail -n 1)"
elif [[ "$WP_VERSION" =~ ^[0-9]+\.[0-9]+$ ]]; then
	TAG="$(echo "$TAGS" | grep -E "^${WP_VERSION//./\\.}(\.[0-9]+)?$" | tail -n 1)"
else
	TAG="$WP_VERSION"
fi

if [ -z "$TAG" ]; then
	echo "No WordPress release found for $WP_VERSION." >&2
	exit 1
fi

echo "Installing WordPress $TAG into $WP_DIR"
git clone --quiet --depth 1 --branch "$TAG" https://github.com/WordPress/WordPress "$WP_DIR"

# Install the SQLite database integration drop-in. The plugin package links in
# a sibling package with a symlink, so copy it with symlinks resolved.
SQLITE_SRC="$(mktemp -d)"
git clone --quiet --depth 1 --branch "$SQLITE_VERSION" https://github.com/WordPress/sqlite-database-integration "$SQLITE_SRC"
SQLITE_DIR="$WP_DIR/wp-content/plugins/sqlite-database-integration"
mkdir -p "$WP_DIR/wp-content/plugins"
cp -RL "$SQLITE_SRC/packages/plugin-sqlite-database-integration" "$SQLITE_DIR"
rm -rf "$SQLITE_SRC"
sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SQLITE_DIR#" \
	-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
	"$SQLITE_DIR/db.copy" > "$WP_DIR/wp-content/db.php"

cat > "$WP_DIR/wp-config.php" <<'PHP'
<?php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
define( 'DB_DIR', __DIR__ . '/wp-content/database/' );
define( 'DB_FILE', '.ht.sqlite' );
$table_prefix = 'wp_';
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_HOME', 'http://example.test' );
define( 'WP_SITEURL', 'http://example.test' );
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
PHP

# Add the test themes and must-use plugin, then the translation files.
mkdir -p "$WP_DIR/wp-content/themes" "$WP_DIR/wp-content/mu-plugins"
cp -R "$HERE/themes/." "$WP_DIR/wp-content/themes/"
cp "$HERE/mu-plugins/backdrop-test.php" "$WP_DIR/wp-content/mu-plugins/"
php "$HERE/make-translations.php" "$WP_DIR"

php "$HERE/install.php" "$WP_DIR"
