<?php
/**
 * File containing a class with the events handlers.
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename;

defined('MOODLE_INTERNAL') || die();

use coding_exception, dml_exception, file_exception;
use local_assignfilesrename\local\assign_file_submission_event;
use local_assignfilesrename\local\file_renamer;
use local_assignfilesrename\local\filename_composer;
use local_assignfilesrename\local\native_transliterator;
use local_assignfilesrename\local\username_extractor;

/**
 * A class to handle assignment submissions events.
 * The events handled:
 * - assignsubmission_file\event\submission_created;
 * - assignsubmission_file\event\submission_updated.
 * See https://docs.moodle.org/dev/Events_API.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
class assignsubmission_file_observer {

    /**
     * @param \assignsubmission_file\event\submission_created $event
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     */
    public static function submission_created(\assignsubmission_file\event\submission_created $event) {
        if (!get_config('local_assignfilesrename', 'enabled')) {
            return;
        }

        global $DB;
        $submissionevent = assign_file_submission_event::from_assign_base_event($event);
        $filestorage = get_file_storage();
        $filenamecomposer = new filename_composer(new username_extractor());

        $filesrenamer = new file_renamer($submissionevent, $filestorage, $DB, $filenamecomposer);
        $filesrenamer->rename_files();
    }

    /**
     * @param \assignsubmission_file\event\submission_updated $event
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     */
    public static function submission_updated(\assignsubmission_file\event\submission_updated $event) {
        if (!get_config('local_assignfilesrename', 'enabled')) {
            return;
        }

        global $DB;
        $submissionevent = assign_file_submission_event::from_assign_base_event($event);
        $filestorage = get_file_storage();
        $filenamecomposer = new filename_composer(new username_extractor());

        $filesrenamer = new file_renamer($submissionevent, $filestorage, $DB, $filenamecomposer);
        $filesrenamer->rename_files();
    }
}
