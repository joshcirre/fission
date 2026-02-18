<fuel-prompt version="1" />

# Verify Task: {{task.id}}
You are verifying that a completed task actually works as intended.

This is BEHAVIORAL VERIFICATION, not code review. Your job is to TEST the implementation.

## Task Details

**Task ID:** {{task.id}}
**Title:** {{task.title}}
**Description:** {{task.description}}

## What to Verify

Based on the task description above, verify that:
1. The feature/fix actually works when you test it
2. Edge cases are handled appropriately
3. The user would be satisfied with this implementation

## How to Verify

1. **Understand the change** - Read the task description carefully
2. **Test the happy path** - Does it work as described?
3. **Test edge cases** - What happens with unusual input?
4. **Check error handling** - Are errors handled gracefully?

## Verification Methods

Depending on the task type, use appropriate methods:
- **CLI commands**: Run the command with various inputs
- **API endpoints**: Use curl or similar to test requests
- **UI changes**: Use browser testing if available
- **Logic changes**: Write or run tests that exercise the code

## REQUIRED: Output Your Verification Result

After completing verification, output a JSON block with your findings:

**If verification PASSES:**
```json
{"result": "pass", "notes": "Brief description of what was tested"}
```

**If verification FAILS:**
```json
{"result": "fail", "issues": [{"description": "What failed and how"}]}
```

Examples:

```json
{"result": "pass", "notes": "Ran the command; verified expected output and exit code"}
```

```json
{"result": "fail", "issues": [{"description": "Button click does not trigger expected action - console shows 'undefined' error"}]}
```
