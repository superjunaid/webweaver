# WebWeaver - AI-powered WordPress Content Creation Plugin

**Production-ready WordPress plugin with REST API, MCP protocol support, and Manus.im/Claude integration.**

---

## 🚀 Quick Start

### Install

1. **Download:** `webweaver-0.2.0.zip` from `releases/`
2. **WordPress Admin > Plugins > Upload**
3. **Activate**

### Setup for Manus.im (5 min)

```bash
# Generate API key
php .tools/create-mcp-auth.php 1

# Add to Manus.im:
# Server: https://yoursite.com/wp-json/wp-mcp/v1
# Header: X-API-Key: wpmc_[your-key]
```

---

## 📁 Repository Structure

```
webweaver/
├── wp-webweaver.php          ← Main plugin file
├── includes/                  ← Plugin code
│   ├── admin/                 ├─ Dashboard & Settings
│   ├── api/                   ├─ REST endpoints
│   ├── builders/              ├─ Page builder support
│   ├── security/              ├─ Auth & rate limiting
│   ├── logging/               └─ Activity log
├── templates/                 ← Admin pages
├── assets/                    ← CSS, JS
├── composer.json              ← Dependencies
│
├── .docs/                     ← Documentation
│   ├── README.md              ├─ Overview
│   ├── QUICK_START.md         ├─ 5-min setup
│   ├── INSTALL-v0.2.0.md      ├─ Installation
│   ├── MANUS_SETUP.md         ├─ Manus.im integration
│   ├── MCP_CLAUDE_SETUP.md    ├─ Claude integration
│   ├── SETUP_GUIDE.md         ├─ Complete config
│   ├── DEPLOYMENT.md          ├─ Production guide
│   ├── ARCHITECTURE.md        └─ Technical details
│
├── .tools/                    ← Development tools
│   ├── create-mcp-auth.php    ├─ Generate API keys
│   ├── fix-rest-api.php       ├─ Fix REST API issues
│   ├── validate-plugin.php    ├─ Validate installation
│   ├── test-mcp-tools.php     ├─ Test API endpoints
│   ├── docker-compose.yml     └─ Local dev environment
│
└── releases/                  ← Plugin packages
    ├── webweaver-0.2.0.zip    ├─ Current version
    └── webweaver-0.1.0.zip    └─ Previous version
```

---

## 📦 Latest Release

**Version:** 0.2.0  
**File:** `releases/webweaver-0.2.0.zip` (49 KB)  
**SHA256:** `aef12a8d5c2990a4066c9af83458c1b7b16becff29d684af7e0c7ef0853187ca`

### What's New in v0.2.0

- ✅ API Key authentication (for Manus.im)
- ✅ X-API-Key header support
- ✅ Bearer token support
- ✅ Manus.im integration guide
- ✅ Multi-client support

---

## ✨ Features

### Core
- **8 REST API Endpoints** - Full post management
- **Admin Dashboard** - System status & settings
- **Builder Support** - Gutenberg, Elementor, Divi
- **Security** - Auth, rate limiting, logging
- **Activity Log** - Full audit trail

### MCP Integration
- **X-API-Key Authentication** - For Manus.im
- **Bearer Token** - OAuth-style
- **Basic Auth** - user:password
- **Session Auth** - WordPress login
- **API Key Generation** - Per-user keys

### Documentation
- **7 Complete Guides** - Setup, deployment, integration
- **Tool Scripts** - Setup helpers
- **Architecture Docs** - Technical reference

---

## 🎯 For Different Users

### End Users (Install & Use)
1. Read: `.docs/INSTALL-v0.2.0.md`
2. Install plugin
3. Configure settings
4. Done!

### Manus.im Users
1. Read: `.docs/MANUS_SETUP.md`
2. Install plugin
3. Generate API key: `.tools/create-mcp-auth.php`
4. Add to Manus.im
5. Start using!

### Developers
1. Read: `.docs/ARCHITECTURE.md`
2. Explore: `includes/`
3. Use: `.tools/` for testing
4. Extend as needed

### DevOps/Production
1. Read: `.docs/DEPLOYMENT.md`
2. Configure: `HTTPS`, database, security
3. Monitor: Activity Log in WordPress
4. Maintain: Keep updated

---

## 🚀 Installation Methods

### Method 1: WordPress Admin (Easiest)
```
1. Plugins > Add New > Upload Plugin
2. Select: releases/webweaver-0.2.0.zip
3. Install Now > Activate
```

### Method 2: SFTP
```bash
unzip releases/webweaver-0.2.0.zip
scp -r webweaver/ user@host:/wp-content/plugins/
# Then activate in WordPress
```

### Method 3: WP-CLI
```bash
wp plugin install releases/webweaver-0.2.0.zip --activate
```

---

## 🔐 Authentication

### For Manus.im
```
X-API-Key: wpmc_[api-key]
```

### For Claude MCP
```
Authorization: Basic base64(user:pass)
```

### For Custom Clients
- X-API-Key
- Bearer token
- Basic auth
- Session/cookie

---

## 📖 Documentation

| Guide | For | Time |
|-------|-----|------|
| `.docs/README.md` | Overview | 5 min |
| `.docs/QUICK_START.md` | Fast setup | 5 min |
| `.docs/INSTALL-v0.2.0.md` | Installation | 10 min |
| `.docs/MANUS_SETUP.md` | Manus.im | 5 min |
| `.docs/MCP_CLAUDE_SETUP.md` | Claude | 10 min |
| `.docs/SETUP_GUIDE.md` | Complete config | 20 min |
| `.docs/DEPLOYMENT.md` | Production | 20 min |
| `.docs/ARCHITECTURE.md` | Technical | 15 min |

---

## 🛠️ Development Tools

All in `.tools/` directory:

| Tool | Purpose |
|------|---------|
| `create-mcp-auth.php` | Generate API keys |
| `validate-plugin.php` | Check installation |
| `fix-rest-api.php` | Fix REST API issues |
| `test-mcp-tools.php` | Test API endpoints |
| `docker-compose.yml` | Local dev environment |

### Run Tests
```bash
cd .tools/
php validate-plugin.php
php test-mcp-tools.php
```

### Start Dev Environment
```bash
cd .tools/
docker-compose up
```

---

## 🔧 System Requirements

- **WordPress:** 6.0+
- **PHP:** 7.4+
- **MySQL:** 5.7+ or MariaDB 10.2+

---

## 📊 API Endpoints

```
GET  /wp-json/wp-mcp/v1/tools              ← Get available tools
GET  /wp-json/wp-mcp/v1/posts              ← List posts
GET  /wp-json/wp-mcp/v1/post/{id}          ← Get post
POST /wp-json/wp-mcp/v1/post               ← Create post
PUT  /wp-json/wp-mcp/v1/post/{id}          ← Update post
POST /wp-json/wp-mcp/v1/media              ← Upload media
PUT  /wp-json/wp-mcp/v1/post/{id}/featured-image ← Set featured image
```

---

## ✅ Verification

### Is it installed correctly?
```bash
# Check plugin activation
wp plugin list | grep webweaver

# Check REST API
curl https://yoursite.com/wp-json/wp-mcp/v1/tools \
  -H "Authorization: Basic [auth-header]"
```

### Troubleshooting
1. Check: `.docs/INSTALL-v0.2.0.md` (FAQ section)
2. Review: `.docs/MANUS_SETUP.md` (for Manus)
3. Run: `.tools/validate-plugin.php`
4. Check: WordPress Activity Log

---

## 🚀 Next Steps

### For Installation
1. Download: `releases/webweaver-0.2.0.zip`
2. Read: `.docs/INSTALL-v0.2.0.md`
3. Install via WordPress Admin
4. Done!

### For Manus.im
1. Read: `.docs/MANUS_SETUP.md`
2. Run: `.tools/create-mcp-auth.php 1`
3. Add to Manus.im
4. Test & enjoy!

### For Development
1. Review: `.docs/ARCHITECTURE.md`
2. Explore: `includes/` folder
3. Modify as needed
4. Test with `.tools/`

---

## 📞 Support

### Quick Help
- **Setup issues:** See `.docs/INSTALL-v0.2.0.md`
- **Manus.im issues:** See `.docs/MANUS_SETUP.md`
- **API issues:** Run `.tools/validate-plugin.php`
- **Production issues:** See `.docs/DEPLOYMENT.md`

### Debugging
1. Check WordPress error log
2. Review Activity Log in Dashboard
3. Run validation tool
4. Check documentation

---

## 📝 Version History

### v0.2.0 (Current) - March 3, 2026
- MCP authentication system
- API key generation
- X-API-Key header support
- Manus.im integration

### v0.1.0 - March 3, 2026
- Initial release
- 8 REST API endpoints
- Admin dashboard
- Security features

---

## 🎓 Learn More

### Quick References
- **File structure:** This README
- **Installation:** `.docs/INSTALL-v0.2.0.md`
- **Manus setup:** `.docs/MANUS_SETUP.md`
- **API reference:** `.docs/SETUP_GUIDE.md`
- **Technical:** `.docs/ARCHITECTURE.md`

### Code Locations
- **Main file:** `wp-webweaver.php`
- **REST API:** `includes/api/`
- **Admin UI:** `includes/admin/` + `templates/`
- **Authentication:** `includes/api/auth/`
- **Security:** `includes/security/`

---

## 🎉 Ready to Deploy!

**Everything is organized and ready:**

```
✅ Plugin: Production-ready
✅ Documentation: Complete (.docs/)
✅ Tools: All available (.tools/)
✅ Releases: Ready to download (releases/)
✅ Code: Clean and organized

Download: releases/webweaver-0.2.0.zip
Install: WordPress Admin
Enjoy! 🚀
```

---

**Status:** ✅ Production Ready  
**Version:** 0.2.0  
**License:** GPL-2.0-or-later  
**Updated:** March 3, 2026
