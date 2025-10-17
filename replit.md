# NextTap Builder

## Overview

NextTap Builder is a comprehensive web platform that combines three core functionalities:
1. **Digital NFC Profile Builder** - Create NFC-enabled digital business profiles with customizable themes
2. **Custom NFC Card Ordering** - Order physical NFC cards using existing profiles or custom designs
3. **Website Builder** - Build websites using 7 pre-designed responsive templates

The platform is designed for shared hosting environments (PHP + MySQL only), with support for user authentication via OTP, admin workflows for order approval, and analytics tracking.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Technology Stack
- **Backend**: Pure PHP 8.2 with no external build tools or Node.js dependencies
- **Database**: MySQL 5.7+ (with SQLite fallback option for quick testing)
- **Frontend**: HTML5, CSS3, Bootstrap 5, vanilla JavaScript with AJAX
- **Email**: PHPMailer 7.0 (SMTP-based)
- **Deployment**: Designed for shared hosting environments (MilesWeb, etc.)

### Application Structure

**Modular PHP Architecture**:
- `config.php` - Global configuration (database, email, file upload limits)
- `includes/` - Shared components (header, footer, db connection, mailer utilities)
- `pages/` - Main application views (dashboard, profiles, orders, websites)
- `templates/` - 7 pre-built website templates with dynamic PHP placeholders
- `api/` - AJAX endpoints for async operations (login, register, OTP verification, order submission)
- `admin/` - Admin dashboard for user management and order approvals
- `assets/` - Static files (CSS with theme switching, vanilla JS utilities)
- `uploads/` - User-generated content (profile images, custom designs, business proofs, website assets)

### Database Schema

**Core Tables**:
- `users` - User accounts with email verification status and OTP codes
- `profiles` - Digital NFC profiles with themes, social links, and public URLs
- `nfc_orders` - Order tracking with two types (profile-based or custom design)
- `otp_verifications` - Email-based verification codes
- `websites` - Website builder projects with template selection and content JSON
- `analytics` - Profile view/tap tracking

### Authentication & Security

**Multi-layer Security**:
- Session-based authentication with PHP `password_hash()`
- Email OTP verification required before order placement
- One-time admin setup script (`create-admin.php`) with token protection and self-destruction
- File upload security: MIME type validation, size limits (5MB), random filenames, extension whitelist
- Directory permissions: 755 (never 777) for upload directories

**Admin Setup Flow**:
- Token-protected setup page prevents unauthorized access
- Self-destructs after first admin creation
- Hard-fails if unable to delete itself (security-first approach)

### Email System

**PHPMailer SMTP Integration**:
- Company email verification via OTP
- Configurable SMTP settings (Gmail App Passwords supported)
- Used for user verification and order notifications

### File Upload Architecture

**Secure Upload Handling**:
- Multiple upload contexts: profile images, custom NFC designs, business proofs, website assets
- MIME type validation (not just extension checking)
- Random filename generation prevents path traversal
- Whitelist: jpg, jpeg, png, pdf only
- 5MB default limit (configurable)

### Public-Facing Features

**URL Structure**:
- Profile pages: `nexttap.in/profile/{username}`
- Websites: `nexttap.in/sites/{username}`
- Public/private profile toggles
- QR code generation for profiles

### Theme System

**Dark/Light Mode**:
- Client-side theme switching with localStorage persistence
- CSS custom properties for dynamic theming
- Applied across entire platform including profile cards

### Admin Workflows

**Order Management**:
- Approval/rejection system for NFC card orders
- Business proof verification
- Order status tracking
- System-wide analytics dashboard

## External Dependencies

### Third-Party Libraries
- **PHPMailer 7.0** - SMTP email sending (installed via Composer)
  - Used for OTP verification emails
  - Supports Gmail App Passwords and custom SMTP configurations

### Database Options
- **Primary**: MySQL 5.7+ (production recommended)
- **Alternative**: SQLite (quick testing/demo mode via config switch)

### SMTP Services
- Configurable SMTP provider (Gmail, custom servers)
- Requires SMTP credentials in `config.php`
- Gmail setup requires App Password (not regular password)

### Frontend Dependencies
- **Bootstrap 5** - Responsive UI framework (CDN-based)
- **Font Awesome** - Icons (implied from theme toggle code)
- No build process or npm required - all vanilla JS and CSS

### File System Requirements
- Writable `uploads/` directory with subdirectories for different upload types
- Proper permissions (755) for security
- Directory structure created by setup scripts

### Server Requirements
- PHP 8.2+
- MySQL 5.7+ or SQLite3
- PHP extensions: ctype, filter, hash (for PHPMailer)
- SMTP access for email sending
- Session support enabled