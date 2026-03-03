# MCP Connection Helper - In-Plugin Setup Tool

The plugin now includes a built-in **MCP Connection Helper** that makes it easy for users to connect to MCP clients like Manus.im and Claude.

## 🎯 What It Does

The MCP Connection Helper provides:

- ✅ **Auto-generated Base64 headers** - No manual encoding needed
- ✅ **API Key generation** - One-click API key creation
- ✅ **Copy-to-clipboard buttons** - Easy copying
- ✅ **Step-by-step guides** - For each MCP client
- ✅ **Multiple auth methods** - X-API-Key, Bearer, Basic Auth
- ✅ **API key management** - View, generate, revoke keys

## 🚀 Accessing the Tool

### In WordPress Admin

1. Go to **WebWeaver > MCP Connection**
2. Choose your MCP client from tabs:
   - **Manus.im** (recommended)
   - **Claude**
   - **Other MCP**
   - **API Keys**

## 📋 Features by Tab

### Tab 1: Manus.im

**Recommended Method: X-API-Key**

1. Click **"Generate API Key"** button
2. Copy the generated key
3. Add to Manus.im:
   - Server URL: Pre-filled
   - Custom Header: X-API-Key
   - Value: [paste your key]
4. Done! ✓

**Alternative Method: Basic Auth**

- Automatically generates base64 header
- Shows how to encode username:password
- Instructions included

### Tab 2: Claude

**Option A: Direct REST API**
- Use X-API-Key method (from Tab 1)
- Copy example curl command
- Paste in Claude

**Option B: MCP Server Wrapper**
- Run MCP server locally
- Connect Claude to localhost:3000
- Instructions provided

### Tab 3: Other MCP

**All Supported Methods:**
1. X-API-Key (RECOMMENDED)
2. Bearer Token
3. Basic Auth
4. Session/Cookie

Each with:
- Explanation
- Header format
- Example usage

### Tab 4: API Keys

**Manage Your Keys**
- View all API keys
- Generate new keys
- Revoke old keys
- Shows key creation time

## 💡 How It Works

### Base64 Encoding (Automatic)

User enters:
```
Username: admin
Password: mypassword
```

Plugin generates:
```
Authorization: Basic YWRtaW46bXlwYXNzd29yZA==
```

**No manual encoding needed!**

### API Key Generation (One Click)

1. Click "Generate API Key" button
2. PHP backend generates secure key
3. Key stored in WordPress options
4. Displayed for copying

```
wpmc_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6...
```

## 🔐 Security Features

### X-API-Key Method
- ✅ Separate from user password
- ✅ Can be revoked independently
- ✅ Unique per client
- ✅ Easy to rotate

### Per-User Management
- Each user can have multiple API keys
- Keys are user-specific
- Revoke without affecting other users
- Audit trail in Activity Log

### Display Protection
- Full keys shown only once
- Obfuscated in key management table
- Copy buttons for easy use
- Never logged in plaintext

## 📱 User Experience

### For Non-Technical Users

1. Open WebWeaver > MCP Connection
2. Click "Generate API Key"
3. Copy the key
4. Paste in their MCP client
5. Done!

### For Developers

- API documentation provided
- All header formats shown
- Example curl commands included
- Link to technical docs

### Copy-to-Clipboard

- One-click copy buttons
- Visual feedback ("Copied! ✓")
- Works on all modern browsers
- No popup notifications

## 🎓 Interface Design

### Responsive Layout
- Works on desktop and tablet
- Mobile-friendly buttons
- Readable code boxes
- Proper spacing

### Color-Coded Info
- 🟢 Green: Recommended methods
- 🔵 Blue: Section headers
- 🟡 Yellow: Warnings
- 🟢 Green: Success messages

### Tab Navigation
- Easy switching between sections
- Active tab highlighted
- One tab open at a time
- Remembers last view (in future version)

## 🛠️ Developer Integration

### For Developers Building on WebWeaver

The helper uses the same `Authenticate` class:

```php
// Generate API key
$api_key = \WP_MCP_Connector\API\Auth\Authenticate::generate_api_key($user_id);

// Verify API key
$user = \WP_MCP_Connector\API\Auth\Authenticate::verify_api_key($api_key);

// Revoke API key
\WP_MCP_Connector\API\Auth\Authenticate::revoke_api_key($api_key);
```

## 📝 Code Location

New file:
```
includes/admin/mcp-connection.php
```

Registered in:
```
includes/plugin.php (line 27)
```

Auth support in:
```
includes/api/auth/authenticate.php
```

## 🚀 For End Users: Step-by-Step Manus.im Setup

### 5-Minute Setup

1. **In WordPress:**
   - Go to WebWeaver > MCP Connection
   - Click "Generate API Key"
   - Copy the key (shown in green box)

2. **In Manus.im:**
   - Open MCP Configuration
   - Server URL: `https://yoursite.com/wp-json/wp-mcp/v1`
   - Add Custom Header:
     - Name: `X-API-Key`
     - Value: [paste your key]
   - Click Save

3. **Test:**
   - Click "Try it out"
   - Should show "Connected ✓"
   - Tools available!

**Done!** 🎉

## 🔄 Future Enhancements

Potential additions:
- [ ] QR code for mobile setup
- [ ] Test connection button (in-plugin)
- [ ] Key expiration dates
- [ ] Usage statistics per key
- [ ] Auto-rotation schedules
- [ ] Two-factor authentication
- [ ] OAuth2 flow
- [ ] Webhooks for MCP events

## 📞 Support

### If User Gets Stuck

1. Check "Other MCP" tab for auth methods
2. Review step-by-step Manus guide
3. Check Activity Log for errors
4. See `.docs/MANUS_SETUP.md` for details

---

**Status:** ✅ Built-in Feature  
**Version:** 0.2.0+  
**Location:** WebWeaver > MCP Connection  
**Audience:** All users (technical and non-technical)
