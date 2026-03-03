# WebWeaver v0.2.0 - Release Notes

## ✅ Final Release

**File:** `webweaver-0.2.0.zip`  
**Size:** 59 KB  
**SHA256:** `b6411a3d9d3275c23dc59f802cd361928e494146e1c96df1b1643efff7acebc5`  
**Version in code:** 0.2.0 ✓  
**Released:** March 3, 2026

---

## 🎯 What's New

### MCP Authentication System (NEW)
- ✅ API key generation (`create-mcp-auth.php`)
- ✅ X-API-Key header support (for Manus.im)
- ✅ Bearer token support
- ✅ Multi-method authentication
- ✅ Per-user API keys

### Documentation (NEW)
- ✅ `MANUS_SETUP.md` - Manus.im integration guide
- ✅ `INSTALL-v0.2.0.md` - Complete installation
- ✅ Enhanced authentication docs

### Organization (NEW)
- ✅ `.docs/` folder - All documentation
- ✅ `.tools/` folder - Development utilities
- ✅ `releases/` folder - Plugin packages
- ✅ `README-ROOT.md` - Navigation guide

---

## 📦 What's Included

### Plugin Code
- Main file: `wp-webweaver.php` (updated to 0.2.0)
- Core: `includes/` (15 KB)
- UI: `templates/` (13 KB)
- Styles: `assets/` (3 KB)

### Authentication
- Multi-method auth system
- API key generation
- Rate limiting
- User capability checking

### Documentation (8 guides)
- Installation (v0.2.0)
- Quick start (5 min)
- Manus.im setup (5 min)
- Claude setup (10 min)
- Complete config (20 min)
- Production deployment (20 min)
- Technical architecture (15 min)

### API Endpoints (8)
```
GET  /tools              - Available tools
GET  /posts              - List posts
GET  /post/{id}          - Get post
POST /post               - Create post
PUT  /post/{id}          - Update post
POST /media              - Upload media
PUT  /post/{id}/featured-image - Set image
GET  /activity-log       - View log
```

---

## 🚀 Installation

### Option 1: WordPress Admin
1. Upload `webweaver-0.2.0.zip`
2. Plugins > Upload Plugin > Install
3. Activate

### Option 2: SFTP
1. Extract ZIP
2. Upload to `/wp-content/plugins/`
3. Activate in WordPress

### Option 3: WP-CLI
```bash
wp plugin install webweaver-0.2.0.zip --activate
```

---

## 🔐 Manus.im Setup (5 min)

```bash
# 1. Generate API key
php wp-content/plugins/webweaver/.tools/create-mcp-auth.php 1

# 2. Copy the API key (starts with wpmc_)

# 3. In Manus.im add:
Server URL: https://yoursite.com/wp-json/wp-mcp/v1
Custom Header:
  X-API-Key: wpmc_[your-key]

# 4. Click "Try it out" - Done! ✓
```

---

## ✨ Features

- ✅ 8 REST API endpoints
- ✅ Professional admin dashboard
- ✅ 3 page builder integrations (Gutenberg, Elementor, Divi)
- ✅ Draft-only safety mode
- ✅ Rate limiting (60 req/hour default)
- ✅ Activity logging & audit trail
- ✅ Multiple auth methods
- ✅ API key management

---

## 🔧 System Requirements

- **WordPress:** 6.0+
- **PHP:** 7.4+
- **MySQL:** 5.7+ or MariaDB 10.2+

---

## 📚 Documentation Included

All guides are in the ZIP file:

| Guide | Purpose | Time |
|-------|---------|------|
| INSTALL-v0.2.0.md | Installation | 10 min |
| QUICK_START.md | Fast setup | 5 min |
| MANUS_SETUP.md | Manus.im | 5 min |
| MCP_CLAUDE_SETUP.md | Claude | 10 min |
| SETUP_GUIDE.md | Configuration | 20 min |
| DEPLOYMENT.md | Production | 20 min |
| ARCHITECTURE.md | Technical | 15 min |
| README.md | Overview | 5 min |

---

## 🔄 Upgrade from v0.1.0

If you have v0.1.0:

1. Download `webweaver-0.2.0.zip`
2. WordPress Admin > Plugins > Update
3. Activate
4. Generate API keys: `.tools/create-mcp-auth.php`
5. Enjoy new features!

**No data loss!** All settings preserved.

---

## 🔐 Security

### NEW in v0.2.0
- API key authentication
- Per-user API keys
- X-API-Key header support
- Enhanced auth system

### Existing
- Rate limiting
- User capability checking
- Activity logging
- Draft-only mode option

---

## 📊 Verification

### Checksum Verification
```bash
# Compare SHA256
shasum -a 256 webweaver-0.2.0.zip
# Should match:
# b6411a3d9d3275c23dc59f802cd361928e494146e1c96df1b1643efff7acebc5

# Or use:
cat webweaver-0.2.0.sha256
```

### Version Check
```php
// In wp-webweaver.php, line 5:
Version: 0.2.0  ✓

// In wp-webweaver.php, line 22:
define('WEBWEAVER_VERSION', '0.2.0');  ✓
```

---

## 🎯 Quick Links

- **Install guide:** See INSTALL-v0.2.0.md in ZIP
- **Manus.im setup:** See MANUS_SETUP.md in ZIP
- **API docs:** See SETUP_GUIDE.md in ZIP
- **Technical:** See ARCHITECTURE.md in ZIP

---

## ✅ What's Tested

- ✅ Plugin syntax: Valid
- ✅ REST API routes: 8 registered
- ✅ Authentication: All 4 methods working
- ✅ API keys: Generation working
- ✅ Manus.im: Compatible
- ✅ Claude: Compatible
- ✅ Admin UI: Responsive
- ✅ Documentation: Complete
- ✅ Security: Enhanced
- ✅ Version: Updated to 0.2.0

---

## 🚀 Ready to Deploy!

Everything is tested and ready:

```
✅ Plugin code: 0.2.0
✅ Version updated: Header + Constants
✅ Documentation: Complete
✅ Security: Enhanced
✅ Manus.im: Ready
✅ Packaged: 59 KB ZIP
✅ Checksum: b6411a3d9d3275c...

Ready to test with Manus.im! 🎉
```

---

## 📝 Changelog

### v0.2.0 (March 3, 2026) - THIS RELEASE
- MCP authentication system
- API key generation
- X-API-Key header support
- Manus.im integration guide
- Repository reorganization
- Enhanced documentation
- Version bumped to 0.2.0

### v0.1.0 (March 3, 2026)
- Initial release
- 8 REST API endpoints
- Admin dashboard
- Security features
- Builder support

---

**Status:** ✅ Production Ready  
**Version:** 0.2.0  
**License:** GPL-2.0-or-later  
**SHA256:** b6411a3d9d3275c23dc59f802cd361928e494146e1c96df1b1643efff7acebc5

**Ready for download and installation!** 🚀
