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
 * This file contains the customcert element imagecoursefield's core interaction API.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_imagecoursefield;

/**
 * The customcert element imagecoursefield's core interaction API.
 *
 * @package    customcertelement_imagecoursefield
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit form instance
     */
    public function render_form_elements($mform) {
        // Get the course custom fields that are of picture type.
        $arrcustomfields = [];
        $handler = \core_course\customfield\course_handler::create();
        $customfields = $handler->get_fields();

        foreach ($customfields as $field) {
            // Only include picture type custom fields (from customfield_picture plugin).
            if ($field->get('type') === 'picture') {
                $arrcustomfields[$field->get('id')] = $field->get_formatted_name();
            }
        }

        \core_collator::asort($arrcustomfields);

        // Create the select box where the course image field is selected.
        $mform->addElement('select', 'imagecoursefield', get_string('imagecoursefield', 'customcertelement_imagecoursefield'), $arrcustomfields);
        $mform->setType('imagecoursefield', PARAM_ALPHANUM);
        $mform->addHelpButton('imagecoursefield', 'imagecoursefield', 'customcertelement_imagecoursefield');

        \mod_customcert\element_helper::render_form_element_width($mform);

        \mod_customcert\element_helper::render_form_element_height($mform);

        if (get_config('customcert', 'showposxy')) {
            \mod_customcert\element_helper::render_form_element_position($mform);
        }
    }

    /**
     * Performs validation on the element values.
     *
     * @param array $data the submitted data
     * @param array $files the submitted files
     * @return array the validation errors
     */
    public function validate_form_elements($data, $files) {
        // Array to return the errors.
        $errors = [];

        // Validate the width.
        $errors += \mod_customcert\element_helper::validate_form_element_width($data);

        // Validate the height.
        $errors += \mod_customcert\element_helper::validate_form_element_height($data);

        // Validate the position.
        if (get_config('customcert', 'showposxy')) {
            $errors += \mod_customcert\element_helper::validate_form_element_position($data);
        }

        return $errors;
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        // Array of data we will be storing in the database.
        $arrtostore = [
            'fieldid' => $data->imagecoursefield,
            'width' => (int) $data->width,
            'height' => (int) $data->height,
        ];

        return json_encode($arrtostore);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        global $CFG;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $imageinfo = json_decode($this->get_data());

        $courseid = \mod_customcert\element_helper::get_courseid($this->id);
        $course = get_course($courseid);

        // Get the image file from the selected course custom field.
        $file = $this->get_course_image_file($course, $imageinfo->fieldid, $preview);

        if ($file) {
            $location = make_request_directory() . '/target';
            $file->copy_content_to($location);
            $pdf->Image($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
        } else if ($preview) { // Can't find an image, but we are in preview mode then display default pic.
            $location = $CFG->dirroot . '/pix/u/f1.png';
            $pdf->Image($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
        }
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        global $COURSE;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return '';
        }

        $imageinfo = json_decode($this->get_data());

        // Get the image file from the selected course custom field.
        $file = $this->get_course_image_file($COURSE, $imageinfo->fieldid, true);

        if ($file) {
            // Use the correct file area for customfield_picture plugin.
            $filearea = 'picture';
            $component = 'customfield_picture';
            
            // Check if the file is from the customfield_picture plugin.
            if ($file->get_component() === 'customfield_picture') {
                $filearea = 'picture';
                $component = 'customfield_picture';
            } else {
                // Fallback to standard custom field file area.
                $filearea = 'customfield_file';
                $component = 'core_course';
            }
            
            $url = \moodle_url::make_pluginfile_url($file->get_contextid(), $component, $filearea, $file->get_itemid(),
                $file->get_filepath(), $file->get_filename());
            
            // The size of the images to use in the CSS style.
            $style = '';
            if ($imageinfo->width === 0 && $imageinfo->height === 0) {
                // Put this in so code checker doesn't complain.
                $style .= '';
            } else if ($imageinfo->width === 0) { // Then the height must be set.
                $style .= 'width: ' . $imageinfo->height . 'mm; ';
                $style .= 'height: ' . $imageinfo->height . 'mm';
            } else if ($imageinfo->height === 0) { // Then the width must be set.
                $style .= 'width: ' . $imageinfo->width . 'mm; ';
                $style .= 'height: ' . $imageinfo->width . 'mm';
            } else { // Must both be set.
                $style .= 'width: ' . $imageinfo->width . 'mm; ';
                $style .= 'height: ' . $imageinfo->height . 'mm';
            }

            return \html_writer::tag('img', '', ['src' => $url, 'style' => $style]);
        } else {
            // No image found, show a placeholder in preview mode.
            $style = '';
            if ($imageinfo->width === 0 && $imageinfo->height === 0) {
                $style .= 'width: 50px; height: 50px;';
            } else if ($imageinfo->width === 0) {
                $style .= 'width: ' . $imageinfo->height . 'mm; height: ' . $imageinfo->height . 'mm;';
            } else if ($imageinfo->height === 0) {
                $style .= 'width: ' . $imageinfo->width . 'mm; height: ' . $imageinfo->width . 'mm;';
            } else {
                $style .= 'width: ' . $imageinfo->width . 'mm; height: ' . $imageinfo->height . 'mm;';
            }
            
            return \html_writer::tag('div', get_string('noimagefield', 'customcertelement_imagecoursefield'), 
                ['style' => $style . ' border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #666;']);
        }
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $imageinfo = json_decode($this->get_data());
            if (!empty($imageinfo->fieldid)) {
                $element = $mform->getElement('imagecoursefield');
                $element->setValue($imageinfo->fieldid);
            }

            if (isset($imageinfo->width) && $mform->elementExists('width')) {
                $element = $mform->getElement('width');
                $element->setValue($imageinfo->width);
            }

            if (isset($imageinfo->height) && $mform->elementExists('height')) {
                $element = $mform->getElement('height');
                $element->setValue($imageinfo->height);
            }
        }

        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the image file from a specific course custom field.
     *
     * @param \stdClass $course the course we are rendering this for
     * @param int $fieldid the custom field ID
     * @param bool $preview Is this a preview?
     * @return \stored_file|false the stored file or false if not found
     */
    protected function get_course_image_file(\stdClass $course, int $fieldid, bool $preview) {
        // Get the course custom field data using the same logic as coursefield element.
        $handler = \core_course\customfield\course_handler::create();
        $data = $handler->get_instance_data($course->id, true);
        $fs = get_file_storage();
        $context = \context_course::instance($course->id);

        // Get the specific field data.
        if (!empty($data[$fieldid])) {
            $fielddata = $data[$fieldid];
            $value = $fielddata->export_value();
            
            // For picture custom fields from customfield_picture plugin.
            if (!empty($value)) {
                // Look for files in the custom field file area for picture fields.
                // The customfield_picture plugin stores files in 'customfield_picture' filearea.
                $files = $fs->get_area_files($context->id, 'customfield_picture', 'picture', $fieldid, 'id', false);
                
                // Get the file we want to display (like userpicture element does).
                $file = null;
                foreach ($files as $filefound) {
                    if (!$filefound->is_directory()) {
                        $file = $filefound;
                        break;
                    }
                }
                
                if ($file) {
                    return $file;
                }
                
                // Fallback: try the standard custom field file area.
                $files = $fs->get_area_files($context->id, 'core_course', 'customfield_file', $fieldid, 'id', false);
                
                foreach ($files as $filefound) {
                    if (!$filefound->is_directory()) {
                        $file = $filefound;
                        break;
                    }
                }
                
                if ($file) {
                    return $file;
                }
            }
        }

        return false;
    }
}
