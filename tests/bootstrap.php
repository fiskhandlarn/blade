<?php

// First we need to load the composer autoloader so we can use WP Mock
require_once __DIR__ . '/../vendor/autoload.php';

// Now call the bootstrap method of WP Mock
WP_Mock::bootstrap();

// Run Wordplate so we can use getBasePath() in tests
new WordPlate\Application(__DIR__);

define('WP_DEBUG', true); // force clean cache directory
