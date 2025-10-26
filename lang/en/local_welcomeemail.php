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
