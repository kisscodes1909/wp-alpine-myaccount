# AGENTS.md (Project Scope)

This file defines collaboration rules for this repository only:
`/Users/macbookair/Work/wp-alpine-myaccount`.

## 1) Goal
Keep changes small, testable, and consistent across Codex, Cursor, and Claude.

## 2) Working Model (Single Source of Truth)
Use these files as shared truth:
- `AGENTS.md`: engineering rules and workflow.
- `PROJECT_CONTEXT.md`: domain and architecture context.
- `TASK_BOARD.md`: active tasks and handoff state.

## 3) Task Intake Template
Every task must include:
- Objective
- Scope (files/folders allowed)
- Done criteria
- Verification commands
- Out-of-scope constraints

If any field is missing, the agent should make minimal safe assumptions and state them.

## 4) Branching + Ownership
- One agent per branch.
- Branch prefixes:
  - `codex/...`
  - `cursor/...`
  - `claude/...`
- Do not have two agents edit the same file at the same time.
- Prefer small PRs by feature slice.

## 5) Code Change Rules
- Keep changes minimal and local.
- Avoid unrelated refactors.
- Do not change build/enqueue patterns unless requested.
- Preserve existing conventions in PHP/JS templates.

## 6) Alpine + Woo My Account Rules
When touching Alpine code for My Account flows, follow:
`/Users/macbookair/Work/wp-alpine-myaccount/.cursor/skills/alpine-woo-myaccount/SKILL.md`

Mandatory points:
- KISS first; avoid unnecessary flags/timeouts/complex flows.
- Register stores/components/directives in JS modules under `assets/js/alpine/`.
- Do not define reusable `Alpine.data(...)` inline in PHP templates.
- Enqueue built bundle (`assets/js/alpine.bundle.js`), not `init.js`.

## 7) Testing and Verification
For each change, run only relevant checks first:
- Build check (if JS touched): `npm run build:alpine`
- Targeted runtime/manual verification steps in changed pages
- Add exact command results to handoff summary

## 8) Handoff Format (Required)
After each working session, append to `TASK_BOARD.md`:
- Date/time
- Agent (Codex/Cursor/Claude)
- Branch
- Files changed
- Commands run + results
- Remaining risks / next action

## 9) Safety
- Never run destructive git/file commands unless explicitly asked.
- Do not overwrite unknown local user changes.
- If unexpected diffs are found, stop and ask before proceeding.
