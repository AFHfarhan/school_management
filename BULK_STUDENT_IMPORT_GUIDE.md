# Bulk Student Import Guide

## Overview
This feature allows administrators to import multiple students at once using Excel (.xlsx), CSV, or ODS (.ods) files. The system will automatically structure the data into the required JSON format for the student database.

## How to Use

1. **Access the Import Section**
   - Login as an admin
   - Navigate to "Kelola Akun" page
   - Scroll down to the "Import Siswa dari Excel/CSV" section

2. **Download Template**
   - Click the "Download Template Excel" button
   - This will download an Excel file (.xlsx) with all required column headers and an example row

3. **Prepare Your Data**
   - Open the template in Excel or any spreadsheet application
   - Fill in the student data according to the column headers
   - Save as Excel (.xlsx), CSV, or ODS (.ods) format

4. **Upload the File**
   - Click "Pilih File Excel/CSV"
   - Select your prepared Excel or CSV file
   - Click "Import Siswa" button
## Supported File Formats

- **.xlsx** - Excel 2007+ format (recommended)
- **.csv** - Comma-separated values
- **.ods** - OpenDocument Spreadsheet (LibreOffice, OpenOffice)


## CSV Column Structure

### Required Column
- `name` - Student's full name (REQUIRED)

### Contact Information
- `phone` - Phone number
- `email` - Student email
- `address` - Full address
- `emergency_contact` - Emergency contact number
- `parent_name` - Parent/guardian name

### Academic Information
- `grade` - Grade level (10, 11, 12)
- `class` - Class name (e.g., "X TKJ 1")
- `student_id` - Student ID/NIS
- `major` - Major/specialization
- `entry_year` - Year of entry (e.g., 2025)
- `period` - Period/semester

### Biodata
- `nisn` - NISN number
- `nik` - National ID number
- `birth_place` - Place of birth
- `birth_date` - Date of birth (format: YYYY-MM-DD)
- `religion` - Religion
- `blood_type` - Blood type (A, B, AB, O)
- `height` - Height in cm
- `weight` - Weight in kg
- `hobbies` - Hobbies (separate with semicolon ;)
- `achievements` - Achievements (separate with semicolon ;)

### Other Information
- `age` - Age in years
- `gender` - Gender (male/female)

## Important Notes

1. **Array Fields**: For fields that contain multiple values (hobbies, achievements), use semicolon (;) as separator:
   ```
   Design;Photography;Music
   ```

2. **Date Format**: Use YYYY-MM-DD format for dates:
   ```
   2009-08-20
   ```

3. **File Format**: 
   - Excel (.xlsx) is recommended for best compatibility
   - CSV files are supported but may have encoding issues with special characters
   - ODS (.ods) is supported
   - Template is generated in .xlsx format

4. **Error Handling**: 
   - If there are errors, the system will show which rows failed
   - Successfully imported students will still be saved
   - Review error messages and fix the problematic rows

5. **Update vs Create**:
   - If a student exists with the same name AND (phone OR email), the record will be updated
   - Otherwise, a new student record will be created
   - All fields except 'name' are optional

## Data Structure in Database

The Excel/CSV columns are automatically mapped to this JSON structure in the database:

```json
{
  "age": 15,
  "gender": "female",
  "contact": {
    "phone": "082345678901",
    "email": "siti.nurhaliza@student.com",
    "address": "Jl. Sudirman No. 45, Jakarta Selatan",
    "emergency_contact": "082345678902",
    "parent_name": "Ibu Haliza"
  },
  "academic": {
    "grade": "10",
    "class": "X TKJ 1",
    "student_id": "SMK2025002",
    "major": "Teknik Komputer dan Jaringan",
    "entry_year": 2025,
    "period": "Semester Ganjil"
  },
  "biodata": {
    "nisn": "0051234568",
    "nik": "3175012345670002",
    "birth_place": "Bandung",
    "birth_date": "2009-08-20",
    "religion": "Islam",
    "blood_type": "B",
    "height": 158,
    "weight": 48,
    "hobbies": ["Design", "Photography", "Music"],
    "achievements": ["Juara 2 Design Competition 2024"],
    "absent": []
  }
}
```

## Example File Content

**Excel/CSV Format:**

| name | age | gender | phone | email | ... |
|------|-----|--------|-------|-------|-----|
| Siti Nurhaliza | 15 | female | 082345678901 | siti.nurhaliza@student.com | ... |

**CSV Raw Format:**
```
name,age,gender,phone,email,address,emergency_contact,parent_name,grade,class,student_id,major,entry_year,period,nisn,nik,birth_place,birth_date,religion,blood_type,height,weight,hobbies,achievements
Siti Nurhaliza,15,female,082345678901,siti.nurhaliza@student.com,"Jl. Sudirman No. 45, Jakarta Selatan",082345678902,Ibu Haliza,10,X TKJ 1,SMK2025002,Teknik Komputer dan Jaringan,2025,Semester Ganjil,0051234568,3175012345670002,Bandung,2009-08-20,Islam,B,158,48,Design;Photography;Music,Juara 2 Design Competition 2024
```

## Troubleshooting

### File Upload Fails
- Check file size (max 10MB)
- Ensure file is in Excel (.xlsx), CSV (.csv), or ODS (.ods) format
- Verify file is not corrupted
- Try using .xlsx format if .csv has issues

### Import Errors
- Check that 'name' column is filled for all students
- Verify date formats are YYYY-MM-DD
- Ensure numeric fields (age, height, weight, entry_year) contain only numbers
- Check that array fields use semicolon separator

### Character Display Issues
- Use .xlsx format instead of .csv for special characters
- Ensure proper UTF-8 encoding if using CSV
- Download and use the provided template for best results
