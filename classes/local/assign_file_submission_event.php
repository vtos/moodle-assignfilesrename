<?php
/**
 * File containing a class with assign submission event data.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use mod_assign\event\base as assign_event_base;

/**
 * A class that encapsulates the data of an assign file submission event.
 * An object of this class is basically a value object with submission event data.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
final class assign_file_submission_event {

    /**
     * @var string
     */
    private $courseshortname;

    /**
     * @var int
     */
    private $contextid;

    /**
     * @var int
     */
    private $submissionid;

    /**
     * @var int
     */
    private $userid;

    /**
     * Constructor.
     *
     * @param string $courseshortname
     * @param int $contextid
     * @param int $submissionid
     * @param int $userid
     */
    public function __construct(string $courseshortname, int $contextid, int $submissionid, int $userid) {
        $this->courseshortname = $courseshortname;
        $this->contextid = $contextid;
        $this->submissionid = $submissionid;
        $this->userid = $userid;
    }

    /**
     * A named constructor.
     * Fetches the required data form the assign event object and returns an instance of the class.
     *
     * @param assign_event_base $event
     * @throws coding_exception
     * @return assign_file_submission_event
     */
    public static function from_assign_base_event(assign_event_base $event): assign_file_submission_event {
        $assign = $event->get_assign();
        $eventdata = $event->get_data();

        $course = $assign->get_course();
        $contextid = $eventdata['contextid'];
        $submissionid = $eventdata['other']['submissionid'];
        $userid = $eventdata['userid'];

        return new self($course->shortname, $contextid, $submissionid, $userid);
    }

    /**
     * @return string
     */
    public function courseshortname(): string {
        return $this->courseshortname;
    }

    /**
     * @return int
     */
    public function contextid(): int {
        return $this->contextid;
    }

    /**
     * @return int
     */
    public function submissionid(): int {
        return $this->submissionid;
    }

    /**
     * @return int
     */
    public function userid(): int {
        return $this->userid;
    }
}
