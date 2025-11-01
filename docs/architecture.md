# 🏗️ Architecture Guidelines

> *Keep it clean. Keep it composable. Keep it Laravel.*
> Architecture isn’t about rules — it’s about **readability, scalability, and flow**.
> Great architecture tells a story your future self will thank you for.

---

## 📚 Table of Contents

1. [🎯 Core Principles](#-core-principles)
2. [🧭 Folder Structure — A Scalable Blueprint](#-folder-structure--a-scalable-blueprint)
3. [🧩 Application Architecture & Folder Structure](#-application-architecture--folder-structure)
4. [🎯 Core Components](#-core-components)
5. [🧠 Domain-Driven Subfolders](#-domain-driven-subfolders)
6. [⚙️ ViewModels & Data Shaping](#️-viewmodels--data-shaping)
7. [🧩 Repository Layer (Optional but Useful)](#-repository-layer-optional-but-useful)
8. [🧰 Other Practical Conventions](#-other-practical-conventions)
9. [🧠 Architectural Wisdom](#-architectural-wisdom)
10. [👨‍💻 Author](#-author)

---

## 🎯 Core Principles

### 1. 🧩 Skinny Controllers, Fat Services

Controllers are narrators — they tell the story, not act it out.

* Handle **validation**, **authorization**, and **delegation** only.
* Let **Services** or **Actions** do the heavy lifting.
* Your controller should read like a clear sentence, not a puzzle.

> **Bad:** `OrderController` deciding discount logic.
> **Good:** `OrderService` handles it, while controller just says *“create order”*.

---

### 2. 🧠 Business Logic Belongs in the Domain

Your business rules deserve a first-class home. Put them where they belong:

| Layer            | Purpose                          | Example                           |
| ---------------- | -------------------------------- | --------------------------------- |
| `app/Services`   | Multi-model or API-driven logic  | `PaymentService`, `ReportService` |
| `app/Actions`    | Single-purpose, reusable actions | `ApproveUser`, `SendInvoice`      |
| `app/Domain/...` | Organized domain-driven logic    | `Orders`, `Billing`, `Users`      |

> If it’s about **what the business does**, it’s not the controller’s job — it’s the service layer’s story.

---

### 3. ⚙️ Jobs for Heavy or Async Tasks

Your app should never wait for something slow.

* Use **Jobs** for:

    * Emails
    * Reports
    * File processing
    * External API calls

Jobs should be:

* **Idempotent** → retry-safe
* **Self-contained** → holds its own data

> Don’t block users for background work — **delegate and dispatch**.

---

### 4. 🧱 Keep Models Pure & Predictable

Models are data keepers — not decision-makers.

* Avoid putting business logic directly in models.
* Use:

    * `scopeActive()`, `scopePaid()` → for filters
    * Accessors & Mutators → for computed attributes
* Models should **never cause side effects** like sending notifications or emails.

> Treat models like mirrors — they reflect data, not logic.

---

## 🧭 Folder Structure — A Scalable Blueprint

> Not just folders — **boundaries for clarity**.

```
app/
├── Actions/
│   ├── Orders/
│   │   ├── CreateOrder.php
│   │   ├── CancelOrder.php
│   │   └── RefundOrder.php
│   └── Users/
│       ├── RegisterUser.php
│       └── VerifyUser.php
│
├── Services/
│   ├── PaymentService.php
│   ├── NotificationService.php
│   └── ReportService.php
│
├── Jobs/
│   ├── SendInvoiceEmail.php
│   ├── SyncInventoryJob.php
│   └── ProcessRefundJob.php
│
├── Models/
│   ├── Order.php
│   ├── User.php
│   └── Product.php
│
└── Http/
    ├── Controllers/
    ├── Middleware/
    └── Requests/
```

> 🗂️ If someone new can open `/app` and instantly grasp what lives where — your architecture is working.

---

## 🧩 Application Architecture & Folder Structure

> “Great architecture isn’t about patterns — it’s about **predictability**.
> Every developer should know *where things live* and *why* they live there.”

---

### 🧱 The Philosophy

Our Laravel apps are structured around **clarity, composability, and purpose**.
We don’t chase patterns for elegance; we choose them for **collaboration and scale**.

> *Controllers tell the story, Services do the work, Models hold the data, Jobs take the load.*

---

### 🗂️ Folder Layout Overview

```
app/
├── Actions/
├── Services/
├── Domain/
├── Http/
├── Jobs/
├── Events/
├── Listeners/
├── Observers/
└── Policies/
```

Each directory has a clear purpose — no overlaps, no mystery.

---

## 🎯 Core Components

### **Controllers — The Storytellers**

* High-level **narrators** — no business logic.
* Handle **validation**, **authorization**, **delegation** only.

```php
public function store(StoreOrderRequest $request)
{
    $order = CreateOrder::run($request->validated());
    return new OrderResource($order);
}
```

---

### **Actions — Single-Purpose Executors**

* Perfect for small, reusable units of work.
* Keep them **pure** — one purpose, one entry point.

```php
class CreateOrder
{
    public function __construct(
        protected PaymentService $payment,
        protected NotificationService $notify
    ) {}

    public function run(array $data)
    {
        $order = Order::create($data);
        $this->payment->charge($order);
        $this->notify->orderCreated($order);
        return $order;
    }
}
```

---

### **Services — The Workhorses**

* Handle cross-model logic or third-party integrations.
* Always **stateless** and **testable**.

```php
class PaymentService
{
    public function charge(Order $order)
    {
        // Stripe or Razorpay logic here
    }
}
```

---

## 🧠 Domain-Driven Subfolders

When your app grows, **group by domain**, not layer.

```
app/Domain/Order/
├── Models/Order.php
├── Actions/CreateOrder.php
├── Events/OrderCreated.php
├── Listeners/SendOrderEmail.php
├── Services/OrderCalculator.php
└── Policies/OrderPolicy.php
```

> 💡 *Rule:* If a directory starts feeling like a junk drawer — it’s time for a domain split.

---

## ⚙️ ViewModels & Data Shaping

Shape complex data before sending to frontend.

```php
class DashboardViewModel
{
    public function __construct(protected User $user) {}

    public function data(): array
    {
        return [
            'orders' => $this->user->orders()->latest()->limit(5)->get(),
            'stats' => [
                'total_spent' => $this->user->orders()->sum('total'),
                'active_subscriptions' => $this->user->subscriptions()->active()->count(),
            ],
        ];
    }
}
```

Controller:

```php
return inertia('Dashboard', (new DashboardViewModel($user))->data());
```

---

## 🧩 Repository Layer (Optional but Useful)

Repositories abstract **data access**, not logic.

```php
interface UserRepository
{
    public function findByEmail(string $email): ?User;
}

class EloquentUserRepository implements UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
```

Use them when your data access patterns grow **complex**, not by default.

---

## 🧰 Other Practical Conventions

* ✅ Name Jobs, Events, Listeners after **intent**, not process.
* ✅ Use DTOs for structured data passing.
* ✅ Keep Observers for model-level events only.
* ✅ Keep Policies focused on action authorization.
* ✅ Document new architectural decisions in `/docs/architecture/decisions/`.

---

## 🧠 Architectural Wisdom

* **Composition > Inheritance** — inject dependencies, don’t extend.
* **Stateless Services** — no hidden side effects.
* **One Purpose per Unit** — each class answers a single “why.”
* **Review Regularly** — architecture evolves as teams grow.
* **Document Big Moves** — future devs should know *why*, not just *what*.

> 💡 *Clean architecture isn’t fancy — it’s understandable.*
> If a new developer can follow your flow without asking, you’ve done it right.

---

## 👨‍💻 Author

**Shakil Alam**
Laravel Developer | [GitHub: itxshakil](https://github.com/itxshakil)

---
