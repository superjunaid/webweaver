# WebWeaver Installation Guide

## Release: v0.1.0

**Ready to install on your WordPress site!**

### 📦 What You Get

- ✅ Complete plugin with 8 REST API endpoints
- ✅ Professional admin dashboard
- ✅ Security features (rate limiting, draft-only mode)
- ✅ Builder support (Gutenberg, Elementor, Divi)
- ✅ Full documentation
- ✅ MCP integration guide

### 📋 Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 7.4 or higher
- **MySQL:** 5.7+ or MariaDB 10.2+

### 🚀 Installation Steps

#### Option 1: WordPress Admin (Easiest)

1. **Download** `webweaver-0.1.0.zip`
2. Go to **WordPress Admin > Plugins > Add New**
3. Click **Upload Plugin**
4. Select `webweaver-0.1.0.zip`
5. Click **Install Now**
6. Click **Activate Plugin**
7. Verify: Look for **"WebWeaver"** menu in sidebar

#### Option 2: SFTP/FTP Upload

1. **Download** `webweaver-0.1.0.zip`
2. **Extract** the archive
3. **Upload** `webweaver/` folder to `/wp-content/plugins/`
4. **Permissions:** `chmod 755 -R webweaver/`
5. Go to **WordPress Admin > Plugins**
6. Find "WebWeaver" and click **Activate**

#### Option 3: WP-CLI (Advanced)

```bash
# Upload zip to server first
wp plugin install webweaver-0.1.0.zip --activate

# Verify
wp plugin list | grep webweaver
```

---

## ✅ Post-Installation

### 1. Enable Pretty Permalinks

This is **required** for REST API to work:

1. Go to **Settings > Permalinks**
2. Select **"Post name"** (not "Plain")
3. Click **Save Changes**

### 2. Configure User Permissions

WebWeaver requires your user to have the following capabilities:

- ✅ Read Posts
- ✅ Edit Posts
- ✅ Create Posts
- ✅ Publish Posts
- ✅ Upload Files

**Option A - Admin User:**
- Already has these by default

**Option B - Other Users:**
1. Go to **Users**
2. Select user
3. Change role to **"Editor"**
4. Click **Update User**

### 3. Visit Dashboard

1. Go to **WebWeaver** menu in WordPress Admin
2. You should see:
   - System Status (HTTPS, plugins, builders)
   - Your Capabilities
   - Quick Links
   - Getting Started Guide

---

## 🔧 Configuration

### WebWeaver Settings

1. Go to **WebWeaver > Settings**
2. Configure:

| Setting | Default | Description |
|---------|---------|-------------|
| **Draft-Only Mode** | ON | New posts start as drafts (safe) |
| **Rate Limit** | 60/hour | API requests per user per hour |
| **Allowed Post Types** | post, page | Content types API can create |
| **HTTPS Required** | OFF | Require HTTPS for API calls |

### Recommended Settings

**For Development:**
- Draft-Only Mode: ON
- Rate Limit: 1000/hour
- HTTPS Required: OFF

**For Production:**
- Draft-Only Mode: ON
- Rate Limit: 60/hour
- HTTPS Required: ON

---

## 📡 API Access

### Test the API

```bash
# Get tools manifest
curl -u admin:password \
  https://yoursite.com/wp-json/wp-mcp/v1/tools

# List posts
curl -u admin:password \
  https://yoursite.com/wp-json/wp-mcp/v1/posts
```

### For MCP Clients (Claude, etc.)

See [MCP_CLAUDE_SETUP.md](MCP_CLAUDE_SETUP.md) for detailed instructions.

Quick start:
```bash
# 1. Create app password (Users > Profile > Application Passwords)
# 2. Use in MCP client configuration
# 3. Test connection
```

---

## ✨ Features Available

### Admin Interface
- 📊 Dashboard with system status
- ⚙️ Settings page
- 📋 Activity log (audit trail)

### REST API (7 Tools)
- 📖 List posts
- 📄 Get post details
- ✏️ Create new posts
- 🔄 Update posts
- 🖼️ Upload media
- 🎬 Set featured images
- 📊 View activity log (admin)

### Security
- 🔐 User authentication
- 📋 Role-based access
- ⏱️ Rate limiting
- 📝 Activity logging
- 🔒 Draft-only mode option
- 🔗 HTTPS support

### Builder Support
- ✅ Gutenberg (WordPress native)
- ✅ Elementor (if installed)
- ✅ Divi (if installed)

---

## 🐛 Troubleshooting

### Plugin doesn't appear in menu

**Check:**
1. Plugin is activated: **Plugins** page
2. User has admin access
3. Refresh page (Ctrl+Shift+R)

### REST API returns 404

**Fix:**
1. Go to **Settings > Permalinks**
2. Change to **"Post name"** structure
3. Click **Save**

### Cannot read/create posts

**Check:**
1. User role is **Editor** or higher
2. Check Dashboard for capabilities
3. Verify permissions under **Users > Your Profile**

### HTTPS/SSL errors

**Solution:**
1. Ensure site uses HTTPS
2. Disable "HTTPS Required" in settings (development only)
3. Or get SSL certificate (production)

---

## 📚 Documentation

All documentation is included:

- **README.md** - Overview & features
- **QUICK_START.md** - 5-minute setup
- **SETUP_GUIDE.md** - Complete guide
- **DEPLOYMENT.md** - Production setup
- **ARCHITECTURE.md** - Technical details
- **MCP_CLAUDE_SETUP.md** - Claude integration

View in WordPress: **WebWeaver Dashboard** has guides

---

## 🔒 Security Checklist

- [ ] Enable pretty permalinks
- [ ] Set user role to Editor (minimum)
- [ ] Enable HTTPS in production
- [ ] Review Activity Log periodically
- [ ] Create app passwords for API access (not user password)
- [ ] Keep WordPress updated
- [ ] Keep plugins updated

---

## 📞 Support

### Getting Help

1. Check the **Dashboard** (WebWeaver menu)
2. Read the included **guides**
3. Review **Activity Log** for errors
4. Check **Settings** page

### Verify Installation

```bash
# Via WordPress REST API
curl https://yoursite.com/wp-json/wp-mcp/v1/tools

# Should return tools manifest with available features
```

---

## 🎉 Next Steps

1. ✅ Activate plugin
2. ✅ Enable permalinks
3. ✅ Configure user permissions
4. ✅ Visit Dashboard
5. ✅ (Optional) Set up MCP for Claude

---

**Version:** 0.1.0  
**Release Date:** March 3, 2026  
**Status:** Production Ready ✅

**Support Files:**
- webweaver-0.1.0.zip (53 KB)
- SHA256: e869fd0daf10e999ceec9071a17301d6240887cf7189ea86232cdfe1342134ac
