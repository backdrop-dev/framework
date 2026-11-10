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

## Upgrading from Backdrop 1.0
Backdrop 2.0 only includes the application, the service container, and the static proxies. The theme features that were part of Backdrop 1.0 are now separate packages, so install the ones your theme uses and add their service providers before booting the application:

| Feature in 1.0 | Package | Service provider |
|---|---|---|
| HTML attributes (`Backdrop\Attr`) | `backdrop-dev/attr` | `Backdrop\Attr\Provider` |
| Template hierarchy (`Backdrop\Template`) | `backdrop-dev/template-hierarchy` | `Backdrop\Template\Hierarchy\Provider` |
| Views (`Backdrop\View`) | `backdrop-dev/view` | `Backdrop\View\Provider` |
| Pagination (`Backdrop\Pagination`) | `backdrop-dev/pagination` | `Backdrop\Pagination\Provider` |
| Translations and locales (`Backdrop\Lang`) | `backdrop-dev/languages` | `Backdrop\Languages\Provider` |
| Customizer (`Backdrop\Customize`) | `backdrop-dev/customize` | `Backdrop\Customize\Provider` |
| Collections (`Backdrop\Tools\Collection`) | `backdrop-dev/tools` | None |
| Template tags for posts, comments, the site, menus, and sidebars | `backdrop-dev/theme` | `Backdrop\Theme\Provider` |

Some names changed along the way:

- The container class is `Backdrop\Core\Container` (it was `Backdrop\Container\Container`).
- The container's `has()` method is now `bound()`.
- The view engine is bound as `Backdrop\View\Engine::class` instead of `view/engine`.
- `Backdrop\Template\hierarchy()` is now `Backdrop\Template\Hierarchy\hierarchy()`, and the `Backdrop\Lang` functions are in `Backdrop\Languages`.
- The post, comment, site, menu, and sidebar functions moved to `Backdrop\Theme\Entry`, `Backdrop\Theme\Comment`, `Backdrop\Theme\Site`, `Backdrop\Theme\Menu`, and `Backdrop\Theme\Sidebar`. `Backdrop\is_classicpress()` is now `Backdrop\Theme\is_classicpress()`.
- `Backdrop\Contracts\Bootable`, `Displayable`, and `Renderable` come from the `backdrop-dev/contracts` package. The container and application contracts from 1.0 were removed.
- Only `Backdrop\booted()` and `Backdrop\app()` are still helper functions in this package. Helpers such as `collect()`, `path()`, `version()`, and `hex_to_rgb()` were removed.

## Running the Tests
Backdrop's tests use [PHPUnit 9.6](https://phpunit.de/), which runs on every supported PHP version (7.4 and later).

```bash
composer install
phpunit
```

If PHPUnit isn't installed globally, you can install it with `composer global require phpunit/phpunit:^9.6` or download the [PHPUnit 9.6 PHAR](https://phar.phpunit.de/phpunit-9.6.phar).

Every push to `develop` and every pull request runs the tests on PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, and 8.5 with GitHub Actions, along with a scan that checks the code is compatible with PHP 7.4 and later.

## Copyright and License
This project is licensed under the GNU General Public License, version 2 or later.

Copyright © 2019–2026 Benjamin Lu
