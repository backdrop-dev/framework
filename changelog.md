# Changelog

## Unreleased
Fixed:
* `extend()` crashed with "Instantiation of class Closure is not allowed". Extensions now run as closures, before a shared instance is stored.
* Constructor auto-wiring could shift arguments into the wrong position. Interface types are now resolved, union types receive one argument, and a required dependency that can't be resolved throws a clear `RuntimeException`.
* `Backdrop\app()` works as soon as an application is created, including inside a service provider's `register()` method, and gives a clear error if no application exists.
* A second application (for example, from a child theme) no longer replaces the first one used by `Backdrop\app()`.
* The readme's boot example called `Backdrop\booted` without parentheses and put the application object in a hook name.
* Added the missing `backdrop-dev/contracts` dependency. `Application` and `ServiceProvider` implement `Backdrop\Contracts\Bootable`, so creating an application failed with "Interface Backdrop\Contracts\Bootable not found" on a fresh install.

## 1.0.0 - June 11, 2023
Added:
* Our very first official release!
