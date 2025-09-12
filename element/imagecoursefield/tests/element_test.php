<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the imagecoursefield element.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_imagecoursefield;

/**
 * Unit tests for the imagecoursefield element.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element_test extends \advanced_testcase {

    /**
     * Test that the element can be instantiated.
     */
    public function test_element_instantiation() {
        $this->resetAfterTest();

        // Create a mock element data.
        $elementdata = new \stdClass();
        $elementdata->id = 1;
        $elementdata->name = 'Test Image Course Field';
        $elementdata->data = json_encode(['fieldid' => 1, 'width' => 100, 'height' => 100]);

        // Test that we can instantiate the element.
        $element = new element($elementdata);
        $this->assertInstanceOf('customcertelement_imagecoursefield\element', $element);
    }

    /**
     * Test that the element can save unique data.
     */
    public function test_save_unique_data() {
        $this->resetAfterTest();

        $elementdata = new \stdClass();
        $elementdata->id = 1;
        $elementdata->name = 'Test Image Course Field';
        $elementdata->data = '';

        $element = new element($elementdata);

        // Test data to save.
        $data = new \stdClass();
        $data->imagecoursefield = '123';
        $data->width = 200;
        $data->height = 150;

        $saveddata = $element->save_unique_data($data);
        $decodeddata = json_decode($saveddata, true);

        $this->assertEquals('123', $decodeddata['fieldid']);
        $this->assertEquals(200, $decodeddata['width']);
        $this->assertEquals(150, $decodeddata['height']);
    }

    /**
     * Test that the element validates form elements correctly.
     */
    public function test_validate_form_elements() {
        $this->resetAfterTest();

        $elementdata = new \stdClass();
        $elementdata->id = 1;
        $elementdata->name = 'Test Image Course Field';
        $elementdata->data = '';

        $element = new element($elementdata);

        // Test with valid data.
        $data = [
            'width' => 100,
            'height' => 100,
            'posx' => 10,
            'posy' => 20
        ];

        $errors = $element->validate_form_elements($data, []);
        $this->assertEmpty($errors);

        // Test with invalid width.
        $data['width'] = -10;
        $errors = $element->validate_form_elements($data, []);
        $this->assertNotEmpty($errors);
    }
}
