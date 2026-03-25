<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enable instruckt
    |--------------------------------------------------------------------------
    | Defaults to true only in local environments. Set INSTRUCKT_ENABLED=true
    | to enable on staging/preview, or INSTRUCKT_ENABLED=false to disable.
    */
    'enabled' => (bool) env('INSTRUCKT_ENABLED', env('APP_ENV') === 'local'),

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    | All HTTP API routes will be registered under this prefix.
    */
    'route_prefix' => env('INSTRUCKT_ROUTE_PREFIX', 'instruckt'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    | Separate middleware stacks for the annotation API and the MCP endpoint.
    |
    | api_middleware  — applied to annotation CRUD routes (POST, PATCH, GET).
    |                   'api' by default (no CSRF). Add 'auth' to gate to
    |                   logged-in users on hosted environments.
    |
    | mcp_middleware  — applied to the /instruckt/mcp SSE endpoint.
    |                   Defaults to 'web'. On hosted environments this should
    |                   include token auth — set INSTRUCKT_MCP_TOKEN and the
    |                   package will validate Bearer tokens automatically.
    */
    'api_middleware' => explode(',', env('INSTRUCKT_MIDDLEWARE', 'api')),
    'mcp_middleware' => explode(',', env('INSTRUCKT_MCP_MIDDLEWARE', 'web')),

    /*
    |--------------------------------------------------------------------------
    | MCP token
    |--------------------------------------------------------------------------
    | When set, the /instruckt/mcp endpoint requires an Authorization: Bearer
    | header matching this value. Strongly recommended for hosted environments.
    |
    | Example .mcp.json entry for Claude Code:
    |   {
    |     "instruckt": {
    |       "url": "https://your-app.com/instruckt/mcp",
    |       "headers": { "Authorization": "Bearer your-secret-token" }
    |     }
    |   }
    */
    'mcp_token' => env('INSTRUCKT_MCP_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | Storage driver
    |--------------------------------------------------------------------------
    | Controls where annotations are persisted.
    |
    | 'file'     — flat JSON in storage/app/_instruckt/annotations.json.
    |              Zero setup. Default for local development.
    |
    | 'database' — stores annotations in the instruckt_annotations table.
    |              Run: php artisan migrate
    |              Required for hosted/multi-instance environments.
    |
    | Defaults to 'file' locally, 'database' everywhere else.
    */
    'store' => env('INSTRUCKT_STORE', env('APP_ENV') === 'local' ? 'file' : 'database'),

    /*
    |--------------------------------------------------------------------------
    | Screenshot storage disk
    |--------------------------------------------------------------------------
    | Laravel filesystem disk used to store annotation screenshots.
    | Use 'local' (default) for local development, or 's3' / any configured
    | disk for hosted environments so screenshots survive deploys.
    */
    'screenshot_disk' => env('INSTRUCKT_SCREENSHOT_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | CDN URL
    |--------------------------------------------------------------------------
    | Where to load instruckt.iife.js from. By default uses the published
    | asset. Override to use a pinned CDN version.
    |
    | Example: 'https://cdn.jsdelivr.net/npm/instruckt@0.1.0/dist/instruckt.iife.js'
    */
    'cdn_url' => env('INSTRUCKT_CDN_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Marker pin colors
    |--------------------------------------------------------------------------
    | Customize the colors of annotation marker pins on the page.
    | All values are CSS color strings. Leave empty for defaults.
    */
    'colors' => [
        // 'default'    => '#6366f1',  // indigo — standard annotations
        // 'screenshot' => '#22c55e',  // green — annotations with screenshots
        // 'dismissed'  => '#71717a',  // gray — dismissed annotations
    ],

    /*
    |--------------------------------------------------------------------------
    | Keyboard shortcuts
    |--------------------------------------------------------------------------
    | Customize the keyboard shortcuts. Values are single key characters.
    | Leave empty for defaults.
    */
    'keys' => [
        // 'annotate'   => 'a',  // toggle annotation mode
        // 'freeze'     => 'f',  // freeze page
        // 'screenshot' => 'c',  // region screenshot capture
        // 'clearPage'  => 'x',  // clear annotations on current page
    ],

    /*
    |--------------------------------------------------------------------------
    | Toolbar tool visibility
    |--------------------------------------------------------------------------
    | Show or hide built-in toolbar tools. Set to false to hide. Omit a key
    | or use true to show. Available: annotate, screenshot, freeze, copy,
    | clear_page, clear_all, minimize.
    */
    'tools' => [
        'annotate' => true,
        'screenshot' => true,
        'freeze' => true,
        'copy' => true,
        'clear_page' => true,
        'clear_all' => true,
        'minimize' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | MCP tool name prefix
    |--------------------------------------------------------------------------
    */
    'mcp_prefix' => 'instruckt',

];
