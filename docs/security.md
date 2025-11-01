# 🧱 Security & Data Protection

> *“Security isn’t a feature — it’s a habit.”*
> Every line of code should assume **someone uninvited** might touch it someday.
> Our job isn’t paranoia — it’s **preparedness**.

---

## 📚 Table of Contents

1. [🔒 Core Philosophy](#-core-philosophy)
2. [🧩 Environment & Configuration Hygiene](#-environment--configuration-hygiene)
3. [🧠 Request Validation — The First Line of Defense](#-request-validation--the-first-line-of-defense)
4. [🛡️ Authentication & Authorization](#️-authentication--authorization)
5. [🧱 Preventing Common Attacks](#-preventing-common-attacks)
6. [🧩 Logging & Monitoring](#-logging--monitoring)
7. [🚦 Database & Deployment Security](#-database--deployment-security)
8. [🔐 Secrets & API Management](#-secrets--api-management)
9. [⚙️ Temporary Debugging Protocol](#️-temporary-debugging-protocol)
10. [🧠 Security Review Checklist](#-security-review-checklist)
11. [👨‍💻 Author](#-author)

---

## 🔒 Core Philosophy

We don’t *add* security later — we **build with it** from the start.
In Laravel, that means protecting at **every layer**: input, logic, storage, and output.

> Think of security as a **layered shield**, not a single lock.

### 🧱 Core Principles

* **Least privilege** — every key, user, and action gets only what it needs.
* **Defense in depth** — secure every layer (code, data, infra, and humans).
* **Zero trust** — validate every assumption; never rely on client logic.

> 💡 *If you assume “no one will try that,” someone already did.*

---

## 🧩 Environment & Configuration Hygiene

### ✅ DO

* Use `.env` for all secrets — DB, API keys, mail credentials.
* Commit only `.env.example` for onboarding (no real values).
* Restrict `.env` access to trusted deployers.
* Rotate keys and tokens periodically.
* Store backups encrypted.

### 🚫 DON’T

* Never push `.env` or `storage/*.key` to Git.
* Never log sensitive data (tokens, passwords, OTPs).
* Never call `env()` directly in runtime logic — use `config()`.

> 💡 `config('services.stripe.secret')` ✅
> `env('STRIPE_SECRET')` ❌

---

## 🧠 Request Validation — The First Line of Defense

Validation is the **front gate** of your app. Nothing unvalidated should reach business logic.

Use **Form Requests**, not inline validation:

```php
class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:card,cod,upi'],
        ];
    }
}
```

* Typecast validated data before using it.
* Validate file **type and size** (`mimes:jpg,png|max:2048`).
* Use `$request->validated()` or `$request->safe()->only()` to reject extra fields.

> 🧩 *If it touches your DB, it must’ve passed a validator.*

---

## 🛡️ Authentication & Authorization

### 🧩 Authentication

* Use Laravel’s **guards**, not custom logic.
* Passwords must be hashed with **Bcrypt** or **Argon2**.
* Use **Sanctum** or **Passport** for API token auth.

### ⚖️ Authorization

* Use **Policies** for resource-level rules.
* Use **Gates** for one-off permissions.
* Apply both in **Controllers** and **Views**.

Example:

```php
public function update(User $user, Order $order)
{
    return $user->id === $order->user_id;
}
```

> 🚨 Never rely on frontend checks. Security **starts and ends on the backend**.

---

## 🧱 Preventing Common Attacks

### 🧠 SQL Injection

* Use **Eloquent** or **Query Builder** — never interpolate user input.
* When raw SQL is required, **bind parameters**:

  ```php
  DB::select('SELECT * FROM users WHERE email = ?', [$email]);
  ```

### 🧠 XSS (Cross-Site Scripting)

* Blade auto-escapes `{{ $value }}` ✅
* Avoid `{!! $value !!}` unless sanitized.
* Clean content via `strip_tags()` or `purify()`.

### 🧠 CSRF (Cross-Site Request Forgery)

* Always include `@csrf` in forms.
* For APIs, use token-based authentication instead.

### 🧠 Mass Assignment

* Explicitly define `$fillable` in every model.
* Never trust `$request->all()` — use `$request->validated()`.

  ```php
  protected $fillable = ['name', 'email', 'password'];
  ```

### 🧠 File Uploads

* Store with randomized filenames using `store()` or `storeAs()`.
* Restrict MIME types and upload paths.
* Serve user files via **signed URLs** or public symlinks, not direct paths.

---

## 🧩 Logging & Monitoring

Logs are your forensic trail — but can also leak gold if careless.

* Use `Log::info()`, `Log::warning()`, `Log::error()` meaningfully.
* **Never log sensitive info** (tokens, passwords, payloads).
* Use `Log::withContext(['user_id' => auth()->id()])` for traceability.
* Use **Sentry**, **Bugsnag**, or **LogRocket** for production tracking.

Example:

```php
Log::info('User login attempt', ['user_id' => $user->id, 'email' => '[REDACTED]']);
```

> 💡 “If it’s in your production logs, assume it could be read.”

---

## 🚦 Database & Deployment Security

* Use **least privilege** DB users — avoid `root`.
* Always backup before migrations.
* Lock DB access by environment (staging ≠ production).
* Enforce **HTTPS** via Nginx + Let’s Encrypt.
* Sanitize or anonymize dumps before sharing.

> 🧩 *Database safety = access control + awareness.*

---

## 🔐 Secrets & API Management

* Store secrets in `.env` or vaults (e.g., AWS, Forge).
* Rotate keys periodically.
* Use Laravel’s `encrypt()` for sensitive storage.
* Restrict outgoing webhooks by IP or domain.

Example:

```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

---

## ⚙️ Temporary Debugging Protocol

Debugging is okay — forgetting to remove it isn’t.

* Use `logger()` or `ray()` — never `dd()`, `dump()`, or `console.log()` in commits.
* For collaborative debugging, create a short-lived branch:

  ```
  git checkout -b debug/order-approval
  git push origin debug/order-approval
  ```

> 🔥 *Debug branches should die young — after they’ve taught their lesson.*

---

## 🧠 Security Review Checklist

Before merging or deploying:

* [ ] Inputs validated
* [ ] No plaintext secrets
* [ ] Policies enforced
* [ ] Safe file handling
* [ ] Logs sanitized
* [ ] HTTPS enabled
* [ ] Debug code removed
* [ ] Test routes deleted
* [ ] Dependencies audited (`composer audit`)

> 🧩 *“Security isn’t built once — it’s practiced daily.”*
> Treat every new feature like a door — ask:
> 🗝️ *Who can open it, and what’s behind it?*

---

## 👨‍💻 Author

**Shakil Alam**
Laravel Developer | [GitHub: itxshakil](https://github.com/itxshakil)

---