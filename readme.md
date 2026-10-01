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

## Copyright and License

This project is licensed under the GNU General Public License, version 2 or later.

Copyright © 2019–2026 Benjamin Lu
