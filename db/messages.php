<?php
/**
 * Message provider definition for the welcome email plugin.
 *
 * Defines the custom message type used to send welcome notifications. By
 * specifying defaults the message will be delivered via email by default
 * when the user is logged in or out, but users can customise their
 * preferences via the messaging settings.
 *
 * @package     local_welcomeemail
 */

defined('MOODLE_INTERNAL') || die();

$messageproviders = [
    // Welcome message sent when a learner is enrolled into a course.
    'course_welcome' => [
        // No specific capability is required to receive a course welcome.
        'capability' => '',
        // Set a default delivery channel for the welcome message. Moodle defines
        // several constants (e.g. MESSAGE_DEFAULT_ENABLED) in lib/messagelib.php
        // to control whether a message type is enabled by default. During plugin
        // installation these constants may not have been loaded yet, which can
        // lead to undefined constant errors. To avoid this, we use the raw
        // integer value 1 (equivalent to MESSAGE_DEFAULT_ENABLED) instead of
        // referencing the constant directly. This ensures the default is set
        // without requiring the constants to be defined at install time.
        'defaults' => [
            // Enable email delivery by default. Users can override this in
            // their notification preferences.
            'email' => 1,
        ],
    ],
];
