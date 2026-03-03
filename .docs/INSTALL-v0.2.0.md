# WebWeaver v0.2.0 - Installation & Manus.im Setup

## 🎯 For Manus.im Users - Quick Setup (5 min)

### Step 1: Download & Install Plugin

1. **Download** `webweaver-0.2.0.zip` from releases
2. **WordPress Admin > Plugins > Upload Plugin**
3. **Select** the ZIP file
4. **Click** Install Now
5. **Click** Activate

### Step 2: Generate API Key

**SSH into your server and run:**

```bash
cd /path/to/wordpress
php wp-content/plugins/webweaver/create-mcp-auth.php 1
```

**You'll see:**
```
✅ API Key Generated!
🔑 API Key: wpmc_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6...
```

**Save this key!** ⚠️

### Step 3: Configure Manus.im

**In Manus.im MCP configuration:**

```
Server Name:      Your Site Name
Transport Type:   HTTP
Server URL:       https://yoursite.com/wp-json/wp-mcp/v1

Custom Headers:   [+ Add custom header]
  Header Name:    X-API-Key
  Header Value:   wpmc_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6...

Click: Save
```

### Step 4: Test Connection

1. **Click "Try it out"** in Manus.im
2. Should see **"✅ Connected"**
3. Tools should appear (6 available)

**Done!** 🎉

---

## 📋 Complete Installation

### Requirements

✅ WordPress 6.0+  
✅ PHP 7.4+  
✅ MySQL 5.7+ or MariaDB 10.2+  
✅ Pretty Permalinks enabled (Settings > Permalinks)  

### Installation Methods

#### Method 1: WordPress Admin (Easiest)

1. Go to **Plugins > Add New**
2. Click **Upload Plugin**
3. Select `webweaver-0.2.0.zip`
4. Click **Install Now**
5. Click **Activate Plugin**

#### Method 2: SFTP Upload

1. Extract `webweaver-0.2.0.zip`
2. Upload `webweaver/` folder to `/wp-content/plugins/`
3. Go to **Plugins** page
4. Click **Activate** on WebWeaver

#### Method 3: WP-CLI (Advanced)

```bash
wp plugin install webweaver-0.2.0.zip --activate
```

### Post-Installation Configuration

#### 1. Enable Pretty Permalinks (Required!)

1. Go to **Settings > Permalinks**
2. Select **"Post name"** (not "Plain")
3. Click **Save Changes**

#### 2. Set User Permissions

1. Go to **Users > Your User**
2. Change role to **"Editor"** (if not admin)
3. Click **Update User**

Required capabilities:
- Read Posts ✓
- Edit Posts ✓
- Create Posts ✓
- Publish Posts ✓
- Upload Files ✓

---

## 🔐 MCP Authentication Setup

### Option 1: API Keys (Recommended for Manus.im)

#### Generate API Key

```bash
php wp-content/plugins/webweaver/create-mcp-auth.php [user_id]
```

Example:
```bash
php wp-content/plugins/webweaver/create-mcp-auth.php 1
```

#### Use in Manus.im

```
Header Name:  X-API-Key
Header Value: wpmc_a1b2c3d4e5f6g7h8...
```

#### Create Multiple Keys

```bash
php wp-content/plugins/webweaver/create-mcp-auth.php 1  # Key 1
php wp-content/plugins/webweaver/create-mcp-auth.php 1  # Key 2 (same user)
php wp-content/plugins/webweaver/create-mcp-auth.php 2  # Key for user 2
```

### Option 2: Basic Auth

```
Header Name:  Authorization
Header Value: Basic base64(username:password)
```

Generate header:
```bash
echo -n "admin:yourpassword" | base64
# Output: YWRtaW46eW91cnBhc3N3b3Jk
```

⚠️ **Use app password in production, not user password**

### Option 3: Bearer Token

```
Header Name:  Authorization
Header Value: Bearer wpmc_a1b2c3d4e5f6g7h8...
```

---

## ✅ Verify Installation

### Check Plugin is Active

1. Go to **Plugins** page
2. Look for "WebWeaver"
3. Should show "**Active**"

### Check Dashboard

1. Click **"WebWeaver"** menu
2. Click **"Dashboard"**
3. Should see system status

### Test API

```bash
curl -H "X-API-Key: wpmc_..." \
  https://yoursite.com/wp-json/wp-mcp/v1/tools
```

Should return JSON with available tools.

---

## 🔧 Configuration

### WebWeaver Settings

Go to **WebWeaver > Settings:**

| Setting | Default | Description |
|---------|---------|-------------|
| **Draft-Only Mode** | ON | New posts are drafts |
| **Rate Limit** | 60/hour | API requests per user |
| **Post Types** | post, page | What can be created |
| **HTTPS Required** | OFF | Enforce HTTPS (production) |

### Recommended Settings

**Development:**
- Draft-Only: ON
- Rate Limit: 1000/hour
- HTTPS: OFF

**Production:**
- Draft-Only: ON
- Rate Limit: 60/hour
- HTTPS: ON

---

## 🚀 Using with Manus.im

### Quick Reference

```
Server URL:    https://yoursite.com/wp-json/wp-mcp/v1
Auth Header:   X-API-Key: wpmc_[your-key]
Available:     6 tools (list, get, create, update posts, media)
```

### Available Tools

Once connected to Manus.im:

- 📖 **list_posts** - Get all posts
- 📄 **get_post** - View post details
- ✍️ **create_post** - Create new post
- 🔄 **update_post** - Edit post
- 🖼️ **upload_media** - Upload images
- 🎬 **set_featured_image** - Set featured image

### Example Manus Commands

```
"List my blog posts"
↓
Calls: GET /wp-json/wp-mcp/v1/posts

"Create a post about AI"
↓
Calls: POST /wp-json/wp-mcp/v1/post

"Upload this image"
↓
Calls: POST /wp-json/wp-mcp/v1/media
```

---

## 🐛 Troubleshooting

### "Unauthorized" Error

**Check:**
1. API key is copied completely
2. Header name is exactly: `X-API-Key`
3. Server URL is correct

**Test:**
```bash
curl -H "X-API-Key: wpmc_..." \
  https://yoursite.com/wp-json/wp-mcp/v1/tools
```

Should return JSON (not error).

### "Connection Refused"

**Check:**
1. WordPress site is accessible
2. Server URL is correct
3. No firewall blocking the connection

**Test:**
```bash
curl https://yoursite.com/wp-json/wp-mcp/v1/tools
```

Should return 401 (unauthorized) or tools JSON.

### "404 Not Found"

**Fix:**
1. Go to **Settings > Permalinks**
2. Change to **"Post name"**
3. Click **Save**

Then test again.

### "Manus can't access my tools"

**Check:**
1. Plugin is activated
2. API key is correct
3. Server URL ends with `/wp-mcp/v1`
4. HTTPS certificate is valid (if using HTTPS)

**Debug:**
1. Check **WebWeaver > Dashboard** for status
2. Check **WebWeaver > Activity Log** for errors
3. Review WordPress error logs

---

## 🔒 Security Checklist

Before going live:

- [ ] Enable HTTPS
- [ ] Set user role to "Editor" minimum
- [ ] Generate strong API keys
- [ ] Store API keys securely (env variables)
- [ ] Disable password login for API users
- [ ] Review Activity Log regularly
- [ ] Set appropriate rate limits

---

## 📚 Documentation

Included in plugin:

- **QUICK_START.md** - 5-minute setup
- **SETUP_GUIDE.md** - Complete configuration
- **MANUS_SETUP.md** - Manus.im integration
- **DEPLOYMENT.md** - Production deployment
- **ARCHITECTURE.md** - Technical details
- **MCP_CLAUDE_SETUP.md** - Claude integration

---

## 🎯 Next Steps

1. ✅ Install plugin
2. ✅ Enable pretty permalinks
3. ✅ Set user permissions
4. ✅ Generate API key
5. ✅ Configure Manus.im
6. ✅ Test connection
7. ✅ Start using!

---

## 💬 Support

### Dashboard

Visit **WebWeaver > Dashboard** for:
- System status
- Your capabilities
- Quick links
- Setup guides

### Activity Log

Visit **WebWeaver > Activity Log** for:
- All API calls
- Who accessed what
- When things happened
- Error tracking

### Documentation

All guides included in plugin folder.

---

**Version:** 0.2.0  
**Updated:** March 3, 2026  
**Status:** Production Ready ✅
