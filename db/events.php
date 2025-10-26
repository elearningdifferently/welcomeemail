<?php
/**
 * Event observers for the welcome email plugin.
 *
 * This file lists the events the plugin is interested in and maps them to
 * observer methods. When a user is enrolled in a course the plugin will
 * send a welcome message.
 *
 * @package     local_welcomeemail
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\\core\\event\\user_enrolment_created',
        'callback'    => '\\local_welcomeemail\\observer::user_enrolment_created',
        'priority'    => 0,
        'internal'    => true,
    ],
];
