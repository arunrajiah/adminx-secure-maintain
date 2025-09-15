# AdminX Secure & Maintain - Deployment Guide

## Table of Contents

1. [Development Environment Setup](#development-environment-setup)
2. [Local Development](#local-development)
3. [Testing](#testing)
4. [Building for Release](#building-for-release)
5. [WordPress.org Submission](#wordpressorg-submission)
6. [Version Management](#version-management)
7. [Continuous Integration](#continuous-integration)
8. [Deployment Checklist](#deployment-checklist)

## Development Environment Setup

### Prerequisites

- **PHP 7.4+** (recommended: PHP 8.1)
- **WordPress 5.0+** (recommended: latest version)
- **Composer** for dependency management
- **Node.js and npm** for asset building (if needed)
- **Git** for version control
- **Local WordPress environment** (Local by Flywheel, XAMPP, MAMP, or Docker)

### Local WordPress Setup

#### Option 1: Local by Flywheel
1. Download and install [Local by Flywheel](https://localwp.com/)
2. Create a new WordPress site
3. Clone the plugin repository to `wp-content/plugins/`

#### Option 2: Docker (Recommended for Developers)
```bash
# Clone WordPress with Docker
git clone https://github.com/docker/awesome-compose.git
cd awesome-compose/wordpress-mysql
docker-compose up -d

# Clone plugin to plugins directory
cd wordpress/wp-content/plugins/
git clone https://github.com/your-username/adminx-secure-maintain.git
```

#### Option 3: XAMPP/MAMP
1. Install XAMPP or MAMP
2. Download WordPress and extract to htdocs
3. Create database and configure wp-config.php
4. Clone plugin to wp-content/plugins/

### Development Tools

#### Code Editor Setup
**VS Code Extensions:**
- PHP Intelephense
- WordPress Snippets
- PHP Debug
- GitLens
- Prettier

**PHPStorm Plugins:**
- WordPress Support
- PHP Annotations
- .env files support

#### Composer Setup
```bash
# Install Composer dependencies
composer install

# Install WordPress Coding Standards
composer global require "squizlabs/php_codesniffer=*"
composer global require wp-coding-standards/wpcs
phpcs --config-set installed_paths ~/.composer/vendor/wp-coding-standards/wpcs
```

## Local Development

### Project Structure
```
adminx-secure-maintain/
├── adminx-secure-maintain.php    # Main plugin file
├── includes/                     # Core PHP classes
│   ├── class-security-hardening.php
│   ├── class-login-protection.php
│   ├── class-activity-logger.php
│   ├── class-file-monitor.php
│   ├── class-database-optimizer.php
│   └── class-admin-interface.php
├── assets/                       # CSS/JS files
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── templates/                    # Admin page templates
│   ├── admin-main.php
│   ├── admin-security.php
│   ├── admin-login.php
│   ├── admin-activity.php
│   ├── admin-file-monitor.php
│   └── admin-database.php
├── docs/                         # Documentation
│   ├── development_progress.md
│   ├── user_guide.md
│   └── deployment_guide.md
├── .github/
│   └── workflows/
│       └── ci.yml
├── readme.txt                    # WordPress.org readme
├── composer.json                 # Composer configuration
└── package.json                  # npm configuration (if needed)
```

### Development Workflow

1. **Create Feature Branch**
```bash
git checkout -b feature/new-feature-name
```

2. **Make Changes**
- Follow WordPress coding standards
- Add proper documentation
- Include error handling

3. **Test Changes**
```bash
# Run PHP syntax check
find . -name "*.php" -exec php -l {} \;

# Run WordPress coding standards
phpcs --standard=WordPress .

# Fix coding standards automatically
phpcbf --standard=WordPress .
```

4. **Commit Changes**
```bash
git add .
git commit -m "Add: New feature description"
git push origin feature/new-feature-name
```

5. **Create Pull Request**
- Submit PR for code review
- Ensure all tests pass
- Update documentation if needed

### Database Development

#### Creating Tables
```php
// Use dbDelta for table creation
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
```

#### Testing Database Changes
1. Test table creation on activation
2. Test data insertion and retrieval
3. Test table cleanup on deactivation
4. Verify database performance

### Asset Development

#### CSS Development
- Use WordPress admin styles as base
- Follow WordPress CSS coding standards
- Ensure responsive design
- Test across different browsers

#### JavaScript Development
- Use jQuery (included with WordPress)
- Follow WordPress JavaScript standards
- Implement proper AJAX handling
- Add error handling and user feedback

## Testing

### Manual Testing Checklist

#### Plugin Activation/Deactivation
- [ ] Plugin activates without errors
- [ ] Database tables are created
- [ ] Default options are set
- [ ] Admin menu appears
- [ ] Plugin deactivates cleanly
- [ ] Scheduled events are cleared

#### Security Features
- [ ] XML-RPC disabling works
- [ ] File editing is disabled
- [ ] WordPress version is hidden
- [ ] Security headers are added
- [ ] Meta tags are removed

#### Login Protection
- [ ] Failed attempts are logged
- [ ] IP blocking works after limit
- [ ] Successful logins clear attempts
- [ ] Blocked IPs can be unblocked
- [ ] Lockout duration is respected

#### Admin Interface
- [ ] All pages load without errors
- [ ] Forms submit correctly
- [ ] AJAX requests work
- [ ] Settings are saved
- [ ] Dashboard shows correct data

### Automated Testing

#### PHPUnit Setup
```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Install WordPress test suite
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

#### Writing Tests
```php
class Test_Security_Hardening extends WP_UnitTestCase {
    public function test_xmlrpc_disabled() {
        // Test XML-RPC disabling
        $this->assertFalse(xmlrpc_enabled());
    }
}
```

#### Running Tests
```bash
# Run all tests
phpunit

# Run specific test
phpunit tests/test-security-hardening.php
```

### Browser Testing

#### Supported Browsers
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

#### Testing Checklist
- [ ] Admin interface displays correctly
- [ ] JavaScript functionality works
- [ ] Forms submit properly
- [ ] AJAX requests complete
- [ ] Responsive design works

## Building for Release

### Pre-Release Checklist

- [ ] All features implemented and tested
- [ ] Documentation updated
- [ ] Version numbers updated
- [ ] Changelog updated
- [ ] Security review completed
- [ ] Performance testing completed
- [ ] Compatibility testing completed

### Version Update Process

1. **Update Version Numbers**
```php
// In main plugin file
define('ADMINX_SECURE_MAINTAIN_VERSION', '1.1.0');

// In readme.txt
Stable tag: 1.1.0
```

2. **Update Changelog**
```
== Changelog ==

= 1.1.0 =
* Added: New feature description
* Fixed: Bug fix description
* Improved: Enhancement description
```

3. **Create Git Tag**
```bash
git tag -a v1.1.0 -m "Version 1.1.0"
git push origin v1.1.0
```

### Building Plugin Package

#### Manual Build
```bash
# Create build directory
mkdir build

# Copy plugin files (excluding development files)
rsync -av --exclude-from='.buildignore' . build/adminx-secure-maintain/

# Create zip file
cd build
zip -r adminx-secure-maintain-1.1.0.zip adminx-secure-maintain/
```

#### Automated Build Script
```bash
#!/bin/bash
# build.sh

VERSION=$1
PLUGIN_NAME="adminx-secure-maintain"

if [ -z "$VERSION" ]; then
    echo "Usage: ./build.sh <version>"
    exit 1
fi

# Clean previous builds
rm -rf build
mkdir build

# Copy files
rsync -av \
    --exclude='.git*' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='build' \
    --exclude='*.md' \
    . build/$PLUGIN_NAME/

# Create zip
cd build
zip -r $PLUGIN_NAME-$VERSION.zip $PLUGIN_NAME/

echo "Build complete: build/$PLUGIN_NAME-$VERSION.zip"
```

## WordPress.org Submission

### Initial Submission

1. **Create WordPress.org Account**
2. **Submit Plugin for Review**
   - Go to https://wordpress.org/plugins/developers/add/
   - Upload your plugin zip file
   - Fill out the submission form
   - Wait for review (typically 1-2 weeks)

3. **Address Review Feedback**
   - Fix any issues identified
   - Resubmit if necessary

### Repository Setup

Once approved, you'll get SVN access:

```bash
# Checkout SVN repository
svn checkout https://plugins.svn.wordpress.org/adminx-secure-maintain

# Add files to trunk
cp -r /path/to/plugin/* trunk/
svn add trunk/*
svn commit -m "Initial commit"

# Create tag for release
svn copy trunk tags/1.0.0
svn commit -m "Tag version 1.0.0"
```

### Release Process

1. **Update Trunk**
```bash
svn update
cp -r /path/to/new/version/* trunk/
svn commit -m "Update to version 1.1.0"
```

2. **Create Release Tag**
```bash
svn copy trunk tags/1.1.0
svn commit -m "Tag version 1.1.0"
```

3. **Update Stable Tag**
Update readme.txt in trunk:
```
Stable tag: 1.1.0
```

## Version Management

### Semantic Versioning

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.0.0): Breaking changes
- **MINOR** (1.1.0): New features, backward compatible
- **PATCH** (1.1.1): Bug fixes, backward compatible

### Release Schedule

- **Major releases**: Every 6-12 months
- **Minor releases**: Every 2-3 months
- **Patch releases**: As needed for critical bugs

### Backward Compatibility

- Maintain database compatibility
- Deprecate features before removal
- Provide migration paths
- Document breaking changes

## Continuous Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/ci.yml
name: WordPress Plugin CI

on:
  push:
    branches: [ "main" ]
  pull_request:
    branches: [ "main" ]

jobs:
  build:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout repository
      uses: actions/checkout@v3

    - name: Set up PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'

    - name: Validate PHP syntax
      run: find . -name "*.php" -print0 | xargs -0 -n1 php -l

    - name: Install Composer dependencies
      run: composer install

    - name: Run WordPress coding standards
      run: ./vendor/bin/phpcs --standard=WordPress .

    - name: Run PHPUnit tests
      run: ./vendor/bin/phpunit

    - name: Build plugin zip
      run: |
        mkdir -p build
        zip -r build/adminx-secure-maintain.zip . -x "*.git*" "node_modules/*" "tests/*" "build/*"
```

### Quality Gates

- All tests must pass
- Code coverage > 80%
- No coding standard violations
- Security scan passes
- Performance benchmarks met

## Deployment Checklist

### Pre-Deployment

- [ ] Code review completed
- [ ] All tests passing
- [ ] Documentation updated
- [ ] Version numbers updated
- [ ] Changelog updated
- [ ] Security review completed
- [ ] Performance testing completed
- [ ] Backup created

### Deployment

- [ ] Plugin package built
- [ ] WordPress.org repository updated
- [ ] Git tags created
- [ ] Release notes published
- [ ] Team notified

### Post-Deployment

- [ ] Plugin directory updated
- [ ] Download links verified
- [ ] Installation tested
- [ ] User feedback monitored
- [ ] Support requests addressed

### Rollback Plan

1. **Identify Issue**
2. **Revert to Previous Tag**
```bash
svn copy tags/1.0.0 trunk
svn commit -m "Rollback to version 1.0.0"
```
3. **Update Stable Tag**
4. **Notify Users**
5. **Fix Issues**
6. **Redeploy**

## Security Considerations

### Code Security

- Validate and sanitize all inputs
- Use WordPress nonces for forms
- Escape all outputs
- Use prepared statements for database queries
- Implement proper capability checks

### Deployment Security

- Use secure connections (HTTPS/SSH)
- Limit access to deployment tools
- Audit deployment logs
- Monitor for unauthorized changes

### Secrets Management

- Never commit API keys or passwords
- Use environment variables
- Rotate credentials regularly
- Use secure storage for sensitive data

---

*Last updated: September 2025*
*For questions about deployment, contact the development team.*
