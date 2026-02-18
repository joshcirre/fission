---
name: fuel-make-plan-actionable
description: Convert an approved plan into well-defined Fuel tasks with epics, dependencies, and review tasks. Use after exiting plan mode.
---

# Make Plan Actionable

Convert an approved plan into well-defined Fuel tasks using `tasks.json` + `fuel add:json`.

## When to Use
Invoke this skill immediately after exiting plan mode, when you have an approved implementation plan to convert into trackable tasks.

## Workflow

### 1. Create an Epic and Save Plan
Every multi-task plan needs an epic to group related work:
```bash
fuel epic:add "Feature name" --description="What this achieves and why"
```
Note the epic ID (e.g., `e-abc123`) for linking tasks.

**Save the plan file** to `.fuel/plans/{title-kebab}-{epic-id}.md`:

You must create an epic if you don't already have one.

Tasks working on this epic will read the plan for context and update it with discoveries.

### 2. Check Execution Mode

First, check if this is a self-guided epic:
```bash
fuel epic:show [epic-id]
```

If the epic shows `self_guided: true`:
- The epic already has its implementation task
- Do NOT create additional tasks
- Just inform the user: "This is a self-guided epic, no breakdown needed."

If `self_guided` is false (default), proceed with normal task breakdown.

### 3. Design for Parallelism FIRST
Before creating tasks, identify what can run in parallel.

**Split by independent files/features, NOT by architectural layers.**

Bad (linear chain, low parallelism):
```
Model → Service → Command → Tests
```

Good (parallel where possible):
```
Model (shared prereq) ──┬──→ API changes (separate files)
                       └──→ UI changes (separate files)
Then: Tests → Review
```

**Rules for parallelism:**
1. **Same file = not parallel.** Avoid multiple tasks touching the same file(s)
2. **Tightly coupled code = one task.** If class A directly wraps/calls class B, keep together
3. **Different files + no shared hotspots = parallel tasks**
4. **Fewer larger parallel tasks > many tiny sequential tasks**
5. **Tests can start once the contract exists.** Otherwise block tests on implementation tasks

**Ask:** "Can task B start immediately, without waiting for A's output?" If no, add a dependency or merge them.

### 4. Create tasks.json

Create a `tasks.json` file in a temporary directory with all tasks. This is the ONLY way to add multiple tasks to an epic.

**JSON Structure (required format):**
```json
{
  "version": 1,
  "defaults": {
    "type": "task",
    "priority": 2,
    "complexity": "simple",
    "labels": []
  },
  "tasks": [
    {
      "id": "T1",
      "title": "Create UserPreference model and migration",
      "description": "Add model at app/Models/UserPreference.php with fields: user_id, key, value. Create migration.",
      "type": "feature",
      "priority": 1,
      "complexity": "simple"
    },
    {
      "id": "T2",
      "title": "Add preferences API endpoints",
      "description": "Add GET/POST /api/preferences routes. Return JSON. Follow existing API patterns in routes/api.php.",
      "type": "feature",
      "priority": 1,
      "complexity": "moderate",
      "blockedBy": ["T1"]
    },
    {
      "id": "T3",
      "title": "Add preferences UI component",
      "description": "Create PreferencesPanel.vue component. Fetch from API, allow editing, save on change.",
      "type": "feature",
      "priority": 1,
      "complexity": "moderate",
      "blockedBy": ["T1"]
    },
    {
      "id": "T4",
      "title": "Add preferences tests",
      "description": "Unit tests for model, feature tests for API endpoints. Cover CRUD operations.",
      "type": "test",
      "complexity": "moderate",
      "blockedBy": ["T2", "T3"]
    },
    {
      "id": "review",
      "title": "Review: User preferences",
      "description": "Verify epic complete. Criteria: 1) Preferences save and load correctly, 2) UI reflects saved state, 3) All tests pass, 4) No debug code",
      "type": "task",
      "complexity": "complex",
      "blockedBy": ["T1", "T2", "T3", "T4"]
    }
  ]
}
```

**Field Reference:**

| Field | Required | Description |
|-------|----------|-------------|
| `id` | Yes | Local reference ID (for blockedBy within this file) |
| `title` | Yes | Task title |
| `description` | No | Detailed description with file paths, behavior, patterns |
| `type` | No | `task`\|`bug`\|`fix`\|`feature`\|`epic`\|`chore`\|`docs`\|`test`\|`refactor` |
| `priority` | No | 0 (highest) to 4 (lowest), defaults to 2 |
| `complexity` | No | `trivial`\|`simple`\|`moderate`\|`complex` |
| `labels` | No | Array of strings |
| `blockedBy` | No | Array of local IDs (`T1`) or external Fuel IDs (`f-abc123`) |

**Always set `complexity` for every task.** Fuel routes work by complexity; correct complexity enables cheaper/faster agents on `trivial`/`simple` tasks.

### 5. JSON Structure Rules

**Required structure:**
- Root must be a flat object with `version`, `defaults`, and `tasks` keys
- `tasks` must be an array (not empty)
- Each task must have `id` and `title`

**Anti-patterns (will be rejected):**
```json
// BAD: Wrapper object
{ "prd": { "version": 1, "tasks": [...] } }

// BAD: Wrong array key
{ "version": 1, "userStories": [...] }
{ "version": 1, "items": [...] }

// BAD: Nested phases
{ "version": 1, "phases": { "phase1": { "tasks": [...] } } }
```

**blockedBy rules:**
- Local references: use the `id` from another task in the same file (e.g., `"T1"`)
- External references: must be full Fuel IDs with exact format `f-xxxxxx` (lowercase hex)
- No self-dependencies
- No cycles (e.g., T1 → T2 → T1)

### 6. Import Tasks

Import all tasks in one command:
```bash
fuel add:json e-abc123 tasks.json
```

Alternative input methods:
```bash
# From STDIN
cat /tmp/tasks.json | fuel add:json e-abc123 -
cat /tmp/tasks.json | fuel add:json e-abc123

# Machine-readable output
fuel add:json e-abc123 /tmp/tasks.json --json
```

The command:
- Validates JSON schema and references
- Creates all tasks atomically (rolls back on any error)
- Maps local IDs to Fuel IDs
- Applies dependencies
- Outputs the ID mapping

### 7. Create Review Task (Mandatory)
Every epic needs a final review task. Include it in your `tasks.json` (see example above).

Review tasks should:
- Have `complexity: "complex"`
- Be blocked by ALL deliverable tasks
- Include verification criteria in description
- Add any required follow-up tasks to make the epic better

**Review tasks must verify:**
1. **Intent** - Does it match the epic description? Would the user be happy?
2. **Correctness** - Do behaviors work? Tests pass? Edge cases handled?
3. **Quality** - No debug calls (dd, console.log), no useless comments, follows patterns

### 8. Unpause Epic
Epics start paused to prevent tasks from being consumed before setup is complete. Once tasks are imported, unpause to start execution:
```bash
fuel unpause e-abc123
```

## Writing Good Descriptions

**Bad:** "Fix the bug"
**Good:** "In `app/Services/TaskService.php:145`, the `find()` method throws when ID not found. Change to return null and update callers in ShowCommand.php:68 and StartCommand.php:42 to handle null."

Include:
- Exact file paths
- Line numbers when relevant
- What to change (methods, patterns)
- Expected behavior
- Patterns to follow from existing code

**Give one clear solution, not options—subagents execute, they don't decide.**

## Complexity Guide
- **trivial** - Typos, single-line fixes
- **simple** - Single file, single focus
- **moderate** - Multiple files, clear scope
- **complex** - Multiple concerns, requires judgement

## Complete Example

Plan: "Add user preferences with API and UI"

**1. Create epic:**
```bash
fuel epic:add "Add user preferences" --description="Allow users to set and retrieve preferences via API and UI"
# Returns: e-abc123
```

**2. Create tasks.json:**
```json
{
  "version": 1,
  "defaults": {
    "type": "feature",
    "priority": 1,
    "complexity": "simple"
  },
  "tasks": [
    {
      "id": "model",
      "title": "Create UserPreference model and migration",
      "description": "Add model at app/Models/UserPreference.php with user_id (FK), key (string), value (text). Create migration with index on user_id.",
      "complexity": "simple"
    },
    {
      "id": "api",
      "title": "Add preferences API endpoints",
      "description": "Add routes in routes/api.php: GET /api/preferences (list), POST /api/preferences (upsert). Controller at app/Http/Controllers/PreferenceController.php. Return JSON, follow existing patterns.",
      "complexity": "moderate",
      "blockedBy": ["model"]
    },
    {
      "id": "ui",
      "title": "Add preferences UI component",
      "description": "Create resources/js/components/PreferencesPanel.vue. Fetch from GET /api/preferences on mount, POST on change. Use existing Vue patterns.",
      "complexity": "moderate",
      "blockedBy": ["model"]
    },
    {
      "id": "tests",
      "title": "Add preferences tests",
      "description": "Feature tests in tests/Feature/PreferencesTest.php: test list, upsert, validation. Unit tests for model in tests/Unit/UserPreferenceTest.php.",
      "type": "test",
      "complexity": "moderate",
      "blockedBy": ["api", "ui"]
    },
    {
      "id": "review",
      "title": "Review: User preferences",
      "description": "Verify epic complete. Criteria: 1) Preferences save and load correctly, 2) UI reflects saved state, 3) All tests pass: vendor/bin/pest tests/Feature/PreferencesTest.php tests/Unit/UserPreferenceTest.php, 4) No debug code",
      "type": "task",
      "complexity": "complex",
      "blockedBy": ["model", "api", "ui", "tests"]
    }
  ]
}
```

**3. Import and unpause:**
```bash
fuel add:json e-abc123 tasks.json
fuel unpause e-abc123
```

Notes:
- `api` and `ui` can run in parallel (different files, both only depend on `model`)
- `tests` waits for both `api` and `ui`
- `review` waits for everything
- If API+UI need to agree on a contract first, add a "Define contract" task and block both on it
