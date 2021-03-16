<?php

/**
 * This file is part of blade.
 *
 * blade is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * blade is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with blade.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * @author Oskar Joelson <oskar@joelson.org>
 */

declare(strict_types=1);

// First we need to load the composer autoloader so we can use WP Mock
require_once __DIR__ . '/../vendor/autoload.php';

// Now call the bootstrap method of WP Mock
WP_Mock::bootstrap();

if (class_exists('WordPlate\Application')) {
    // Run Wordplate so we can use getBasePath() in tests
    new WordPlate\Application(__DIR__);
}

// force clean cache directory
// phpcs:disable
define('WP_DEBUG', true);
// phpcs:enable
