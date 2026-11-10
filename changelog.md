# Changelog

## Unreleased
Added:
* PHPUnit test suite for the container, application, proxies, and service providers.
* GitHub Actions workflow that runs the tests on PHP 7.4 through 8.5 and scans for PHP 7.4 compatibility.
* `Application::isBooted()` and `Proxy::hasContainer()`.

Changed:
* Backdrop only includes the application, the service container, and the static proxies. The theme features from 1.0 are separate packages; see "Upgrading from Backdrop 1.0" in the readme.
* `Application::VERSION` is now `2.0.0`.
* `add()`, `singleton()`, `$container['key'] = ...`, and `$container->key = ...` now replace an existing binding (and its shared instance) instead of silently keeping the first one, matching `bind()` and the `develop` branch.
* Extensions added with `extend()` are kept when the binding is added or replaced later.
* `composer.json` is included in release archives.
* Requires the stable `backdrop-dev/contracts` 1.0 release instead of its `develop` branch, so `composer.json` no longer sets `minimum-stability`.

Fixed:
* The container stores and resolves falsy values (`0`, `'0'`, `''`, `false`, `[]`) and `null` instances.
* Abstract classes and classes that can't be instantiated resolve to `false` instead of causing a fatal error.
* `remove()` works with an alias and also removes the binding's extensions.
* Service providers added after `boot()` are booted immediately.
* Two instances of the same service provider class are now both booted.
* `extend()` crashed with "Instantiation of class Closure is not allowed". Extensions now run as closures, before a shared instance is stored.
* Constructor auto-wiring could shift arguments into the wrong position. Interface types are now resolved, union types receive one argument, and a required dependency that can't be resolved throws a clear `RuntimeException`.
* `Backdrop\app()` works as soon as an application is created, including inside a service provider's `register()` method, and gives a clear error if no application exists.
* A second application (for example, from a child theme) no longer replaces the first one used by `Backdrop\app()`.
* The readme's boot example called `Backdrop\booted` without parentheses and put the application object in a hook name.
* Added the missing `backdrop-dev/contracts` dependency. `Application` and `ServiceProvider` implement `Backdrop\Contracts\Bootable`, so creating an application failed with "Interface Backdrop\Contracts\Bootable not found" on a fresh install.
* Proxy aliases with a leading backslash (e.g., `\Backdrop\App`) weren't recognized as the same alias, and checking whether an alias exists called the autoloader.

## 1.0.0 - November 7, 2026
The first official release of Backdrop, the themes framework. 1.0 is maintained on the `develop` branch; see its changelog for details.
