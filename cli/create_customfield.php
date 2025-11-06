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
require_once($CFG->dirroot . '/customfield/field/checkbox/classes/field_controller.php');

$shortname = $options['shortname'];
$displayname = $options['name'];
$categoryname = $options['category'];
$force = $options['force'];

// Parameter validation.
if (!preg_match('/^[a-z0-9_]+$/', $shortname)) {
    cli_error('Shortname must be lowercase alphanumeric underscore.');
}

$admin = get_admin();
if (!$admin) {
    cli_error('Admin user not found.');
}

// Ensure capability context (system).
$syscontext = context_system::instance();

$transaction = $DB->start_delegated_transaction();

// Find or create category.
$category = $DB->get_record('customfield_category', [
    'name' => $categoryname,
    'component' => 'core_course',
    'area' => 'course',
]);
if (!$category) {
    $category = (object) [
        'name' => $categoryname,
        'component' => 'core_course',
        'area' => 'course',
        'sortorder' => 0,
        'id' => null,
        'timecreated' => time(),
        'timemodified' => time(),
    ];
    $category->id = $DB->insert_record('customfield_category', $category);
    echo "Created category '{$categoryname}' (id={$category->id})\n";
}

// Check for existing field.
$field = $DB->get_record_sql("SELECT f.* FROM {customfield_field} f
    WHERE f.component='core_course' AND f.area='course' AND f.shortname = :shortname", ['shortname' => $shortname]);
if ($field) {
    if ($force) {
        // Delete existing field + its data (cascades not automatic, remove manually).
        $DB->delete_records('customfield_data', ['fieldid' => $field->id]);
        $DB->delete_records('customfield_field', ['id' => $field->id]);
        echo "Deleted existing field '{$shortname}'\n";
        $field = null;
    } else {
        echo "Field '{$shortname}' already exists (id={$field->id}). Nothing to do.\n";
        $transaction->allow_commit();
        exit(0);
    }
}

if (!$field) {
    $field = (object) [
        'type' => 'checkbox',
        'name' => $displayname,
        'shortname' => $shortname,
        'component' => 'core_course',
        'area' => 'course',
        'categoryid' => $category->id,
        'description' => 'Enable/disable sending welcome email on enrolment.',
        'descriptionformat' => FORMAT_HTML,
        'sortorder' => 0,
        'required' => 0,
        'locked' => 0,
        'visible' => 2, // 2 = visible to all with course edit perms.
        'timecreated' => time(),
        'timemodified' => time(),
    ];
    // Checkbox stores its default in configdata JSON.
    $config = [
        'defaultvalue' => '0',
    ];
    $field->configdata = json_encode($config);
    $field->id = $DB->insert_record('customfield_field', $field);
    echo "Created field '{$shortname}' (id={$field->id}) in category '{$categoryname}'\n";
}

$transaction->allow_commit();

echo "Done.\n";
