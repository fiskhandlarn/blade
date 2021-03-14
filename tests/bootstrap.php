<?php

// PSR1.Files.SideEffects

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
