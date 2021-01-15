<?php
/**
 * Settings for the plugin.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage( 'local_assignfilesrename',
        get_string('pluginname', 'local_assignfilesrename'));

    $enabledsetting = new admin_setting_configcheckbox('local_assignfilesrename/enabled',
        get_string('settingsenabled', 'local_assignfilesrename'),
        get_string('settingsenableddesc', 'local_assignfilesrename'),0);
    $settings->add($enabledsetting);

    $ADMIN->add('localplugins', $settings);
}
