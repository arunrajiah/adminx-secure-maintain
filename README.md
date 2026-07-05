<p align="center">
  <img src="docs/assets/logo.svg" alt="AdminX Secure Maintain logo" width="96" height="96">
</p>

# AdminX Secure Maintain 🔒

![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue.svg)
![Security](https://img.shields.io/badge/Security-Enhanced-red.svg)
![Version](https://img.shields.io/badge/version-1.0.0-green.svg)
![License](https://img.shields.io/badge/license-GPL%20v2-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)

A comprehensive WordPress security and maintenance plugin designed for administrators to protect websites, monitor threats, and maintain optimal site health and security.

## 🎯 Core Features

- **Security Scanning**: Real-time malware and vulnerability detection
- **Firewall Protection**: Advanced firewall rules and IP blocking
- **Login Security**: Two-factor authentication and login monitoring
- **File Integrity Monitoring**: Core file change detection and alerts
- **Backup Management**: Automated secure backups and restoration
- **Maintenance Mode**: Professional maintenance page management
- **Security Hardening**: WordPress security best practices implementation
- **Threat Intelligence**: Real-time security threat monitoring

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Minimum 128MB PHP memory limit
- SSL certificate recommended
- cURL support for external security APIs

## 🔧 Installation

### Via WordPress Admin
1. Navigate to **Plugins > Add New**
2. Search for "AdminX Secure Maintain"
3. Click **Install Now** and then **Activate**

### Manual Installation
1. Download the plugin zip file
2. Upload to `/wp-content/plugins/` directory
3. Extract the files
4. Activate through the WordPress admin panel

### Git Clone (Development)
```bash
git clone https://github.com/arunrajiah/adminx-secure-maintain.git
cd adminx-secure-maintain
```

## ⚙️ Configuration

1. After activation, navigate to **AdminX > Security**
2. Configure firewall settings:
   - Set up IP whitelist/blacklist
   - Configure rate limiting
   - Enable geographic blocking
3. Set up security monitoring:
   - Configure scan schedules
   - Set up threat notifications
   - Enable file integrity monitoring
4. Backup configuration:
   - Set automated backup schedules
   - Configure backup storage locations
   - Set retention policies

## 🚀 Usage

### Security Dashboard
1. Access **AdminX > Security Dashboard**
2. Monitor real-time security status
3. Review security alerts and recommendations
4. Manage blocked IPs and threats

### Backup Management
1. Navigate to **AdminX > Backups**
2. Create manual backups
3. Schedule automated backups
4. Restore from backup points

### Maintenance Mode
1. Access **AdminX > Maintenance**
2. Enable maintenance mode
3. Customize maintenance page
4. Set maintenance schedules

## 🔒 Security Features

- **Two-Factor Authentication**: TOTP and SMS support
- **Brute Force Protection**: Login attempt monitoring and blocking
- **SQL Injection Prevention**: Advanced input sanitization
- **XSS Protection**: Cross-site scripting prevention
- **CSRF Protection**: Cross-site request forgery prevention
- **File Upload Security**: Malicious file detection
- **Database Security**: Encrypted sensitive data storage

## 🏗️ Technical Architecture

```
adminx-secure-maintain/
├── includes/
│   ├── class-security-scanner.php
│   ├── class-firewall-manager.php
│   ├── class-backup-manager.php
│   └── class-maintenance-mode.php
├── admin/
│   ├── css/
│   ├── js/
│   └── partials/
├── public/
│   ├── css/
│   └── js/
└── adminx-secure-maintain.php
```

## 🔍 Security API Integration

### Threat Intelligence
```php
// Configure threat intelligence API
$threat_api = new AdminX_Threat_Intelligence($api_key);

// Check IP reputation
$reputation = $threat_api->check_ip_reputation($ip_address);

// Get latest threat signatures
$signatures = $threat_api->get_threat_signatures();
```

### Malware Scanning
```php
// Initialize malware scanner
$scanner = new AdminX_Malware_Scanner();

// Scan specific directory
$results = $scanner->scan_directory('/wp-content/uploads/');

// Get scan report
$report = $scanner->generate_report();
```

## 🔧 Troubleshooting

### Common Issues

**Firewall blocking legitimate traffic**
- Review firewall logs
- Adjust rate limiting settings
- Update IP whitelist

**Backup failures**
- Check file permissions
- Verify storage space
- Review backup logs

**False positive security alerts**
- Update threat signatures
- Adjust sensitivity settings
- Review scan exclusions

**Two-factor authentication issues**
- Verify time synchronization
- Check backup codes
- Reset 2FA settings if needed

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes and test thoroughly
4. Commit with clear messages: `git commit -m 'Add new feature'`
5. Push to your fork: `git push origin feature/new-feature`
6. Submit a pull request

### Development Setup
```bash
# Set up local WordPress development environment
# Copy plugin to wp-content/plugins/adminx-secure-maintain/

# Run WordPress Coding Standards check
phpcs --standard=WordPress --extensions=php ./

# Run PHP syntax validation
find . -name "*.php" -exec php -l {} \;
```

## 📝 Changelog

### 1.0.0
- Initial release
- Security scanning engine
- Firewall protection system
- Backup management tools
- Maintenance mode functionality

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 👨‍💻 Author

**Arun Rajiah**
- GitHub: [@arunrajiah](https://github.com/arunrajiah)
- LinkedIn: [arunrajiah](https://linkedin.com/in/arunrajiah)

## 🆘 Support

For support and questions:
- Create an issue on [GitHub](https://github.com/arunrajiah/adminx-secure-maintain/issues)
- GitHub Discussions: [AdminX Secure Maintain Discussions](https://github.com/arunrajiah/adminx-secure-maintain/discussions)

---

*Part of the AdminX plugin suite for WordPress administrators.*