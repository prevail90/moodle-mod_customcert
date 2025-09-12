# Course Image Field Element

This element automatically finds and displays the first image from course custom fields in your custom certificates, similar to how the user picture element works.

## Prerequisites

This element requires the [customfield_picture plugin](https://moodle.org/plugins/customfield_picture) to be installed and configured.

## Setup Instructions

1. **Install the customfield_picture plugin**:
   - Download and install the customfield_picture plugin from the Moodle plugins directory
   - The plugin allows you to create "Picture" custom fields for courses

2. **Create course custom fields**:
   - Go to Site administration > Courses > Course custom fields
   - Create a new custom field of type "Picture"
   - Configure the field settings as needed

3. **Add images to courses**:
   - Edit individual courses
   - Upload images to the picture custom fields you created

4. **Use the Course Image Field element**:
   - When creating or editing a custom certificate template
   - Add a new element and select "Course image field"
   - Choose which picture custom field to use from the dropdown
   - Configure width and height settings
   - The element will display the image from the selected course custom field

## Features

- **Field selection**: Choose which specific picture custom field to display
- **Dynamic image loading**: Images are pulled from course custom fields at certificate generation time
- **Simple sizing**: Set custom width and height
- **Preview support**: See how the image will look in the drag-and-drop editor
- **PDF rendering**: Images are properly rendered in the final PDF certificate

## How It Works

The element:
1. Shows a dropdown of all available picture custom fields
2. Uses the selected field to retrieve the image from that specific course custom field
3. Displays that image with the specified dimensions
4. Works for both HTML preview and PDF output

## Technical Details

The element works by:
1. Retrieving all course custom fields and filtering for picture types
2. Storing the selected field ID in the element data
3. Using the field ID to get the specific course custom field data
4. Finding the associated image file in the customfield_picture file storage
5. Rendering the image with the specified dimensions
6. Supporting both HTML preview and PDF output

## File Storage

The element looks for images in the following file areas:
- Primary: `customfield_picture` component, `picture` file area
- Fallback: `core_course` component, `customfield_file` file area

This ensures compatibility with the customfield_picture plugin's file storage system.
