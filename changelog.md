# Changelog

## Unreleased
Added:
* PHPUnit test suite covering the container, application, helpers, attributes, views, templates, pagination, and theme functions.
* GitHub Actions workflow that runs the tests on PHP 7.4 through 8.5 and scans for PHP 7.4 compatibility.
* `Backdrop\booted()` helper and `Application::isBooted()` so a child theme can reuse an existing application.

Changed:
* Backdrop is documented as a themes-only framework. The core-only package lives on the `2.0` branch.
* `Backdrop\app()` works as soon as the application is created, not only after `boot()`.
* `boot()` only runs once, providers are only booted once, and existing `Backdrop\App` aliases are not redeclared.
* Service providers added after `boot()` are registered and booted immediately.
* Pagination previous and next links have default "Previous" and "Next" text.

Fixed:
* The container now stores and resolves falsy values (`0`, `''`, `false`, `[]`, `null`).
* The container no longer tries to build abstract classes or fails on intersection types.
* Extensions added before a binding are kept.
* PHP 8.1+ deprecation notices from the container's `ArrayAccess` methods and PHP 8.4 notices from `View`.
* `replace_html_class()` and the comment reply link corrupted HTML when the class attribute was empty.
* The comments link showed on posts with comments closed and no comments.
* `Backdrop\Comment\is_approved()` checked the global comment instead of the one passed in.
* `attr()` names such as `render` or `all` caused infinite recursion.
* The archive description filter could return a `WP_Error`.
* Custom comments templates in a parent theme weren't found from a child theme.
* Body and post class filters could warn when no object was queried.
* The privacy policy page template wasn't part of the template hierarchy.
* Duplicate entries in the language hierarchy (e.g., `fr_FR`).
* Radio image control URLs containing encoded characters such as `%20`.
* `hex_to_rgb()` warnings on invalid colors.
* `View\Engine` now implements the `Engine` contract.
* `html_entity_decode()` and `trim()` calls now pass explicit flags and characters so behavior is identical on PHP 7.4 through 8.x.

## 1.0.0 - June 11, 2023
Added:
* Our very first official release!
