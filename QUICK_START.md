# WebWeaver Quick Start

Fast setup for developers and AI agents.

## 1. Install & Activate
```bash
# Upload plugin files
# Activate in WordPress Admin > Plugins
```

## 2. Create App Password
```
User Profile > Application Passwords > Create "WebWeaver AI"
```

## 3. Generate Auth Header
```bash
# Replace with your credentials
echo -n "username:apppassword" | base64
# Output: dXNlcm5hbWU6YXBwcGFzc3dvcmQ=
```

## 4. Test Connection
```bash
curl -X GET \
  https://yoursite.com/wp-json/wp-mcp/v1/tools \
  -H "Authorization: Basic dXNlcm5hbWU6YXBwcGFzc3dvcmQ="
```

## 5. Use the API

### List Posts
```bash
curl https://yoursite.com/wp-json/wp-mcp/v1/posts \
  -H "Authorization: Basic ..."
```

### Create Post
```bash
curl -X POST https://yoursite.com/wp-json/wp-mcp/v1/post \
  -H "Authorization: Basic ..." \
  -H "Content-Type: application/json" \
  -d '{"title":"New Post","content":"<p>Hello</p>"}'
```

### Update Post
```bash
curl -X PUT https://yoursite.com/wp-json/wp-mcp/v1/post/{id} \
  -H "Authorization: Basic ..." \
  -H "Content-Type: application/json" \
  -d '{"content":"<p>Updated</p>"}'
```

## API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/tools` | Get available tools |
| GET | `/posts` | List posts |
| GET | `/post/{id}` | Get post details |
| POST | `/post` | Create post |
| PUT | `/post/{id}` | Update post |
| POST | `/media` | Upload file |
| PUT | `/post/{id}/featured-image` | Set featured image |

## Environment Variables
```bash
export WEBWEAVER_URL="https://yoursite.com/wp-json/wp-mcp/v1"
export WEBWEAVER_AUTH="Basic dXNlcm5hbWU6YXBwcGFzc3dvcmQ="
```

## Troubleshooting

**404 on API endpoints?**
- Check plugin is activated
- Go to Settings > Permalinks (set to Post name)

**401 Unauthorized?**
- Verify auth header
- Check app password is correct

**403 Forbidden?**
- User needs Editor role
- Check capabilities on Dashboard

## Next Steps
- 📖 Read [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed instructions
- 🔍 Check Dashboard for system status
- 📋 View Activity Log for API activity
- ⚙️ Configure Settings as needed
