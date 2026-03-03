# WebWeaver Repository Structure

## 📁 Root Directory

```
webweaver/
├── wp-webweaver.php              ← MAIN PLUGIN FILE
├── composer.json                 ← Dependencies
├── README-ROOT.md                ← START HERE
├── .gitignore                    ← Git ignore rules
│
├── 🔧 PLUGIN CODE
├── includes/                     Plugin functionality
│   ├── admin/
│   │   ├── menu.php              Admin menu & pages
│   │   └── assets.php            Scripts & styles
│   ├── api/
│   │   ├── routes.php            REST API routes
│   │   ├── auth/
│   │   │   └── authenticate.php  Authentication (NEW: Multi-method)
│   │   └── endpoints/
│   │       ├── tools.php         Tools manifest
│   │       ├── posts.php         Post endpoints
│   │       ├── media.php         Media upload
│   │       └── activitylog.php   Activity log
│   ├── builders/
│   │   ├── registry.php          Builder detection
│   │   ├── gutenberg.php         Gutenberg support
│   │   ├── elementor.php         Elementor support
│   │   └── divi.php              Divi support
│   ├── security/
│   │   ├── policy.php            Security policies
│   │   ├── meta.php              Security metadata
│   │   └── ratelimit.php         Rate limiting
│   ├── logging/
│   │   └── activity.php          Activity logging
│   └── database/
│       └── install.php           Database tables
│
├── templates/                    Admin templates
│   └── admin/
│       ├── dashboard.php         Main dashboard
│       ├── settings.php          Settings page
│       └── activity.php          Activity log page
│
├── assets/                       Static files
│   └── admin/
│       ├── admin.css             Styles
│       └── admin.js              Scripts
│
├── 📚 DOCUMENTATION (.docs/)
├── .docs/
│   ├── README.md                 Overview
│   ├── QUICK_START.md            5-minute setup
│   ├── INSTALL-v0.2.0.md         Installation guide
│   ├── MANUS_SETUP.md            Manus.im integration
│   ├── MCP_CLAUDE_SETUP.md       Claude setup
│   ├── SETUP_GUIDE.md            Complete configuration
│   ├── DEPLOYMENT.md             Production deployment
│   ├── ARCHITECTURE.md           Technical architecture
│   ├── EXAMPLES.md               Usage examples
│   ├── STRUCTURE.md              (Old - see this file)
│   ├── SUMMARY.md                Feature summary
│   └── BUILD_SUMMARY.txt         Build notes
│
├── 🛠️ DEVELOPMENT TOOLS (.tools/)
├── .tools/
│   ├── create-mcp-auth.php       Generate API keys
│   ├── validate-plugin.php       Validate installation
│   ├── check-permissions.php     Check user permissions
│   ├── grant-permissions.php     Grant missing permissions
│   ├── fix-rest-api.php          Fix REST API issues
│   ├── fix-htaccess.php          Fix .htaccess
│   ├── test-mcp-direct.php       Test API directly
│   ├── test-mcp-tools.php        Test tools endpoint
│   ├── test-auth.php             Test authentication
│   ├── test-mcp.sh               Shell test suite
│   ├── test-setup.sh             Automated setup test
│   ├── mcp-server.js             MCP server wrapper
│   ├── docker-compose.yml        Local dev environment
│   └── .releaserc                Release configuration
│
├── 📦 RELEASES (releases/)
└── releases/
    ├── webweaver-0.2.0.zip       Current version (49 KB)
    ├── webweaver-0.2.0.sha256    Checksum
    └── webweaver-0.1.0.zip       Previous version
```

---

## 🎯 How to Navigate

### 👤 If you want to...

#### Install the plugin
1. Go to: `releases/`
2. Download: `webweaver-0.2.0.zip`
3. Read: `.docs/INSTALL-v0.2.0.md`

#### Use with Manus.im
1. Read: `.docs/MANUS_SETUP.md`
2. Run: `.tools/create-mcp-auth.php`
3. Configure in Manus.im

#### Use with Claude
1. Read: `.docs/MCP_CLAUDE_SETUP.md`
2. Set up MCP wrapper (`.tools/mcp-server.js`)

#### Configure for production
1. Read: `.docs/DEPLOYMENT.md`
2. Follow security checklist
3. Test with `.tools/validate-plugin.php`

#### Understand the code
1. Read: `.docs/ARCHITECTURE.md`
2. Explore: `includes/` folder
3. Review: Code comments

#### Fix issues
1. Check: `.docs/[relevant-guide].md`
2. Run: `.tools/validate-plugin.php`
3. Check: WordPress Activity Log

---

## 📦 Directory Details

### `wp-webweaver.php` (1 KB)
**The main plugin file**
- Plugin header (name, version, author)
- Defines constants
- Loads autoloader
- Registers hooks

### `includes/` (15 KB)
**All plugin functionality**

- `autoloader.php` - Class autoloading
- `plugin.php` - Main plugin class
- `admin/` - Dashboard, menu, assets
- `api/` - REST routes, endpoints, auth
- `builders/` - Builder detection & support
- `security/` - Auth, rate limiting, policies
- `logging/` - Activity logging
- `database/` - Table creation

### `templates/` (13 KB)
**Admin interface pages**

- `admin/dashboard.php` - Main dashboard (NEW: Better UI)
- `admin/settings.php` - Configuration page
- `admin/activity.php` - Activity log viewer

### `assets/` (3 KB)
**Frontend styling**

- `admin.css` - Admin styles
- `admin.js` - Admin scripts

### `.docs/` (110 KB)
**Documentation**

8 complete guides:
- Installation
- Quick start (5 min)
- Manus.im setup (5 min)
- Claude setup (10 min)
- Complete configuration (20 min)
- Production deployment (20 min)
- Technical architecture (15 min)
- Code examples (10 min)

### `.tools/` (30 KB)
**Development utilities**

Setup & testing:
- `create-mcp-auth.php` - Generate API keys
- `validate-plugin.php` - Check installation
- `fix-rest-api.php` - Fix REST API
- `test-mcp-tools.php` - Test endpoints
- `docker-compose.yml` - Local dev

### `releases/` (120 KB)
**Plugin packages**

- `webweaver-0.2.0.zip` - Current (49 KB)
- `webweaver-0.1.0.zip` - Previous (53 KB)
- SHA256 checksums

---

## 🔄 Development Workflow

### Adding a feature
1. Modify code in `includes/`
2. Test with `.tools/test-*.php`
3. Update docs in `.docs/`

### Creating a release
1. Test everything
2. Update version in `wp-webweaver.php`
3. Run `.tools/validate-plugin.php`
4. Create ZIP from plugin files
5. Add to `releases/` folder
6. Calculate SHA256 checksum

### Setting up dev environment
1. Extract `releases/webweaver-0.2.0.zip`
2. Run `.tools/docker-compose.yml`
3. WordPress available at http://localhost:8888

---

## 📊 File Organization Benefits

| Aspect | Benefit |
|--------|---------|
| Separate `.docs/` | Find guides easily, not cluttered root |
| Separate `.tools/` | Keep dev tools organized and out of plugin |
| `releases/` folder | Easy to find downloadable packages |
| `includes/` structure | Mirrors class namespace for auto-loading |
| `templates/` folder | Admin UI isolated from logic |
| `README-ROOT.md` | Quick overview of everything |

---

## 🎯 Quick Links by Role

### **Users Installing Plugin**
```
releases/webweaver-0.2.0.zip
↓
.docs/INSTALL-v0.2.0.md
↓
WordPress Admin > Plugins > Upload
```

### **Manus.im Integration**
```
.docs/MANUS_SETUP.md
↓
.tools/create-mcp-auth.php
↓
Manus.im Configuration
```

### **Developers**
```
.docs/ARCHITECTURE.md
↓
includes/api/
↓
.tools/test-mcp-tools.php
```

### **DevOps/Deployment**
```
.docs/DEPLOYMENT.md
↓
Production Checklist
↓
.tools/validate-plugin.php
```

---

## ✅ Quality Assurance

- **Organized:** Clear folder structure
- **Documented:** 8 comprehensive guides
- **Tested:** Tools for validation
- **Packaged:** Ready-to-install ZIP files
- **Versioned:** Multiple releases available
- **Clean:** No clutter in root directory

---

## 🚀 Next Steps

1. **Review:** Read `README-ROOT.md` (this directory)
2. **Install:** Download from `releases/`
3. **Configure:** Follow `.docs/INSTALL-v0.2.0.md`
4. **Integrate:** Follow `.docs/MANUS_SETUP.md`
5. **Enjoy:** Start using the plugin!

---

**Organization Complete!** ✅

Everything is properly organized:
- 📁 Plugin code in `includes/`
- 📚 Docs in `.docs/`
- 🛠️ Tools in `.tools/`
- 📦 Releases in `releases/`
- 🧭 Navigation guide in `README-ROOT.md`
