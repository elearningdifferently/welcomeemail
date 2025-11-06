<?php
// This file is part of Moodle - http://moodle.org/
//
// GNU GPL v3 or later.

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'category' => 'Automation',
    'name' => 'Enable welcome emails',
    'shortname' => 'welcomeemail_enabled',
    'force' => false,
], [
    'h' => 'help',
    'c' => 'category',
    'n' => 'name',
    's' => 'shortname',
    'f' => 'force',
]);

if ($options['help']) {
    $help = "Create (if absent) the course custom field used by local_welcomeemail.\n\n" .
        "Options:\n" .
        "--category   Category name to create/use (default: Automation)\n" .
        "--name       Field display name (default: Enable welcome emails)\n" .
        "--shortname  Field shortname (default: welcomeemail_enabled)\n" .
        "--force      Recreate field if it already exists (deletes existing)\n" .
        "-h --help    Show this help\n";
    echo $help;
    exit(0);
}

require_once($CFG->dirroot . '/customfield/classes/api.php');
require_once($CFG->dirroot . '/course/classes/customfield/course_handler.php');

$shortname = $options['shortname'];
$displayname = $options['name'];
$categoryname = $options['category'];
$force = $options['force'];

// Parameter validation.
if (!preg_match('/^[a-z0-9_]+$/', $shortname)) {
    cli_error('Shortname must be lowercase alphanumeric underscore.');
}

try {
    $handler = \core_course\customfield\course_handler::create();

    // Find or create category using API so context/itemid are set properly.
    $categories = \core_customfield\api::get_categories_with_fields('core_course', 'course', 0);
    $targetcategory = null;
    foreach ($categories as $cat) {
        if ($cat->get('name') === $categoryname) {
            $targetcategory = $cat;
            break;
        }
    }
    if (!$targetcategory) {
        $catrecord = (object)[
            'name' => $categoryname,
        ];
        $targetcategory = \core_customfield\category_controller::create(0, $catrecord, $handler);
        \core_customfield\api::save_category($targetcategory);
        cli_writeln("Created category '{$categoryname}' (id=".$targetcategory->get('id').")");
    }

    // Check for existing field by shortname in course component/area.
    $existing = $DB->get_record('customfield_field', [
        'shortname' => $shortname,
        'categoryid' => $targetcategory->get('id'),
    ]);
    if ($existing) {
        if ($force) {
            $fc = \core_customfield\field_controller::create($existing->id);
            \core_customfield\api::delete_field_configuration($fc);
            cli_writeln("Deleted existing field '{$shortname}'");
            $existing = null;
        } else {
            cli_writeln("Field '{$shortname}' already exists (id={$existing->id}). Nothing to do.");
            exit(0);
        }
    }

    // Create field via API.
    $frecord = (object) [
        'type' => 'checkbox',
        'shortname' => $shortname,
        'name' => $displayname,
        'categoryid' => $targetcategory->get('id'),
    ];
    $field = \core_customfield\field_controller::create(0, $frecord, $targetcategory);
    $formdata = (object) [
        'name' => $displayname,
        'shortname' => $shortname,
        'categoryid' => $targetcategory->get('id'),
        'type' => 'checkbox',
        'configdata' => [
            'defaultvalue' => '0',
            // Course-specific visibility/locked settings live in configdata for this handler.
            'visibility' => \core_course\customfield\course_handler::VISIBLETOALL,
            'locked' => 0,
        ],
    ];
    \core_customfield\api::save_field_configuration($field, $formdata);
    cli_writeln("Created field '{$shortname}' (id=".$field->get('id').") in category '{$categoryname}'");

    cli_writeln('Done.');
} catch (\Throwable $e) {
    cli_error('Failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
}
