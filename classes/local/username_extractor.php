<?php
/**
 * File containing a class to perform extracting of a user name.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use core_text;
use stdClass;

/**
 * A class which implements the basic logic to get a user name.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
final class username_extractor implements username_extractor_interface {

    /**
     * @param stdClass $user
     * @return string
     */
    public function username(stdClass $user): string {
        $userfirstnameparts = explode(" ", $user->firstname, 2);
        $user->firstname = core_text::substr($userfirstnameparts[0], 0, 1);
        if (isset($userfirstnameparts[1])) {
            $user->firstname .= core_text::substr($userfirstnameparts[1], 0, 1);
        }
        $return = fullname($user, true);
        $return = str_replace(" ", "_", $return);
        return $return;
    }
}
