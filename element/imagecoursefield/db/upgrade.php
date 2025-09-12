<?php
// This file is part of Moodle - http://moodle.org/
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
 * Customcert imagecoursefield element upgrade code.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Customcert imagecoursefield element upgrade code.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_customcertelement_imagecoursefield_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2024120101) {
        // Initial version - no database changes needed as this is a new element.
        // The element automatically finds course image custom fields without storing field IDs.
        
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2024120101, 'customcertelement', 'imagecoursefield');
    }

    return true;
}
