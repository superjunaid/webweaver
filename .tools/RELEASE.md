# Release Process

## Quick Release

```bash
./release.sh 0.2.1
```

This automatically:
1. ✅ Updates version in code (wp-webweaver.php)
2. ✅ Builds plugin ZIP
3. ✅ Calculates SHA256 checksum
4. ✅ Copies to releases/ folder
5. ✅ Commits to git
6. ✅ Creates GitHub release with ZIP attached

## Prerequisites

### GitHub CLI (for auto GitHub release)

```bash
# Install
brew install gh

# Authenticate (first time)
gh auth login
```

### Git Setup

```bash
# Check remote
git remote -v
# Should show: origin https://github.com/superjunaid/webweaver.git

# Check branch
git branch
# Should be on main
```

## Manual Steps (if needed)

### 1. Update Version

Edit `wp-webweaver.php`:
```php
 * Version: 0.2.1
define('WEBWEAVER_VERSION', '0.2.1');
```

### 2. Build ZIP

```bash
mkdir -p /tmp/webweaver-build/webweaver
cp -r includes templates assets wp-webweaver.php composer.json /tmp/webweaver-build/webweaver/
cp .docs/*.md /tmp/webweaver-build/webweaver/

cd /tmp/webweaver-build
zip -r -q webweaver-0.2.1.zip webweaver/
shasum -a 256 webweaver-0.2.1.zip
```

### 3. Copy to Releases

```bash
cp /tmp/webweaver-build/webweaver-0.2.1.zip releases/
echo "[sha256]  webweaver-0.2.1.zip" > releases/webweaver-0.2.1.sha256
```

### 4. Commit

```bash
git add wp-webweaver.php releases/webweaver-0.2.1.*
git commit -m "Release: WebWeaver v0.2.1"
git push
```

### 5. GitHub Release

```bash
# Option A: Using GitHub CLI
gh release create v0.2.1 releases/webweaver-0.2.1.zip \
  --title "WebWeaver v0.2.1" \
  --notes "See releases page for details"

# Option B: Manual
# Visit: https://github.com/superjunaid/webweaver/releases/new
# - Tag: v0.2.1
# - Title: WebWeaver v0.2.1
# - Upload: releases/webweaver-0.2.1.zip
# - Click Publish
```

## After Release

1. ✅ Verify on GitHub: https://github.com/superjunaid/webweaver/releases
2. ✅ Download ZIP from release page
3. ✅ Test on WordPress
4. ✅ Announce in documentation

## Troubleshooting

### "gh command not found"
```bash
brew install gh
gh auth login
```

### Git not pushing
```bash
git log # See commits
git push # Push commits
```

### Version not updating
```bash
# Check file
grep "Version:" wp-webweaver.php
grep "WEBWEAVER_VERSION" wp-webweaver.php

# Edit manually if needed
```

## Files Modified in Release

- `wp-webweaver.php` - Version bumped
- `releases/webweaver-0.2.1.zip` - Plugin package
- `releases/webweaver-0.2.1.sha256` - Checksum
- GitHub releases page - Release created

## Version Format

Use semantic versioning:
```
0.0.0
│ │ └─ Patch (bug fixes)
│ └─── Minor (new features)
└───── Major (breaking changes)
```

Examples:
- `0.2.0` - Initial MCP auth feature
- `0.2.1` - Bug fix or enhancement
- `1.0.0` - Major release

---

**Usage:** `./release.sh 0.2.1`
