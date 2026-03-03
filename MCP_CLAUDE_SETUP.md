# Connecting WebWeaver to Claude (MCP)

## Quick Setup

### Step 1: Start the MCP Wrapper Server

```bash
cd /path/to/webweaver

# Install Node.js if needed (macOS)
brew install node

# Run MCP server
node mcp-server.js
```

You should see:
```
🚀 WebWeaver MCP Server starting...
📍 WordPress: http://localhost:8888
🔐 Auth: admin
🌐 Listening on port 3000
✅ MCP server ready at http://localhost:3000
```

### Step 2: Add to Claude

1. **Open Claude Desktop**
2. Click **Settings** (gear icon, top right)
3. Go to **Developer** tab
4. Click **Add custom connector**
5. Fill in:
   - **Name:** `WebWeaver`
   - **Remote MCP server URL:** `http://localhost:3000`
6. Click **Add**

### Step 3: Test It

In Claude, ask:
```
What posts exist on my WordPress site?
```

or

```
Create a new blog post about AI with this content: [content]
```

---

## Configuration

### For Your Own WordPress Site

Create `.env` file:

```bash
WORDPRESS_URL=https://yoursite.com
WORDPRESS_USER=your_username
WORDPRESS_PASSWORD=app_password_here  # Use app password, not user password
MCP_PORT=3000
```

Then run:
```bash
node mcp-server.js
```

### Generate App Password

On your WordPress:

1. **Users > Your User**
2. Scroll to **Application Passwords**
3. Name: `Claude MCP`
4. Copy the password
5. Use in `.env` file

---

## Available Tools

Once connected, Claude can:

### Read Posts
```
"Get all my blog posts"
"Show me draft posts"
"Search for posts about marketing"
```

### Create Posts
```
"Create a new blog post with title 'Hello World' and content '...'"
```

### Update Posts
```
"Update post #42 with new content"
```

---

## Troubleshooting

### Connection refused
- Make sure `node mcp-server.js` is running
- Check port 3000 is available: `lsof -i :3000`

### 401 Unauthorized
- Check WordPress credentials in `.env`
- Verify app password is correct
- Make sure user has Editor role or higher

### Tools not appearing in Claude
- Restart Claude desktop
- Check `http://localhost:3000/tools` in browser
- Verify WordPress API is working

---

## Architecture

```
Claude Desktop
     ↓ (HTTP)
MCP Server (Node.js)
     ↓ (REST API)
WordPress (REST API)
     ↓
Database
```

---

## API Endpoints

The MCP server proxies these WordPress endpoints:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/tools` | List available tools |
| GET | `/posts` | List posts |
| GET | `/post/{id}` | Get post details |
| POST | `/post` | Create new post |
| PUT | `/post/{id}` | Update post |

---

## Security

⚠️ **For Local Development Only**

For production:
1. Use HTTPS on both services
2. Use strong, unique app passwords
3. Restrict MCP server to localhost
4. Keep WordPress updated

---

## Next Steps

1. ✅ Start MCP server: `node mcp-server.js`
2. ✅ Add to Claude: Settings > Developer > Add custom connector
3. ✅ Test with Claude: Ask it to list/create posts

---

**Questions?** Check [SETUP_GUIDE.md](SETUP_GUIDE.md) for more details.
