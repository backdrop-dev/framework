# Backdrop: Themes Framework

Backdrop is a framework for developing themes for ClassicPress and WordPress.

Backdrop provides the core application layer, including a service container and service provider system, along with theme features such as a template hierarchy, views, HTML attributes, pagination, and Customizer helpers.

Backdrop is designed for themes. When the application is created, it adds filters that change the template hierarchy, body and post classes, menu item classes, and the document `<head>`, so it should not be used inside plugins. For a core-only package without these theme features, use the `2.0` branch.

## Requirements

- [ClassicPress](https://www.classicpress.net/)
- [WordPress](https://wordpress.org/)
- [PHP 7.4+](https://www.php.net/releases/7_4_0.php)
- [Composer](https://getcomposer.org/)

## Installation

Use Composer to install Backdrop:

```bash
composer require backdrop-dev/framework
```

## Loading Backdrop

If Backdrop is bundled directly with your theme, load Composer's autoloader from the parent theme:

```php
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once get_parent_theme_file_path( 'vendor/autoload.php' );
}
```

## Registering and Booting Backdrop

Backdrop isn't fully booted until an instance of the `Backdrop\Core\Application` class is created, the necessary service providers are registered, and the application's `boot()` method is called.

Create the application before registering your theme's service providers. If an application has already been booted (for example, by a parent theme), reuse it instead of creating a new one:

```php
// Create a new application, or reuse the existing one.
$app = Backdrop\booted() ? Backdrop\app() : new Backdrop\Core\Application();

// Add service providers.
$app->provider( YourTheme\Provider::class );

// Create an action hook for child themes.
do_action( 'your-theme/bootstrap', $app );

// Boot the application.
$app->boot();
```

As soon as the application has been created, it can be accessed through the `Backdrop\app()` helper:

```php
$app = Backdrop\app();
```

After the application has been booted, the `Backdrop\App` static proxy is also available. Calling `boot()` more than once has no effect, and service providers added after booting are registered and booted immediately.

## Translations

Backdrop's own strings (such as "(Untitled)" or archive titles) use the `backdrop` textdomain, but Backdrop doesn't ship separate translation files. Instead, these strings are translated with your parent theme's translations, using the theme's `Text Domain` header. To translate them, make sure your theme's translation template (`.pot`) includes Backdrop's strings. Translation tools often skip them by default, because they live in the `vendor` folder and use the `backdrop` textdomain, so check your tool's options for including that folder and other textdomains. Then load your theme's translations as usual:

```php
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'your-theme', get_template_directory() . '/languages' );
} );
```

This works with WordPress's just-in-time translation loading (WordPress 6.7 and later), with `switch_to_locale()`, and with ClassicPress.

Backdrop also uses your theme's language directory, set by the `Domain Path` header in `style.css` (`/languages` if it isn't set):

- **Locale functions:** a `{locale}.php` file, lowercase and hyphenated (for example, `languages/fr-fr.php` for `fr_FR`), is loaded on `after_setup_theme`. A child theme's file is loaded before the parent theme's.
- **Child theme translations:** a child theme can provide the parent theme's translations in its own language directory as `{parent-text-domain}-{locale}.mo` (for example, `your-theme-fr_FR.mo`), so they aren't lost when the parent theme updates. This file replaces the parent theme's translations for that locale, so it needs to contain every string, including Backdrop's.

## Running the Tests

Backdrop's tests use [PHPUnit 9.6](https://phpunit.de/), which runs on every supported PHP version (7.4 and later). WordPress functions are replaced with small stubs, so the tests don't need a WordPress install.

```bash
composer install
phpunit
```

If PHPUnit isn't installed globally, you can install it with `composer global require phpunit/phpunit:^9.6` or download the [PHPUnit 9.6 PHAR](https://phar.phpunit.de/phpunit-9.6.phar).

Integration tests check features that depend on WordPress itself, such as translations, against a real WordPress install that uses SQLite (so no database server is needed). They require PHP's `pdo_sqlite` extension:

```bash
composer install
tests/integration/setup.sh /tmp/wordpress
php tests/integration/run.php /tmp/wordpress
```

Set `WP_VERSION` (for example, `WP_VERSION=6.6`) to test a specific WordPress version. The default is the latest release.

Every push to `develop` and every pull request runs the tests on PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, and 8.5 with GitHub Actions, along with a scan that checks the code is compatible with PHP 7.4 and later, and the integration tests on the latest WordPress release and WordPress 6.6.

## Copyright and License

This project is licensed under the GNU General Public License, version 2 or later.

Copyright © 2019–2026 Benjamin Lu
