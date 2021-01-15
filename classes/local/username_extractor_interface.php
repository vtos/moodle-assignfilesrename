<?php
/**
 * File containing a declaration of an interface to perform extracting of a user name.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * An interface to define the basic functionality of a user name extractor.
 * The object of this class can be conveniently passed as a dependency to perform its task when needed.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
interface username_extractor_interface {

    /**
     * Return the user name to include in the file name.
     *
     * @param stdClass $user A standard user record from the {user} database table.
     * @return string
     */
    public function username(stdClass $user): string;
}
