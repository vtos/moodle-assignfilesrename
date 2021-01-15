<?php
/**
 * File with the definition of events handlers of the plugin.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => 'assignsubmission_file\event\submission_created',
        'callback' => '\local_assignfilesrename\assignsubmission_file_observer::submission_created'
    ],
    [
        'eventname' => 'assignsubmission_file\event\submission_updated',
        'callback' => '\local_assignfilesrename\assignsubmission_file_observer::submission_updated'
    ]
];
