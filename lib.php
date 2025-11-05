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
function local_welcomeemail_course_edit_form($formwrapper, $mform) {
    global $COURSE, $DB;

    // Skip if we don't have a valid course ID.
    if (empty($COURSE->id) || $COURSE->id <= 0) {
        return;
    }

    // Get the current setting value (default to 0/disabled).
    $courseid = $COURSE->id;
    $enabled = get_config('local_welcomeemail', 'course_' . $courseid . '_enabled');
    
    // Add a header for our settings.
    $mform->addElement('header', 'welcomeemail_header', get_string('welcomeemail_course_header', 'local_welcomeemail'));
    
    // Add checkbox to enable/disable welcome emails for this course.
    $mform->addElement('advcheckbox', 'welcomeemail_enabled', 
        get_string('welcomeemail_course_enable', 'local_welcomeemail'),
        get_string('welcomeemail_course_enable_desc', 'local_welcomeemail'));
    $mform->setType('welcomeemail_enabled', PARAM_INT);
    $mform->setDefault('welcomeemail_enabled', $enabled ? 1 : 0);
    
    $mform->addHelpButton('welcomeemail_enabled', 'welcomeemail_course_enable', 'local_welcomeemail');
}

/**
 * Process the course settings after form submission.
 *
 * This function is called after the course edit form is submitted.
 * We save the welcome email enabled/disabled setting for the course.
 *
 * @param object $data The form data
 * @param object $course The course object
 */
function local_welcomeemail_course_edit_submit($data, $course) {
    // Skip if we don't have a valid course ID.
    if (empty($course->id) || $course->id <= 0) {
        return;
    }
    
    $enabled = isset($data->welcomeemail_enabled) ? $data->welcomeemail_enabled : 0;
    set_config('course_' . $course->id . '_enabled', $enabled, 'local_welcomeemail');
}
