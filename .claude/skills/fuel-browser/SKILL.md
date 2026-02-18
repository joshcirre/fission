---
name: fuel-browser
description: Control a headless browser for testing, screenshots, and web automation. Use when testing web pages, taking screenshots, interacting with web pages, verifying UI changes, or scraping rendered HTML.
user-invocable: false
---

# Headless Browser

## Quick Start

```bash
fuel browser:create ctx page1                    # Create context + page
fuel browser:goto page1 "http://localhost:3000"  # Navigate
fuel browser:snapshot page1 -i                   # Get interactive elements with refs
fuel browser:click page1 @e1                     # Click by ref
fuel browser:fill page1 @e2 "text"               # Fill by ref
fuel browser:refresh page1                       # Reload after code changes
fuel browser:rescreenshot page1                  # Refresh + screenshot in one
fuel browser:close ctx                           # Cleanup
```

## Quick Screenshot

Take a screenshot of any URL in one command - no setup needed. Saves to `.fuel/context/browser/screenshots/` by default:

```bash
fuel browser:screenshot --url="http://localhost:3000"                    # Auto-saves to .fuel/context/browser/screenshots/screenshot-xxxx.png (default)
fuel browser:screenshot --url="http://localhost:3000" /tmp/shot.png      # Custom path
fuel browser:screenshot --url="http://localhost:3000" --base64           # Get base64 data URI
```

## Core Workflow

1. Navigate: `fuel browser:goto page "http://..."`
2. Snapshot: `fuel browser:snapshot page -i` (returns elements with refs like `@e1`, `@e2`)
3. Interact using refs from the snapshot
4. Re-snapshot after navigation or significant DOM changes

## Commands

### Create Context with Page
```bash
fuel browser:create <context_id> [page_id]
```
Creates a browser context and initial page. Page defaults to `{context_id}-tab1`.

Options:
- `--viewport='{"width":1280,"height":720}'` - Set viewport size
- `--user-agent="..."` - Custom user agent
- `--dark` - Use dark color scheme

### Navigate to URL
```bash
fuel browser:goto <page_id> <url>
```

Options:
- `--wait-until=load|domcontentloaded|networkidle` - Wait condition (use `networkidle` for SPAs)
- `--timeout=30000` - Navigation timeout (ms)
- `--html` - Return rendered HTML/DOM after navigation

### Refresh Page
```bash
fuel browser:refresh <page_id>
```
Reload the current page. Useful after code changes to see updated output.

Options:
- `--wait-until=load|domcontentloaded|networkidle` - Wait condition (default: load)
- `--timeout=30000` - Reload timeout (ms)

### Accessibility Snapshot
```bash
fuel browser:snapshot <page_id> [-i] [-s|--scope=<selector>] [--json]
```
Get the accessibility tree with element refs (@e1, @e2, etc).

Options:
- `-i` / `--interactive` - Only include interactive elements (recommended)
- `-s` / `--scope` - Scope snapshot to CSS selector
- `--json` - Output as JSON

Example output:
```
@e1 [heading] "Welcome"
@e2 [link] "Learn more"
@e3 [textbox] "Email"
@e4 [button] "Submit"
```

Examples with scope:
```bash
fuel browser:snapshot page1 -i -s "#main"     # Interactive elements in #main only
fuel browser:snapshot page1 --scope="form"    # Snapshot just the form
```

### Click Element
```bash
fuel browser:click <page_id> <target>
```
Target is auto-detected: starts with `@` = ref, otherwise = CSS selector.

Examples:
```bash
fuel browser:click page1 @e3              # Click by ref
fuel browser:click page1 "button.submit"  # Click by selector
```

### Fill Input
```bash
fuel browser:fill <page_id> <target> <value>
```
Clear input and fill with value. Target auto-detected.

Examples:
```bash
fuel browser:fill page1 @e3 "user@example.com"     # By ref
fuel browser:fill page1 "input#email" "test@x.com" # By selector
```

### Type Text
```bash
fuel browser:type <page_id> <target> <text> [--delay=0]
```
Type character by character (doesn't clear first). Target auto-detected.

Options:
- `--delay=50` - Delay between keystrokes in milliseconds

Examples:
```bash
fuel browser:type page1 @e2 "hello world"
fuel browser:type page1 "input#search" "query" --delay=50
```

### Get Text Content
```bash
fuel browser:text <page_id> <target>
```
Get text content of element. Target auto-detected.

Examples:
```bash
fuel browser:text page1 @e1       # By ref
fuel browser:text page1 "h1"      # By selector
```

### Get HTML
```bash
fuel browser:html <page_id> <target> [--inner]
```
Get HTML of element. Target auto-detected.

Options:
- `--inner` - Return innerHTML instead of outerHTML

Examples:
```bash
fuel browser:html page1 @e5 --inner
fuel browser:html page1 "div.content"
```

### Take Screenshot
```bash
# Quick screenshot (recommended) - saves to .fuel/context/browser/screenshots with random filename
fuel browser:screenshot --url=<url>

# Specify output path
fuel browser:screenshot --url=<url> /path/to/file.png

# Screenshot existing page
fuel browser:screenshot <page_id>
fuel browser:screenshot <page_id> /path/to/file.png

# Get base64 data URI (use --base64 flag)
fuel browser:screenshot --url=<url> --base64

# JPEG for smaller file size
fuel browser:screenshot --url=<url> --format=jpeg /path/to/file.jpg
```

Options:
- `--format=png` - Image format: png (default, lossless) or jpeg (smaller)
- `--quality=80` - JPEG quality 1-100 (default 80, ignored for PNG)
- `--full-page` - Capture entire scrollable page
- `--width=1280` - Viewport width (only with --url)
- `--height=720` - Viewport height (only with --url)
- `--dark` - Use dark color scheme (only with --url)
- `--base64` - Return base64 data URI instead of saving to file (default behavior saves to a temp file and returns its path)
- `--path=` - Alternative to positional path argument

### Re-screenshot Page (Refresh + Screenshot)
```bash
fuel browser:rescreenshot <page_id>
fuel browser:rescreenshot <page_id> /path/to/file.png
fuel browser:rescreenshot <page_id> --base64
```
Refreshes the page (networkidle), waits for CSS animations to complete, then takes a screenshot. Ideal after code changes.

Options:
- `--format=png` - Image format: png or jpeg
- `--quality=80` - JPEG quality 1-100
- `--full-page` - Capture entire scrollable page
- `--base64` - Return base64 data URI

### Wait for Condition
```bash
fuel browser:wait <page_id> [--selector=] [--url=] [--text=] [--timeout=30000]
fuel browser:wait <page_id> <ref>         # Wait for ref
fuel browser:wait <page_id> <milliseconds> # Wait fixed time
```
Wait for selector, URL pattern, text, ref, or fixed time.

Options:
- `--selector="..."` - Wait for CSS selector
- `--url="**/path"` - Wait for URL pattern (glob)
- `--text="..."` - Wait for text on page
- `--state=visible|hidden` - State for selector (default: visible)
- `--timeout=30000` - Timeout in milliseconds

Examples:
```bash
fuel browser:wait page1 --selector=".modal"
fuel browser:wait page1 --url="**/dashboard"
fuel browser:wait page1 --text="Success"
fuel browser:wait page1 --selector=".spinner" --state=hidden
fuel browser:wait page1 @e1        # Wait for ref to be visible
fuel browser:wait page1 2000       # Wait 2 seconds
```

### Scroll Page
```bash
fuel browser:scroll <page_id> <direction> [amount]
```
Scroll the page in the specified direction.

Arguments:
- `<direction>` - Direction to scroll: up, down, left, or right
- `[amount]` - Pixels to scroll (default: 100)

Examples:
```bash
fuel browser:scroll page1 down 500    # Scroll down 500px
fuel browser:scroll page1 up 200      # Scroll up 200px
fuel browser:scroll page1 right 100   # Scroll right 100px
fuel browser:scroll page1 down        # Scroll down 100px (default)
```

### Scroll Element Into View
```bash
fuel browser:scrollintoview <page_id> <target>
```
Scroll an element into view. Target is auto-detected: starts with `@` = ref, otherwise = CSS selector.

Examples:
```bash
fuel browser:scrollintoview page1 @e5          # Scroll ref into view
fuel browser:scrollintoview page1 "footer"     # Scroll selector into view
fuel browser:scrollintoview page1 "#comments"  # Scroll element into view
```

### Run Playwright Code
```bash
fuel browser:run <page_id> "<code>"
```
Run Playwright code with access to `page` object.

```bash
fuel browser:run page1 "return await page.title()"
fuel browser:run page1 "await page.click('.btn')"
fuel browser:run page1 "return await page.locator('.item').count()"
```

### Additional Commands
```bash
fuel browser:refresh <page_id>             # Reload page after code changes
fuel browser:rescreenshot <page_id>        # Refresh + wait + screenshot in one
fuel browser:page <context_id> <page_id>  # Create additional page/tab
fuel browser:close <context_id>            # Close context and all pages
fuel browser:status                        # Check daemon status
```

## Example: Form Submission

```bash
fuel browser:create test page1
fuel browser:goto page1 "http://localhost:3000/form"
fuel browser:snapshot page1 -i
# @e1 [textbox] "Name"
# @e2 [textbox] "Email"
# @e3 [button] "Submit"

fuel browser:fill page1 @e1 "John Doe"
fuel browser:fill page1 @e2 "john@example.com"
fuel browser:click page1 @e3
fuel browser:wait page1 --text="Thank you"
fuel browser:close test
```

## Example: Visual Testing

```bash
fuel browser:screenshot --url="http://localhost:3000" .fuel/context/browser/screenshots/desktop.png --width=1920 --height=1080
fuel browser:screenshot --url="http://localhost:3000" .fuel/context/browser/screenshots/mobile.png --width=375 --height=812
fuel browser:screenshot --url="http://localhost:3000" .fuel/context/browser/screenshots/dark.png --dark
```

## Example: SPA Testing

```bash
fuel browser:create spa page1
fuel browser:goto page1 "http://localhost:3000" --wait-until=networkidle
fuel browser:snapshot page1 -i
fuel browser:click page1 "a[href='/products']"
fuel browser:wait page1 --url="**/products"
fuel browser:snapshot page1 -i
fuel browser:fill page1 "input[type='search']" "laptop"
fuel browser:wait page1 --text="results"
fuel browser:close spa
```

## Tips

1. **Always snapshot first** - get refs before interacting
2. **Use refs (@e1)** for reliability over CSS selectors
3. **Use `--wait-until=networkidle`** for SPAs
4. **Use `browser:wait`** after actions that trigger async updates
5. **Re-snapshot after navigation** - refs become invalid after page changes
6. **Use `browser:rescreenshot`** after code changes - refreshes, waits for animations, and screenshots in one command
7. **Screenshots auto-wait for CSS animations** - no need for manual delays, `document.getAnimations()` is checked automatically
