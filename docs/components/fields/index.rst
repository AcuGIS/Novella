**********************
Add Fields
**********************

.. contents:: Table of Contents

Add Field
==================

Adding New Fields for Datasets

This application is designed to be extensible, allowing you to add new metadata fields as needed. Here's how to add new fields to datasets:

Step 1: Database Migration

Create a new migration file in the `database/migrations/` directory::

    -- Example: add_new_field.sql
    -- Add new field to metadata_records table
    ALTER TABLE metadata_records
    ADD COLUMN IF NOT EXISTS new_field_name VARCHAR(255);

    -- Add comment to explain the column
    COMMENT ON COLUMN metadata_records.new_field_name IS 'Description of what this field contains';


Step 2: Update the Form Template

Add the new field to the form in `templates/form.twig`::


    <div class="mb-4">
        <label for="new_field_name" class="block text-sm font-medium text-gray-700 mb-1">New Field Label</label>
        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
               id="new_field_name" name="new_field_name"
               value="">
    </div>

Step 3: Update the Controller

Modify the relevant controller (usually `src/Controllers/GisController.php`) to handle the new field::

    *   Add the field to the data array when creating/updating records
    *   Include the field in validation if required
    *   Update any display logic to show the new field

Step 4: Update Display Templates

Add the new field to display templates like `templates/dataset_detail.twig`::

    <div class="mb-3">
        <strong>New Field:</strong> 
    </div>


Step 5: Update XML Export

If the field should be included in XML exports, update the XML generation logic in the controller.

Common Field Types

**Text fields:** `VARCHAR(255)` or `TEXT`

**Date fields:** `DATE` or `TIMESTAMP`

**Numeric fields:** `INTEGER`, `DECIMAL`, or `REAL`

**Boolean fields:** `BOOLEAN`

**Array fields:** `TEXT[]` for multiple values

Best Practices

*   Always use `IF NOT EXISTS` in migrations to prevent errors
*   Add meaningful comments to database columns
*   Follow the existing naming conventions (snake\_case for database, camelCase for form fields)
*   Test the new field thoroughly before deploying
*   Consider whether the field should be required or optional
*   Update documentation when adding new fields

Example: Adding a "Data Source" Field

Here's a complete example of adding a "Data Source" field:

1.  1 **Migration:** Create `add_data_source_field.sql`
2.  2 **Form:** Add input field to the Identification Info section
3.  3 **Controller:** Include in create/update methods
4.  4 **Display:** Show in dataset detail view
5.  5 **Export:** Include in XML generation   





