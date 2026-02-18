---
name: fuel-create-plan
description: Create a fuel epic and detailed implementation plan for new functionality. Use when the user wants to plan something, or they're building something across multiple files/tasks, designing implementations, entering plan mode, exiting plan mode, or when explicitly requested. This must be used when planning.
---

# Create Plan

Design implementations with full codebase context before breaking into tasks.
Critical: Do NOT start implementing. Your job is to create a fuel epic and plan markdown .fuel/plans/*.md file.

## When to Use

Invoke this skill when:
- Entering plan mode for a new feature
- Creating an epic or spec
- Designing an implementation approach
- You need architectural context before planning
- Requirements are unclear and you need to interview the user

**After plan approval**, use the `fuel-make-plan-actionable` skill to convert to tasks if it is a parallel epic and not selfguided.

## Workflow

### 0. Interview 

If the user's request is unclear or missing key details, ask the user questions to gather requirements. Ask about:

- **Goal** - What should this feature achieve? What problem does it solve?
- **Scope** - What's in scope vs out of scope? What's the MVP?
- **User Impact** - Who uses this? What's their workflow?
- **Constraints** - Performance requirements, compatibility needs, deadlines?
- **Integration** - What existing features/services does this interact with?
- **Success Criteria** - How will we know this is complete and working?

Keep questions focused and specific. Aim for 1-4 questions per round. Use the gathered answers to inform your planning.

After each response, decide whether to:
- Ask follow-up questions (if answers reveal complexity)
- Ask about a new aspect (if current area is clear)

### 1. Read Reality for Context

Start by understanding the codebase architecture:

```bash
cat .fuel/reality.md
```

Look for:
- **Architecture** - Overall structure, patterns in use
- **Modules** - Where related functionality lives
- **Entry Points** - Where to hook new features
- **Patterns** - Conventions to follow
- **Recent Changes** - Related work that might inform design

### 2. Explore Related Code

Use reality.md to identify relevant files, then explore:
- Similar implementations to follow as patterns
- Interfaces your feature should implement
- Tests that show expected behavior

### 3. Design the Solution

Write a clear plan that includes:
- **Goal** - What the feature achieves and why
- **Approach** - How you'll implement it
- **Files to modify** - Specific paths
- **New files needed** - With proposed locations
- References to existing code patterns in the project if useful
- **Edge cases** - Errors, validation, boundaries
- **Testing strategy** - How to verify it works
- Acceptance criteria - How we know we've achieved the user's intent - machine-verifiable
- Smoketesting - Use the functionality as we go to ensure it actually works
- For complex interactions, ASCII diagrams can help (architecture, flow charts, sequence diagrams)

**If the feature requires dev services** (e.g., artisan serve, Vite, queue workers), consider defining them in `.fuel/run.yml` so other agents can start them with `fuel run`.

### 4. Create Epic

Pass `--selfguided` if you chose selfguided mode, otherwise leave it out.

```bash
fuel epic:add "Feature name" --description="What and why" [--selfguided]
```

Note the epic ID (e.g., `e-abc123`). A plan file is auto-created at `.fuel/plans/{title-kebab}-{epic-id}.md`, which you must immediately merge your plan into based on its pre-existing sections.

**Epics start paused** - tasks won't be consumed until you run `fuel unpause e-abc123`. This gives you time to add all tasks and dependencies before execution begins.

### 5. Choose Execution Mode

Based on your exploration, decide between parallel and selfguided. **State your choice and briefly justify it** — the user can override.

**Prefer selfguided when:**
- Exploratory/research-heavy (unclear how to implement until you start)
- Tightly coupled changes (most tasks touch the same files)
- Requirements may evolve during implementation
- Requires iterative verification (UI tweaks, performance tuning, visual changes)
- Work can't be cleanly split into independent units

**Prefer parallel when:**
- Clear, well-defined requirements
- Work naturally splits into independent units (different files/modules/areas)
- Tasks have minimal interdependency
- Each unit can be verified independently

**Announce your choice**, e.g.: "Using parallel — the work splits cleanly into API, UI, and tests with no shared files." or "Using selfguided — this is exploratory and needs iterative verification."

### 6. Document the Plan

Write your plan to the epic's plan file. Merge your thinking with the existing structure of the plan.

Fuel will setup the recommended template in the file it shares with you at `.fuel/plans/*.md`.

**CRITICAL for self-guided mode:** Without explicit `- [ ]` checkbox criteria, the agent will complete everything in one pass instead of iterating. Each criterion should be:
- Specific and testable (not vague like "make it work")
- Independent (can be verified separately)
- Measurable (clear pass/fail state)

### 7. Commit the Plan File

After writing the plan, commit it to git:

```bash
git add .fuel/plans/{epic-title-kebab}-{epic-id}.md
git commit -m "plan: {epic title}"
```

Plan files in `.fuel/plans/` are tracked in git for version control and audit purposes.

### 8. Exit Plan Mode

Once your plan is complete, exit plan mode for approval if required.
After approval, if parallel mode, use the `fuel-make-plan-actionable` skill to convert the plan into tasks.

## When Reality Doesn't Exist

If `.fuel/reality.md` is a stub or empty:
- Explore the codebase manually
- Focus on similar existing features
- Document what you learn in your plan for future reference

After the first epic completes, reality.md will be populated.

## Example Planning Session

### Example 1: Clear Requirements
1. User asks: "Add user notification preferences"
2. Read `.fuel/reality.md` - find existing UserPreference model, NotificationService
3. Explore `app/Services/NotificationService.php` - understand current flow
4. Design: extend UserPreference model, add preference check to NotificationService
5. Choose execution mode: "Using parallel — work splits cleanly into model extension, service integration, and tests with minimal overlap"
6. Create epic: `fuel epic:add "User notification preferences"`
7. Write plan to `.fuel/plans/user-notification-preferences-e-xxxx.md`
8. Commit plan: `git add .fuel/plans/user-notification-preferences-e-xxxx.md && git commit -m "plan: User notification preferences"`
9. Exit plan mode, await approval
10. On approval, invoke `fuel-make-plan-actionable` to create tasks

### Example 2: Unclear Requirements (Interview First)
1. User asks: "Make the app faster"
2. Interview: Ask questions to clarify scope
   - "Which part of the app feels slow? API responses, page loads, or background jobs?"
   - "What's the current performance baseline and target?"
   - "Are there specific user workflows affected?"
3. User responds: "API responses take 2-3 seconds, target is <500ms, affects dashboard load"
4. Read `.fuel/reality.md` - find API architecture, caching strategy
5. Profile: Explore dashboard API endpoints, check for N+1 queries
6. Design: add query optimization, implement response caching, add indexes
7. Choose execution mode: "Using selfguided — exploratory work requiring profiling and iterative performance testing to hit the <500ms target"
8. Create epic: `fuel epic:add "Optimize dashboard API performance" --selfguided`
9. Write plan to `.fuel/plans/optimize-dashboard-api-performance-e-xxxx.md`
10. Commit plan: `git add .fuel/plans/optimize-dashboard-api-performance-e-xxxx.md && git commit -m "plan: Optimize dashboard API performance"`
11. Exit plan mode, await approval
12. Epic will run in selfguided mode (no task breakdown needed)

## Next: Convert to Tasks

Once your plan is approved, use the **fuel-make-plan-actionable** skill to:
- Break the plan into individual tasks with `fuel add:json e-xxxx /path/tasks.json`
- Set proper complexity and dependencies
- Create a mandatory review task
- **Unpause the epic** with `fuel unpause e-xxxx` to start execution

The two skills form a complete workflow:
1. **fuel-create-plan** → Design with context
2. **fuel-make-plan-actionable** → Convert to executable tasks
3. **fuel unpause** → Start execution once all tasks are ready
