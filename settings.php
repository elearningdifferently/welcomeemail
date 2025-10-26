<?php
/**
 * Configuration settings for the Welcome Email plugin.
 *
 * This file defines the admin settings that allow site administrators to
 * customise the subject and body of the welcome message sent to students
 * when they are enrolled in a course. The settings support simple
 * replacement tokens such as [[studentfullname]] and [[coursename]] to
 * personalise the message.
 *
 * @package     local_welcomeemail
 */

defined('MOODLE_INTERNAL') || die();

// Only add settings if the user has the capability to manage site configuration.
if ($hassiteconfig) {
    // Create a new settings page under the "Local plugins" category.
    $settings = new admin_settingpage('local_welcomeemail',
        get_string('pluginname', 'local_welcomeemail'));

    // Heading for the settings page.
    $settings->add(new admin_setting_heading('local_welcomeemail_settings_heading',
        get_string('settings_heading', 'local_welcomeemail'),
        get_string('settings_heading_desc', 'local_welcomeemail')));

    // Subject template setting. Tokens will be replaced when the message is sent.
    $settings->add(new admin_setting_configtext('local_welcomeemail/subjecttemplate',
        get_string('subjecttemplate', 'local_welcomeemail'),
        get_string('subjecttemplate_desc', 'local_welcomeemail'),
        get_string('subjecttemplate_default', 'local_welcomeemail')));

    // Message body template. Use a textarea to allow multi‑line messages.
    $settings->add(new admin_setting_configtextarea('local_welcomeemail/messagetemplate',
        get_string('messagetemplate', 'local_welcomeemail'),
        get_string('messagetemplate_desc', 'local_welcomeemail'),
        get_string('messagetemplate_default', 'local_welcomeemail')));

    // Add the settings page into the "Local plugins" section of the admin tree.
    $ADMIN->add('localplugins', $settings);
}