# WebWeaver Deployment Guide

Production deployment instructions for WordPress hosting environments.

## Pre-Deployment Checklist

- [ ] WordPress 6.0+ installed
- [ ] PHP 7.4+ available
- [ ] MySQL/MariaDB 5.7+ configured
- [ ] SSL/HTTPS certificate ready
- [ ] Backup of WordPress installation
- [ ] Database backup
- [ ] Plugin files ready

## Installation Methods

### Method 1: WordPress Admin Upload (Easiest)

1. **Prepare Plugin**
   - Extract WebWeaver files
   - Create `webweaver.zip` with correct structure

2. **Upload via WordPress**
   - Login to WordPress Admin
   - Go to **Plugins > Add New**
   - Click **Upload Plugin**
   - Select `webweaver.zip`
   - Click **Install Now**

3. **Activate**
   - Click **Activate Plugin**
   - Check **WebWeaver** menu appears

4. **Configure**
   - Go to **WebWeaver > Settings**
   - Enable HTTPS requirement
   - Set rate limits
   - Save settings

### Method 2: SFTP/FTP Upload

1. **Upload Files**
   ```bash
   # Connect via SFTP
   sftp user@example.com
   
   # Navigate to plugins directory
   cd /public_html/wp-content/plugins/
   
   # Upload webweaver directory
   put -r webweaver/
   ```

2. **Set Permissions**
   ```bash
   chmod -R 755 webweaver/
   chmod -R 644 webweaver/*.php
   ```

3. **Activate in WordPress**
   - Go to **Plugins** page
   - Click **Activate** on WebWeaver

### Method 3: WP-CLI (Advanced)

```bash
# SSH into server
ssh user@example.com

# Navigate to WordPress root
cd /public_html

# Install plugin
wp plugin install webweaver --activate

# Verify installation
wp plugin list | grep webweaver

# Create app password for API
wp user list --field=ID,user_login
wp plugin install application-passwords --activate
```

## Post-Installation Configuration

### 1. Enable HTTPS (Required for Production)

**WordPress Settings:**
```
Settings > General
- WordPress Address (URL): https://example.com
- Site Address (URL): https://example.com
```

**SSL Certificate:**
- Use Let's Encrypt (free)
- Or existing certificate
- Verify HTTPS in browser

**WebWeaver Settings:**
```
WebWeaver > Settings
- ✅ Enable "HTTPS Required"
- Set rate limit to 60/hour
- Enable Draft-Only Mode
- Save
```

### 2. Create Application Passwords

**For Each AI Agent/Integration:**

1. Go to **Users > {Username}**
2. Scroll to **Application Passwords**
3. Enter name: `WebWeaver Agent`
4. Click **Create Application Password**
5. **Copy the password** (shown only once)
6. Store securely (environment variables, .env file, etc.)

### 3. Configure User Roles

**For AI Agents:**
- Create dedicated user with **Editor** role
- Or use existing trusted admin/editor

**For Content Creators:**
- Create users with **Editor** role
- Or **Author** role (limited)

**Not Recommended:**
- ❌ Contributor role (too limited)
- ❌ Multiple agents sharing password
- ❌ Using admin password

### 4. Database Optimization

```sql
-- Optimize activity log queries
ALTER TABLE wp_webweaver_activity_log ADD INDEX idx_user_time (user_id, timestamp);
ALTER TABLE wp_webweaver_activity_log ADD INDEX idx_action (action);

-- Archive old logs (optional)
DELETE FROM wp_webweaver_activity_log 
WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### 5. Set Up Monitoring

**Activity Log Review:**
- Weekly: Review Activity Log for suspicious activity
- Monthly: Archive or delete old logs
- Alert on: Failed auth attempts, unusual patterns

**WordPress Logs:**
- Enable debug logging
- Monitor `wp-content/debug.log`
- Look for WebWeaver errors

## Security Configuration

### 1. Firewall Rules

```
# Allow REST API access
Allow: /wp-json/wp-mcp/v1/*

# Block admin brute force
Limit: /wp-login.php to 5 attempts/10 min

# Rate limit
Limit: /wp-json/ to 100 requests/min per IP
```

### 2. Web Server Configuration

**Nginx:**
```nginx
# Block direct access to plugin files
location ~* /wp-content/plugins/webweaver/\.php$ {
    deny all;
}

# REST API rate limiting
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
location ~ /wp-json/wp-mcp/ {
    limit_req zone=api burst=20;
}
```

**Apache:**
```apache
# .htaccess in plugin directory
<Files "*.php">
    deny from all
</Files>

# Allow specific API endpoint
<FilesMatch "^wp-webweaver\.php$">
    allow from all
</FilesMatch>
```

### 3. Database Security

```bash
# Restrict database access
mysql> GRANT ALL ON wordpress.* TO 'wp_user'@'localhost' IDENTIFIED BY 'strong_password';
mysql> FLUSH PRIVILEGES;

# Regular backups
mysqldump -u wp_user -p wordpress > backup_$(date +%Y%m%d).sql
```

### 4. WordPress Hardening

```php
// wp-config.php

// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Disable plugin/theme installation
define('DISALLOW_FILE_MODS', true);

// Security keys (generate new ones)
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
```

## Performance Optimization

### 1. Caching

```php
// Enable object cache (WordPress native or Redis)
define('WP_CACHE', true);

// Plugin caching
- Activate WP Super Cache or WP Fastest Cache
- Exclude REST API: /wp-json/wp-mcp/
```

### 2. Database Optimization

```bash
# Run repair
wp db optimize

# Check post counts
wp eval 'echo count_user_posts(1);'
```

### 3. Activity Log Pruning

```sql
-- Archive logs older than 90 days
INSERT INTO wp_webweaver_activity_log_archive
SELECT * FROM wp_webweaver_activity_log
WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);

DELETE FROM wp_webweaver_activity_log
WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

## Backup & Recovery

### Daily Backups

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/backups/wordpress"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u wp_user -p wordpress > "$BACKUP_DIR/db_$DATE.sql"

# Backup WordPress files
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/html/

# Keep 30 days
find "$BACKUP_DIR" -name "*.sql" -mtime +30 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +30 -delete
```

### Recovery Procedure

```bash
# 1. Stop WordPress
systemctl stop apache2

# 2. Restore database
mysql -u wp_user -p wordpress < backup_20260301.sql

# 3. Restore files
tar -xzf files_20260301.tar.gz -C /

# 4. Fix permissions
chown -R www-data:www-data /var/www/html/wp-content

# 5. Start WordPress
systemctl start apache2

# 6. Verify
wp --allow-root plugin list
```

## Environment Variables

Create `.env` file in WordPress root:

```bash
# WebWeaver Configuration
WEBWEAVER_DRAFT_ONLY=true
WEBWEAVER_RATE_LIMIT=60
WEBWEAVER_ALLOWED_POST_TYPES=post,page
WEBWEAVER_HTTPS_REQUIRED=true

# AI Agent Credentials (use in MCP config)
WEBWEAVER_API_URL=https://example.com/wp-json/wp-mcp/v1
WEBWEAVER_AUTH_USERNAME=webweaver_agent
# NEVER commit password - use CI/CD secrets instead
```

## Monitoring & Logging

### WordPress Error Log

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Log file location
// /wp-content/debug.log
```

### Activity Monitoring

```bash
# Check logs
tail -f /var/www/html/wp-content/debug.log

# Monitor API activity
wp db query "SELECT * FROM wp_webweaver_activity_log ORDER BY timestamp DESC LIMIT 10;"

# Export activity for analysis
wp db export activity_export_$(date +%Y%m%d).sql --tables=wp_webweaver_activity_log
```

## Troubleshooting Deployment

### Plugin Doesn't Appear

1. Check file permissions:
   ```bash
   ls -la /wp-content/plugins/webweaver/
   ```

2. Check wp-webweaver.php header:
   ```bash
   head -15 /wp-content/plugins/webweaver/wp-webweaver.php
   ```

3. Check WordPress error log:
   ```bash
   tail /wp-content/debug.log
   ```

### REST API Returns 404

1. Check permalink settings (must be non-default):
   ```bash
   wp option get permalink_structure
   ```

2. Flush rewrite rules:
   ```bash
   wp rewrite flush
   ```

3. Test REST API:
   ```bash
   curl https://example.com/wp-json/
   ```

### Authentication Fails

1. Verify app password is correct
2. Check if user is active:
   ```bash
   wp user list --field=ID,user_login,user_status
   ```

3. Test WordPress authentication:
   ```bash
   wp user meta list 1  # Check user 1 settings
   ```

## Scaling to Multiple Environments

### Development
```
- Local Docker setup
- Draft-only mode: ON
- Rate limit: 1000/hour
- Debug logging: ON
```

### Staging
```
- Mirror production environment
- HTTPS: ON
- Rate limit: 100/hour
- Backups: Daily
```

### Production
```
- High availability setup
- HTTPS: ON (required)
- Rate limit: 60/hour
- Backups: Daily + offsite
- Monitoring: Active
- CDN: Optional
- Load balancer: If needed
```

## Support & Maintenance

### Regular Tasks

**Daily:**
- Check error logs
- Review API activity

**Weekly:**
- Review Activity Log
- Check plugin updates
- Monitor resource usage

**Monthly:**
- Archive activity logs
- Database optimization
- Security audit
- Backup verification

**Quarterly:**
- Update WordPress
- Update plugins
- Security assessment
- Performance review

---

**Deployment Guide Version:** 1.0  
**Last Updated:** March 2026  
**Compatible With:** WordPress 6.0+, PHP 7.4+
