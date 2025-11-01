# 💻 Coding Standards

> “Code is read more often than it’s written — so write it like your future self has to debug it at 2 AM.”

---

### 📑 Table of Contents

1. [🧩 Core Principles](#-core-principles)
2. [🧱 Structure & Style](#-structure--style)
3. [🧠 Naming Philosophy](#-naming-philosophy)
4. [⚙️ Laravel-Specific Rules](#️-laravel-specific-rules)
5. [🔍 Debugging & Logging](#-debugging--logging)
6. [🧰 Comments & Documentation](#-comments--documentation)
7. [🧼 Commit Hygiene](#-commit-hygiene)
8. [📚 Recommended Reading & Watchlist](#-recommended-reading--watchlist)
9. [✍️ Author](#️-author)

---

### 🧩 Core Principles

1. **Readability over cleverness.**
   We favor clarity. One readable `if` block beats a fancy one-liner.
   The next developer should *understand your intention* at a glance.

2. **Consistency beats creativity.**
   Write in the *Laravel way* — leverage built-in helpers, Eloquent conventions, and framework patterns before inventing your own.

3. **Refactor when you touch.**
   Every edit is a chance to clean up — name things better, simplify logic, or extract a method.
   Leave the file a little better than you found it.

4. **Always stick to CRUD Controllers.**
   Keep controllers focused on the resource they manage — `index`, `store`, `show`, `update`, `destroy`.
   If you find yourself adding methods like `approveInvoice()` or `cancelBooking()`, it’s a sign that logic belongs in a dedicated **Action** or **Service class**.

   ```php
   // ✅ Good: Focused and RESTful
   class PostController extends Controller
   {
       public function index() { ... }
       public function store() { ... }
       public function update(Post $post) { ... }
       public function destroy(Post $post) { ... }
   }

   // ❌ Bad: Bloated and non-RESTful
   class PostController extends Controller
   {
       public function publishPost() { ... }
       public function archiveOldPosts() { ... }
   }
   ```

---

### 🧱 Structure & Style

* **PSR-12 as base**, Laravel’s flavor on top.
* 120 characters max per line — readable on split screens.
* **Use `snake_case`** for database columns, **`camelCase`** for variables/methods, and **`PascalCase`** for classes.
* Keep **imports alphabetized and grouped** (classes, traits, facades).

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

---

### 🧠 Naming Philosophy

Names are contracts — they describe *what*, not *how*.

| Type       | Example                         | Notes                                         |
| ---------- | ------------------------------- | --------------------------------------------- |
| Controller | `UserProfileController`         | Avoid `ManageController` or `MainController`. |
| Method     | `updatePassword()`              | Action-based, verb-first.                     |
| Variable   | `$activeUsers`, `$invoiceTotal` | Descriptive, plural where needed.             |
| Collection | `$users`                        | Never `$data` or `$stuff`.                    |
| Boolean    | `$isActive`, `$hasPermission`   | Read naturally in conditions.                 |

---

### ⚙️ Laravel-Specific Rules

* **Controllers stay thin.** Push business logic into **Actions** or **Service classes**.
* **Requests handle validation.** Never validate directly in controllers.
* **Policies** guard permissions. No `if ($user->role === 'admin')` in controllers.
* Use **Resource Collections** for consistent JSON structures.
* Prefer **Eloquent scopes** over query duplication.
* **Use CRUD routes only.** Avoid unnecessary endpoints for maintainability and consistency.

```php
// ✅ Do this
$users = User::active()->with('roles')->paginate(20);

// ❌ Not this
$users = User::where('is_active', true)->with('roles')->paginate(20);
```

---

### 🔍 Debugging & Logging

* **No `dd()`, `dump()`, or `console.log()`** in committed code — ever.
* For temporary debugging:

    1. Use `Log::debug()` or `ray()` (if installed).
    2. Keep logs contextual:

       ```php
       Log::debug('Payment webhook received', ['payload' => $request->all()]);
       ```
    3. If you must trace something complex, create a **temporary branch** (e.g., `debug/payment-sync`) and remove traces before merging.
* Before pushing, **search for “dd(”, “dump(”, or “console.log(”** — it’s part of the team’s pre-commit ritual.

---

### 🧰 Comments & Documentation

* Write **why**, not **what** — code already shows *what* it does.
* Good comment example:

  ```php
  // We cache this query to avoid hitting DB on every dashboard load.
  ```
* Use **DocBlocks** only for complex methods or interfaces.
* Avoid redundant comments:

  ```php
  // Bad
  $count++; // increment count
  ```

---

### 🧼 Commit Hygiene

* Keep commits **atomic and meaningful**:

    * ✅ `fix: prevent duplicate emails on registration`
    * ❌ `update file`, `changes`, `misc`
* Write commit messages in **present tense.**
* Squash trivial commits before merging (typos, spacing, console.log removals).

---

### 📚 Recommended Reading & Watchlist

> Continuous learning keeps standards sharp.

| Type       | Resource                                                                                               | Why it’s worth it                             |
| ---------- | ------------------------------------------------------------------------------------------------------ | --------------------------------------------- |
| 📖 Article | [Laravel Beyond CRUD – Spatie](https://spatie.be/guidelines/laravel-beyond-crud)                       | Deep dive into structuring apps cleanly.      |
| 📖 Docs    | [Laravel Controllers](https://laravel.com/docs/controllers)                                            | Official conventions — your first checkpoint. |
| 🎥 Video   | [Laracasts – Controllers and Actions](https://laracasts.com/series/laravel-8-from-scratch/episodes/15) | Hands-on explanation with real examples.      |
| 🎥 Video   | [Refactoring to Actions – Laracasts](https://laracasts.com/series/laravel-beyond-crud/episodes/2)      | Learn when to move logic out of controllers.  |

---

### ✍️ Author

**Written & Maintained by:** [Shakil Alam](https://github.com/itxshakil)
*Laravel Developer — writing standards for clarity, collaboration, and clean architecture.*

---