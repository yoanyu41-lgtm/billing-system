# Company Settings Feature

## Overview
The Company Settings feature allows you to manage your company information from the admin panel. This information is automatically used when printing installment contracts, making it easy to keep contracts up-to-date without code changes.

## Features

### Company Information Management
- **Company Name** (English & Khmer)
- **Address** (English & Khmer)
- **Contact Information** (Phone & Email)
- **Business License Number** (Optional)
- **Company Logo** (Optional - JPG, JPEG, PNG up to 2MB)

### Automatic Contract Integration
When you print an installment contract, it automatically pulls the latest company information from the database:
- Company name appears in header and footer
- Address shown in seller section
- Phone and email displayed in contact section

## How to Use

### Accessing Company Settings

1. Login as an **Admin** user
2. Navigate to **Settings** from the sidebar
3. Click the **🏢 Company Settings** button in the top-right corner

### Updating Company Information

1. Fill in the form with your company details
2. Upload a logo (optional) - accepts JPG, JPEG, PNG up to 2MB
3. Click **💾 Save Changes**
4. Success message will appear confirming the update

### Viewing Changes in Contracts

1. Go to any installment plan
2. Click **Print Contract**
3. The contract will display your updated company information

## Technical Details

### Database Storage
Company settings are stored in the `settings` table using key-value pairs:

| Key | Description |
|-----|-------------|
| `company_name` | Company name in English |
| `company_name_km` | Company name in Khmer |
| `company_address` | Address in English |
| `company_address_km` | Address in Khmer |
| `company_phone` | Contact phone number |
| `company_email` | Contact email address |
| `company_business_license` | Business license number (optional) |
| `company_logo` | Path to uploaded logo file (optional) |

### Routes
- **View Settings:** `GET /admin/settings/company`
- **Update Settings:** `POST /admin/settings/company`

### Controller Methods
- `SettingController@companySettings` - Display company settings form
- `SettingController@updateCompanySettings` - Process form submission and update database

### Files Modified
1. `app/Http/Controllers/SettingController.php` - Added company settings methods
2. `routes/web.php` - Added company settings routes
3. `resources/views/admin/settings/company.blade.php` - Company settings form
4. `resources/views/admin/settings/index.blade.php` - Added link to company settings
5. `resources/views/installments/contract.blade.php` - Updated to use database values
6. `lang/en/app.php` & `lang/km/app.php` - Added translation keys
7. `database/seeders/CompanySettingsSeeder.php` - Default settings seeder

## Default Values
The system comes with these default values (can be customized):

- **Company Name:** CityTech Computer Shop
- **Company Name (KM):** ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច
- **Address:** Phnom Penh, Cambodia
- **Address (KM):** ភ្នំពេញ ប្រទេសកម្ពុជា
- **Phone:** 012-345-678
- **Email:** info@citytech.com

## Logo Management

### Uploading a Logo
1. Click **Upload New Logo** in the Company Settings page
2. Select an image file (JPG, JPEG, PNG)
3. Maximum file size: 2MB
4. Click **Save Changes**

### Logo Storage
- Logos are stored in `storage/app/public/company/`
- Old logos are automatically deleted when uploading a new one
- Logo path is stored in database for easy retrieval

## Benefits

✅ **No Code Changes Required** - Update company info through admin panel  
✅ **Bilingual Support** - English and Khmer for both company name and address  
✅ **Automatic Contract Updates** - All future contracts use the latest information  
✅ **Logo Support** - Add your company logo for professional branding  
✅ **Easy to Use** - Simple form interface with validation  

## Future Enhancements

Potential improvements for future versions:
- Multiple company profiles (for businesses with multiple branches)
- Logo positioning options in contracts
- Additional contact fields (fax, website, social media)
- Template customization options
- Contract header/footer customization

---

**Note:** Only Admin users can access and modify company settings.
