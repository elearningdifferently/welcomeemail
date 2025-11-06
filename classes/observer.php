<?php
namespace local_welcomeemail;

use core\event\user_enrolment_created;
use core_user;
use core\message\message;
use moodle_url;
use context_course;
use stdClass;

/**
 * Observer class for handling events.
 *
 * When a user is enrolled into a course this observer builds and sends a
 * welcome message using Moodle's messaging API. The message provider is
 * defined in db/messages.php. This implementation uses the example
 * pattern from the official Message API documentation where a
 * \core\message\message object is constructed and passed to
 * message_send()【920527302919501†L258-L283】.
 *
 * @package     local_welcomeemail
 */
class observer {
    /**
     * Handler for the user enrolment created event.
     *
     * @param user_enrolment_created $event The event object representing the enrolment.
     */
    public static function user_enrolment_created(user_enrolment_created $event): void {
        global $DB;

        // Retrieve the enrolled user.
        $userid = $event->relateduserid;
        if (!$userid) {
            return;
        }
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', IGNORE_MISSING);
        if (!$user) {
            return;
        }

        // Retrieve the course information.
        $courseid = $event->courseid;
        $course = get_course($courseid);

        // Determine if welcome emails are enabled via custom course field 'welcomeemail_enabled'.
        static $cache = [];
        if (!array_key_exists($courseid, $cache)) {
            $value = 0;
            try {
                // Use the course customfield handler API for safer access.
                $handler = \core_course\customfield\course_handler::create();
                // Parent context might be needed for handler (category), but safe to omit for read-only.
                $dataobjects = $handler->get_instance_data($courseid); // Returns array of data objects.
                foreach ($dataobjects as $data) {
                    $field = $data->get_field();
                    if ($field && $field->get('shortname') === 'welcomeemail_enabled') {
                        $raw = $data->get_value();
                        // Checkbox custom field stores '1' for checked.
                        $value = ($raw === '1' || $raw === 1) ? 1 : 0;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                // Fail closed (disabled) if any error occurs.
                $value = 0;
            }
            $cache[$courseid] = $value;
        }
        if (!$cache[$courseid]) {
            return; // Not enabled for this course.
        }

        // Build the link to the course.
        $courselink = (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false);

        // Build token replacements for subject and message templates.
        $tokens = [
            '[[studentfirstname]]' => $user->firstname,
            '[[studentlastname]]'  => $user->lastname,
            '[[studentfullname]]'  => fullname($user),
            '[[coursename]]'       => format_string($course->fullname),
            '[[courselink]]'       => $courselink,
        ];

        // Fetch the configured subject and message templates from plugin settings.
        $subjecttemplate = get_config('local_welcomeemail', 'subjecttemplate');
        $messagetemplate = get_config('local_welcomeemail', 'messagetemplate');

        // If templates are empty, fall back to language strings defined in lang/en/local_welcomeemail.php.
        if (empty(trim($subjecttemplate))) {
            // Using get_string with a string parameter rather than stdClass because the language string accepts a single placeholder.
            $subjecttemplate = get_string('welcomeemailsubject', 'local_welcomeemail', $tokens['[[coursename]]']);
        }
        if (empty(trim($messagetemplate))) {
            $stddata = new stdClass();
            // The default lang string expects firstname, coursename and courselink.
            $stddata->firstname  = $tokens['[[studentfirstname]]'];
            $stddata->coursename = $tokens['[[coursename]]'];
            $stddata->courselink = $tokens['[[courselink]]'];
            $messagetemplate = get_string('welcomeemailtext', 'local_welcomeemail', $stddata);
        }

        // Replace tokens in the templates.
        $subject = strtr($subjecttemplate, $tokens);
        $fullmessage = strtr($messagetemplate, $tokens);

        // Compose the message using Moodle's messaging API.
        $message = new message();
        $message->component         = 'local_welcomeemail';
        $message->name              = 'course_welcome';
        $message->userfrom          = core_user::get_noreply_user();
        $message->userto            = $user;
        $message->subject           = $subject;
        $message->fullmessage       = $fullmessage;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml   = nl2br($fullmessage);
        $message->smallmessage      = '';
        $message->notification      = 1; // This is a system-generated notification.
        $message->contexturl        = $courselink;
        $message->contexturlname    = $tokens['[[coursename]]'];

        // Send the message.
        message_send($message);
    }
}
