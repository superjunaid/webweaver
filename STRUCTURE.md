# Plugin Structure

## Directory Layout

```
wordpress-mcp/
├── wp-mcp-connector.php          # Main plugin file (entry point)
├── README.md                      # User documentation
├── STRUCTURE.md                   # This file
│
├── includes/                      # Core plugin code
│   ├── autoloader.php             # PSR-4 autoloader
│   ├── plugin.php                 # Main Plugin class
│   │
│   ├── api/
│   │   ├── routes.php             # REST route registration
│   │   ├── auth/
│   │   │   └── authenticate.php   # Auth checks
│   │   └── endpoints/
│   │       ├── posts.php          # POST/GET/PUT /post(s)
│   │       ├── tools.php          # GET /tools (manifest)
│   │       ├── media.php          # POST /media, PUT featured-image
│   │       └── activitylog.php    # GET /activity-log
│   │
│   ├── builders/
│   │   ├── registry.php           # Detect active builders
│   │   ├── factory.php            # Builder dispatcher
│   │   ├── gutenberg.php          # Gutenberg support
│   │   ├── elementor.php          # Elementor support
│   │   └── divi.php               # Divi support
│   │
│   ├── security/
│   │   ├── policy.php             # Draft-only, allowlists, etc.
│   │   ├── ratelimit.php          # Rate limiting
│   │   └── meta.php               # Meta key allowlist
│   │
│   ├── logging/
│   │   └── activity.php           # Activity log writes
│   │
│   ├── database/
│   │   └── install.php            # Database table creation
│   │
│   └── admin/
│       ├── menu.php               # Admin menu registration
│       └── assets.php             # CSS/JS enqueuing
│
├── templates/
│   └── admin/
│       ├── dashboard.php          # Main admin page
│       ├── settings.php           # Settings form
│       └── activity.php           # Activity log table
│
└── assets/
    └── admin/
        ├── admin.css              # Styles
        └── admin.js               # JavaScript helpers
```

## Key Classes & Namespaces

| Class | Namespace | Purpose |
|-------|-----------|---------|
| Plugin | `WP_MCP_Connector` | Main initialization & hooks |
| Routes | `WP_MCP_Connector\API` | REST endpoint registration |
| Posts | `WP_MCP_Connector\API\Endpoints` | CRUD operations |
| Tools | `WP_MCP_Connector\API\Endpoints` | Tools manifest |
| Media | `WP_MCP_Connector\API\Endpoints` | Media upload |
| ActivityLog | `WP_MCP_Connector\API\Endpoints` | Logs endpoint |
| Authenticate | `WP_MCP_Connector\API\Auth` | Auth checks |
| Registry | `WP_MCP_Connector\Builders` | Builder detection |
| Factory | `WP_MCP_Connector\Builders` | Builder dispatcher |
| Gutenberg | `WP_MCP_Connector\Builders` | Gutenberg impl. |
| Elementor | `WP_MCP_Connector\Builders` | Elementor impl. |
| Divi | `WP_MCP_Connector\Builders` | Divi impl. |
| Policy | `WP_MCP_Connector\Security` | Access control |
| RateLimit | `WP_MCP_Connector\Security` | Rate limiting |
| Meta | `WP_MCP_Connector\Security` | Meta allowlist |
| Activity | `WP_MCP_Connector\Logging` | Logging |
| Install | `WP_MCP_Connector\Database` | DB setup |
| Menu | `WP_MCP_Connector\Admin` | Admin UI |
| Assets | `WP_MCP_Connector\Admin` | Asset enqueue |

## Flow Diagrams

### Authentication Flow
```
Request with Authorization header
    ↓
Authenticate::check_auth()
    ├─ is_user_logged_in()?
    ├─ current_user_can('...')?
    └─ Return: true or WP_Error
```

### Create Post Flow
```
POST /wp-json/wp-mcp/v1/post
    ↓
Posts::create_post()
    ├─ Check rate limit
    ├─ Validate permissions
    ├─ Check policy (allowed types)
    ├─ wp_insert_post()
    ├─ Factory::set_content() → Gutenberg/Elementor/Divi
    ├─ Update meta
    └─ Activity::log()
    ↓
Return: post object
```

### Update Post Flow
```
PUT /wp-json/wp-mcp/v1/post/{id}
    ↓
Posts::update_post()
    ├─ Check permissions
    ├─ Check policy (allowed post)
    ├─ wp_save_post_revision()  ← Revision snapshot
    ├─ wp_update_post()
    ├─ Factory::set_content()
    ├─ Update meta
    └─ Activity::log()
    ↓
Return: updated post object
```

### Builder Content Set Flow
```
Factory::set_content($post_id, $builder, $payload)
    ├─ 'elementor' → Elementor::set_layout()
    │   └─ update_post_meta('_elementor_data', json)
    ├─ 'divi' → Divi::set_layout()
    │   └─ wp_update_post() with post_content
    └─ 'gutenberg' → Gutenberg::set_content()
        └─ wp_update_post() with post_content
```

## Database Schema

### wp_mcp_activity_log
```sql
CREATE TABLE wp_mcp_activity_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id BIGINT,
    action VARCHAR(50),           -- create, update, delete, etc.
    post_id BIGINT,
    builder_mode VARCHAR(20),     -- gutenberg, elementor, divi
    result VARCHAR(20),           -- success, fail
    message LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    KEY (timestamp),
    KEY (user_id),
    KEY (post_id),
    KEY (action)
);
```

## WordPress Options (Config)

```php
wp_mcp_connector_version             // Plugin version
wp_mcp_connector_draft_only          // Draft-only mode (1/0)
wp_mcp_connector_rate_limit          // Max writes per hour
wp_mcp_connector_allowed_post_types  // Array: ['post', 'page', ...]
wp_mcp_connector_protected_pages     // Array: [1, 2, 3, ...]
wp_mcp_connector_allowed_page_ids    // Array: [10, 20, 30, ...] (optional)
```

## Hooks

```php
// Fired when activity is logged
do_action('wp_mcp_activity_logged', $post_id, $action, $result);
```

## Extending the Plugin

### Add a custom builder
1. Create `includes/builders/mybuilder.php`
2. Implement `MyBuilder::set_layout()` and `MyBuilder::get_layout()`
3. Register in `Registry::detect_builders()`
4. Update Tools manifest

### Add custom policies
1. Extend `Security\Policy` with custom checks
2. Use in endpoint checks before operations

### Add custom meta keys
1. Call `Security\Meta::add_allowed_key('my_meta_key')` in plugin init

## Notes

- **Autoloader**: PSR-4 compliant, case-insensitive file names
- **Security**: All inputs sanitized with wp_kses_post / sanitize_text_field
- **Revisions**: wp_save_post_revision() called before every update
- **Logging**: Every tool call logged to activity table
- **Builders**: Registry detects installed plugins/themes automatically
