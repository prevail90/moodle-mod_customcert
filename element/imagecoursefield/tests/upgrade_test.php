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
 * Unit tests for the imagecoursefield element upgrade.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_imagecoursefield;

/**
 * Unit tests for the imagecoursefield element upgrade.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_test extends \advanced_testcase {

    /**
     * Test that the upgrade function works correctly.
     */
    public function test_upgrade_function() {
        $this->resetAfterTest();

        // Test that the upgrade function exists and can be called.
        $this->assertTrue(function_exists('xmldb_customcertelement_imagecoursefield_upgrade'));
        
        // Test upgrading from version 0 (initial install).
        $result = xmldb_customcertelement_imagecoursefield_upgrade(0);
        $this->assertTrue($result);
        
        // Test upgrading from a previous version.
        $result = xmldb_customcertelement_imagecoursefield_upgrade(2024120100);
        $this->assertTrue($result);
        
        // Test upgrading from the same version (should still return true).
        $result = xmldb_customcertelement_imagecoursefield_upgrade(2024120101);
        $this->assertTrue($result);
    }
}
