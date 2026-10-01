# Changelog

## Unreleased
Added:
* PHPUnit test suite for the container, application, proxies, and service providers.
* GitHub Actions workflow that runs the tests on PHP 7.4 through 8.5 and scans for PHP 7.4 compatibility.
* `Application::isBooted()` and `Proxy::hasContainer()`.

Changed:
* `Application::VERSION` is now `2.0.0`.
* `composer.json` is included in release archives.

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

## 1.0.0 - June 11, 2023
Added:
* Our very first official release!
