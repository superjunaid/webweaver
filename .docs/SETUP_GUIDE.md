# WebWeaver Setup Guide

Complete instructions for installing and configuring WebWeaver for AI agents and content creators.

## Table of Contents
1. [Installation](#installation)
2. [Initial Configuration](#initial-configuration)
3. [Authentication Setup](#authentication-setup)
4. [Testing the API](#testing-the-api)
5. [Connecting AI Agents](#connecting-ai-agents)
6. [Troubleshooting](#troubleshooting)

---

## Installation

### Requirements
- WordPress 6.0+
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+

### Steps

1. **Upload Plugin Files**
   ```bash
   # Option 1: Via WordPress Admin
   - Go to Plugins > Add New
   - Upload webweaver.zip
   - Click "Install Now"
   
   # Option 2: Via SFTP
   - Extract webweaver/ to /wp-content/plugins/
   ```

2. **Activate Plugin**
   ```
   - Go to Plugins
   - Find "WebWeaver"
   - Click "Activate"
   ```

3. **Verify Installation**
   ```
   - Go to Admin Dashboard
   - Look for "WebWeaver" menu item
   - Click to view the dashboard
   ```

---

## Initial Configuration

### 1. Configure Settings

Navigate to **WebWeaver > Settings** to adjust:

| Setting | Default | Description |
|---------|---------|-------------|
| **Draft-Only Mode** | ON | Forces new posts to be drafts (recommended) |
| **Rate Limit** | 60/hour | Max API requests per user per hour |
| **Allowed Post Types** | post, page | Content types AI can create/edit |
| **HTTPS Required** | OFF | Recommend enabling in production |

### 2. Enable HTTPS (Production)

For security in production:

```bash
# Update WordPress settings
# Settings > General > WordPress Address (HTTPS)
# Settings > General > Site Address (HTTPS)
```

### 3. Set User Roles

Assign appropriate WordPress roles:

- **Editor**: Can create/edit posts (recommended for AI agents)
- **Author**: Can only edit own posts
- **Contributor**: Limited permissions

---

## Authentication Setup

### For AI Agents/MCP Clients

#### Step 1: Create Application Password

1. Go to **Users > Profile** (your user account)
2. Scroll to **Application Passwords** section
3. Enter name: `WebWeaver AI Agent`
4. Click **Create Application Password**
5. Copy the generated password (save securely!)

**Example:**
```
Username: admin
App Password: wpdx rvfq 2xrj 8jjf
```

#### Step 2: Create Base64 Auth Header

Convert credentials to Base64 for API authentication:

**JavaScript:**
```javascript
const username = "admin";
const password = "wpdx rvfq 2xrj 8jjf";
const credentials = btoa(`${username}:${password}`);
const authHeader = `Basic ${credentials}`;
```

**Python:**
```python
import base64

username = "admin"
password = "wpdx rvfq 2xrj 8jjf"
credentials = base64.b64encode(f"{username}:{password}".encode()).decode()
auth_header = f"Basic {credentials}"
```

**Bash:**
```bash
echo -n "admin:wpdx rvfq 2xrj 8jjf" | base64
# Output: YWRtaW46d3BkeCByA...
```

#### Step 3: Store Credentials

Store safely in your environment:

```bash
# .env file (NEVER commit to git!)
WEBWEAVER_URL=https://example.com
WEBWEAVER_AUTH=Basic YWRtaW46d3BkeCByA...
```

---

## Testing the API

### Check Tools Manifest

Verify the API is working:

```bash
# Get available tools
curl -X GET \
  https://example.com/wp-json/wp-mcp/v1/tools \
  -H "Authorization: Basic YWRtaW46d3BkeCByA..."
```

**Response:**
```json
{
  "version": "0.1.0",
  "site_url": "https://example.com",
  "builders": ["gutenberg", "elementor", "divi"],
  "capabilities": {
    "can_read_posts": true,
    "can_edit_posts": true,
    "can_create_posts": true,
    "can_publish_posts": true,
    "can_upload_files": true
  },
  "tools": [
    {
      "name": "list_posts",
      "description": "List posts/pages with filtering",
      "endpoint": "GET /wp-json/wp-mcp/v1/posts"
    },
    ...
  ]
}
```

### List Posts

```bash
curl -X GET \
  'https://example.com/wp-json/wp-mcp/v1/posts?type=post&per_page=10' \
  -H "Authorization: Basic YWRtaW46d3BkeCByA..."
```

### Create a Post

```bash
curl -X POST \
  https://example.com/wp-json/wp-mcp/v1/post \
  -H "Authorization: Basic YWRtaW46d3BkeCByA..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Post from AI",
    "content": "<p>Hello, world!</p>",
    "status": "draft",
    "type": "post"
  }'
```

---

## Connecting AI Agents

### Claude (MCP Protocol)

Configure Claude to use WebWeaver:

**claude_desktop_config.json:**
```json
{
  "mcpServers": {
    "webweaver": {
      "command": "curl",
      "args": ["-H", "Authorization: Basic YOUR_AUTH_HERE"],
      "env": {
        "WEBWEAVER_URL": "https://example.com/wp-json/wp-mcp/v1"
      }
    }
  }
}
```

### Other AI Agents

Any MCP-compatible client can connect:

1. Configure base URL: `https://example.com/wp-json/wp-mcp/v1`
2. Set authorization header with app password
3. Fetch `/tools` to get available endpoints
4. Use endpoints to create/edit content

---

## Common Workflows

### 1. Create a New Blog Post

```bash
# Step 1: Create draft post
POST /post
{
  "title": "My New Article",
  "content": "<p>Article content...</p>",
  "type": "post",
  "status": "draft"
}

# Step 2: Upload featured image
POST /media
(with image file)

# Step 3: Set featured image
PUT /post/{post_id}/featured-image
{
  "media_id": {attachment_id}
}

# Step 4: Publish (if not draft-only mode)
PUT /post/{post_id}
{
  "status": "publish"
}
```

### 2. Update Existing Content

```bash
# Get post details
GET /post/{post_id}

# Update content
PUT /post/{post_id}
{
  "content": "<p>Updated content...</p>",
  "title": "Updated Title"
}
```

### 3. Find Posts by Type

```bash
# Filter by post type
GET /posts?type=page&per_page=20

# Filter by status
GET /posts?status=draft

# Search posts
GET /posts?search=keyword
```

---

## API Endpoints Reference

### Authentication
All endpoints require `Authorization: Basic` header with app password

### Tools
```
GET /tools
- Get manifest with available tools and capabilities
```

### Posts
```
GET /posts
- List posts with filters (type, status, builder, search)

GET /post/{id}
- Get single post content and metadata

POST /post
- Create new post

PUT /post/{id}
- Update existing post
```

### Media
```
POST /media
- Upload media file (multipart form-data)

PUT /post/{id}/featured-image
- Set post featured image
```

### Activity Log
```
GET /activity-log (admin only)
- View all API activity for audit trail
```

---

## Security Best Practices

### 1. Use Application Passwords
- ✅ Do: Use app-specific passwords
- ❌ Don't: Use actual user password for API

### 2. HTTPS Only
- Enable HTTPS in production
- Configure `HTTPS_REQUIRED` in settings

### 3. Rate Limiting
- Default: 60 requests/hour
- Adjust in Settings if needed
- Monitor for abuse

### 4. Draft-Only Mode
- Keep enabled to prevent accidental publishing
- Review drafts before publishing

### 5. Audit Trail
- Check Activity Log regularly
- Monitor who accessed the API
- Review all content changes

### 6. Permissions
- Create dedicated user for AI agents
- Set minimal required role (Editor)
- Disable when not needed

---

## Troubleshooting

### API Returns 404

**Issue**: Endpoints not found

**Solution:**
1. Verify plugin is activated
2. Check WordPress permalink settings (recommend Post name)
3. Ensure REST API is enabled: `Settings > Permalinks` (should be non-default)

```bash
# Test REST API
curl https://example.com/wp-json/
```

### 401 Unauthorized

**Issue**: Authentication failed

**Solution:**
1. Verify app password is correct
2. Check Base64 encoding of credentials
3. Use current auth header format

```bash
# Test authentication
curl -u username:password https://example.com/wp-json/wp-mcp/v1/tools
```

### 403 Forbidden

**Issue**: User doesn't have permission

**Solution:**
1. Check user role (needs Editor or higher)
2. Verify capabilities in Dashboard
3. Add user to correct role

### Rate Limit Exceeded

**Issue**: Error: "Rate limit exceeded"

**Solution:**
1. Increase rate limit in Settings
2. Space out API requests
3. Check for duplicate/loop requests

### Builder Detection Issues

**Issue**: Builders not showing as active

**Solution:**
1. Install page builder plugin (Gutenberg, Elementor, Divi)
2. Activate the plugin
3. Refresh dashboard
4. Check Activity Log for errors

---

## Getting Help

- 📖 Read the README.md
- 🐛 Check Activity Log for errors
- ⚙️ Review Settings page
- 📊 View system info in Dashboard
- 🔍 Check WordPress error logs

---

## Version History

### v0.1.0 (Initial Release)
- MCP API with 6 core tools
- Support for Gutenberg, Elementor, Divi
- Draft-only mode
- Rate limiting
- Activity logging
- Admin dashboard

---

**Last Updated:** March 2026  
**Plugin Version:** 0.1.0
