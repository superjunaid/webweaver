wh# WebWeaver

**A WordPress.org plugin that turns any WP site into an AI-operable backend for content creation & editing via secure API.**

## Overview

WP MCP Connector enables AI platforms (Claude, Manus, etc.) to design, create, and edit WordPress content—including pages and posts with **Gutenberg**, **Elementor**, and **Divi** builder support—while enforcing strict security guardrails.

## Features

- ✅ **3 Builder Modes**: Gutenberg, Elementor, Divi
- ✅ **Secure API**: REST endpoints with WordPress auth + Application Passwords
- ✅ **Safety Guardrails**: Draft-only mode, rate limiting, permission scoping
- ✅ **Revisions & Rollback**: Automatic snapshots before any write
- ✅ **Activity Logging**: Audit trail of all changes
- ✅ **Admin UI**: Settings, activity log, status dashboard
- ✅ **Media Support**: Upload images, set featured images
- ✅ **Policy Engine**: Allowlist/denylist pages, post types, control publish access

## 📖 Documentation

| Guide | Purpose |
|-------|---------|
| **[QUICK_START.md](QUICK_START.md)** | 5-minute setup for developers |
| **[SETUP_GUIDE.md](SETUP_GUIDE.md)** | Complete installation & configuration |
| **[DEPLOYMENT.md](DEPLOYMENT.md)** | Production deployment instructions |
| **[ARCHITECTURE.md](ARCHITECTURE.md)** | System design & technical overview |

## Installation

1. **Download** the plugin or clone into `/wp-content/plugins/webweaver/`
2. **Activate** via WordPress admin (Plugins > Installed Plugins)
3. **Configure** at WebWeaver > Settings
4. **Generate** Application Password for API access (User Profile)

👉 **[See SETUP_GUIDE.md for detailed instructions](SETUP_GUIDE.md)**

## Quick Start

### Setup (Admin)

1. Go to **WebWeaver** > **Settings**
2. Configure:
   - Draft-Only Mode (default: ON)
   - Allowed post types (default: post, page)
   - Rate limits (default: 60 ops/hour)
   - Protected pages (optional)

### API Usage (Agent/Integration)

#### 1. Fetch Tools Manifest
```bash
curl -H "Authorization: Basic <base64(username:password)>" \
  https://yoursite.com/wp-json/wp-mcp/v1/tools
```

Response includes supported builders, current policies, available tools.

#### 2. Create Gutenberg Page
```bash
curl -X POST \
  -H "Authorization: Basic <base64(username:password)>" \
  -H "Content-Type: application/json" \
  https://yoursite.com/wp-json/wp-mcp/v1/post \
  -d '{
    "title": "Landing Page",
    "type": "page",
    "status": "draft",
    "builder_mode": "gutenberg",
    "builder_payload": {
      "content": "<!-- wp:paragraph -->\\n<p>Hello World</p>\\n<!-- /wp:paragraph -->"
    }
  }'
```

#### 3. Create Elementor Page
```bash
curl -X POST \
  -H "Authorization: Basic <base64(username:password)>" \
  -H "Content-Type: application/json" \
  https://yoursite.com/wp-json/wp-mcp/v1/post \
  -d '{
    "title": "Elementor Page",
    "type": "page",
    "status": "draft",
    "builder_mode": "elementor",
    "builder_payload": {
      "elementor_json": {...},
      "mode": "replace"
    }
  }'
```

#### 4. Update Post
```bash
curl -X PUT \
  -H "Authorization: Basic <base64(username:password)>" \
  -H "Content-Type: application/json" \
  https://yoursite.com/wp-json/wp-mcp/v1/post/123 \
  -d '{
    "title": "Updated Title",
    "builder_payload": {...}
  }'
```

#### 5. List Posts
```bash
curl -H "Authorization: Basic <base64(username:password)>" \
  'https://yoursite.com/wp-json/wp-mcp/v1/posts?type=page&builder=elementor&per_page=20'
```

#### 6. Upload Media
```bash
curl -X POST \
  -H "Authorization: Basic <base64(username:password)>" \
  -F "file=@image.jpg" \
  https://yoursite.com/wp-json/wp-mcp/v1/media
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/wp-json/wp-mcp/v1/tools` | Get tools manifest & policy info |
| GET | `/wp-json/wp-mcp/v1/posts` | List posts/pages |
| GET | `/wp-json/wp-mcp/v1/post/{id}` | Get post |
| POST | `/wp-json/wp-mcp/v1/post` | Create post |
| PUT | `/wp-json/wp-mcp/v1/post/{id}` | Update post |
| POST | `/wp-json/wp-mcp/v1/media` | Upload media |
| PUT | `/wp-json/wp-mcp/v1/post/{id}/featured-image` | Set featured image |
| GET | `/wp-json/wp-mcp/v1/activity-log` | Get activity log (admin) |

## Authentication

Use **WordPress Application Passwords** (recommended):
1. Go to your user profile in WP Admin
2. Scroll to "Application Passwords"
3. Create a password, copy it
4. Use `Authorization: Basic base64(username:password)` in API calls

## Security Features

- **Draft-Only Mode**: AI can only create drafts; humans publish
- **Rate Limiting**: Max 60 write ops per user per hour (configurable)
- **Permission Checks**: Enforces WordPress capabilities (edit_posts, publish_posts, etc.)
- **Allowlist/Denylist**: Control which pages/post types are editable
- **Revisions**: Every update creates a post revision for rollback
- **Activity Logging**: Audit trail of all operations with timestamps, users, IPs
- **Meta Allowlist**: Restrict which post meta keys can be set
- **Input Sanitization**: wp_kses_post for HTML, sanitize_text_field for text

## Builder Support

### Gutenberg
- Input/Output: Block HTML in post_content
- Payload: `{ "content": "block html" }`

### Elementor
- Meta-based layout in `_elementor_data` (JSON)
- Payload: `{ "elementor_json": {...}, "mode": "replace|merge" }`
- Modes:
  - `replace`: Replace entire layout
  - `merge`: Merge JSON objects

### Divi
- Layout stored in post_content as shortcodes
- Payload: `{ "content": "shortcode content", "mode": "replace|append" }`
- Modes:
  - `replace`: Replace entire layout
  - `append`: Add new section

## Admin Dashboard

- **Status**: Plugin version, HTTPS status, draft-only mode
- **Builders**: Detection of installed builders
- **Settings**: Draft-only, rate limits, post types, protected pages
- **Activity Log**: Searchable, filterable audit trail
- **Tools Manifest Link**: Direct link to API spec

## Configuration

All settings stored in WordPress options:

```php
// Draft-only mode (1 = on, 0 = off)
get_option('wp_mcp_connector_draft_only', 1);

// Rate limit per hour
get_option('wp_mcp_connector_rate_limit', 60);

// Allowed post types (array)
get_option('wp_mcp_connector_allowed_post_types', ['post', 'page']);

// Protected page IDs (array)
get_option('wp_mcp_connector_protected_pages', []);

// Allowed page IDs (array, optional)
get_option('wp_mcp_connector_allowed_page_ids', []);
```

## Hooks

Extend with custom hooks:

```php
// Fired when an activity is logged
do_action('wp_mcp_activity_logged', $post_id, $action, $result);
```

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Elementor (optional, for Elementor support)
- Divi Theme/Plugin (optional, for Divi support)

## Roadmap

- **V0.1** (MVP): Core CRUD, all 3 builders, draft-only, revisions ✅
- **V0.2**: Media, templates, granular policies
- **V0.3**: Partial updates, SEO integrations, external MCP adapter

## Troubleshooting

### API returns 401 Unauthorized
- Ensure application password is correct
- Check Authorization header format: `Basic base64(username:password)`

### Pages not showing as Elementor/Divi
- Plugin needs to be active for detection
- Check post meta: `_elementor_data` (Elementor), `_et_pb_use_builder` (Divi)

### HTTPS Warning
- Plugin requires HTTPS for production
- Check site URL in Settings > General

### Activity Log Empty
- Check database table exists: `wp_mcp_activity_log`
- Ensure database installation completed

## License

GPL 2.0 or later

## Support

Issues & feature requests: [GitHub Issues](https://github.com/yourusername/wordpress-mcp/issues)

---

**Built with ❤️ for AI-powered WordPress workflows**
