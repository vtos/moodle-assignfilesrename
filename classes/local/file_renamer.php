<?php
/**
 * File containing a class to perform files renaming.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use coding_exception, dml_exception, file_exception;
use file_storage, moodle_database, core_text;

/**
 * A class that incorporates the files renaming functionality.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
final class file_renamer {

    /**
     * @var assign_file_submission_event
     */
    private $event;

    /**
     * @var file_storage
     */
    private $filestorage;

    /**
     * @var moodle_database
     */
    private $database;

    /**
     * @var filename_composer_interface
     */
    private $filenamecomposer;

    /**
     * Constructor to set the dependencies.
     *
     * @param assign_file_submission_event $event
     * @param file_storage $fs
     * @param moodle_database $db
     * @param filename_composer_interface $filenamecomposer
     */
    public function __construct(assign_file_submission_event $event, file_storage $fs, moodle_database $db,
            filename_composer_interface $filenamecomposer) {
        $this->event = $event;
        $this->filestorage = $fs;
        $this->database = $db;
        $this->filenamecomposer = $filenamecomposer;
    }

    /**
     * Renames files in the files storage according to the required rules.
     *
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     */
    public function rename_files(): void {
        $contextid = $this->event->contextid();
        $submissionid = $this->event->submissionid();
        $userid = $this->event->userid();
        $courseshortname = $this->event->courseshortname();

        $files = $this->filestorage->get_area_files($contextid, 'assignsubmission_file',
            ASSIGNSUBMISSION_FILE_FILEAREA, $submissionid, 'timemodified', false);
        if (!$files) {
            return;
        }

        $user = $this->database->get_record('user', ['id' => $userid]);

        $filesnames = [];
        foreach ($files as $file) {
            $filesnames[] = $file->get_filename();
        }
        foreach ($files as $file) {
            $oldfilename = $file->get_filename();
            $newfilename = $this->filenamecomposer->compose($filesnames, $courseshortname, $user,
                $file->get_timecreated(), $oldfilename);
            if ($this->file_requires_renaming($file->get_filename(), $newfilename)) {
                $file->rename($file->get_filepath(), $newfilename);
                $oldfilenamekey = array_search($oldfilename, $filesnames);
                unset($filesnames[$oldfilenamekey]);
                $filesnames[] = $newfilename;
            }
        }
    }

    /**
     * @param string $oldfilename
     * @param string $newfilename
     * @return bool
     */
    private function file_requires_renaming(string $oldfilename, string $newfilename): bool {
        if ($oldfilename == $newfilename) {
            return false;
        }
        $filenamewithoutext = pathinfo($newfilename, PATHINFO_FILENAME);
        $filenamepattern = "/^{$filenamewithoutext}\\(([0-9]+)\\)/";
        return (preg_match($filenamepattern, $oldfilename, $matches) == 1) ? false : true;
    }
}
