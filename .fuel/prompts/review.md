<fuel-prompt version="1" />

# Review Task: {{task.id}}
You are reviewing work. You MUST only review, you are not allowed to make any file edits, you MUST respond with one message with valid JSON as described below.

You are reviewing the work done on task **{{task.id}}**: {{task.title}}

## Task Description

{{task.description}}

## Git Status

```
{{git.status}}
```

## Git Diff

```diff
{{git.diff}}
```

---

## Review Checklist

Complete each check and note any issues found.

### 1. CHECK UNCOMMITTED CHANGES

Look at the git status output above. Are there uncommitted changes (modified files, untracked files that should be committed)?

### 2. VERIFY RELEVANT TESTS

If the changes affect code that has tests, run those relevant tests to verify they pass.
Don't run the entire test suite - only tests related to the files that were changed.

### 3. CHECK TASK COMPLETION

Compare the git diff to the task description above.

- Does the change actually address what was asked?
- Are there any missing requirements from the description?
- Is the implementation complete?

---

## REQUIRED: Output Your Review Result

After completing your review, you MUST output a JSON block with your findings.
This is REQUIRED - the system parses this output to track review results.

**If ALL checks pass**, run:
```bash
fuel done {{task.id}}
```

Then output:
```json
{"result": "pass", "issues": []}
```

**If ANY issues were found**, output (do NOT run fuel done):
```json
{"result": "fail", "issues": [{"type": "TYPE", "description": "DESCRIPTION"}]}
```

Issue types: `uncommitted_changes`, `tests_failing`, `incomplete`, `other`

Examples:

```json
{"result": "fail", "issues": [{"type": "uncommitted_changes", "description": "Modified files not committed: src/Service.php, src/Controller.php"}, {"type": "tests_failing", "description": "UserServiceTest::testCreate failed - expected 200, got 500"}]}
```

```json
{"result": "fail", "issues": [{"type": "tests_failing", "description": "UserServiceTest::testCreate failed - expected 200, got 500"}]}
```

```json
{"result": "fail", "issues": [{"type": "incomplete", "description": "Missing validation for email field as specified in requirements"}]}
```
