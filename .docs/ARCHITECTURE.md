# WebWeaver Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      AI Agent / MCP Client                      │
│                                                                  │
│  • Claude, ChatGPT, or custom MCP-compatible client             │
│  • Sends HTTP requests with authorization                       │
│  • Receives JSON responses                                      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                    HTTPS REST API
                   (Base64 Auth Header)
                             │
┌────────────────────────────┴────────────────────────────────────┐
│                   WordPress Environment                          │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │               WebWeaver Plugin (v0.1.0)                   │  │
│  │                                                            │  │
│  │  ┌─────────────────────────────────────────────────────┐ │  │
│  │  │          REST API Router                            │ │  │
│  │  │  ┌──────────────────────────────────────────────┐  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/tools              (GET) │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/posts              (GET) │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/post                (POST) │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/post/{id}          (PUT) │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/media              (POST) │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/post/{id}/featured-image │  │ │  │
│  │  │  │ /wp-json/wp-mcp/v1/activity-log       (GET) │  │ │  │
│  │  │  └──────────────────────────────────────────────┘  │ │  │
│  │  │                      ↓                              │ │  │
│  │  │  ┌──────────────────────────────────────────────┐  │ │  │
│  │  │  │   Endpoint Handlers                          │  │ │  │
│  │  │  │  • Tools (get_manifest)                      │  │ │  │
│  │  │  │  • Posts (list, get, create, update)       │  │ │  │
│  │  │  │  • Media (upload, set featured image)       │  │ │  │
│  │  │  │  • Activity Log (audit trail)               │  │ │  │
│  │  │  └──────────────────────────────────────────────┘  │ │  │
│  │  │                      ↓                              │ │  │
│  │  │  ┌──────────────────────────────────────────────┐  │ │  │
│  │  │  │   Core Modules                               │  │ │  │
│  │  │  │  ├─ Authentication (App Passwords)           │  │ │  │
│  │  │  │  ├─ Authorization (Roles & Capabilities)    │  │ │  │
│  │  │  │  ├─ Rate Limiting (per user, per hour)      │  │ │  │
│  │  │  │  ├─ Builder Detection (Gutenberg, etc)      │  │ │  │
│  │  │  │  ├─ Security Policies (Draft-only mode)     │  │ │  │
│  │  │  │  └─ Activity Logging (All API calls)        │  │ │  │
│  │  │  └──────────────────────────────────────────────┘  │ │  │
│  │  └─────────────────────────────────────────────────────┘ │  │
│  │                      ↓                                     │  │
│  │  ┌─────────────────────────────────────────────────────┐ │  │
│  │  │      WordPress Core Functions                       │ │  │
│  │  │   • Posts API (get_posts, wp_insert_post)          │ │  │
│  │  │   • Media API (media_upload, set_post_thumbnail)  │ │  │
│  │  │   • User Capabilities (current_user_can)          │ │  │
│  │  │   • Settings/Options (get_option, update_option) │ │  │
│  │  └─────────────────────────────────────────────────────┘ │  │
│  │                      ↓                                     │  │
│  │  ┌─────────────────────────────────────────────────────┐ │  │
│  │  │      WordPress Database                            │ │  │
│  │  │   • Posts, Pages, Attachments                      │ │  │
│  │  │   • Post Meta, User Data                           │ │  │
│  │  │   • Activity Logs                                  │ │  │
│  │  └─────────────────────────────────────────────────────┘ │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │           Page Builders (Optional)                        │  │
│  │  ├─ Gutenberg (WordPress native)                         │  │
│  │  ├─ Elementor (if installed)                             │  │
│  │  └─ Divi (if installed)                                  │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Request Flow

```
1. AI Agent Request
   ↓
   GET /wp-json/wp-mcp/v1/posts?type=post

2. WordPress REST Server
   ├─ Receives request
   └─ Routes to endpoint handler

3. WebWeaver Router
   ├─ Extracts namespace: wp-mcp/v1
   ├─ Matches route: /posts
   └─ Calls: Endpoints\Posts::list_posts()

4. Authentication
   ├─ Checks Basic Auth header
   ├─ Verifies user exists
   └─ Confirms user is logged in

5. Authorization
   ├─ Checks user capabilities
   ├─ Verifies: read_posts permission
   └─ Allows or denies access

6. Rate Limiting
   ├─ Gets user ID + hour
   ├─ Checks cache: wp_mcp_rate_limit_{user}_{hour}
   ├─ Increments counter
   └─ Returns 429 if exceeded

7. Business Logic
   ├─ Query posts from database
   ├─ Filter by type, status, builder
   ├─ Paginate results
   └─ Format as JSON

8. Logging
   ├─ Records action in activity_log
   ├─ Stores user, timestamp, action
   └─ Stores request/response details

9. Response
   ├─ Format JSON response
   ├─ Add status codes
   └─ Return to AI Agent

10. AI Agent Processing
    └─ Parses JSON response
    └─ Uses data for next action
```

## Database Schema

### Activity Log Table
```sql
CREATE TABLE wp_webweaver_activity_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  action VARCHAR(50) NOT NULL,      -- create_post, update_post, etc
  post_id BIGINT,
  details LONGTEXT,                  -- JSON encoded data
  ip_address VARCHAR(45),
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  INDEX (post_id),
  INDEX (action),
  INDEX (timestamp)
);
```

## Authentication Flow

```
User Account
    ↓
Application Password Generated
(via WordPress User Settings)
    ↓
Credentials: username + app_password
    ↓
Base64 Encode: base64(username:app_password)
    ↓
HTTP Header: Authorization: Basic {base64_creds}
    ↓
API Request
    ↓
WordPress verifies credentials
(WordPress Application Passwords feature)
    ↓
Sets current user context
    ↓
Checks user permissions
    ↓
Execute request as authenticated user
```

## Security Layers

```
┌─────────────────────────────────────┐
│  1. HTTPS Transport                 │
│     └─ Encrypts credentials         │
├─────────────────────────────────────┤
│  2. Application Passwords           │
│     └─ Separate from user password  │
├─────────────────────────────────────┤
│  3. Base64 Auth Headers             │
│     └─ WordPress validates          │
├─────────────────────────────────────┤
│  4. WordPress User Verification     │
│     └─ Checks user exists & active  │
├─────────────────────────────────────┤
│  5. Capability Checks               │
│     └─ Verifies permissions         │
│        (read_posts, edit_posts, etc)│
├─────────────────────────────────────┤
│  6. Rate Limiting                   │
│     └─ 60 requests/hour per user    │
├─────────────────────────────────────┤
│  7. Draft-Only Mode                 │
│     └─ Forces new posts to drafts   │
├─────────────────────────────────────┤
│  8. Activity Logging                │
│     └─ Full audit trail             │
├─────────────────────────────────────┤
│  9. CORS (if applicable)            │
│     └─ Restricts cross-origin calls │
└─────────────────────────────────────┘
```

## Builder Integration

Each builder has a detection & compatibility layer:

```
Builders\Registry
    ├─ Gutenberg (Native WordPress)
    │  ├─ Detection: check_if_gutenberg_active()
    │  ├─ Content Format: WordPress JSON blocks
    │  └─ Storage: wp_posts.post_content
    │
    ├─ Elementor (if installed)
    │  ├─ Detection: check_if_elementor_active()
    │  ├─ Content Format: Elementor JSON model
    │  └─ Storage: wp_postmeta._elementor_data
    │
    └─ Divi (if installed)
       ├─ Detection: check_if_divi_active()
       ├─ Content Format: Divi JSON model
       └─ Storage: wp_postmeta._et_pb_...
```

## Admin Interface

```
WordPress Admin
    └─ WebWeaver Menu
        ├─ Dashboard
        │  ├─ System Status
        │  ├─ Active Builders
        │  ├─ User Capabilities
        │  ├─ Getting Started Guide
        │  ├─ API Reference
        │  └─ Security Tips
        │
        ├─ Settings
        │  ├─ Draft-Only Mode
        │  ├─ Rate Limit
        │  ├─ Allowed Post Types
        │  └─ HTTPS Requirement
        │
        └─ Activity Log
           ├─ All API calls
           ├─ User actions
           ├─ Timestamps
           └─ Filter options
```

## Deployment Options

### Local Development
```
Docker Compose
├─ WordPress Container (PHP 8.1)
├─ MySQL Container
└─ Volume mounts for code
```

### Shared Hosting
```
WordPress Installation
└─ WebWeaver Plugin (FTP/Upload)
    └─ Auto-activates on install
```

### VPS/Cloud
```
Docker/Kubernetes
├─ WordPress + PHP
├─ MySQL Database
├─ Reverse Proxy (Nginx)
├─ SSL Certificate
└─ Monitoring/Logging
```

## Scaling Considerations

- **Database**: Posts table with proper indexing
- **Cache**: WordPress object cache for rate limiting
- **Logging**: Activity log pruning for large installations
- **Concurrency**: PHP-FPM pool sizing
- **Sessions**: Application password handling
- **Load Balancing**: Sticky sessions not required

---

**Architecture Version:** 1.0  
**Last Updated:** March 2026
