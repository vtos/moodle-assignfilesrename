<?php
/**
 * File containing an interface to build a file name for renaming.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * An interface to define the basic functionality of a file name builder.
 * The object of this class can be conveniently passed as a dependency to perform its task when needed.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
interface filename_composer_interface {

    /**
     * @param array $filesnames
     * @param string $coursename
     * @param stdClass $user
     * @param int $timestamp
     * @param string $filename
     * @return string
     */
    public function compose(array $filesnames, string $coursename, stdClass $user, int $timestamp,
        string $filename): string;
}
