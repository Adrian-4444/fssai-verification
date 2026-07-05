# FSSAI License Verification & Reporting System

## Project Overview

This is a web application designed to verify the authenticity of Indian food licenses (FSSAI) and provide a platform for reporting fraudulent ones. The system allows consumers and businesses to check if a food license is genuine and report suspicious licenses with evidence.

## Key Features

1. **Dual-Input Verification**: Users can manually enter a 14-digit FSSAI license number or upload an image containing the license for automatic reading.

2. **Real-time Verification**: Instant database lookup to check license authenticity with detailed results including business information, expiry status, and validity.

3. **Reporting System**: Users can report suspicious or fake licenses with evidence upload (images) and location details.

4. **Admin Dashboard**: Administrators can review and manage reported licenses, update their status, and track verification statistics.

5. **Responsive Design**: Works on desktop, tablet, and mobile devices.

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: PostgreSQL (configured for Neon.tech cloud database)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **APIs**: RESTful JSON APIs for verification and reporting
- **Security**: Prepared statements, input sanitization, file upload validation

## Project Structure

```
fssai-verification/
├── admin/              # Admin dashboard and management
├── api/                # REST API endpoints
├── assets/             # CSS, JavaScript, images, uploads
├── config/             # Database configuration
├── database/           # Database schema and sample data
├── includes/           # Common header/footer components
├── index.php           # Landing page
├── report.php          # Report fake license form
└── verify.php          # License verification interface
```

## Database Schema

The system uses four main tables:

1. **valid_licenses**: Stores authentic FSSAI licenses for verification
2. **reported_fake_licenses**: Stores user reports of suspicious/fake licenses
3. **verification_logs**: Tracks all verification attempts for analytics
4. **admin_users**: Simple admin authentication

## Workflow

### License Verification Process:
1. User enters a 14-digit license number or uploads an image
2. System sanitizes and validates the input
3. Queries the database for matching license
4. Returns verification results:
   - Valid license with business details
   - Invalid license with reporting option
   - Expired license notification

### Reporting Process:
1. User fills out report form with license details
2. Can optionally upload evidence image
3. Form data is validated and sanitized
4. Report is stored in database for admin review
5. User receives confirmation with reference number

### Admin Process:
1. Admin logs into dashboard with credentials
2. Views statistics and pending reports
3. Reviews individual reports with evidence
4. Updates report status (Pending/Under Review/Verified Fake/Dismissed)
5. Adds notes for internal tracking

## Security Features

- SQL Injection Prevention: Prepared statements with PDO
- XSS Prevention: HTML escaping on all outputs
- File Upload Validation: MIME type checking, size limits
- Session-based Authentication: For admin access
- Input Sanitization: Server-side validation

## API Endpoints

### Verify License
- **POST** `/api/verify-license.php`
- Accepts license number and returns verification status

### Submit Report
- **POST** `/api/submit-report.php`
- Accepts report details and evidence, stores in database

## Installation Requirements

- XAMPP (Apache + MySQL + PHP 7.4+)
- Modern web browser (Chrome, Firefox, Edge, Safari)

## Sample Data

The system includes 15 sample licenses across different Indian states for testing purposes, covering various license types and business categories.

## Customization Options

- Adding real license data through SQL imports
- Changing admin passwords with PHP password hashing
- Modifying the color scheme and branding
- Extending the database schema for additional fields