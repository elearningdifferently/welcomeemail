<?php
// English strings for the welcome email plugin.

$string['pluginname'] = 'Course welcome email';
$string['messageprovider:course_welcome'] = 'Welcome message when a user is enrolled in a course';
$string['welcomeemailsubject'] = 'Welcome to {$a}';
$string['welcomeemailtext'] = 'Hello {$a->firstname},\n\nWelcome to "{$a->coursename}". We are delighted to have you join this course.\n\nYou can access the course using the link below:\n{$a->courselink}\n\nBest regards,\nThe Course Team';

// Settings strings.
$string['settings_heading'] = 'Welcome email settings';
$string['settings_heading_desc'] = 'Configure the subject and message body for the welcome email sent to new students. Use the available tokens to personalise the content.';
$string['subjecttemplate'] = 'Subject template';
$string['subjecttemplate_desc'] = 'Define the subject line for the welcome email. The following tokens can be used to insert dynamic values: [[studentfirstname]], [[studentlastname]], [[studentfullname]], [[coursename]].';
$string['subjecttemplate_default'] = 'Welcome to [[coursename]]';
$string['messagetemplate'] = 'Message body template';
$string['messagetemplate_desc'] = 'Define the body of the welcome email. You can use the same tokens as in the subject template plus [[courselink]] to include a link to the course.';
$string['messagetemplate_default'] = "Hello [[studentfirstname]],\n\nWelcome to \"[[coursename]]\". We are delighted to have you join this course.\n\nYou can access the course using the link below:\n[[courselink]]\n\nBest regards,\nThe Course Team";

// Test email strings.
$string['testemail_heading'] = 'Test email';
$string['testemail_heading_desc'] = 'Send a test welcome email to verify your configuration.';
$string['testemail'] = 'Test email address';
$string['testemail_desc'] = 'Enter an email address where the test message should be sent.';
$string['testemail_button'] = 'Send test email';
$string['testemail_success'] = 'Test email has been sent to {$a}';
$string['testemail_error'] = 'Failed to send test email. Please check your email configuration.';
$string['testemail_invalidemail'] = 'Please enter a valid email address.';

// Course-specific settings strings.
$string['welcomeemail_course_header'] = 'Welcome email';
$string['welcomeemail_course_enable'] = 'Enable welcome emails for this course';
$string['welcomeemail_course_enable_desc'] = 'Send a welcome email when students are enrolled in this course';
$string['welcomeemail_course_enable_help'] = 'When enabled, students will receive a welcome email when they are enrolled in this course. You can configure the email template in the plugin settings.';

