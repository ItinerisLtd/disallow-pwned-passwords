<?php
/**
 * Plugin Name:     Disallow Pwned Passwords
 * Plugin URI:      https://github.com/ItinerisLtd/disallow-pwned-passwords
 * Description:     TODO
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://itineris.co.uk
 * License:         GPL-2.0-or-later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     disallow-pwned-passwords
 */

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

// If this file is called directly, abort.
use Itineris\DisallowPwnedPasswords\Handlers\FormSubmission;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ObjectCachedClient;

if (! defined('WPINC')) {
    die;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * @return void
 */
function run(): void
{
    $plugin = new Plugin();
    $plugin->run();
}

run();
