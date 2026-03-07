<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enable instruckt
    |--------------------------------------------------------------------------
    | Set to false in production or use an environment variable to gate access.
    */
    /*
    | Defaults to true only in local environments. Set INSTRUCKT_ENABLED=false
    | to disable explicitly, or INSTRUCKT_ENABLED=true to enable on staging.
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
    | Applied to all instruckt API routes. 'web' gives you session/CSRF.
    | Add 'auth' here if you want to gate annotations to logged-in users.
    */
    /*
    | Uses 'api' by default (no CSRF). Add 'auth' here to gate to logged-in users.
    */
    'middleware' => explode(',', env('INSTRUCKT_MIDDLEWARE', 'api')),

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
    | MCP tool name prefix
    |--------------------------------------------------------------------------
    */
    'mcp_prefix' => 'instruckt',

];
