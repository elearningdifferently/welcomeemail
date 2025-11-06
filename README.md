# local_welcomeemail

Sends a personalised welcome message to learners the moment they are enrolled into a course.

## Features

- Listens for user enrolment events using the Events API. The `db/events.php` file declares an observer for `core\event\user_enrolment_created`. When this event is dispatched, the observer method `\local_welcomeemail\observer::user_enrolment_created` is invoked. Moodle's events system looks for observers defined in a plugin's `db/events.php` file; each observer specifies the fully qualified event class and a callback function to handle the event【407287759140883†L269-L314】.
- Defines a message provider in `db/messages.php`. The provider uses the `'course_welcome'` key and sets defaults so that the message is delivered via email for logged‑in and logged‑off users. Message providers are registered in `db/messages.php` and referenced in the message object via the `$message->name` property【920527302919501†L171-L199】.
- Uses the Message API to send notifications. A new `\core\message\message` object is constructed, properties such as `component`, `name`, `userfrom`, `userto`, `subject`, `fullmessage` and `contexturl` are populated, and `message_send()` is invoked to deliver the notification【920527302919501†L258-L283】.

## Installation

1. Place `local/welcomeemail` in your Moodle codebase and visit **Site administration → Notifications** to install.
2. (Optional) Customise subject/body templates in plugin settings or language strings.
3. Create the Course custom field below.
4. Enrol a test user to verify delivery.

### Create the course custom field

UI method:
1. **Site administration → Courses → Custom fields**.
2. Add/select a category (e.g. "Automation").
3. Add *Checkbox* field:
	- Name: Enable welcome emails
	- Shortname: `welcomeemail_enabled`
	- Default: Unchecked (or checked for global enable)
	- Description: Optional explanatory text.
4. Save.

CLI method (alternative):

```bash
php local/welcomeemail/cli/create_customfield.php
```

Use `--help` for options (e.g. `--force` to recreate):

```bash
php local/welcomeemail/cli/create_customfield.php --force
```

## Customisation

- Language strings: Edit `lang/en/local_welcomeemail.php`.
- Tokens (subject/body): `[[studentfirstname]]`, `[[studentlastname]]`, `[[studentfullname]]`, `[[coursename]]`, `[[courselink]]`.
- Notification preferences: Users can adjust channels under **Preferences → Notification preferences**.
- Per‑course control: Toggle custom field `welcomeemail_enabled` in course settings.

## Uninstallation

Remove `local/welcomeemail` then visit **Site administration → Notifications**. Optionally delete the `welcomeemail_enabled` custom field.

## Troubleshooting

| Symptom | Cause | Fix |
|---------|-------|-----|
| No email sent | Custom field missing/unchecked | Create/enable `welcomeemail_enabled` |
| Empty template | Settings not configured | Set plugin templates or rely on language fallbacks |
| Duplicate emails | Multiple enrol events | Review enrol plugins firing events |

For deeper debugging enable developer mode and inspect mail logs.

