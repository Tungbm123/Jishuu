# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Ngôn ngữ

Luôn trả lời bằng **tiếng Việt**.

## Project Overview

PHP/MySQL admin dashboard for managing courses, users, categories, and groups. Runs on XAMPP (localhost). No framework — custom MVC-like architecture.

**Stack:** PHP, MySQL (PDO), AdminLTE 4 (Bootstrap 5), PHPMailer

## Running the App

- Start Apache + MySQL via XAMPP
- Access at: `http://localhost/manager_course`
- Database: `course_manager` on `localhost`, user `root`, no password

No build step, no package manager, no tests. PHP files are served directly.

## Architecture

### Routing

All requests go through `index.php`. Routes use query params:

```
?module=<module>&action=<action>
→ loads: modules/<module>/<action>.php
```

Default: `module=dashboard`, `action=index`. Missing files → `modules/errors/404.php`.

### Request Lifecycle

1. `index.php` loads config, DB connection, helpers, layout wrapper
2. Layout wrapper (`templates/layouts/index.php`) calls `layout('header')`, `layout('sidebar')`, etc.
3. Module file is included — it handles logic AND outputs its HTML content
4. Footer/JS loaded at end

### Key Includes

| File | Purpose |
|------|---------|
| `config.php` | Constants: `_HOST`, `_DB`, `_USER`, `_PASS`, `_HOST_URL`, `_MODULES` (`dashboard`), `_ACTION` (`index`), `_TUNGBM` (access guard) |
| `includes/connect.php` | PDO connection → `$conn` global |
| `includes/database.php` | `getAll($sql)`, `getOnce($sql)`, `getRows($sql)`, `insert($table, $data)`, `update($table, $data, $condition)`, `delete($table, $condition)`, `getLastIdInsert()` |
| `includes/functions.php` | `filterData($method)`, `layout($name)`, `isPost()`, `isGet()`, `redirect($path)`, `getMsg($msg, $type)`, `formError($errors, $field)`, `getOldData($oldData, $key)`, `isLogin()`, `sendMail()` |
| `includes/session.php` | `setSession()`, `getSession()`, `removeSession()`, `setSessionFlash()`, `getSessionFlash()` |

### Access Guard

Every non-entry PHP file starts with:
```php
if (!defined('_TUNGBM')) { die('Truy cập ko hợp lệ'); }
```

### Authentication

Token-based: on login, a token is stored in both `$_SESSION['token_login']` and the `token_login` DB table. `isLogin()` validates the session token against the DB. `header.php` redirects to login if `!isLogin()`.

### Module Pattern

Each module follows CRUD pattern:
```
modules/<name>/list.php   — SELECT + display
modules/<name>/add.php    — INSERT
modules/<name>/edit.php   — UPDATE
modules/<name>/delete.php — DELETE
```

Data flow in a module: `filterData('POST')` → validate → DB function → `redirect()` or display with `getMsg()` / `formError()`.

### Templates

- `templates/layouts/` — header, sidebar, footer, breadcrumb partials
- `templates/assets/` — CSS (`adminlte.css`, `custom.css`, `login.css`), JS (`adminlte.js`), images
- `templates/uploads/` — user-uploaded files

The `layout($name, $data)` function in `functions.php` includes the corresponding file from `templates/layouts/`.

## Known Issues

- `modules/course/list.php`: search filter variable `$filter` not used in `$getDetailUser` query
- Some files mix module naming (e.g., form hidden `module=users` but file is in `modules/course/`)

---

## Workflow Orchestration

### 1. Plan Node Default
- Enter plan mode for ANY non-trivial task (3+ steps or architectural decisions)
- If something goes sideways, STOP and re-plan immediately – don't keep pushing
- Use plan mode for verification steps, not just building
- Write detailed specs upfront to reduce ambiguity

### 2. Subagent Strategy
- Use subagents liberally to keep main context window clean
- Offload research, exploration, and parallel analysis to subagents
- For complex problems, throw more compute at it via subagents
- One task per subagent for focused execution

### 3. Self-Improvement Loop
- After ANY correction from the user: update `tasks/lessons.md` with the pattern
- Write rules for yourself that prevent the same mistake
- Ruthlessly iterate on these lessons until mistake rate drops
- Review lessons at session start for relevant project

### 4. Verification Before Done
- Never mark a task complete without proving it works
- Diff behavior between main and your changes when relevant
- Ask yourself: "Would a staff engineer approve this?"
- Run tests, check logs, demonstrate correctness

### 5. Demand Elegance (Balanced)
- For non-trivial changes: pause and ask "is there a more elegant way?"
- If a fix feels hacky: "Knowing everything I know now, implement the elegant solution"
- Skip this for simple, obvious fixes – don't over-engineer
- Challenge your own work before presenting it

### 6. Autonomous Bug Fixing
- When given a bug report: just fix it. Don't ask for hand-holding
- Point at logs, errors, failing tests – then resolve them
- Zero context switching required from the user
- Go fix failing CI tests without being told how

## Task Management

1. **Plan First**: Write plan to `tasks/todo.md` with checkable items
2. **Verify Plan**: Check in before starting implementation
3. **Track Progress**: Mark items complete as you go
4. **Explain Changes**: High-level summary at each step
5. **Document Results**: Add review section to `tasks/todo.md`
6. **Capture Lessons**: Update `tasks/lessons.md` after corrections

## Core Principles

- **Simplicity First**: Make every change as simple as possible. Impact minimal code.
- **No Laziness**: Find root causes. No temporary fixes. Senior developer standards.
- **Minimal Impact**: Changes should only touch what's necessary. Avoid introducing bugs.
