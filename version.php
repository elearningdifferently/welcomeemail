<?php
// This file defines the version and other metadata for the plugin.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_welcomeemail
 */

defined('MOODLE_INTERNAL') || die();

$plugin = new stdClass();
$plugin->component = 'local_welcomeemail';
$plugin->version   = 2025110501;      // The current plugin version (YYYYMMDDXX). Incremented to trigger upgrade and refresh language caches.
$plugin->release   = '1.2';           // Human readable release name.
$plugin->requires  = 2024011500;      // Requires this Moodle version (Moodle 4.4 or later). Adjust as needed for Moodle 5.0.
$plugin->maturity  = MATURITY_STABLE;
