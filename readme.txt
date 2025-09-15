=== AdminX Secure & Maintain ===
Contributors: adminx
Tags: security, maintenance, hardening, login protection, file monitor
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Comprehensive security hardening and maintenance tools for WordPress administrators.

== Description ==

AdminX Secure & Maintain is a comprehensive WordPress security and maintenance plugin designed specifically for website administrators. It provides essential security hardening features, login protection, activity monitoring, file change detection, and database optimization tools.

= Key Features =

**Security Hardening:**
* Disable XML-RPC to prevent brute force attacks
* Disable file editing from WordPress admin
* Hide WordPress version information
* Add security headers (X-Content-Type-Options, X-Frame-Options, etc.)
* Remove unnecessary WordPress meta tags

**Login Protection:**
* Limit login attempts with IP-based blocking
* Configurable lockout duration
* Login activity logging
* Real-time monitoring of failed login attempts
* IP blacklist management

**Activity Logging:**
* Track user logins and admin activities
* Monitor post/page edits and plugin changes
* IP address and user agent logging
* Searchable activity history

**File Monitor:**
* Detect unauthorized file changes
* Monitor core WordPress files
* Track theme and plugin modifications
* File integrity checking

**Database Optimizer:**
* Clean spam comments and trash posts
* Remove expired transients
* Optimize database tables
* Scheduled automatic cleanup
* Database size monitoring

= Why Choose AdminX Secure & Maintain? =

* **All-in-One Solution**: Multiple security and maintenance tools in one plugin
* **Lightweight**: Optimized code that doesn't slow down your site
* **User-Friendly**: Clean, intuitive admin interface
* **No External Dependencies**: All features work locally on your server
* **Regular Updates**: Actively maintained and updated
* **WordPress Standards**: Follows WordPress coding standards and best practices

= Perfect For =

* Website administrators managing multiple WordPress sites
* Security-conscious website owners
* Developers who need comprehensive site monitoring
* Anyone wanting to harden their WordPress installation

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/adminx-secure-maintain` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the AdminX Security menu in your WordPress admin to configure the plugin.
4. Review and adjust security settings according to your needs.
5. Monitor the dashboard for security status and activity logs.

== Frequently Asked Questions ==

= Will this plugin slow down my website? =

No, AdminX Secure & Maintain is designed to be lightweight and efficient. Most security features have minimal impact on site performance, and database optimization actually helps improve performance.

= Can I use this with other security plugins? =

Yes, but we recommend reviewing settings to avoid conflicts. Some features may overlap with other security plugins, so you may want to disable duplicate functionality.

= What happens if I get locked out? =

If you're locked out due to login protection, you can disable the plugin via FTP or through your hosting control panel to regain access.

= Does this plugin work with multisite? =

Currently, this plugin is designed for single-site installations. Multisite support may be added in future versions.

= How often should I run database optimization? =

The plugin can automatically optimize your database weekly, but you can also run manual optimizations as needed. For most sites, weekly optimization is sufficient.

= Will this plugin conflict with caching plugins? =

No, AdminX Secure & Maintain is compatible with most caching plugins. The security features work at the server level and don't interfere with caching.

== Screenshots ==

1. Main dashboard showing security status overview
2. Security settings configuration page
3. Login protection and blocked IPs management
4. Activity log with detailed user actions
5. File monitor showing detected changes
6. Database optimizer with cleanup options

== Changelog ==

= 1.0.0 =
* Initial release
* Security hardening features (XML-RPC, file editing, version hiding)
* Login protection with attempt limiting
* Activity logging system
* File change monitoring
* Database optimization tools
* Comprehensive admin interface
* Security headers implementation
* IP blocking and management

== Upgrade Notice ==

= 1.0.0 =
Initial release of AdminX Secure & Maintain. Install to start securing and maintaining your WordPress site.

== Privacy Policy ==

AdminX Secure & Maintain stores the following data locally in your WordPress database:

* Login attempt logs (IP addresses, usernames, timestamps, user agents)
* User activity logs (user IDs, actions performed, timestamps)
* File change detection logs (file paths, change types, timestamps)

This data is used solely for security monitoring and is not transmitted to external servers. You can delete this data at any time by deactivating the plugin.

== Support ==

For support, feature requests, or bug reports, please visit our GitHub repository or contact us through the WordPress.org support forums.

== Contributing ==

We welcome contributions! Please visit our GitHub repository to submit issues, feature requests, or pull requests.
