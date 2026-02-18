<fuel-prompt version="5" />

== SELF-GUIDED EPIC EXECUTION ==
Iteration {{ iteration }} of {{ max_iterations }}

You are executing an epic incrementally. Each iteration you assess progress, execute one task, and decide what to do next.

== YOUR TASK ==
**Task:** {{ task.short_id }}
**Title:** {{ task.title }}
**Epic:** {{ epic.short_id }}

== PREVIOUS PROGRESS ==
{{ progress_log }}

---

== CODEBASE CONTEXT: Study this ==
{{ reality }}

== EPIC PLAN: Study this ==
{{ plan }}


== INSTRUCTIONS ==

### 1. Assess Current State
Study all context above to understand the biggest picture.

### 2. Execute ONE Criterion
Pick the next most important unchecked criterion and implement it fully:
- Write code / Implement the requirements on the current branch
- Run tests and quality gates for the affected files
- **Smoke test it yourself** - actually run/use what you built:
  - CLI command? Run it: `./fuel mycommand`
  - Web feature? Load it in browser
  - API endpoint? Call it with curl
  - Library code? Write a quick test script
  - Figure out a safe way to use it
- If .fuel/run.yml exists, run `fuel run` to start dev services. Use `fuel run:status` to find URLs (e.g., Vite dev server, artisan serve).
- Fix any issues found

You MUST execute only one task / criterion. Do not implement everything.

**WARNING: Unit tests passing ≠ feature works.** Tests may mock, stub, or early-return. You must verify the real thing works.

### 3. Commit Your Changes
```bash
git add <files>
git commit -m "feat({{ epic.short_id }}): [description of what you did]"
```

### 4. Update Plan File
Edit `{{ epic_plan_filename }}` to:
- Check off completed criterion: `- [x] Criterion text`
- Append to Progress Log section: `- Iteration {{ iteration }}: [what you did, files modified, learnings (patterns, gotchas)]`

### 5. Decide Next Action

**CRITICAL: You MUST run ONE of these commands before exiting. Do NOT exit without running one of these:**

**All acceptance criteria complete? Happy with the work?**
```bash
fuel done {{ task.short_id }} --commit=[hash from step 3] --notes='Final summary of what was completed'
```

**More work to do?**
```bash
fuel selfguided:continue {{ task.short_id }} --commit=[git_hash] --notes='{{Progress summary}}'
```

**IMPORTANT:** Always pass the `--commit` flag with the commit hash from step 3. This tracks which commit was made in this iteration, enabling proper commit history across selfguided task iterations.

**Stuck or need human input?**
Only use when you cannot work around this issues.
```bash
fuel selfguided:blocked {{ task.short_id }} --reason='Why you are blocked'
```

**REMINDER: Your session will end after this. You MUST run one of the above commands NOW.**


== QUALITY GATES ==
Before marking any criterion complete:
- [ ] **You ran/used the feature and it actually works** (not just tests!)
- [ ] Browser tests verified (if web page)
- [ ] Changes committed with descriptive message

== COMMITS ==
Quality checks run automatically on commit via pre-commit hook.
If commit fails: read error output, fix issues, commit again.

FORBIDDEN: git commit --no-verify, git commit -n

== RULES ==
- ONE criterion per iteration - don't try to do everything at once
- ALWAYS update the plan file to track progress
- ALWAYS commit before continuing or finishing
- If stuck on same criterion 3+ times and can't work around it, use `selfguided:blocked`
