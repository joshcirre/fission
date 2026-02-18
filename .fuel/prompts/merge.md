<fuel-prompt version="5" />

# Merge Epic: {{epic.title}}

You are merging an epic's work from its isolated mirror back into the main project.

**CRITICAL: You are NOT implementing anything. You are merging already-completed work.**

## Epic Details

**Epic ID:** {{epic.id}}
**Epic Title:** {{epic.title}}
**Mirror Path:** {{mirror.path}}
**Branch:** {{mirror.branch}}
**Base Commit:** {{mirror.base_commit}}

## Your Assignment

Merge the epic's branch from the mirror into the main project and verify it passes quality gates.

## Step 1: Understand the Epic

Read the epic plan to understand what was built:
- **Plan file:** .fuel/plans/{{epic.plan_file}}

Understanding the epic's intent is critical for intelligent conflict resolution.

Ensure the Fuel epic plan file is committed and pushed as part of this merge.
Explicitly verify both copies and keep the best final version:
- Main copy: `{{project.path}}/.fuel/plans/{{epic.plan_file}}`
- Mirror copy: `{{mirror.path}}/.fuel/plans/{{epic.plan_file}}`
- If one copy is newer and more complete, use it.
- If each copy has unique useful detail, merge both into one final plan file in main.
- Stage, commit, and push the final `.fuel/plans/{{epic.plan_file}}` update.

## Step 2: Fetch the Epic Branch

You are working in the MAIN project directory: {{project.path}}

Fetch the epic's branch from the mirror:
```bash
git fetch {{mirror.path}} {{mirror.branch}}:{{mirror.branch}}
```

This brings the epic's commits into your local repository without checking them out.

## Step 3: Merge with No Fast-Forward

Create a merge commit to preserve the epic's history:
```bash
git merge {{mirror.branch}} --no-ff -m "Merge epic {{epic.id}}: {{epic.title}}"
```

## Step 4: Resolve Conflicts (If Any)

If conflicts occur:
1. **Read the epic plan** to understand the intent
2. **Examine both sides** of the conflict with `git diff`
3. **Resolve based on epic intent** - the epic's changes should generally win unless they break the main branch
4. **Preserve both changes** when possible (merge, don't replace)
5. **Test after resolution** to ensure nothing broke

Common conflict scenarios:
- **Epic modified file, main branch also modified it**: Merge both changes carefully
- **Epic added feature, main branch refactored**: Adapt epic's feature to new structure
- **Import/use statements**: Merge both, remove duplicates

After resolving conflicts:
```bash
git add <resolved-files>
git commit -m "Merge epic {{epic.id}}: {{epic.title}}"
```

## Step 5: Quality Checks

Quality checks run automatically on commit via pre-commit hook.
If commit fails: read error output, fix issues, commit again.

## Step 6: Verify Completeness

Sanity checks:
- Does the merged code match the epic's intent?
- Are all epic tasks' changes present?
- Do all tests pass?
- Is the code formatted correctly?

## Step 7: Push Changes

Push the merged changes to remote:
```bash
git push
```

## FORBIDDEN

**DO NOT run fuel commands.** You are NOT working on tasks, you are merging completed work.

Specifically, do NOT run:
- `fuel start`, `fuel done`, `fuel add`
- `fuel status`, `fuel board`, `fuel tree`
- Any other fuel commands

FORBIDDEN: `git commit --no-verify`, `git commit -n`

Your job is git operations and quality verification ONLY.

## Success Criteria

The merge is complete when:
1. The epic's branch is merged into the main branch (current branch)
2. All conflicts are resolved
3. All quality gates pass
4. The most up-to-date/detailed epic plan file is present in main and committed
5. The code is committed
6. Changes are pushed to remote

When done, simply exit. The system will handle cleanup.

## Working Directory

{{project.path}}
