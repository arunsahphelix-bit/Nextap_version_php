# Security Guide - NextTap Builder

## Critical Security Measures

### 1. Admin Account Creation

**IMMEDIATE ACTION REQUIRED AFTER DEPLOYMENT:**

The `create-admin.php` file is a one-time setup tool that MUST be handled securely:

- ✅ Only accessible when NO admin user exists
- ✅ Self-destructs after creating first admin
- ⚠️ **Delete manually if auto-deletion fails**
- ❌ **NEVER leave on production server**

**Setup Process:**
```bash
# 1. Deploy application
# 2. Extract token from create-admin.php (line 4)
cat create-admin.php | grep SETUP_TOKEN
# 3. Visit /create-admin.php?token=YOUR_TOKEN_HERE
# 4. Create admin with strong password
# 5. File self-destructs (hard-fails if unable)
# 6. Verify file is deleted
ls -la | grep create-admin.php  # Should return nothing
```

**Token Protection:**
- The setup token is randomly generated in create-admin.php
- Without the token, page returns 404 (Not Found)
- Prevents unauthorized access even in race conditions
- Token is visible only to those with file system access

### 2. File Upload Security

All file uploads are protected with:

- **MIME Type Validation**: Verifies actual file content, not just extension
- **File Size Limits**: 5MB maximum (configurable in config.php)
- **Random Filenames**: Prevents path traversal and filename guessing
- **Extension Whitelist**: Only allowed: jpg, jpeg, png, pdf

**Directory Permissions:**
```bash
# Secure permissions (NOT 777)
chmod 755 uploads uploads/designs uploads/proofs uploads/profile_images uploads/website_assets
```

**Why not 777?**
- 777 = World-writable = Security risk
- 755 = Owner write, others read = Secure for web apps

### 3. IDOR (Insecure Direct Object Reference) Protection

All API endpoints verify resource ownership:

**Example from api/create-order.php:**
```php
// Verify user owns the profile before creating order
$stmt = $db->prepare("SELECT id FROM profiles WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $selected_profile_id, $user_id);
```

**Protected Endpoints:**
- Profile editing (only owner can edit)
- Order creation (only with owned profiles)
- Website management (only owner can publish/edit)
- Analytics (only owner can view)

### 4. SQL Injection Prevention

**All database queries use prepared statements:**
```php
// ✅ SECURE
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// ❌ NEVER DO THIS
$query = "SELECT * FROM users WHERE email = '$email'";
```

### 5. Password Security

- **Hashing**: bcrypt (PASSWORD_DEFAULT in PHP)
- **Cost Factor**: 10 (adjustable in PHP config)
- **Never Stored Plain**: Passwords are hashed before storage
- **Min Length**: 6 characters (8 recommended for admin)

### 6. Session Security

- Session cookies are HTTP-only (prevents XSS theft)
- Session data stored server-side only
- Session ID regenerated on login
- Logout destroys session completely

### 7. Email OTP Security

- OTP expires after 10 minutes
- 6-digit random code
- One-time use only
- Sent via encrypted SMTP

## Production Deployment Checklist

### Pre-Deployment
- [ ] Remove all default/test accounts
- [ ] Set strong database password
- [ ] Configure SMTP with app-specific password
- [ ] Review all environment variables
- [ ] Set secure session configuration

### During Deployment
- [ ] Enable HTTPS (TLS/SSL)
- [ ] Set restrictive file permissions (755/644)
- [ ] Configure firewall rules
- [ ] Set up database backups
- [ ] Configure error logging (not to public)

### Post-Deployment
- [ ] Create admin user via create-admin.php
- [ ] **Verify create-admin.php is deleted**
- [ ] Remove setup-database.php
- [ ] Test all authentication flows
- [ ] Verify file upload restrictions
- [ ] Test IDOR protections
- [ ] Enable rate limiting (if applicable)
- [ ] Set up monitoring/alerts

## Security Headers (Add to .htaccess or server config)

```apache
# Prevent clickjacking
Header always set X-Frame-Options "SAMEORIGIN"

# XSS Protection
Header always set X-XSS-Protection "1; mode=block"

# Prevent MIME sniffing
Header always set X-Content-Type-Options "nosniff"

# HTTPS only
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

# Referrer Policy
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

## Known Limitations

⚠️ **Current Implementation:**

- No CSRF protection (add tokens for production)
- No rate limiting on login/registration
- No two-factor authentication (2FA)
- No password reset functionality
- No account lockout after failed attempts

**For Production:** Implement these additional security layers.

## Incident Response

If you suspect a security breach:

1. **Immediate Actions:**
   - Disable application access
   - Change all passwords and secrets
   - Review access logs
   - Check for unauthorized admin accounts

2. **Investigation:**
   - Review database for anomalies
   - Check uploaded files for malicious content
   - Audit user activities
   - Review server logs

3. **Recovery:**
   - Restore from clean backup if needed
   - Apply security patches
   - Update all credentials
   - Notify affected users

## Reporting Security Issues

If you discover a security vulnerability:

1. **DO NOT** disclose publicly
2. Document the issue with steps to reproduce
3. Contact the security team immediately
4. Wait for confirmation before disclosure

## Regular Security Maintenance

**Monthly:**
- Review user accounts (remove inactive)
- Check for failed login attempts
- Review uploaded files
- Update dependencies

**Quarterly:**
- Rotate database passwords
- Rotate API keys/secrets
- Security audit of new features
- Review and update permissions

**Annually:**
- Full security assessment
- Penetration testing
- Dependency vulnerability scan
- Review and update security policies
