# Giải thích: Logic `selected` trong dropdown search

## Context

User reports that filling in the search form (nhóm người dùng + keyword) and clicking "Tìm kiếm" does not populate `$filter`. Debug shows `$filter` is empty or missing the expected fields.

Two separate bugs exist, and both must be fixed for search to work end-to-end.

---

## Root Cause Analysis

### Bug 1 — Wrong `module` in hidden input (Critical)

File: `modules/course/list.php`, line ~35

```html
<input type="hidden" name="module" value="users">  <!-- BUG: should be "course" -->
<input type="hidden" name="action" value="list">
```

When the form is submitted (method=GET), the URL becomes:
```
?module=users&action=list&group=X&keyword=Y
```

The router in `index.php` reads `module=users` and loads `modules/users/list.php` — **not** `modules/course/list.php`. So the debug output in `course/list.php` is never reached after the search click. The user sees either the users list page or a 404.

**Fix:** Change `value="users"` → `value="course"`.

---

### Bug 2 — `$filter` not used in the DB query (Functional)

File: `modules/course/list.php`

After fixing Bug 1, the GET params (`group`, `keyword`) will correctly appear in `$filter`. However, the query `$getDetailUser` does not use `$filter` — it selects all rows unconditionally.

**Fix:** Append `WHERE` / `AND` clauses to the query using `$filter['keyword']` and `$filter['group']` when they are not empty.

---

## Implementation Plan

### Step 1 — Fix hidden input in search form

**File:** `modules/course/list.php`

Change:
```html
<input type="hidden" name="module" value="users">
```
To:
```html
<input type="hidden" name="module" value="course">
```

---

### Step 2 — Use `$filter` in the DB query

**File:** `modules/course/list.php`

Locate where `$getDetailUser` (or similar) is assigned and add conditional WHERE clauses:

```php
$filter = filterData(); // already exists

$sql = "SELECT ... FROM ... WHERE 1=1";

if (!empty($filter['keyword'])) {
    $keyword = $filter['keyword'];
    $sql .= " AND (u.fullname LIKE '%$keyword%' OR u.email LIKE '%$keyword%')";
}

if (!empty($filter['group'])) {
    $sql .= " AND u.group_id = '{$filter['group']}'";
}

$getDetailUser = getAll($sql);
```

Adjust column/table names to match the actual schema used in the file.

---

## Files to Modify

| File | Change |
|------|--------|
| `modules/course/list.php` | Fix hidden input value + add WHERE clauses to query |

No other files need to change.

---

## Verification

1. Load `?module=course&action=list` — confirm page loads normally
2. Type a keyword in the search box, click "Tìm kiếm"
3. Verify URL becomes `?module=course&action=list&keyword=xxx` (not `module=users`)
4. Confirm `$filter` debug output shows `keyword` and `group` keys with the entered values
5. Confirm the result table filters correctly
6. Test with empty search (no filter) to confirm all records still show
