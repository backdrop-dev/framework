# Backdrop: Themes & Plugins Framework
Backdrop is a framework for developing themes & plugins for ClassicPress and WordPress.

Backdrop is the core application layer that consists of a service container and it can be use alone or alongside with Backdrop's available packages.

## Requirements
- [ClassicPress](https://www.classicpress.net/)
- [WordPress](https://wordpress.org)
- [PHP 7.4](https://www.php.net/releases/7_4_0.php)
- [Composer](https://getcomposer.org)

## Installation
Use the following command from your preferred command line utility to install Backdrop.

<pre>
composer require backdrop-dev/framework
</pre>

## Themes
if bundling this directly in your theme, add the following code.
<pre>
if ( file_exists( get_parent_theme_file_path( 'vendor/autoload.php' ) ) ) {
	require_once( get_parent_theme_file_path( 'vendor/autoload.php' ) );
}
</pre>

## Plugins
if bundling this directly in your plugin, add the following code.
<pre>
if ( file_exists( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
}
</pre>

## Registering and Booting Backdrop
Backdrop isn't launched until an instance of its `Backdrop\Core\Application` class is created and its `boot()` method is called. Before creating a new application, check the `Backdrop\booted()` function. If an application has already been booted (for example, by a parent theme), use the existing instance via the `Backdrop\app()` helper function.

```php
// Create a new application, or reuse the existing one.
$app = Backdrop\booted() ? Backdrop\app() : new Backdrop\Core\Application();

// Add service providers.
$app->provider( YourProject\Provider::class );

// Create an action hook for child themes or plugins.
do_action( 'your-project/bootstrap', $app );

// Boot the application.
$app->boot();
```

As soon as an application has been created, it can be accessed through the `Backdrop\app()` helper, including inside a service provider's `register()` method. After the application has been booted, the `Backdrop\App` static proxy is also available.

## Copyright and Licenses
This project is licensed under the GNU GPL, version 2 or later.

2019–2023 © Benjamin Lu
