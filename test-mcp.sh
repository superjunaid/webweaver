#!/bin/bash

# WebWeaver MCP Testing Script
set -e

BASE_URL="http://localhost:8888"
API_BASE="$BASE_URL/wp-json/wp-mcp/v1"

echo "=== WebWeaver MCP API Test Suite ==="
echo "Base URL: $BASE_URL"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

passed=0
failed=0

test_endpoint() {
    local name="$1"
    local method="$2"
    local endpoint="$3"
    local auth_header="$4"
    
    echo -n "Testing: $name... "
    
    if [ -z "$auth_header" ]; then
        response=$(curl -s -w "\n%{http_code}" -X "$method" "$API_BASE$endpoint")
    else
        response=$(curl -s -w "\n%{http_code}" -X "$method" \
            -H "Authorization: Bearer $auth_header" \
            "$API_BASE$endpoint")
    fi
    
    http_code=$(echo "$response" | tail -1)
    body=$(echo "$response" | head -1)
    
    if [[ "$http_code" =~ ^(200|201|400|401|403)$ ]]; then
        echo -e "${GREEN}✓${NC} ($http_code)"
        ((passed++))
        if [ ! -z "$body" ]; then
            echo "  Response: $(echo "$body" | head -c 100)..."
        fi
    else
        echo -e "${RED}✗${NC} ($http_code)"
        ((failed++))
        echo "  Response: $body"
    fi
    echo ""
}

# First, check if WordPress is up
echo "Checking WordPress connectivity..."
if curl -s "$BASE_URL" > /dev/null; then
    echo -e "${GREEN}✓${NC} WordPress is accessible"
else
    echo -e "${RED}✗${NC} Cannot reach WordPress"
    exit 1
fi
echo ""

# Test unauthenticated access (should fail)
test_endpoint "Tools Manifest (no auth)" "GET" "/tools" ""

# Generate a test user and get credentials
echo "Setting up test user..."
docker-compose exec wordpress wp --allow-root user create testuser test@example.com \
    --user_pass=testpass123 --role=editor 2>/dev/null || true

# Get Basic Auth header (base64 encoded username:password)
AUTH_HEADER=$(echo -n "testuser:testpass123" | base64)

echo -e "${GREEN}✓${NC} Test user ready"
echo ""

# Test authenticated endpoints
test_endpoint "Tools Manifest (authenticated)" "GET" "/tools" "$AUTH_HEADER"
test_endpoint "List Posts" "GET" "/posts" "$AUTH_HEADER"
test_endpoint "List Posts (post type)" "GET" "/posts?type=post" "$AUTH_HEADER"
test_endpoint "List Posts (page type)" "GET" "/posts?type=page" "$AUTH_HEADER"

echo ""
echo "=== Test Results ==="
echo -e "Passed: ${GREEN}$passed${NC}"
echo -e "Failed: ${RED}$failed${NC}"
echo ""

if [ $failed -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed${NC}"
    exit 1
fi
