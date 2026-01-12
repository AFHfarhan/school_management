# Mandatory Components Implementation

## Overview
Added a new section for managing mandatory school components with file upload capability and automatic data retrieval for editing.

## Features Implemented

### 1. Add Mandatory Component Section
**Location**: Above "Add Other Component" section

**Fields**:
- **Component Name**: Dropdown with predefined options:
  - School Name
  - School Logo
  - Class
  - Surat Peringatan 1
  - Surat Peringatan 2
  - Surat Pemanggilan Orang Tua
  - Tahun Ajaran

- **Category**: Auto-filled with `'mandatory'` (hidden field)
- **Data**: JSON textarea (supports JSON, arrays, key-value pairs, comma-separated lists)
- **Upload File**: Conditional field (shown for Surat Peringatan 1, 2, and Pemanggilan Orang Tua)

**File Upload**:
- Accepted formats: PDF, DOC, DOCX, JPG, PNG
- Max size: 5MB
- Saved to: `public/global_assets/uploads/`
- Link stored in: `component_table.data.uploads`

**Auto-fill Feature**:
- When selecting a component name that already exists in database
- Automatically fetches and populates data field
- AJAX request to `/v1/components/mandatory/{name}`
- Excludes upload path from display (shown separately)

**Save Logic**:
- Uses `updateOrCreate` based on `name` + `category='mandatory'`
- Allows updating existing mandatory components
- File upload handled separately and merged into data JSON

### 2. Mandatory Components DataTable
**Display**:
- Shows only components with `category='mandatory'`
- Columns: #, Name, Data, File, Actions
- Separate "File" column with download link
- Data excludes uploads path for cleaner display

**Actions**:
- **Edit**: Populates the form above with existing data
- **Delete**: Removes the mandatory component
- **View File**: Opens uploaded file in new tab

### 3. Other Components DataTable
**Updated Filter**:
- Shows only components where `category != 'mandatory'` OR `category IS NULL`
- Original functionality preserved
- Clearly labeled as "Other Components List"

## Database Structure

**Table**: `component_table`

**Mandatory Component Record Example**:
```json
{
  "id": 1,
  "name": "Surat Peringatan 1",
  "category": "mandatory",
  "data": {
    "title": "Surat Peringatan Pertama",
    "description": "Warning letter for first offense",
    "uploads": "global_assets/uploads/1734598765_surat_peringatan_1.pdf"
  },
  "created_at": "2025-12-19 10:00:00",
  "updated_at": "2025-12-19 10:00:00"
}
```

## Controller Updates

### ComponentController.php

#### New Methods:

**1. `getMandatoryComponent($name)`**
- Route: `GET /v1/components/mandatory/{name}`
- Purpose: AJAX endpoint for auto-fill
- Returns: JSON with component data if exists

**2. Updated `index()`**
- Separates mandatory and other components
- Passes both to view: `$mandatoryComponents` and `$components`

**3. Updated `store()`**
- Handles file upload
- Saves file to `public/global_assets/uploads/`
- Stores file path in `data.uploads`
- Uses `updateOrCreate` for mandatory components
- Returns different success message for mandatory

## File Upload Flow

1. User selects mandatory component with upload requirement
2. JavaScript shows upload field
3. User uploads file (validated server-side)
4. File saved with timestamp prefix: `{timestamp}_{originalname}`
5. Path stored in data JSON: `data.uploads = "global_assets/uploads/..."`
6. Display separates data from upload path
7. Download link generated using `asset()` helper

## JavaScript Functions

### Form Handling:
```javascript
// Show/hide upload field based on selection
$('#mandatory_name').on('change', function() {
    var selectedName = $(this).val();
    var uploadRequiredNames = ['Surat Peringatan 1', 'Surat Peringatan 2', 'Surat Pemanggilan Orang Tua'];
    
    if (uploadRequiredNames.includes(selectedName)) {
        $('#mandatory_upload_group').show();
    } else {
        $('#mandatory_upload_group').hide();
    }
    
    fetchExistingMandatoryComponent(selectedName);
});
```

### Auto-fill AJAX:
```javascript
function fetchExistingMandatoryComponent(name) {
    $.ajax({
        url: '/v1/components/mandatory/' + encodeURIComponent(name),
        method: 'GET',
        success: function(response) {
            if (response.success && response.component) {
                // Populate form with existing data
                // Remove uploads key for display
            }
        }
    });
}
```

### Edit Button:
```javascript
$(document).on('click', '.btn-edit-mandatory', function(){
    var id = $(this).data('id');
    var name = $(this).data('name');
    var rawData = $(this).attr('data-data');
    
    $('#mandatory_name').val(name).trigger('change');
    // Parse and display data
    // Scroll to form
});
```

## Routes Added

```php
Route::get('components/mandatory/{name}', [ComponentController::class, 'getMandatoryComponent'])
    ->name('component.mandatory.get');
```

## View Structure

### Sections Order:
1. **Add Mandatory Component Form** (Green header)
2. **Mandatory Components DataTable** (Green header)
3. **Add Other Component Form** (Blue header)
4. **Other Components List DataTable** (Blue header)

### Color Coding:
- Mandatory sections: `bg-success` (green)
- Other sections: `bg-primary` (blue)

## File Validation

**Server-side** (Laravel):
```php
'upload_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
```

**Client-side** (HTML):
```html
<input type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
```

## Usage Examples

### Adding School Name:
1. Select "School Name" from dropdown
2. Enter data: `{"nama":"SMK Negeri 1"}`
3. Submit
4. Stored with category='mandatory'

### Adding Surat Peringatan 1:
1. Select "Surat Peringatan 1"
2. Upload field appears
3. Enter data: `{"title":"Surat Peringatan Pertama"}`
4. Upload PDF file
5. Submit
6. File saved to uploads folder
7. Path stored in data.uploads

### Editing Existing:
1. Click "Edit" on mandatory component
2. Form auto-fills with current data
3. Modify data or upload new file
4. Submit updates using updateOrCreate

## Security Considerations

1. **File Upload**:
   - Size limit: 5MB
   - Type restriction: PDF, DOC, DOCX, JPG, PNG
   - Filename sanitization: timestamp prefix prevents conflicts
   - Stored outside webroot (in public folder with direct access)

2. **Data Validation**:
   - JSON validation on server-side
   - XSS prevention via Laravel escaping
   - CSRF protection on forms

3. **Access Control**:
   - Only authenticated users with teacher guard
   - Super admin role recommended for access

## Testing Checklist

- [x] Create uploads directory
- [ ] Add mandatory component without file
- [ ] Add mandatory component with file upload
- [ ] Edit existing mandatory component
- [ ] Delete mandatory component
- [ ] Auto-fill works when selecting existing component
- [ ] File upload field shows/hides correctly
- [ ] Files are downloadable from datatable
- [ ] Other components still work correctly
- [ ] DataTables initialize properly
- [ ] Mobile responsive layout

## Future Enhancements

1. File version history
2. File preview in modal
3. Bulk upload for multiple components
4. File size compression
5. Image thumbnail generation
6. Template system for letters
7. Digital signature integration
8. Export/import mandatory components
9. Audit trail for changes
10. File encryption for sensitive documents

## Files Modified

1. `resources/views/teacher/superadmin/managecomponent.blade.php`
   - Added mandatory component form
   - Added mandatory components datatable
   - Updated JavaScript for AJAX and file upload handling
   
2. `app/Http/Controllers/Teacher/SuperAdmin/ComponentController.php`
   - Updated `index()` to separate components
   - Updated `store()` for file handling
   - Added `getMandatoryComponent()` for AJAX

3. `routes/web.php`
   - Added route for mandatory component AJAX endpoint

4. `public/global_assets/uploads/`
   - Created directory for file storage

## Troubleshooting

**Issue**: Upload field doesn't show
- **Solution**: Check JavaScript console, ensure jQuery is loaded

**Issue**: File not uploading
- **Solution**: Check form has `enctype="multipart/form-data"`

**Issue**: Auto-fill not working
- **Solution**: Check AJAX route is registered, check network tab

**Issue**: File download 404
- **Solution**: Verify file exists in uploads folder, check asset() path

**Issue**: DataTable reinitialization error
- **Solution**: Ensure unique table IDs, destroy before reinit

---

**Implementation Date**: December 19, 2025
**Status**: ✅ Complete and Ready for Testing
