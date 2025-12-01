# 💻 Coding Standards

> "Code is read more often than it's written — so write it like your future self has to debug it at 2 AM."

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
11. [🚀 Project Development & Code Management](#-project-development--code-management)
12. [🔒 Security Best Practices](#-security-best-practices)
13. [⚡ Performance Optimization](#-performance-optimization)
14. [🧪 Testing Standards](#-testing-standards)
15. [📚 Recommended Reading & Watchlist](#-recommended-reading--watchlist)
16. [✍️ Author](#️-author)

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
   If you find yourself adding methods like `approveInvoice()` or `cancelBooking()`, it's a sign that logic belongs in a dedicated **Action** or **Service class**.

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

* **PSR-12 as base**, Laravel's flavor on top.
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

> "Write code that feels obvious when you read it six months later."

Clean code isn't about perfection — it's about **intent, clarity, and flow**.
When your functions read like sentences and your classes feel like stories, you're doing it right.

#### 🎯 Guiding Principles

1. **Do one thing well.**
   Each class, method, or component should have one clear purpose.
   If you need to explain what else it does — it's doing too much.

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
| **I – Interface Segregation** | No class should depend on methods it doesn't use.             | Create smaller interfaces (e.g., `Payable`, `Refundable`) instead of one bloated one. |
| **D – Dependency Inversion**  | Depend on abstractions, not concrete implementations.         | Type-hint interfaces in constructors — Laravel's IoC will resolve them.               |

---

#### 💡 Code Feels Better When...

* Functions are short — **under 20 lines**.
* Names describe **why it exists**, not what it does.
* No "magic" strings or numbers — use **constants** or **enums**.
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

> "APIs should feel like conversations — clear, predictable, and kind."

Your API is not just a data pipeline — it's a **promise of stability and clarity** to everyone who uses it.

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
   Keep controllers clean — let Laravel's FormRequest handle input logic.

---

#### 🛡️ Security & Performance

* Use **Laravel Sanctum** or **Passport** for authentication.
* Don't expose raw IDs — use UUIDs or hashed identifiers if public.
* Add **rate limiting** for sensitive endpoints.
* **Paginate** long lists — never return entire datasets.
* Sanitize all inputs and outputs — never trust client data blindly.

---

### 📤 File Upload Guidelines

> "Files are part of your data — treat them like you treat your database."

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
* Before pushing, **search for "dd(", "dump(", or "console.log("** — it's part of the team's pre-commit ritual.

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

### 🚀 Project Development & Code Management

> "A well-structured project is half the battle won."

#### **1. Maintain Consistent Third-Party Versions**

* Always use the **same version** of Bootstrap, jQuery, Font Awesome, and other external libraries throughout the project.
* Before adding any dependency, **verify if it already exists** in the project.
* If you must upgrade a version, it must be upgraded **project-wide**, not partially.
* Prefer **locally hosted CSS/JS files** over CDNs to ensure stability and offline builds.

```json
// package.json example
{
  "dependencies": {
    "bootstrap": "5.3.0",  // ✅ Consistent version
    "jquery": "3.7.0"
  }
}
```

---

#### **2. HTML, CSS & JavaScript Structure**

* Never place CSS or JavaScript directly inside HTML files.
* If HTML, styles, and scripts must stay grouped, **extract them into proper components** or dedicated `.css` and `.js` files.
* Maintain clean separation of concerns for easy maintainability and scalability.

```blade
{{-- ❌ Bad: Inline styles and scripts --}}
<div style="color: red;">
    <script>alert('hello');</script>
</div>

{{-- ✅ Good: Proper separation --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
```

---

#### **3. Controller & Backend Architecture Standards**

* **Controller methods must not exceed 30 lines**.
* Extract heavy logic into:

  * **Model scopes**
  * **Service classes**
  * **Form Requests**
  * **Actions / Helpers**
* A controller should ideally have **no more than 8 public methods**.
* Long or multi-stage logic must be moved to dedicated service or domain layers.

```php
// ✅ Good: Thin controller
class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CreateOrderAction $action)
    {
        $order = $action->execute($request->validated());
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully');
    }
}

// ❌ Bad: Fat controller with 50+ lines of business logic
```

---

#### **4. Setup & Onboarding Requirements**

* Every new project must include a **setup script** or onboarding checklist inside `composer.json` or `README.md`.
* Ensure the following at minimum:

  * Running `composer install` should prepare the project.
  * Migrations should run correctly on a fresh setup.
  * Include seeders if needed for testing/demo.
* Testing, QA, and design teams should be able to set up the project **without developer help**.

```json
// composer.json - Post-install automation
{
  "scripts": {
    "post-install-cmd": [
      "@php artisan key:generate --ansi",
      "@php artisan storage:link"
    ],
    "setup": [
      "@composer install",
      "@php artisan migrate:fresh --seed",
      "@php artisan optimize:clear"
    ]
  }
}
```

---

#### **5. Code Quality Tools (Mandatory)**

* Include **Laravel Pint**, **Larastan**, and **Rector** in all new projects.
* Use shared team-approved configuration files.
* Make sure formatting, static analysis, and refactoring run before every pull request.

```bash
# Add to your pre-commit hook or CI pipeline
composer pint              # Code formatting
composer larastan          # Static analysis
composer rector --dry-run  # Automated refactoring checks
```

```yaml
# phpstan.neon - Larastan configuration example
includes:
    - ./vendor/nunomaduro/larastan/extension.neon

parameters:
    level: 5
    paths:
        - app
    excludePaths:
        - app/Console/Kernel.php
```

---

#### **6. Version Control & Code Review**

* All merges must go through **pull requests**.
* Peer review is required before merging into `develop` or `main`.
* Use descriptive PR titles and descriptions.
* Link related issues in PR descriptions.

---

#### **7. Environment & Configuration Practices**

* Never hard-code credentials; use `.env`.
* Keep `.env.example` updated with all required keys.
* Maintain separate configs for `local`, `staging`, and `production`.

```env
# .env.example - Always keep this updated
APP_NAME="Laravel App"
APP_ENV=local
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
MAIL_MAILER=smtp
# ... all other required keys
```

---

#### **8. Folder Structure & Naming Conventions**

* Follow consistent naming conventions for controllers, models, views, and components.
* Keep folder structures clean—avoid dumping unrelated files into core directories.

```
app/
├── Actions/           # Single-purpose action classes
├── Services/          # Business logic services
├── Http/
│   ├── Controllers/   # Thin CRUD controllers only
│   ├── Requests/      # Form validation
│   └── Resources/     # API responses
├── Models/            # Eloquent models with scopes
└── Policies/          # Authorization logic
```

---

#### **9. Documentation & Comments**

* Document non-obvious logic clearly.
* Update `README.md` whenever installation steps or project requirements change.
* Use inline comments only where logic isn't self-explanatory.

```markdown
# README.md must include:
- Project overview
- Prerequisites
- Installation steps
- Environment setup
- Running tests
- Deployment process
- Common troubleshooting
```

---

### 🔒 Security Best Practices

> "Security isn't a feature — it's a foundation."

#### **Authentication & Authorization**

* Always use Laravel's built-in authentication scaffolding.
* Implement **middleware** for route protection — never check auth in controllers.
* Use **policies** for resource-level authorization.
* Enable **two-factor authentication** for admin accounts.

```php
// ✅ Good: Policy-based authorization
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // ... update logic
}

// ❌ Bad: Manual role checking
if ($request->user()->role !== 'admin') {
    abort(403);
}
```

---

#### **Input Validation & Sanitization**

* Never trust user input — validate everything.
* Use Form Requests for complex validation rules.
* Sanitize HTML inputs to prevent XSS attacks.

```php
// Always use validated data
$validated = $request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed',
]);

User::create($validated); // ✅ Safe
User::create($request->all()); // ❌ Dangerous
```

---

#### **Database Security**

* Use **parameterized queries** — Eloquent does this by default.
* Never concatenate SQL strings manually.
* Implement **soft deletes** for sensitive data.
* Regularly backup databases and test restoration.

```php
// ✅ Safe: Eloquent handles parameterization
User::where('email', $email)->first();

// ❌ Dangerous: SQL injection risk
DB::select("SELECT * FROM users WHERE email = '$email'");
```

---

#### **API Security**

* Implement rate limiting on all public endpoints.
* Use HTTPS only — redirect HTTP to HTTPS.
* Add CORS policies — don't allow `*` in production.
* Rotate API tokens regularly.

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', fn(Request $req) => $req->user());
});
```

---

#### **Dependency Management**

* Regularly run `composer audit` and `npm audit`.
* Keep dependencies updated — security patches matter.
* Avoid using abandoned packages.

```bash
# Check for vulnerabilities
composer audit
npm audit

# Update dependencies safely
composer update --with-dependencies
npm update
```

---

### ⚡ Performance Optimization

> "Fast code is good code — but optimize wisely."

#### **Database Optimization**

* **Eager load relationships** to avoid N+1 queries.
* Add indexes to frequently queried columns.
* Use `chunk()` or `lazy()` for large datasets.

```php
// ❌ Bad: N+1 query problem
$users = User::all();
foreach ($users as $user) {
    echo $user->posts->count(); // Query executed for each user
}

// ✅ Good: Eager loading
$users = User::withCount('posts')->get();
foreach ($users as $user) {
    echo $user->posts_count; // Single query
}
```

---

#### **Caching Strategy**

* Cache expensive queries and computations.
* Use cache tags for grouped invalidation.
* Set appropriate TTL values — not too short, not too long.

```php
// Cache with tags for easy clearing
$stats = Cache::tags(['dashboard', 'stats'])
    ->remember('dashboard.stats', 3600, function () {
        return $this->calculateDashboardStats();
    });

// Clear related cache when data changes
Cache::tags(['dashboard'])->flush();
```

---

#### **Asset Optimization**

* Minify and compress CSS/JS in production.
* Use Laravel Mix or Vite for asset compilation.
* Implement lazy loading for images.
* Enable browser caching with proper headers.

```javascript
// vite.config.js - Production optimization
export default {
  build: {
    minify: 'terser',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'axios']
        }
      }
    }
  }
}
```

---

#### **Queue Processing**

* Move time-consuming tasks to queues.
* Process emails, notifications, and file uploads asynchronously.
* Monitor queue workers — use Horizon for Redis queues.

```php
// Dispatch jobs to queue
SendWelcomeEmail::dispatch($user)->onQueue('emails');

// In controller - fast response
public function store(Request $request)
{
    $order = Order::create($request->validated());
    
    ProcessOrderJob::dispatch($order);
    
    return response()->json(['message' => 'Order processing']);
}
```

---

### 🧪 Testing Standards

> "Untested code is broken code waiting to be discovered."

#### **Testing Philosophy**

* Write tests for business-critical features first.
* Aim for **70%+ code coverage**, but prioritize quality over quantity.
* Run tests before every commit and in CI/CD pipeline.

---

#### **Test Organization**

```
tests/
├── Feature/          # End-to-end tests
│   ├── Auth/
│   ├── Orders/
│   └── Payments/
├── Unit/             # Isolated logic tests
│   ├── Services/
│   └── Actions/
└── Browser/          # Laravel Dusk tests (optional)
```

---

#### **Writing Good Tests**

* Use descriptive test names — they're documentation.
* Follow AAA pattern: Arrange, Act, Assert.
* Use factories and seeders for test data.

```php
/** @test */
public function user_can_create_order_with_valid_data()
{
    // Arrange
    $user = User::factory()->create();
    $product = Product::factory()->create();
    
    // Act
    $response = $this->actingAs($user)->post('/orders', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    
    // Assert
    $response->assertRedirect('/orders');
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
}
```

---

#### **Continuous Integration**

* Run tests automatically on every push.
* Block merges if tests fail.
* Generate coverage reports.

```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run Tests
        run: |
          composer install
          php artisan test --coverage
```

---

### 📚 Recommended Reading & Watchlist

> Continuous learning keeps standards sharp.

| Type       | Resource                                                                                               | Why it's worth it                                    |
| ---------- | ------------------------------------------------------------------------------------------------------ | ---------------------------------------------------- |
| 📖 Article | [Laravel Beyond CRUD – Spatie](https://spatie.be/guidelines/laravel-beyond-crud)                       | Deep dive into structuring apps cleanly.             |
| 📖 Docs    | [Laravel Controllers](https://laravel.com/docs/controllers)                                            | Official conventions — your first checkpoint.        |
| 📖 Book    | Clean Code by Robert C. Martin                                                                         | Timeless principles for writing maintainable code.   |
| 📖 Book    | Refactoring by Martin Fowler                                                                           | Learn when and how to improve existing code.         |
| 🎥 Video   | [Laracasts – Controllers and Actions](https://laracasts.com/series/laravel-8-from-scratch/episodes/15) | Hands-on explanation with real examples.             |
| 🎥 Video   | [Refactoring to Actions – Laracasts](https://laracasts.com/series/laravel-beyond-crud/episodes/2)      | Learn when to move logic out of controllers.         |
| 🎥 Series  | [Laravel Package Development – Laracasts](https://laracasts.com/series/laravel-package-development)    | Build reusable, well-structured packages.            |
| 🎥 Talk    | [The Art of Code - Dylan Beattie](https://www.youtube.com/watch?v=6avJHaC3C2U)                         | Inspiring talk on writing beautiful, expressive code. |

---

### ✍️ Author

**Written & Maintained by:** [Shakil Alam](https://github.com/itxshakil)
*Laravel Developer — writing standards for clarity, collaboration, and clean architecture.*

---

> **Remember:** These standards evolve. Suggest improvements, challenge outdated rules, and keep the conversation going. Great code is a team sport.

**Last Updated:** December 2025
