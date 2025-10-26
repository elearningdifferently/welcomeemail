<?php
/**
 * Custom admin setting for sending test emails.
 *
 * @package     local_welcomeemail
 */

namespace local_welcomeemail;

/**
 * Admin setting class that displays an email input and a button to send test emails.
 */
class admin_setting_test_email extends \admin_setting {

    /**
     * Constructor.
     *
     * @param string $name Unique name for the setting.
     * @param string $visiblename Visible name for the setting.
     * @param string $description Description of the setting.
     */
    public function __construct($name, $visiblename, $description) {
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true since this setting doesn't save anything.
     *
     * @return bool Always returns true.
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns an empty string since this setting doesn't save anything.
     *
     * @return string Empty string.
     */
    public function get_defaultsetting() {
        return '';
    }

    /**
     * No data needs to be written.
     *
     * @param string $data The data to write (ignored).
     * @return string Empty string.
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Outputs the HTML for the test email input and button.
     *
     * @param mixed $data Not used.
     * @param string $query Not used.
     * @return string HTML for the setting.
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $CFG;

        $testurl = new \moodle_url('/local/welcomeemail/test_email.php');
        
        $html = \html_writer::start_div('form-inline');
        
        // Create the email input field.
        $html .= \html_writer::empty_tag('input', [
            'type' => 'email',
            'id' => 'id_s_local_welcomeemail_testemail',
            'name' => 'testemail',
            'class' => 'form-control',
            'placeholder' => get_string('testemail', 'local_welcomeemail'),
            'size' => '30',
            'style' => 'margin-right: 10px;'
        ]);

        // Create the submit button.
        $html .= \html_writer::start_tag('button', [
            'type' => 'button',
            'class' => 'btn btn-secondary',
            'id' => 'id_send_test_email'
        ]);
        $html .= get_string('testemail_button', 'local_welcomeemail');
        $html .= \html_writer::end_tag('button');
        
        $html .= \html_writer::end_div();

        // Add JavaScript to handle the button click.
        $html .= \html_writer::script("
            document.addEventListener('DOMContentLoaded', function() {
                var button = document.getElementById('id_send_test_email');
                var emailInput = document.getElementById('id_s_local_welcomeemail_testemail');
                
                if (button && emailInput) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        var email = emailInput.value.trim();
                        
                        if (email === '') {
                            alert('" . get_string('testemail_invalidemail', 'local_welcomeemail') . "');
                            return;
                        }
                        
                        // Create a form and submit it.
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '" . $testurl->out(false) . "';
                        
                        var emailField = document.createElement('input');
                        emailField.type = 'hidden';
                        emailField.name = 'testemail';
                        emailField.value = email;
                        form.appendChild(emailField);
                        
                        var sesskey = document.createElement('input');
                        sesskey.type = 'hidden';
                        sesskey.name = 'sesskey';
                        sesskey.value = '" . sesskey() . "';
                        form.appendChild(sesskey);
                        
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
            });
        ");

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }
}
