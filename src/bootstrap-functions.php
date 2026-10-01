<?php
/**
 * Functions files bootstrap.
 *
 * This file loads all of our function files necessary for using the framework.
 * Class files are loaded separately through Composer's autoloader.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

/**
 * Load framework function files that are not handled by the class autoloader.
 */
require_once __DIR__ . '/functions-filters.php';
require_once __DIR__ . '/functions-helpers.php';
require_once __DIR__ . '/Attr/functions-attr.php';
require_once __DIR__ . '/Comment/functions-comment.php';
require_once __DIR__ . '/Lang/functions-lang.php';
require_once __DIR__ . '/Menu/functions-menu.php';
require_once __DIR__ . '/Pagination/functions-pagination.php';
require_once __DIR__ . '/Post/functions-post.php';
require_once __DIR__ . '/Sidebar/functions-sidebar.php';
require_once __DIR__ . '/Site/functions-site.php';
require_once __DIR__ . '/Template/functions-template.php';
require_once __DIR__ . '/Theme/functions-theme.php';
require_once __DIR__ . '/View/functions-view.php';