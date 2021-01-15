<?php
/**
 * File containing unit tests for assign files renaming.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

use local_assignfilesrename\local\file_renamer;
use local_assignfilesrename\local\assign_file_submission_event;
use local_assignfilesrename\local\filename_composer;
use local_assignfilesrename\local\username_extractor;

/**
 * Provides the unit tests for files renaming.
 */
class files_rename_test extends advanced_testcase {

    /**
     * Convenience function to create an instance of an assignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @throws coding_exception
     * @return assign The assign class.
     */
    private function create_instance($params = []) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        return new assign($context, $cm, $params['course']);
    }

    /**
     * Test renaming of files uploaded as assign submissions.
     */
    public function test_files_renaming() {
        global $DB;

        $this->resetAfterTest(true);

        set_config('alternativefullnameformat', 'lastname firstname');
        set_config('timezone', 'Europe/Moscow');

        $course = $this->getDataGenerator()->create_course(['shortname' => 'math102']);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student',
            ['lastname' => 'Doe', 'firstname' => 'John']);

        $this->setUser($user);

        $assign = $this->create_instance([
            'course' => $course,
            'name' => 'Assign 1',
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
            'maxattempts' => 3,
            'assignsubmission_onlinetext_enabled' => true,
            'assignfeedback_comments_enabled' => true
        ]);

        $contextid = $assign->get_context()->id;

        $submission = new stdClass();
        $submission->assignment = $assign->get_instance()->id;
        $submission->userid = $user->id;
        $submission->timecreated = time();
        $submission->timemodified = time();
        $submission->onlinetext_editor = [
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE
        ];

        $notices = [];

        $assign->save_submission($submission, $notices);
        $submission = $assign->get_user_submission($user->id, false);

        // 'Upload' a file. Actually we don't, just create a file and link it with the submission.
        $file = new stdClass;
        $file->component = 'assignsubmission_file';
        $file->filearea = ASSIGNSUBMISSION_FILE_FILEAREA;
        $file->filepath = '/';
        $file->filename = 'some_file_uploaded.txt';
        $file->contextid = $contextid;
        $file->itemid = $submission->id;
        $file->timecreated = 1608660276;
        $file->timemodified = 1608660276;

        $filestorage = get_file_storage();
        $filestorage->create_file_from_string($file, 'Some content');

        $submissionevent = new assign_file_submission_event($course->shortname, $contextid,
            $submission->id, $user->id);
        $filenamecomposer = new filename_composer(new username_extractor());
        $filesrenamer = new file_renamer($submissionevent, $filestorage, $DB, $filenamecomposer);
        $filesrenamer->rename_files();

        $files = $filestorage->get_area_files($contextid, 'assignsubmission_file',
            ASSIGNSUBMISSION_FILE_FILEAREA, $submission->id, 'timemodified', false);

        $expected = [
            'math102_Doe_J_22122020_210436.txt'
        ];

        $filenames = [];
        foreach ($files as $file) {
            $filenames[] = $file->get_filename();
        }

        $this->assertEquals($expected, $filenames);
    }
}
