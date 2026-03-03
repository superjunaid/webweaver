#!/usr/bin/env node
/**
 * WebWeaver MCP Server
 * Wraps the REST API as an MCP-compatible server
 * 
 * Usage: node mcp-server.js
 * 
 * Configure in Claude:
 * - Name: WebWeaver
 * - Remote MCP server URL: http://localhost:3000
 */

const http = require('http');
const https = require('https');
const url = require('url');

// Configuration
const WORDPRESS_URL = process.env.WORDPRESS_URL || 'http://localhost:8888';
const WORDPRESS_USER = process.env.WORDPRESS_USER || 'admin';
const WORDPRESS_PASSWORD = process.env.WORDPRESS_PASSWORD || 'wordpress';
const MCP_PORT = process.env.MCP_PORT || 3000;

// Create Basic Auth header
const authHeader = Buffer.from(`${WORDPRESS_USER}:${WORDPRESS_PASSWORD}`).toString('base64');

console.log(`🚀 WebWeaver MCP Server starting...`);
console.log(`📍 WordPress: ${WORDPRESS_URL}`);
console.log(`🔐 Auth: ${WORDPRESS_USER}`);
console.log(`🌐 Listening on port ${MCP_PORT}`);

// Helper to make WordPress API calls
function wordpressAPI(endpoint, method = 'GET', body = null) {
    return new Promise((resolve, reject) => {
        const apiUrl = new url.URL(`${WORDPRESS_URL}/wp-json/wp-mcp/v1${endpoint}`);
        const protocol = apiUrl.protocol === 'https:' ? https : http;

        const options = {
            hostname: apiUrl.hostname,
            port: apiUrl.port,
            path: apiUrl.pathname + apiUrl.search,
            method: method,
            headers: {
                'Authorization': `Basic ${authHeader}`,
                'Content-Type': 'application/json',
                'User-Agent': 'WebWeaver-MCP-Server/1.0'
            }
        };

        const req = protocol.request(options, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                try {
                    resolve({
                        status: res.statusCode,
                        data: JSON.parse(data)
                    });
                } catch (e) {
                    resolve({
                        status: res.statusCode,
                        data: data
                    });
                }
            });
        });

        req.on('error', reject);
        
        if (body) {
            req.write(JSON.stringify(body));
        }
        req.end();
    });
}

// MCP Server
const server = http.createServer(async (req, res) => {
    // Enable CORS
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    
    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    const parsed = url.parse(req.url, true);
    const pathname = parsed.pathname;
    const query = parsed.query;

    try {
        // MCP Info Endpoint
        if (pathname === '/' || pathname === '/mcp') {
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({
                name: 'WebWeaver',
                version: '1.0.0',
                description: 'AI-powered WordPress content creation',
                capabilities: ['read', 'write', 'media']
            }));
            return;
        }

        // Tools Endpoint
        if (pathname === '/tools' || pathname === '/mcp/tools') {
            const result = await wordpressAPI('/tools');
            res.writeHead(result.status, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify(result.data));
            return;
        }

        // List Posts
        if (pathname === '/posts' || pathname === '/mcp/posts') {
            const queryStr = Object.keys(query)
                .map(k => `${k}=${query[k]}`)
                .join('&');
            const result = await wordpressAPI(`/posts${queryStr ? '?' + queryStr : ''}`);
            res.writeHead(result.status, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify(result.data));
            return;
        }

        // Get Post
        const postMatch = pathname.match(/^\/post\/(\d+)/);
        if (postMatch) {
            const postId = postMatch[1];
            const result = await wordpressAPI(`/post/${postId}`);
            res.writeHead(result.status, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify(result.data));
            return;
        }

        // Create Post
        if (pathname === '/post' && req.method === 'POST') {
            let body = '';
            req.on('data', chunk => body += chunk);
            req.on('end', async () => {
                const data = JSON.parse(body);
                const result = await wordpressAPI('/post', 'POST', data);
                res.writeHead(result.status, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify(result.data));
            });
            return;
        }

        // Update Post
        const updateMatch = pathname.match(/^\/post\/(\d+)$/) && req.method === 'PUT';
        if (updateMatch) {
            const postId = pathname.match(/\d+/)[0];
            let body = '';
            req.on('data', chunk => body += chunk);
            req.on('end', async () => {
                const data = JSON.parse(body);
                const result = await wordpressAPI(`/post/${postId}`, 'PUT', data);
                res.writeHead(result.status, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify(result.data));
            });
            return;
        }

        // Default response
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            error: 'Not found',
            available: [
                'GET /tools',
                'GET /posts?type=post&per_page=10',
                'GET /post/{id}',
                'POST /post',
                'PUT /post/{id}'
            ]
        }));

    } catch (error) {
        console.error('Error:', error);
        res.writeHead(500, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            error: error.message
        }));
    }
});

server.listen(MCP_PORT, '0.0.0.0', () => {
    console.log(`✅ MCP server ready at http://localhost:${MCP_PORT}`);
    console.log(`\n📋 Available endpoints:`);
    console.log(`  GET  /tools              - List available tools`);
    console.log(`  GET  /posts              - List posts`);
    console.log(`  GET  /post/{id}          - Get post details`);
    console.log(`  POST /post               - Create new post`);
    console.log(`  PUT  /post/{id}          - Update post`);
});

server.on('error', (err) => {
    console.error('Server error:', err);
    process.exit(1);
});
