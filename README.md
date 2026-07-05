# FSSAI License Verification & Reporting System

A web application for verifying the authenticity of Indian food licenses (FSSAI) and reporting fraudulent ones.

## 🛡️ Features

- **Dual-Input Verification**: Manual entry or QR/Image-based scanning
- **Real-time Verification**: Instant database lookup with detailed results
- **Reporting System**: Report suspicious/fake licenses with evidence upload
- **Admin Dashboard**: Review and manage reported licenses
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## 📋 Prerequisites

- **XAMPP** (Apache + MySQL + PHP 7.4+)
  - Download from: https://www.apachefriends.org/
- Modern web browser (Chrome, Firefox, Edge, Safari)

## 🚀 Installation

### Step 1: Copy Project Files

Copy the `fssai-verification` folder to your XAMPP `htdocs` directory:

```bash
# Windows
C:\xampp\htdocs\fssai-verification

# macOS
/Applications/XAMPP/htdocs/fssai-verification

# Linux
/opt/lampp/htdocs/fssai-verification
```

### Step 2: Create Database

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `fssai_verification`
3. Select the database
4. Import the SQL file: `database/schema.sql`

Or run via command line:

```bash
# Navigate to XAMPP MySQL bin directory
cd /opt/lampp/bin  # Linux
# or
cd /Applications/XAMPP/xamppfiles/bin  # macOS

# Import schema
./mysql -u root -p fssai_verification < /path/to/fssai-verification/database/schema.sql
```

### Step 3: Configure Database Connection

Edit `config/database.php` if needed:

```php
private $host = 'localhost';
private $db_name = 'fssai_verification';
private $username = 'root';
private $password = ''; // Default XAMPP password is empty
```

### Step 4: Set Upload Permissions

Ensure the uploads directory is writable:

```bash
chmod 755 assets/uploads
chmod 644 assets/uploads/*
```

### Step 5: Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Access the application at: http://localhost/fssai-verification

## 📁 Project Structure

```
fssai-verification/
├── admin/
│   ├── dashboard.php      # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Logout handler
│   └── view-report.php    # View report details
├── api/
│   ├── submit-report.php  # Report submission endpoint
│   └── verify-license.php # License verification endpoint
├── assets/
│   ├── css/
│   │   └── style.css      # Custom styles
│   ├── js/
│   │   └── main.js        # JavaScript utilities
│   └── uploads/           # Uploaded evidence images
├── config/
│   └── database.php       # Database connection
├── database/
│   └── schema.sql         # Database schema + sample data
├── includes/
│   ├── footer.php         # Common footer
│   └── header.php         # Common header
├── index.php              # Landing page
├── report.php             # Report fake license form
└── verify.php             # Verification results page
```

## 🔐 Default Admin Credentials

```
Username: admin
Password: admin123
```

**⚠️ Change these credentials immediately after first login!**

## 📊 Database Tables

| Table | Description |
|-------|-------------|
| `valid_licenses` | Authentic FSSAI licenses for verification |
| `reported_fake_licenses` | User reports of suspicious licenses |
| `verification_logs` | Audit trail of all verification attempts |
| `admin_users` | Admin user accounts |

## 🎨 Pages Overview

| Page | URL | Description |
|------|-----|-------------|
| Home | `/` | Landing page with quick verification |
| Verify | `/verify.php` | Full verification page with results |
| Report | `/report.php` | Submit fake license report |
| Admin Login | `/admin/login.php` | Admin authentication |
| Dashboard | `/admin/dashboard.php` | Manage reports |

## 🔧 API Endpoints

### Verify License

**POST** `/api/verify-license.php`

```json
{
    "license_number": "10012021000001",
    "input_method": "Manual"
}
```

**Response (Valid):**
```json
{
    "success": true,
    "found": true,
    "status": "success",
    "result": "Valid",
    "message": "Valid FSSAI License",
    "license": { ... },
    "expiry_status": { ... }
}
```

**Response (Invalid):**
```json
{
    "success": true,
    "found": false,
    "status": "error",
    "result": "Invalid",
    "message": "License number not found in FSSAI database"
}
```

### Submit Report

**POST** `/api/submit-report.php`

```json
{
    "license_number": "12345678901234",
    "reporter_name": "John Doe",
    "reporter_email": "john@example.com",
    "location": "Main Street Market",
    "city": "Mumbai",
    "state": "Maharashtra",
    "description": "Suspicious license on product packaging",
    "evidence_image_data": "data:image/jpeg;base64,..."
}
```

## 🧪 Testing

### Sample License Numbers

The database includes 15 sample licenses for testing:

| License Number | Business Name | Status |
|----------------|---------------|--------|
| 10012021000001 | Mumbai Fresh Foods Pvt Ltd | Active |
| 10012022000002 | Pune Dairy Products | Active |
| 11012020000004 | Delhi Organic Foods | Active |
| 12012021000006 | Bangalore Tech Canteen | Active |
| 13012021000009 | Chennai Ready Meals | Active |

### Test Scenarios

1. **Valid License**: Enter `10012021000001` → Should show business details
2. **Invalid License**: Enter `12345678901234` → Should show warning + report option
3. **Report Submission**: Submit a report with image → Should appear in admin dashboard
4. **Admin Review**: Login → Change report status → Verify update

## 🛡️ Security Features

- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Prevention**: HTML escaping on all outputs
- **File Upload Validation**: MIME type checking, size limits
- **Session-based Authentication**: For admin access
- **Input Sanitization**: Server-side validation

## 🎨 Color Palette

| Color | Hex | Usage |
|-------|-----|-------|
| Primary Blue | `#0d6efd` | Buttons, headers, links |
| Dark Blue | `#0a58ca` | Gradients, hover states |
| Success Green | `#198754` | Valid licenses, success states |
| Warning | `#ffc107` | Pending reports, alerts |
| Danger | `#dc3545` | Fake licenses, errors |

## 📝 Customization

### Adding Real Licenses

Import your own license data:

```sql
INSERT INTO valid_licenses (
    license_number, business_name, owner_name,
    address, city, state, pincode,
    license_type, issue_date, expiry_date, status
) VALUES (
    '10012023000000', 'Your Business Name', 'Owner Name',
    'Street Address', 'City', 'State', '123456',
    'State', '2023-01-01', '2028-01-01', 'Active'
);
```

### Changing Admin Password

Generate a new password hash:

```php
<?php echo password_hash('your_new_password', PASSWORD_DEFAULT); ?>
```

Update the database:

```sql
UPDATE admin_users
SET password_hash = '$2y$10$...'
WHERE username = 'admin';
```

## 🐛 Troubleshooting

### Database Connection Failed

- Ensure MySQL is running in XAMPP
- Check database name in `config/database.php`
- Verify credentials match your XAMPP setup

### File Upload Not Working

- Check `assets/uploads/` directory permissions
- Verify `upload_max_filesize` in php.ini
- Check `post_max_size` in php.ini

### QR Scanner Not Starting

- Ensure HTTPS (or localhost) for camera access
- Check browser permissions for camera
- Try a different browser

### Admin Login Fails

- Clear browser cache and cookies
- Check session configuration in php.ini
- Verify password hash is correct

## 📄 License

This project is for educational purposes. FSSAI is a registered trademark of the Food Safety and Standards Authority of India.

## 🤝 Support

For issues or questions:
- Check the troubleshooting section
- Review XAMPP logs: `xampp/apache/logs/error.log`
- Enable PHP error reporting for debugging

## 📞 Official FSSAI Contact

- **Website**: https://www.fssai.gov.in
- **Helpline**: 1800-11-2100
- **Email**: info@fssai.gov.in
