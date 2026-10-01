# Backdrop: Themes & Plugins Framework

Backdrop is a framework for developing themes and plugins for ClassicPress and WordPress.

Backdrop provides the core application layer, including a service container and service provider system. It can be used on its own or alongside other Backdrop packages.

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

## Themes

If Backdrop is bundled directly with your theme, load Composer's autoloader from the parent theme:

```php
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once get_parent_theme_file_path( 'vendor/autoload.php' );
}
```

## Plugins

If Backdrop is bundled directly with your plugin, load Composer's autoloader from the plugin directory:

```php
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}
```

## Registering and Booting Backdrop

Backdrop isn't fully booted until an instance of the `Backdrop\Core\Application` class is created, the necessary service providers are registered, and the application's `boot()` method is called.

Create the application before registering your project's service providers:

```php
// Create a new application.
$app = new Backdrop\Core\Application();

// Add service providers.
$app->provider( YourProject\Provider::class );

// Create an action hook for child themes or plugins.
do_action( 'your-project/bootstrap', $app );

// Boot the application.
$app->boot();
```

Once the application has been created, it can be accessed through the `Backdrop\app()` helper. After the application has been booted, the `Backdrop\App` static proxy is also available.

For example:

```php
$app = Backdrop\app();
```

The application should normally be created and booted once by the theme or plugin that is responsible for initializing Backdrop.

## Copyright and License

This project is licensed under the GNU General Public License, version 2 or later.

Copyright © 2019–2026 Benjamin Lu