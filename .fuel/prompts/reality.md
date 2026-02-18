<fuel-prompt version="1" />

== UPDATE REALITY INDEX ==

You are updating .fuel/reality.md - a lean architectural index of this codebase.
This file helps AI agents quickly understand the codebase structure.

== COMPLETED WORK ==
{{context.completed_work}}

== INSTRUCTIONS ==
1. Read {{context.reality_path}} (create if missing using the structure below)
2. Decide if an update is REQUIRED (see DECISION GATE). If not, exit without modifying any files.
3. If updating, keep it LEAN - this is an INDEX, not documentation
4. Focus on:
   - New modules/services/commands added → add to Modules table
   - New patterns discovered → add to Patterns section
   - Entry points for common tasks → update Entry Points
   - Append to Recent Changes (keep last 5 entries, architecture-only)
   - Remove stale/outdated content if the work changed existing modules

== REALITY.MD STRUCTURE ==
If the file doesn't exist, create it with this structure:

```markdown
# Reality

## Architecture
Brief 2-3 sentence overview of what this codebase is and how it's structured.

## Modules
| Module | Purpose | Entry Point |
|--------|---------|-------------|
| Example | What it does | app/path/File.php |

## Entry Points
**Add a command:** Copy `app/Commands/ExampleCommand.php`
**Add a service:** Create in `app/Services/`, inject via constructor or `app()`

## Patterns
- Pattern: Description

## Recent Changes
- YYYY-MM-DD: Brief description of change

_Last updated: YYYY-MM-DD by UpdateReality_
```

== DECISION GATE ==
Only update reality.md when the completed work changes architecture surface area.
REQUIRED update triggers:
- Added/removed/renamed a module, service, command, or major directory
- Introduced a new runtime pathway or entry point (new command, new daemon, new IPC route)
- Established a new pattern that future work should follow
- Significantly changed how core flows work (storage, IPC, task lifecycle, agents)

If NONE of the above are true, DO NOT update.

== SKIP CONDITIONS ==
Do NOT modify reality.md if the completed work:
- Adds flags/options to existing commands without new architecture
- Touches formatting, output, wording, or UI/tables only
- Is a bug fix without architectural impact
- Is a test, docs, or comment change
- Is trivial or inconsequential to codebase understanding

If unsure, DO NOT update. Exit without modifying any files.

== RULES ==
- Be concise - one line per module/pattern
- Don't duplicate information already in CLAUDE.md
- Update the "Last updated" timestamp
- Only modify .fuel/reality.md - no other files

== CLOSING ==
After updating .fuel/reality.md, you MUST commit and push the changes:
1. git add .fuel/reality.md
2. git commit -m "chore: update reality index"
3. git push (only if a remote is configured - check with git remote first)

If you did NOT update the file, exit without committing.
