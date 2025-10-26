<?php
/**
 * Test email script for the Welcome Email plugin.
 *
 * This script sends a test welcome email to a specified email address
 * to allow administrators to verify their email configuration.
 *
 * @package     local_welcomeemail
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Require login and check admin capabilities.
require_login();
require_capability('moodle/site:config', context_system::instance());

// Get the test email address from the POST request.
$testemail = optional_param('testemail', '', PARAM_EMAIL);
$sesskey = optional_param('sesskey', '', PARAM_RAW);

// Confirm the session key to prevent CSRF attacks.
if (!confirm_sesskey($sesskey)) {
    print_error('invalidsesskey');
}

// Validate the email address.
if (empty($testemail) || !validate_email($testemail)) {
    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'local_welcomeemail']),
        get_string('testemail_invalidemail', 'local_welcomeemail'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Build sample tokens for the test email.
global $USER, $SITE;

$tokens = [
    '[[studentfirstname]]' => 'John',
    '[[studentlastname]]'  => 'Doe',
    '[[studentfullname]]'  => 'John Doe',
    '[[coursename]]'       => 'Sample Course Name',
    '[[courselink]]'       => (new moodle_url('/course/view.php', ['id' => SITEID]))->out(false),
];

// Fetch the configured subject and message templates from plugin settings.
$subjecttemplate = get_config('local_welcomeemail', 'subjecttemplate');
$messagetemplate = get_config('local_welcomeemail', 'messagetemplate');

// If templates are empty, use defaults.
if (empty(trim($subjecttemplate))) {
    $subjecttemplate = get_string('subjecttemplate_default', 'local_welcomeemail');
}
if (empty(trim($messagetemplate))) {
    $messagetemplate = get_string('messagetemplate_default', 'local_welcomeemail');
}

// Replace tokens in the templates.
$subject = strtr($subjecttemplate, $tokens);
$fullmessage = strtr($messagetemplate, $tokens);

// Create a temporary user object for the test email.
$testuser = new stdClass();
$testuser->id = -1;
$testuser->email = $testemail;
$testuser->firstname = 'Test';
$testuser->lastname = 'User';
$testuser->maildisplay = true;
$testuser->mailformat = 1;
$testuser->maildigest = 0;
$testuser->emailstop = 0;
$testuser->deleted = 0;
$testuser->suspended = 0;
$testuser->auth = 'manual';
$testuser->username = 'testuser_' . time();

// Compose the message using Moodle's messaging API.
$message = new \core\message\message();
$message->component         = 'local_welcomeemail';
$message->name              = 'course_welcome';
$message->userfrom          = core_user::get_noreply_user();
$message->userto            = $testuser;
$message->subject           = $subject;
$message->fullmessage       = $fullmessage;
$message->fullmessageformat = FORMAT_PLAIN;
$message->fullmessagehtml   = nl2br($fullmessage);
$message->smallmessage      = '';
$message->notification      = 1;
$message->contexturl        = $tokens['[[courselink]]'];
$message->contexturlname    = $tokens['[[coursename]]'];

// Send the message.
$result = message_send($message);

// Redirect back to the settings page with a success or error message.
if ($result) {
    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'local_welcomeemail']),
        get_string('testemail_success', 'local_welcomeemail', $testemail),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
} else {
    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'local_welcomeemail']),
        get_string('testemail_error', 'local_welcomeemail'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}
