<?php
/**
 * File containing a class to build a file name for renaming.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

namespace local_assignfilesrename\local;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use core_text;

/**
 * A class which implements the basic logic to build a file name to match the required rules.
 * Requires an implementation of 'username_extractor_interface' as a dependency.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */
final class filename_composer implements filename_composer_interface {

    /**
     * @var username_extractor_interface
     */
    private $usernameextractor;

    /**
     * A constructor to set some dependencies.
     * @param username_extractor_interface $usernameextractor
     */
    public function __construct(username_extractor_interface $usernameextractor) {
        $this->usernameextractor = $usernameextractor;
    }

    /**
     * Implementation of file name composing.
     *
     * @param array $filesnames
     * @param string $coursename
     * @param stdClass $user
     * @param int $timestamp
     * @param string $filename
     * @return string
     */
    public function compose(array $filesnames, string $coursename, stdClass $user, int $timestamp,
        string $filename): string {
        $timecreated = userdate($timestamp, "%d%m%Y_%H%M%S");
        list($timecreateddate, $timecreatedtime) = explode('_', $timecreated);

        $newnameparts = [];
        $newnameparts[] = $coursename;
        $newnameparts[] = $this->usernameextractor->username($user);
        $newnameparts[] = $timecreateddate;
        $newnameparts[] = $timecreatedtime;
        $newfilename = implode("_", $newnameparts);
        if ($fileextension = pathinfo($filename, PATHINFO_EXTENSION)) {
            $newfilename .= ".{$fileextension}";
        }

        if (in_array($newfilename, $filesnames)) {
            $newfilename = $this->increment_filename($filesnames, $newfilename);
        }

        return $newfilename;
    }

    /**
     * @param array $filesnames
     * @param string $filename
     * @return string
     */
    private function increment_filename(array $filesnames, string $filename): string {
        if ($fileextension = pathinfo($filename, PATHINFO_EXTENSION)) {
            $fileextensionpattern = "\\.{$fileextension}";
        } else {
            $fileextensionpattern = '';
        }
        $pattern = "/\\(([0-9]+)\\){$fileextensionpattern}/";

        $newfilename = $filename;

        while (in_array($newfilename, $filesnames)) {
            preg_match_all($pattern, $newfilename, $matches);
            if (isset($matches[1][0])) {
                $filescounter = $matches[1][0] + 1;
                $replace = "({$filescounter})";
                if ($fileextension) {
                    $replace .= ".{$fileextension}";
                }
                $newfilename = preg_replace($pattern, $replace, $newfilename);
            } else {
                if ($fileextension) {
                    $extensionpart = core_text::substr($newfilename, core_text::strrpos($newfilename,
                        ".{$fileextension}"));
                    $newfilename = str_replace($extensionpart, "(1){$extensionpart}", $newfilename);
                } else {
                    $newfilename .= "(1)";
                }
            }
        }

        return $newfilename;
    }
}
