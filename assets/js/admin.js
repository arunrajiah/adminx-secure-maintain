/**
 * AdminX Secure & Maintain - Admin JavaScript
 *
 * @package AdminX_Secure_Maintain
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * AdminX Admin Object
     */
    var AdminX = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.loadDashboardData();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            // Security settings form
            $(document).on('submit', '#adminx-security-form', this.saveSecuritySettings);
            
            // Login protection actions
            $(document).on('click', '.adminx-unblock-ip', this.unblockIP);
            $(document).on('click', '#adminx-refresh-blocked-ips', this.refreshBlockedIPs);
            
            // File monitor actions
            $(document).on('click', '#adminx-scan-files', this.scanFiles);
            
            // Database optimizer actions
            $(document).on('click', '#adminx-optimize-db', this.optimizeDatabase);
            $(document).on('click', '#adminx-clean-db', this.cleanDatabase);
            
            // Tab navigation
            $(document).on('click', '.adminx-tab', this.switchTab);
        },
        
        /**
         * Save security settings
         */
        saveSecuritySettings: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('input[type="submit"]');
            var originalText = $button.val();
            
            $button.val('Saving...').prop('disabled', true);
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_save_security_settings',
                    nonce: adminx_ajax.nonce,
                    settings: $form.serialize()
                },
                success: function(response) {
                    if (response.success) {
                        AdminX.showNotice('Settings saved successfully!', 'success');
                    } else {
                        AdminX.showNotice('Error saving settings: ' + response.data, 'error');
                    }
                },
                error: function() {
                    AdminX.showNotice('Error saving settings. Please try again.', 'error');
                },
                complete: function() {
                    $button.val(originalText).prop('disabled', false);
                }
            });
        },
        
        /**
         * Unblock IP address
         */
        unblockIP: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var ipHash = $button.data('ip-hash');
            
            if (!confirm('Are you sure you want to unblock this IP address?')) {
                return;
            }
            
            $button.text('Unblocking...').prop('disabled', true);
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_unblock_ip',
                    nonce: adminx_ajax.nonce,
                    ip_hash: ipHash
                },
                success: function(response) {
                    if (response.success) {
                        $button.closest('tr').fadeOut();
                        AdminX.showNotice('IP address unblocked successfully!', 'success');
                    } else {
                        AdminX.showNotice('Error unblocking IP: ' + response.data, 'error');
                    }
                },
                error: function() {
                    AdminX.showNotice('Error unblocking IP. Please try again.', 'error');
                },
                complete: function() {
                    $button.text('Unblock').prop('disabled', false);
                }
            });
        },
        
        /**
         * Refresh blocked IPs list
         */
        refreshBlockedIPs: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $container = $('#adminx-blocked-ips-list');
            
            $button.text('Refreshing...').prop('disabled', true);
            $container.html('<div class="adminx-spinner"></div>');
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_get_blocked_ips',
                    nonce: adminx_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AdminX.renderBlockedIPs(response.data);
                    } else {
                        $container.html('<p>Error loading blocked IPs.</p>');
                    }
                },
                error: function() {
                    $container.html('<p>Error loading blocked IPs.</p>');
                },
                complete: function() {
                    $button.text('Refresh').prop('disabled', false);
                }
            });
        },
        
        /**
         * Render blocked IPs
         */
        renderBlockedIPs: function(blockedIPs) {
            var $container = $('#adminx-blocked-ips-list');
            
            if (blockedIPs.length === 0) {
                $container.html('<p>No blocked IP addresses.</p>');
                return;
            }
            
            var html = '<table class="adminx-table">';
            html += '<thead><tr><th>IP Hash</th><th>Blocked Until</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            $.each(blockedIPs, function(index, ip) {
                html += '<tr>';
                html += '<td>' + ip.hash + '</td>';
                html += '<td>' + ip.blocked_until + '</td>';
                html += '<td><button class="adminx-button adminx-unblock-ip" data-ip-hash="' + ip.hash + '">Unblock</button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            $container.html(html);
        },
        
        /**
         * Scan files
         */
        scanFiles: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $results = $('#adminx-file-scan-results');
            
            $button.text('Scanning...').prop('disabled', true);
            $results.html('<div class="adminx-spinner"></div> Scanning files...');
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_scan_files',
                    nonce: adminx_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AdminX.renderScanResults(response.data);
                    } else {
                        $results.html('<p class="adminx-notice error">Error scanning files: ' + response.data + '</p>');
                    }
                },
                error: function() {
                    $results.html('<p class="adminx-notice error">Error scanning files. Please try again.</p>');
                },
                complete: function() {
                    $button.text('Scan Files').prop('disabled', false);
                }
            });
        },
        
        /**
         * Render scan results
         */
        renderScanResults: function(results) {
            var $container = $('#adminx-file-scan-results');
            var html = '<div class="adminx-notice success">File scan completed!</div>';
            
            if (results.changes && results.changes.length > 0) {
                html += '<h4>File Changes Detected:</h4>';
                html += '<table class="adminx-table">';
                html += '<thead><tr><th>File</th><th>Change Type</th><th>Detected</th></tr></thead>';
                html += '<tbody>';
                
                $.each(results.changes, function(index, change) {
                    html += '<tr>';
                    html += '<td>' + change.file_path + '</td>';
                    html += '<td>' + change.change_type + '</td>';
                    html += '<td>' + change.detected_at + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
            } else {
                html += '<p>No file changes detected.</p>';
            }
            
            $container.html(html);
        },
        
        /**
         * Optimize database
         */
        optimizeDatabase: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $results = $('#adminx-db-results');
            
            $button.text('Optimizing...').prop('disabled', true);
            $results.html('<div class="adminx-spinner"></div> Optimizing database...');
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_optimize_database',
                    nonce: adminx_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $results.html('<div class="adminx-notice success">Database optimized successfully!</div>');
                    } else {
                        $results.html('<div class="adminx-notice error">Error optimizing database: ' + response.data + '</div>');
                    }
                },
                error: function() {
                    $results.html('<div class="adminx-notice error">Error optimizing database. Please try again.</div>');
                },
                complete: function() {
                    $button.text('Optimize Database').prop('disabled', false);
                }
            });
        },
        
        /**
         * Clean database
         */
        cleanDatabase: function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to clean the database? This will remove spam, trash, and expired transients.')) {
                return;
            }
            
            var $button = $(this);
            var $results = $('#adminx-db-results');
            
            $button.text('Cleaning...').prop('disabled', true);
            $results.html('<div class="adminx-spinner"></div> Cleaning database...');
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_clean_database',
                    nonce: adminx_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $results.html('<div class="adminx-notice success">Database cleaned successfully!</div>');
                    } else {
                        $results.html('<div class="adminx-notice error">Error cleaning database: ' + response.data + '</div>');
                    }
                },
                error: function() {
                    $results.html('<div class="adminx-notice error">Error cleaning database. Please try again.</div>');
                },
                complete: function() {
                    $button.text('Clean Database').prop('disabled', false);
                }
            });
        },
        
        /**
         * Switch tabs
         */
        switchTab: function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var target = $tab.data('target');
            
            // Update active tab
            $('.adminx-tab').removeClass('active');
            $tab.addClass('active');
            
            // Show target content
            $('.adminx-tab-content').hide();
            $(target).show();
        },
        
        /**
         * Load dashboard data
         */
        loadDashboardData: function() {
            if ($('#adminx-dashboard').length === 0) {
                return;
            }
            
            $.ajax({
                url: adminx_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'adminx_get_dashboard_data',
                    nonce: adminx_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AdminX.renderDashboardData(response.data);
                    }
                }
            });
        },
        
        /**
         * Render dashboard data
         */
        renderDashboardData: function(data) {
            // Update security status
            if (data.security) {
                $.each(data.security, function(key, value) {
                    var $element = $('#adminx-' + key.replace('_', '-'));
                    if ($element.length) {
                        $element.removeClass('enabled disabled').addClass(value ? 'enabled' : 'disabled');
                        $element.text(value ? 'Enabled' : 'Disabled');
                    }
                });
            }
            
            // Update login stats
            if (data.login_stats) {
                $('#adminx-today-attempts').text(data.login_stats.today_attempts || 0);
                $('#adminx-today-failed').text(data.login_stats.today_failed || 0);
                $('#adminx-blocked-count').text(data.login_stats.blocked_ips ? data.login_stats.blocked_ips.length : 0);
            }
        },
        
        /**
         * Show notice
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var $notice = $('<div class="adminx-notice ' + type + '">' + message + '</div>');
            $('.adminx-container').prepend($notice);
            
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        AdminX.init();
    });
    
})(jQuery);
