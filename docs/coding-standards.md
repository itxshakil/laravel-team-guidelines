# 💻 Coding Standards

> “Code is read more often than it’s written — so write it like your future self has to debug it at 2 AM.”

---

### 📑 Table of Contents

1. [🧩 Core Principles](#-core-principles)
2. [🧱 Structure & Style](#-structure--style)
3. [🧠 Naming Philosophy](#-naming-philosophy)
4. [🧼 Clean Code](#-clean-code)
5. [🌐 API Design Guidelines](#-api-design-guidelines)
6. [📤 File Upload Guidelines](#-file-upload-guidelines)
7. [⚙️ Laravel-Specific Rules](#️-laravel-specific-rules)
8. [🔍 Debugging & Logging](#-debugging--logging)
9. [🧰 Comments & Documentation](#-comments--documentation)
10. [🧼 Commit Hygiene](#-commit-hygiene)
11. [📚 Recommended Reading & Watchlist](#-recommended-reading--watchlist)
12. [✍️ Author](#️-author)

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

### 🧼 Clean Code

> “Write code that feels obvious when you read it six months later.”

Clean code isn’t about perfection — it’s about **intent, clarity, and flow**.
When your functions read like sentences and your classes feel like stories, you’re doing it right.

#### 🎯 Guiding Principles

1. **Do one thing well.**
   Each class, method, or component should have one clear purpose.
   If you need to explain what else it does — it’s doing too much.

2. **Make it easy to change.**
   The best code is the one you can refactor safely next year without fear.

3. **Express intent, not mechanics.**
   Prefer `UserAuthenticator` over `DoLoginHandler`.
   Code should read like a sentence, not an instruction manual.

4. **Be allergic to duplication.**
   Repeated logic belongs in a service, trait, or helper — not copy-pasted between controllers.

5. **Refactor early and often.**
   Never leave messes behind. Each touchpoint is a chance to improve design and clarity.

---

#### 🧩 The SOLID Way (Laravel Edition)

| Principle                     | Essence                                                       | Laravel Example                                                                       |
| ----------------------------- | ------------------------------------------------------------- | ------------------------------------------------------------------------------------- |
| **S – Single Responsibility** | A class should do one thing only.                             | Keep validation in Requests, logic in Services, persistence in Models.                |
| **O – Open/Closed**           | Code should be open for extension, closed for modification.   | Use interfaces or strategy classes when adding new payment methods.                   |
| **L – Liskov Substitution**   | Subclasses must stand in for their parents without surprises. | If you extend `PaymentGateway`, your subclass should behave like one.                 |
| **I – Interface Segregation** | No class should depend on methods it doesn’t use.             | Create smaller interfaces (e.g., `Payable`, `Refundable`) instead of one bloated one. |
| **D – Dependency Inversion**  | Depend on abstractions, not concrete implementations.         | Type-hint interfaces in constructors — Laravel’s IoC will resolve them.               |

---

#### 💡 Code Feels Better When...

* Functions are short — **under 20 lines**.
* Names describe **why it exists**, not what it does.
* No “magic” strings or numbers — use **constants** or **enums**.
* You can follow the logic **top-to-bottom** without mental gymnastics.
* Comments explain **why**, not **what**.

Example:

```php
// Good
public function handle()
{
    // Cache to avoid hitting DB repeatedly
    return Cache::remember('dashboard.stats', 300, fn() => $this->generateStats());
}
```

---

### 🌐 API Design Guidelines

> “APIs should feel like conversations — clear, predictable, and kind.”

Your API is not just a data pipeline — it’s a **promise of stability and clarity** to everyone who uses it.

#### 🧭 Design Philosophy

1. **Predictability is kindness.**
   Stick to REST:

    * `GET /posts`
    * `POST /posts`
    * `PUT /posts/{id}`
    * `DELETE /posts/{id}`

2. **Version everything.**
   Always prefix routes with a version: `/api/v1/…`.
   It buys you freedom for future changes.

3. **Consistent structure wins trust.**
   Every response should follow the same shape:

   ```json
   {
     "success": true,
     "message": "Post created successfully.",
     "data": { ... }
   }
   ```

4. **Errors deserve dignity.**
   Return meaningful status codes and messages:

    * `200` — OK
    * `201` — Created
    * `400` — Bad Request
    * `401` — Unauthorized
    * `403` — Forbidden
    * `404` — Not Found
    * `422` — Validation Error
    * `500` — Server Error

5. **Validation belongs to Requests.**
   Keep controllers clean — let Laravel’s FormRequest handle input logic.

---

#### 🛡️ Security & Performance

* Use **Laravel Sanctum** or **Passport** for authentication.
* Don’t expose raw IDs — use UUIDs or hashed identifiers if public.
* Add **rate limiting** for sensitive endpoints.
* **Paginate** long lists — never return entire datasets.
* Sanitize all inputs and outputs — never trust client data blindly.

---

### 📤 File Upload Guidelines

> “Files are part of your data — treat them like you treat your database.”

Uploads can quietly become messy. Handle them deliberately and predictably.

#### 📁 Structure

* Use **Storage Facade**, never direct paths:

  ```php
  $path = $request->file('avatar')->store('users/avatars');
  ```
* Store only the **path** in the database, not the file content.
* Organize by entity:

  ```
  storage/app/public/users/{id}/profile.jpg
  storage/app/public/invoices/{id}/receipt.pdf
  ```

#### 🧾 Validation & Security

* Always validate uploads:

  ```php
  $request->validate([
      'avatar' => 'required|file|mimes:jpg,png|max:2048',
  ]);
  ```
* Restrict file types, size, and access levels.
* Use `Storage::url()` for public assets — never expose raw paths.
* Delete associated files when their model is deleted (use model events or observers).

#### ⚙️ Advanced Tips

* For large uploads, queue or chunk them.
* Use **temporary signed URLs** for restricted downloads.
* Regularly clean up orphaned files via a scheduled job.

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