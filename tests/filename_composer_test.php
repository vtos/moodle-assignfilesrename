<?php
/**
 * File containing unit tests for file names composing.
 *
 * @package local_assignfilesrename
 * @author Vitaly Potenko <potenkov@gmail.com>
 */

use local_assignfilesrename\local\filename_composer;
use local_assignfilesrename\local\username_extractor;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides the unit tests for file names composing.
 */
class filename_composer_test extends advanced_testcase {

    /**
     * Data provider for the test_compose test case.
     *
     * @return array An array of test cases.
     */
    public function compose_data_provider() {
        return [
            [[], 'math01', 'Doe', 'John', 1608660276, 'somename.pdf',
                'math01_Doe_J_22122020_210436.pdf'],
            [[], 'eng101', 'Butina', 'Tanya', 1578449169,'somenamewithoutextension',
                'eng101_Butina_T_8012020_050609'],
            [[], 'math01', 'Shine', 'Nanny', 1608660276, 'Namewitha.dot.pdf',
                'math01_Shine_N_22122020_210436.pdf'],
            [[], 'math01', 'Siedel', 'Oliver', 1608660276, 'Namewithdots.pdf.andextensioninbetween.pdf',
                'math01_Siedel_O_22122020_210436.pdf'],
            [
                [
                    'math01_Doe_J_22122020_210436.pdf'
                ],
                'math01', 'Doe', 'John', 1608660276, 'somename.pdf', 'math01_Doe_J_22122020_210436(1).pdf'
            ],
            [
                [
                    'math01_Doe_J_22122020_210436',
                    'math01_Doe_J_22122020_210436(1)',
                    'math01_Doe_J_22122020_210436(2)',
                    'math01_Doe_J_22122020_210436(3)',
                    'math01_Doe_J_22122020_210436(4)',
                    'math01_Doe_J_22122020_210436(5)',
                    'math01_Doe_J_22122020_210436(6)',
                    'math01_Doe_J_22122020_210436(7)',
                    'math01_Doe_J_22122020_210436(8)',
                    'math01_Doe_J_22122020_210436(9)',
                    'math01_Doe_J_22122020_210436(10)'
                ],
                'math01', 'Doe', 'John', 1608660276, 'somenamewithoutextension', 'math01_Doe_J_22122020_210436(11)'
            ],
            [
                [
                    'math01_Shine_N_22122020_210436.pdf',
                    'math01_Shine_N_22122020_210436(4).pdf',
                    'math01_Shine_N_22122020_210436(1).pdf'
                ],
                'math01', 'Shine', 'Nanny', 1608660276, 'Namewitha.dot.pdf', 'math01_Shine_N_22122020_210436(2).pdf'
            ]
        ];
    }

    /**
     * @dataProvider compose_data_provider
     * @param array $storedfilenames
     * @param string $courseshortname
     * @param string $userlastname
     * @param string $userfirstname
     * @param int $filecreatedtimestamp
     * @param string $oldfilename
     * @param string $expected
     */
    public function test_compose(array $storedfilenames, string $courseshortname, string $userlastname, string $userfirstname,
         int $filecreatedtimestamp, string $oldfilename, string $expected) {

        $this->resetAfterTest();

        set_config('alternativefullnameformat', 'lastname firstname');
        set_config('timezone', 'Europe/Moscow');

        $user = $this->getDataGenerator()->create_user();
        $user->firstname = $userfirstname;
        $user->lastname = $userlastname;

        $filenamecomposer = new filename_composer(new username_extractor());
        $renamed = $filenamecomposer->compose($storedfilenames, $courseshortname, $user, $filecreatedtimestamp, $oldfilename);

        $this->assertEquals($expected, $renamed);
    }
}
