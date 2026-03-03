#!/bin/bash
set -e

echo "=== WebWeaver Local Test Environment Setup ==="
echo ""

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker not found. Install from https://www.docker.com/products/docker-desktop"
    exit 1
fi
echo "✓ Docker found"

# Check Docker Compose
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose not found"
    exit 1
fi
echo "✓ Docker Compose found"

# Start containers
echo ""
echo "Starting Docker containers..."
docker-compose up -d

# Wait for WordPress to be ready
echo ""
echo "Waiting for WordPress to be ready..."
for i in {1..30}; do
    if docker exec webweaver-wordpress wp --allow-root core is-installed &> /dev/null; then
        echo "✓ WordPress is ready"
        break
    fi
    echo "  Waiting... ($i/30)"
    sleep 2
done

# Install plugin
echo ""
echo "Setting up WebWeaver plugin..."
docker exec webweaver-wordpress wp --allow-root plugin activate webweaver || true
echo "✓ Plugin ready"

# Run validation
echo ""
echo "Running plugin validation..."
if docker exec webweaver-wordpress php /var/www/html/wp-content/plugins/webweaver/validate-plugin.php; then
    VALIDATION_PASS=true
else
    VALIDATION_PASS=false
fi

echo ""
echo "=== Setup Complete ==="
echo ""
echo "✓ WordPress running at http://localhost:8888"
echo "✓ Plugin validation: $([ "$VALIDATION_PASS" = true ] && echo 'PASSED ✓' || echo 'FAILED ✗')"
echo ""
echo "Dashboard: http://localhost:8888/wp-admin"
echo "Default credentials: admin / wordpress"
echo ""
echo "Commands:"
echo "  docker-compose down     # Stop containers"
echo "  docker-compose logs -f  # View logs"
echo "  docker-compose ps       # Check status"
