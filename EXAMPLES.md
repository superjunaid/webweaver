# API Usage Examples

## Authentication Setup

### Generate Application Password

1. Go to WP Admin → Users → Your Profile
2. Scroll to "Application Passwords"
3. Create: "AI Agent" (or your integration name)
4. Copy the generated password
5. Encode: `base64_encode("username:password")`

### Example Base Auth
```
Username: johndoe
Password: 1234 5678 1234 5678 1234 5678
Base64: am9obmRvZToxMjM0IDU2NzggMTIzNCA1Njc4IDEyMzQgNTY3OA==
Header: Authorization: Basic am9obmRvZToxMjM0IDU2NzggMTIzNCA1Njc4IDEyMzQgNTY3OA==
```

---

## 1. Get Tools Manifest

**Request:**
```bash
curl -H "Authorization: Basic <base64>" \
  https://example.com/wp-json/wp-mcp/v1/tools
```

**Response:**
```json
{
  "version": "0.1.0",
  "site_url": "https://example.com",
  "builders": {
    "gutenberg": {
      "name": "Gutenberg",
      "status": "active"
    },
    "elementor": {
      "name": "Elementor",
      "version": "3.18.0",
      "status": "active"
    },
    "divi": {
      "name": "Divi",
      "status": "active"
    }
  },
  "capabilities": {
    "can_read_posts": true,
    "can_edit_posts": true,
    "can_create_posts": true,
    "can_publish_posts": false,
    "can_upload_files": true
  },
  "policies": {
    "draft_only_mode": true,
    "allowed_post_types": ["post", "page"],
    "rate_limit_per_hour": 60,
    "https_required": true
  },
  "tools": [
    {
      "name": "list_posts",
      "description": "List posts/pages with filtering",
      "endpoint": "GET /wp-json/wp-mcp/v1/posts",
      "parameters": {
        "type": "string (post|page)",
        "status": "string",
        "builder": "string (gutenberg|elementor|divi)"
      }
    },
    ...
  ]
}
```

---

## 2. Create Gutenberg Post

**Request:**
```bash
curl -X POST \
  -H "Authorization: Basic <base64>" \
  -H "Content-Type: application/json" \
  https://example.com/wp-json/wp-mcp/v1/post \
  -d '{
    "title": "My Blog Post",
    "type": "post",
    "status": "draft",
    "excerpt": "A great post about WordPress",
    "slug": "my-blog-post",
    "builder_mode": "gutenberg",
    "builder_payload": {
      "content": "<!-- wp:paragraph -->\n<p>This is my blog post content.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>Heading</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>More content here.</p>\n<!-- /wp:paragraph -->"
    },
    "meta": {
      "_yoast_wpseo_title": "My Blog Post | Example Site",
      "_yoast_wpseo_metadesc": "A great post about WordPress"
    }
  }'
```

**Response:**
```json
{
  "id": 42,
  "title": "My Blog Post",
  "excerpt": "A great post about WordPress",
  "status": "draft",
  "type": "post",
  "builder_mode": "gutenberg",
  "edit_url": "https://example.com/wp-admin/post.php?post=42&action=edit",
  "view_url": "https://example.com/?p=42",
  "builder_content": "<!-- wp:paragraph -->..."
}
```

---

## 3. Create Elementor Page

**Request:**
```bash
curl -X POST \
  -H "Authorization: Basic <base64>" \
  -H "Content-Type: application/json" \
  https://example.com/wp-json/wp-mcp/v1/post \
  -d '{
    "title": "Sales Landing Page",
    "type": "page",
    "status": "draft",
    "builder_mode": "elementor",
    "builder_payload": {
      "elementor_json": {
        "version": "0.4",
        "type": "page",
        "sections": [
          {
            "id": "section_1",
            "elType": "section",
            "settings": {},
            "elements": [
              {
                "id": "column_1",
                "elType": "column",
                "settings": {
                  "_column_size": 100
                },
                "elements": [
                  {
                    "id": "text_1",
                    "elType": "widget",
                    "settings": {
                      "title": "Welcome",
                      "content": "This is a sales page created by AI."
                    }
                  }
                ]
              }
            ]
          }
        ]
      },
      "mode": "replace"
    }
  }'
```

**Response:**
```json
{
  "id": 43,
  "title": "Sales Landing Page",
  "status": "draft",
  "type": "page",
  "builder_mode": "elementor",
  "edit_url": "https://example.com/wp-admin/post.php?post=43&action=edit",
  "view_url": "https://example.com/sales-landing-page/",
  "builder_content": { /* elementor JSON */ }
}
```

---

## 4. Create Divi Page

**Request:**
```bash
curl -X POST \
  -H "Authorization: Basic <base64>" \
  -H "Content-Type: application/json" \
  https://example.com/wp-json/wp-mcp/v1/post \
  -d '{
    "title": "Service Page",
    "type": "page",
    "status": "draft",
    "builder_mode": "divi",
    "builder_payload": {
      "content": "[et_pb_section fullwidth=\"off\" specialty=\"off\"][et_pb_row][et_pb_column type=\"4_4\"][et_pb_text]\n<h1>Our Services</h1>\n<p>We offer world-class digital services.</p>\n[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]",
      "mode": "replace"
    }
  }'
```

**Response:**
```json
{
  "id": 44,
  "title": "Service Page",
  "status": "draft",
  "type": "page",
  "builder_mode": "divi",
  "edit_url": "https://example.com/wp-admin/post.php?post=44&action=edit",
  "view_url": "https://example.com/services/",
  "builder_content": "[et_pb_section...]"
}
```

---

## 5. Update Existing Post

**Request:**
```bash
curl -X PUT \
  -H "Authorization: Basic <base64>" \
  -H "Content-Type: application/json" \
  https://example.com/wp-json/wp-mcp/v1/post/42 \
  -d '{
    "title": "Updated: My Blog Post",
    "excerpt": "Updated excerpt",
    "status": "draft",
    "builder_payload": {
      "content": "<!-- wp:paragraph -->\n<p>Updated content.</p>\n<!-- /wp:paragraph -->"
    }
  }'
```

**Response:**
```json
{
  "id": 42,
  "title": "Updated: My Blog Post",
  "excerpt": "Updated excerpt",
  "status": "draft",
  "type": "post",
  "builder_mode": "gutenberg",
  "edit_url": "https://example.com/wp-admin/post.php?post=42&action=edit",
  "view_url": "https://example.com/?p=42",
  "builder_content": "<!-- wp:paragraph -->..."
}
```

---

## 6. List Posts with Filters

**Request:**
```bash
# Get all published pages with Elementor
curl -H "Authorization: Basic <base64>" \
  'https://example.com/wp-json/wp-mcp/v1/posts?type=page&builder=elementor&status=publish&per_page=20&page=1'

# Search posts
curl -H "Authorization: Basic <base64>" \
  'https://example.com/wp-json/wp-mcp/v1/posts?type=post&search=landing&per_page=10'
```

**Response:**
```json
{
  "data": [
    {
      "id": 43,
      "title": "Sales Landing Page",
      "excerpt": "",
      "status": "draft",
      "type": "page",
      "builder_mode": "elementor",
      "edit_url": "https://example.com/wp-admin/post.php?post=43&action=edit",
      "view_url": "https://example.com/sales-landing-page/"
    },
    {
      "id": 45,
      "title": "Product Page",
      "excerpt": "",
      "status": "draft",
      "type": "page",
      "builder_mode": "elementor",
      "edit_url": "https://example.com/wp-admin/post.php?post=45&action=edit",
      "view_url": "https://example.com/product/"
    }
  ],
  "total": 2,
  "page": 1,
  "per_page": 20
}
```

---

## 7. Get Single Post

**Request:**
```bash
curl -H "Authorization: Basic <base64>" \
  https://example.com/wp-json/wp-mcp/v1/post/42
```

**Response:**
```json
{
  "id": 42,
  "title": "My Blog Post",
  "excerpt": "A great post",
  "status": "draft",
  "type": "post",
  "builder_mode": "gutenberg",
  "edit_url": "https://example.com/wp-admin/post.php?post=42&action=edit",
  "view_url": "https://example.com/?p=42",
  "builder_content": "<!-- wp:paragraph -->..."
}
```

---

## 8. Upload Media

**Request:**
```bash
curl -X POST \
  -H "Authorization: Basic <base64>" \
  -F "file=@/path/to/image.jpg" \
  https://example.com/wp-json/wp-mcp/v1/media
```

**Response:**
```json
{
  "id": 100,
  "url": "https://example.com/wp-content/uploads/2024/02/image.jpg",
  "title": "image"
}
```

---

## 9. Set Featured Image

**Request:**
```bash
curl -X PUT \
  -H "Authorization: Basic <base64>" \
  -H "Content-Type: application/json" \
  https://example.com/wp-json/wp-mcp/v1/post/42/featured-image \
  -d '{
    "media_id": 100
  }'
```

**Response:**
```json
{
  "post_id": 42,
  "media_id": 100,
  "featured_image_url": "https://example.com/wp-content/uploads/2024/02/image.jpg"
}
```

---

## 10. Get Activity Log (Admin Only)

**Request:**
```bash
# Get all activity
curl -H "Authorization: Basic <base64>" \
  https://example.com/wp-json/wp-mcp/v1/activity-log

# Filter by post
curl -H "Authorization: Basic <base64>" \
  'https://example.com/wp-json/wp-mcp/v1/activity-log?post_id=42&per_page=50'

# Filter by user
curl -H "Authorization: Basic <base64>" \
  'https://example.com/wp-json/wp-mcp/v1/activity-log?user_id=1'

# Filter by action
curl -H "Authorization: Basic <base64>" \
  'https://example.com/wp-json/wp-mcp/v1/activity-log?action=create'
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "timestamp": "2024-02-15 14:32:10",
      "user_id": 1,
      "action": "create",
      "post_id": 42,
      "builder_mode": "gutenberg",
      "result": "success",
      "ip_address": "192.168.1.100",
      "user_agent": "curl/7.85.0"
    },
    {
      "id": 2,
      "timestamp": "2024-02-15 14:35:22",
      "user_id": 1,
      "action": "update",
      "post_id": 42,
      "builder_mode": "gutenberg",
      "result": "success",
      "ip_address": "192.168.1.100",
      "user_agent": "curl/7.85.0"
    }
  ],
  "total": 2,
  "page": 1,
  "per_page": 25
}
```

---

## Integration Example (Python)

```python
import requests
import base64
import json

BASE_URL = "https://example.com"
USERNAME = "johndoe"
PASSWORD = "1234 5678 1234 5678 1234 5678"

# Encode credentials
auth = base64.b64encode(f"{USERNAME}:{PASSWORD}".encode()).decode()

class WordPressAI:
    def __init__(self, base_url, username, password):
        self.base_url = base_url
        self.api_url = f"{base_url}/wp-json/wp-mcp/v1"
        self.auth = base64.b64encode(f"{username}:{password}".encode()).decode()
        self.headers = {
            "Authorization": f"Basic {self.auth}",
            "Content-Type": "application/json"
        }
    
    def get_tools(self):
        """Fetch tools manifest"""
        r = requests.get(f"{self.api_url}/tools", headers=self.headers)
        return r.json()
    
    def create_post(self, title, content, builder="gutenberg", post_type="post"):
        """Create a new post"""
        data = {
            "title": title,
            "type": post_type,
            "status": "draft",
            "builder_mode": builder,
            "builder_payload": {
                "content": content
            }
        }
        r = requests.post(f"{self.api_url}/post", headers=self.headers, json=data)
        return r.json()
    
    def update_post(self, post_id, **kwargs):
        """Update a post"""
        r = requests.put(
            f"{self.api_url}/post/{post_id}",
            headers=self.headers,
            json=kwargs
        )
        return r.json()
    
    def list_posts(self, post_type="post", status="draft"):
        """List posts"""
        r = requests.get(
            f"{self.api_url}/posts",
            headers=self.headers,
            params={"type": post_type, "status": status}
        )
        return r.json()

# Usage
wp = WordPressAI(BASE_URL, USERNAME, PASSWORD)

# Create a post
post = wp.create_post(
    "New Blog Post",
    "<!-- wp:paragraph -->\n<p>Hello World</p>\n<!-- /wp:paragraph -->"
)
print(f"Created post {post['id']}")

# Update it
wp.update_post(post['id'], title="Updated Title")

# List posts
posts = wp.list_posts()
print(f"Found {posts['total']} posts")
```

---

## Error Handling

### 401 Unauthorized
```json
{
  "code": "rest_forbidden",
  "message": "Unauthorized",
  "data": {
    "status": 401
  }
}
```

### 403 Forbidden (Draft-Only Mode)
```json
{
  "code": "rest_forbidden",
  "message": "Draft-only mode prevents publishing",
  "data": {
    "status": 403
  }
}
```

### 429 Rate Limit Exceeded
```json
{
  "code": "rate_limit",
  "message": "Rate limit exceeded",
  "data": {
    "status": 429
  }
}
```

### 400 Bad Request
```json
{
  "code": "invalid",
  "message": "Title is required",
  "data": {
    "status": 400
  }
}
```

---

## Safe Mode Workflow

For maximum safety, use this workflow:

1. **Always use draft status**
   ```json
   {"status": "draft", ...}
   ```

2. **Test in draft before publish**
   - Create in draft
   - Verify output
   - Admin publishes manually

3. **Monitor activity log**
   ```bash
   curl -H "Authorization: Basic <base64>" \
     https://example.com/wp-json/wp-mcp/v1/activity-log
   ```

4. **Use revisions for rollback**
   - Every update creates a revision
   - Admins can restore via WP Admin > Posts

---

See [README.md](README.md) for more details.
