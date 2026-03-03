# Connecting WebWeaver to Manus.im (MCP)

## Quick Setup (2 minutes)

### Step 1: Generate API Key

**Run this command on your server:**

```bash
cd /path/to/wordpress
php wp-content/plugins/webweaver/create-mcp-auth.php 1
```

You'll get output like:
```
✅ API Key Generated!
🔑 API Key: wpmc_a1b2c3d4e5f6g7h8...
```

**Save this API key securely!** ⚠️

### Step 2: Configure Manus.im

In Manus.im MCP configuration dialog:

1. **Server Name:** `SuperJunaid Site` (or your site name)
2. **Transport Type:** `HTTP`
3. **Server URL:** `https://yourdomain.com/wp-json/wp-mcp/v1`
4. **Custom Headers:** Click "+ Add custom header"
   - **Header Name:** `X-API-Key`
   - **Header Value:** `wpmc_a1b2c3d4e5f6g7h8...` (your key from Step 1)
5. Click **Save**

### Step 3: Test It

Click **"Try it out"** in Manus.im

You should see:
```
✅ Connected
Tools available: 6+
```

---

## Authentication Methods

WebWeaver supports 3 auth methods for MCP:

### Method 1: X-API-Key (Recommended) ⭐

**Best for:** Manus.im and dedicated MPC clients

```
Header Name:  X-API-Key
Header Value: wpmc_a1b2c3d4e5f6g7h8...
```

**In Manus.im:**
- Add custom header
- Set Header Name: `X-API-Key`
- Set Header Value: Your API key
- Save

### Method 2: Basic Auth

**Best for:** Testing or simple integrations**

```
Header Name:  Authorization
Header Value: Basic base64(username:password)
```

**Example:**
```bash
# Generate header
echo -n "admin:wordpress" | base64
# Output: YWRtaW46d29yZHByZXNz

# Use in Manus.im:
Header Name:  Authorization
Header Value: Basic YWRtaW46d29yZHByZXNz
```

⚠️ **Note:** Use app password, not user password in production

### Method 3: Bearer Token

**Best for:** API-only access**

```
Header Name:  Authorization
Header Value: Bearer wpmc_a1b2c3d4e5f6g7h8...
```

---

## Generate API Keys

### Create New API Key

```bash
php create-mcp-auth.php [user_id]
```

Example:
```bash
php create-mcp-auth.php 1    # For admin user
php create-mcp-auth.php 5    # For user ID 5
```

### Revoke API Key (Manual)

1. Go to **WordPress Admin > Users**
2. Edit the user
3. Delete API key from user metadata (if dashboard added)
4. Or contact your admin

---

## Manus.im Configuration Complete

### Your Config:

```json
{
  "name": "SuperJunaid Site",
  "transport": "HTTP",
  "url": "https://yourdomain.com/wp-json/wp-mcp/v1",
  "headers": {
    "X-API-Key": "wpmc_a1b2c3d4e5f6g7h8..."
  }
}
```

### Available Tools:

Once connected, Manus.im can:
- 📖 List your WordPress posts
- ✍️ Create new posts
- 🖼️ Upload images
- 🎬 Set featured images
- 🔄 Update posts
- 📊 View activity log

---

## Troubleshooting

### "Unauthorized" Error

**Check:**
1. API key is correct and copied fully
2. Header name is exactly: `X-API-Key`
3. Server URL ends with: `/wp-json/wp-mcp/v1`
4. HTTPS is enabled (if using https://)

### "Connection refused"

**Check:**
1. Server URL is correct
2. WordPress site is accessible
3. WebWeaver plugin is activated
4. Permalinks are set to "Post name"

**Fix:**
```bash
wp option get siteurl
wp option get permalink_structure
```

### "No tools available"

**Check:**
1. Click dashboard > WebWeaver > Dashboard
2. Check system status
3. Verify user has Editor role
4. Review Activity Log for errors

---

## Security Best Practices

### ✅ DO:
- Generate unique API keys per client
- Use HTTPS for production
- Store API keys securely (env variables)
- Rotate keys periodically
- Review Activity Log

### ❌ DON'T:
- Share API keys in code
- Commit keys to version control
- Use user passwords
- Expose keys in URLs
- Log keys to files

---

## Multiple Manus.im Instances

If you have multiple MCP clients:

```bash
# Generate one key per client
php create-mcp-auth.php 1  # Key for Client 1
php create-mcp-auth.php 1  # Key for Client 2
php create-mcp-auth.php 2  # Key for different user
```

Each gets its own API key.

---

## API Key Format

Generated keys have this format:
```
wpmc_[64 random hex characters]
```

Example:
```
wpmc_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6...
```

---

## Next Steps

1. ✅ Generate API key: `php create-mcp-auth.php 1`
2. ✅ Add to Manus.im (Custom header: X-API-Key)
3. ✅ Test connection (Try it out)
4. ✅ Start using: Ask Manus to create/edit posts

---

## Command Reference

```bash
# Generate API key
php create-mcp-auth.php 1

# Test API connection
curl -H "X-API-Key: wpmc_..." https://yoursite.com/wp-json/wp-mcp/v1/tools

# Check WordPress users
wp user list

# Reset password if needed
wp user update 1 --prompt=user_pass
```

---

**Version:** 0.1.0  
**Updated:** March 3, 2026  
**Status:** Ready for Production ✅
