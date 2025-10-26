# local_welcomeemail

This plugin sends a welcome notification to users when they are enrolled into a course. It listens for the `core\event\user_enrolment_created` event and uses the Moodle Messaging API to send the notification.

## Features

- Listens for user enrolment events using the Events API. The `db/events.php` file declares an observer for `core\event\user_enrolment_created`. When this event is dispatched, the observer method `\local_welcomeemail\observer::user_enrolment_created` is invoked. Moodle's events system looks for observers defined in a plugin's `db/events.php` file; each observer specifies the fully qualified event class and a callback function to handle the event【407287759140883†L269-L314】.
- Defines a message provider in `db/messages.php`. The provider uses the `'course_welcome'` key and sets defaults so that the message is delivered via email for logged‑in and logged‑off users. Message providers are registered in `db/messages.php` and referenced in the message object via the `$message->name` property【920527302919501†L171-L199】.
- Uses the Message API to send notifications. A new `\core\message\message` object is constructed, properties such as `component`, `name`, `userfrom`, `userto`, `subject`, `fullmessage` and `contexturl` are populated, and `message_send()` is invoked to deliver the notification【920527302919501†L258-L283】.

## Installation

1. Copy the `welcomeemail` directory to the `local` directory of your Moodle installation, resulting in `moodle/local/welcomeemail`.
2. Log in to Moodle as an administrator and navigate to **Site administration → Notifications** to trigger the plugin installation.
3. Review and confirm the installation steps. The plugin does not add database tables but registers a new message provider and event observer.
4. After installation, when a user is enrolled into any course via the standard enrolment methods, the plugin will automatically send them a welcome email. The subject and body text can be customised in the language pack (`lang/en/local_welcomeemail.php`).

## Customisation

- **Language strings**: To modify the welcome subject or body, edit the appropriate strings in `lang/en/local_welcomeemail.php`. The body uses placeholders `{$a->firstname}`, `{$a->coursename}` and `{$a->courselink}` to personalise the message.
- **Message preferences**: Users can opt‑in or opt‑out of receiving welcome emails in their messaging preferences. The provider definition includes default settings that permit email delivery, but users can change these settings via **Preferences → Notification preferences**.

## Uninstallation

To uninstall the plugin, remove the `local/welcomeemail` directory from your Moodle installation and visit **Site administration → Notifications** to complete the cleanup.

