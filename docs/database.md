# 🧮 Database & Eloquent Design

> “A clean database schema is like a well-architected house — everything fits, flows, and makes sense without needing a map.”

---

### 📑 Table of Contents

1. [🧩 Core Philosophy](#-core-philosophy)
2. [🧱 Schema & Naming](#-schema--naming)
3. [🧠 Migrations](#-migrations)
4. [⚙️ Eloquent Model Rules](#️-eloquent-model-rules)
5. [🔗 Relationships](#-relationships)
6. [🧮 Query Design](#-query-design)
7. [🧰 Factories & Seeders](#-factories--seeders)
8. [🔍 Performance & Optimization](#-performance--optimization)
9. [📚 Additional Reading & Watchlist](#-additional-reading--watchlist)
10. [✍️ Author](#️-author)

---

### 🧩 Core Philosophy

1. **Database is the foundation.**
   Every naming, constraint, and relationship choice echoes throughout the app — so treat migrations as code, not setup scripts.

2. **Design for relationships, not for queries.**
   Think in terms of *models interacting naturally* — not in terms of `joins` and foreign keys alone.

3. **Let Eloquent do the heavy lifting.**
   Don’t fight the ORM. Use its strengths — relationships, scopes, accessors, and mutators — before dropping into raw queries.

---

### 🧱 Schema & Naming

| Type        | Example                    | Convention                          |
| ----------- | -------------------------- | ----------------------------------- |
| Table       | `users`, `order_items`     | Always plural, `snake_case`         |
| Column      | `first_name`, `created_at` | Consistent, descriptive             |
| Foreign key | `user_id`                  | Singular, aligns with related model |
| Pivot table | `post_tag`                 | Alphabetical order of models        |
| Index       | `idx_users_email`          | Prefix with `idx_`, clear purpose   |

**Example:**

```php
Schema::create('user_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('phone')->nullable();
    $table->timestamps();
});
```

---

### 🧠 Migrations

* **Each migration does one thing well.**
  Don’t mix schema changes with data migrations.

* **Never rename or delete columns** in production migrations — create new columns and handle data transition in a separate migration.

* **Use `enum` sparingly.** Prefer string-based `status` with constants or enums in code.

  ```php
  class OrderStatus
  {
      public const PENDING = 'pending';
      public const SHIPPED = 'shipped';
      public const CANCELLED = 'cancelled';
  }
  ```

* **Indexes and constraints** are not optional — they’re your performance armor and integrity check.

---

### ⚙️ Eloquent Model Rules

* One model = one table.
* Keep models **light** — no query logic in them. That’s for **Scopes** or **Repository/Service classes**.
* Use **`fillable`** or **`guarded`** (never both empty).
* Always define **relationships explicitly** — never rely on dynamic guessing.
* Use **casts** for clarity:

  ```php
  protected $casts = [
      'is_active' => 'boolean',
      'settings' => 'array',
      'joined_at' => 'datetime',
  ];
  ```

---

### 🔗 Relationships

* Always **name relationships semantically**:

  ```php
  public function posts() { return $this->hasMany(Post::class); }
  public function manager() { return $this->belongsTo(User::class, 'manager_id'); }
  ```

* Use **`withDefault()`** for optional `belongsTo` relations — prevents null-checking chaos.

  ```php
  $this->belongsTo(Profile::class)->withDefault();
  ```

* **Avoid circular eager loads** — `with(['user.posts.comments.user'])` can explode queries.
  Optimize using **`loadCount()`** and **`select()`** when only counts or partial data are needed.

---

### 🧮 Query Design

* Prefer **query scopes** over scattered query conditions:

  ```php
  public function scopeActive($query)
  {
      return $query->where('is_active', true);
  }
  ```

* For complex logic, **use custom builder classes**:

  ```php
  class UserBuilder extends Builder {
      public function withPremium() {
          return $this->where('plan', 'premium');
      }
  }
  ```

* Always **paginate**, never `->get()` large datasets.

* Cache frequent aggregates (counts, sums) using tags or cache keys that make sense.

---

### 🧰 Factories & Seeders

* Factories should generate **realistic** data — not random nonsense.

  ```php
  'email' => fake()->unique()->safeEmail(),
  'joined_at' => now()->subDays(fake()->numberBetween(1, 365)),
  ```

* Seeders are for **environments**, not for real data.
  Separate your seeders:

  ```
  ├── seeders/
  │   ├── DatabaseSeeder.php
  │   ├── DevSeeder.php
  │   ├── TestSeeder.php
  │   └── ProductionSeeder.php
  ```

* Never seed sensitive data or real emails. Use fakes even in staging.

---

### 🔍 Performance & Optimization

* Use **`lazy()` or `chunk()`** for big loops.
* Index foreign keys and heavily queried columns.
* Cache expensive joins with identifiers like `cache("user:{$id}:dashboard")`.
* Use **database transactions** for multi-step writes.

  ```php
  DB::transaction(function () use ($order) {
      $order->update(['status' => 'paid']);
      $order->invoice()->create([...]);
  });
  ```

---

> 🧭 **Rule of Thumb:**
> “If a query makes you nervous to run in production, it needs a transaction, a limit, or a rethink.”

---

### 📚 Additional Reading & Watchlist

| Type       | Resource                                                                                                    | Why it’s worth it                               |
| ---------- | ----------------------------------------------------------------------------------------------------------- | ----------------------------------------------- |
| 📖 Docs    | [Laravel Eloquent ORM](https://laravel.com/docs/eloquent)                                                   | The foundation of model–database interaction.   |
| 📖 Article | [Eloquent Performance Patterns – Laravel News](https://laravel-news.com/eloquent-performance-patterns)      | Covers real-world optimizations beyond indexes. |
| 📖 Guide   | [Laravel Beyond CRUD – Database Layer](https://spatie.be/guidelines/laravel-beyond-crud)                    | How to structure repositories and builders.     |
| 🎥 Video   | [Laracasts – Eloquent Relationships Deep Dive](https://laracasts.com/series/eloquent-relationships)         | Practical examples of modeling real-world data. |
| 🎥 Video   | [Optimizing Eloquent Queries](https://laracasts.com/series/eloquent-performance-tips)                       | Learn how to handle large datasets efficiently. |
| 📖 Blog    | [Database Design Best Practices – PlanetScale](https://planetscale.com/blog/database-design-best-practices) | Schema planning and indexing insights.          |

---

### ✍️ Author

**Written & Maintained by:** [Shakil Alam](https://github.com/itxshakil)
*Laravel Developer — crafting opinionated standards for scalable, maintainable applications.*

---