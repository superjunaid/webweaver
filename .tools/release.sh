#!/bin/bash
set -e

# WebWeaver Release Script
# Automates version updates, ZIP creation, and GitHub release

VERSION="${1:-}"

if [ -z "$VERSION" ]; then
    echo "❌ Usage: ./release.sh <version>"
    echo "Example: ./release.sh 0.2.1"
    exit 1
fi

echo "🚀 WebWeaver Release Script"
echo "=============================="
echo "Version: $VERSION"
echo ""

# Validate version format (0.1.0, 0.2.1, etc)
if ! [[ $VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo "❌ Invalid version format. Use: 0.1.0, 0.2.1, etc"
    exit 1
fi

PLUGIN_FILE="wp-webweaver.php"
BUILD_DIR="/tmp/webweaver-release-$VERSION"
RELEASES_DIR="releases"

echo "Step 1: Update version in code..."

# Update wp-webweaver.php
sed -i '' "s/Version: [0-9.]\+/Version: $VERSION/" "$PLUGIN_FILE"
sed -i '' "s/define('WEBWEAVER_VERSION', '[0-9.]\+')/define('WEBWEAVER_VERSION', '$VERSION')/" "$PLUGIN_FILE"

echo "✓ Updated $PLUGIN_FILE"

echo ""
echo "Step 2: Build plugin package..."

# Clean build directory
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR/webweaver"

# Copy plugin files
cp -r includes "$BUILD_DIR/webweaver/"
cp -r templates "$BUILD_DIR/webweaver/"
cp -r assets "$BUILD_DIR/webweaver/"
cp "$PLUGIN_FILE" "$BUILD_DIR/webweaver/"
cp composer.json "$BUILD_DIR/webweaver/"

# Copy documentation
cp .docs/*.md "$BUILD_DIR/webweaver/" 2>/dev/null || true

# Create .gitignore
cat > "$BUILD_DIR/webweaver/.gitignore" << 'GITIGNORE'
.DS_Store
*.swp
*.swo
*~
.vscode/
.idea/
test-*.php
check-*.php
grant-*.php
fix-*.php
create-*.php
validate-*.php
GITIGNORE

echo "Version: $VERSION" > "$BUILD_DIR/webweaver/VERSION"

# Build ZIP
cd "$BUILD_DIR"
ZIP_FILE="webweaver-$VERSION.zip"
zip -r -q "$ZIP_FILE" webweaver/
ZIP_PATH="$BUILD_DIR/$ZIP_FILE"

echo "✓ Created $ZIP_FILE"

# Calculate SHA256
SHA256=$(shasum -a 256 "$ZIP_PATH" | awk '{print $1}')
echo "✓ SHA256: $SHA256"

echo ""
echo "Step 3: Copy to releases directory..."

# Copy to releases folder
mkdir -p "$RELEASES_DIR"
cp "$ZIP_PATH" "$RELEASES_DIR/$ZIP_FILE"
echo "$SHA256  $ZIP_FILE" > "$RELEASES_DIR/$ZIP_FILE.sha256"

echo "✓ Copied to $RELEASES_DIR/"
echo "✓ SHA256 saved to $RELEASES_DIR/$ZIP_FILE.sha256"

echo ""
echo "Step 4: Git commit..."

# Stage changes
git add "$PLUGIN_FILE"
git add "$RELEASES_DIR/$ZIP_FILE"
git add "$RELEASES_DIR/$ZIP_FILE.sha256"

# Commit
git commit -m "Release: WebWeaver v$VERSION

- MCP Connection Helper in-plugin tool
- Auto base64 generation
- API key management
- Step-by-step guides for Manus.im and Claude
- Professional UI with tabs and copy buttons
- SHA256: $SHA256"

echo "✓ Committed to git"

echo ""
echo "Step 5: Create GitHub release..."

# Check if gh CLI is installed
if ! command -v gh &> /dev/null; then
    echo "⚠️  GitHub CLI not found. Install with: brew install gh"
    echo "   Or use: gh release create v$VERSION --notes 'See RELEASE-NOTES-v$VERSION.md'"
    exit 1
fi

# Create GitHub release with ZIP attached
gh release create "v$VERSION" \
    "$RELEASES_DIR/$ZIP_FILE" \
    --title "WebWeaver v$VERSION" \
    --notes "## WebWeaver v$VERSION

### New Features
- ✅ MCP Connection Helper (in-plugin tool)
- ✅ Auto Base64 generation
- ✅ One-click API key generation
- ✅ Step-by-step guides for Manus.im and Claude
- ✅ Professional UI with tabs and copy buttons

### Files
- \`webweaver-$VERSION.zip\` (plugin package)
- SHA256: \`$SHA256\`

### Installation
1. Download \`webweaver-$VERSION.zip\`
2. WordPress Admin > Plugins > Upload
3. Activate

### Setup
- Go to: **WebWeaver > MCP Connection**
- Generate API Key
- Copy and use in Manus.im

### Documentation
- 📖 [Installation Guide](.docs/INSTALL-v$VERSION.md)
- 🎯 [Manus.im Setup](.docs/MANUS_SETUP.md)
- 🔗 [MCP Connection Helper](.docs/MCP_CONNECTION_HELPER.md)
- 📚 [Full Docs](.docs/)

**Ready for production!** 🚀"

echo "✓ Created GitHub release v$VERSION"

echo ""
echo "=============================="
echo "✅ Release Complete!"
echo "=============================="
echo ""
echo "📦 Package: webweaver-$VERSION.zip"
echo "📍 Location: $RELEASES_DIR/"
echo "🔐 SHA256: $SHA256"
echo "🌐 GitHub: https://github.com/superjunaid/webweaver/releases/tag/v$VERSION"
echo ""
echo "Next steps:"
echo "1. Visit: https://github.com/superjunaid/webweaver/releases/tag/v$VERSION"
echo "2. Download ZIP from release page"
echo "3. Install on your WordPress site"
echo ""
echo "✨ Done!"
