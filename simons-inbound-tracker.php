<?php namespace Carbontwelve\InboundTracker;
/**
 * Plugin Name: Simons Inbound Tracker
 * Description: Inbound affiliate link tracker. PHP 5.3+ Only.
 * Plugin URI:
 * Version:     1.0.0
 * Author:      Simon Dann
 * Author URI:  http://photogabble.co.uk
 * License:     GPL
 * Text Domain: simons_button_board
 * Domain Path: /languages
 */

use SplClassLoader;
use Exception;

// Include the PSR-0 Classloader
if ( ! class_exists('SplClassLoader')){
    require __DIR__ . '/Vendor/SplClassLoader.php';
}

$loader = new SplClassLoader('Carbontwelve\InboundTracker', __DIR__ . '/App');

if (!$loader->register()) {
    throw new Exception('Unable to initialize the auto loader.');
}

// Start Button Board Plugin
$buttonBoard = new \Carbontwelve\InboundTracker\Start;
