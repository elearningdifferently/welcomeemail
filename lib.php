<?php
/**
 * Library functions for the Welcome Email plugin.
 *
 * @package     local_welcomeemail
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add settings to the course edit form.
 *
 * This function is called by Moodle when building the course settings form.
 * We add a checkbox to enable/disable welcome emails for the course.
 *
 * @param object $formwrapper The moodleform wrapper for course settings
 * @param object $mform The actual form object
 */
// Legacy course edit form callbacks removed in favour of hook-based implementation.
