# 🧰 Testing & Quality Assurance

> *“A test is not a chore — it’s a conversation with your future self.”*
> We test to **build confidence**, not chase numbers.
> Clean tests mirror clear thinking.

---

## 📚 Table of Contents

1. [🎯 Purpose](#-purpose)
2. [🧪 Testing Philosophy](#-testing-philosophy)
3. [🧩 Test Structure](#-test-structure)
4. [🧠 Types of Tests](#-types-of-tests)
5. [🔬 Best Practices](#-best-practices)
6. [🧰 Testing Tools](#-testing-tools)
7. [⚙️ CI Integration](#️-ci-integration)
8. [🚨 Debugging Rule](#-debugging-rule)
9. [🧩 Golden Rule](#-golden-rule)
10. [👨‍💻 Author](#-author)

---

## 🎯 Purpose

Testing isn’t about **coverage metrics** — it’s about **trust**.
Trust that your app behaves the same **today**, **tomorrow**, and **under stress**.

We write tests to:

* Catch regressions early
* Document system behavior
* Build team confidence during refactors

> **Goal:** Write tests that are **meaningful**, **fast**, and **self-explanatory**.

---

## 🧪 Testing Philosophy

### 1. 🧩 Test Behavior, Not Implementation

Write tests that express *what* the system does — not *how* it does it.

✅ `it_sends_email_after_order_placed()`
❌ `it_calls_notification_service_send_method()`

> If the internals change but the business rule doesn’t — your test should still pass.

---

### 2. ⚡ Fast Feedback > Perfect Isolation

Run tests frequently.
Use **unit tests** for logic and **feature tests** for end-to-end flow.

> Speed gives confidence — confidence drives refactoring.

---

### 3. 💬 Every Test Should Teach Something

A teammate reading your test should learn **what** matters to the business.

> If a test reads like documentation, you’re doing it right.

---

### 4. 🚨 Fail Loudly

Never hide the “why” behind vague assertions.

```php
$this->assertEquals('paid', $order->status, 'Order should be marked as paid after processing');
```

> A failing test should tell you **exactly** what broke — not just *that* it broke.

---

## 🧩 Test Structure

```
tests/
├── Feature/
│   ├── Http/
│   │   ├── Orders/
│   │   │   ├── CreateOrderTest.php
│   │   │   ├── CancelOrderTest.php
│   │   │   └── RefundOrderTest.php
│   │   └── Users/
│   └── Api/
│       └── PaymentWebhookTest.php
│
├── Unit/
│   ├── Services/
│   │   ├── PaymentServiceTest.php
│   │   └── ReportServiceTest.php
│   ├── Actions/
│   │   └── CreateOrderTest.php
│   └── Helpers/
│       └── MoneyFormatterTest.php
│
└── Datasets/
    ├── order-data.php
    └── user-profiles.php
```

> 📁 **Naming Rule:**
> Test filenames must mirror the class or feature — no “MiscTest.php”.

---

## 🧠 Types of Tests

### **Unit Tests**

Small, isolated, lightning-fast.

```php
test('it calculates discount correctly', function () {
    $service = new DiscountService;
    expect($service->calculate(200, 10))->toBe(180);
});
```

No database. No filesystem. No HTTP.
Just **logic** and **expectations**.

---

### **Feature Tests**

Test the **flow** — from request to response.

```php
test('user can place an order successfully', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    actingAs($user)
        ->postJson('/orders', ['product_id' => $product->id])
        ->assertStatus(201)
        ->assertJson(['status' => 'pending']);
});
```

Use Laravel helpers like `actingAs()`, `getJson()`, `postJson()`.

---

### **Integration Tests**

Verify how systems talk to each other — database, service, and APIs.

```php
Http::fake(['stripe.com/*' => Http::response(['status' => 'success'], 200)]);
```

Use **fakes** for third-party services — never hit real APIs.

---

### **Browser Tests (Optional)**

For apps with frontend stacks (Inertia/Vue).

> Use **Laravel Dusk** or **Playwright** for end-to-end UI flow.

---

## 🔬 Best Practices

* ✅ **Name tests like sentences**
  `it_sends_email_after_successful_order()`
  not `test_email_sent()`

* ✅ Follow the **Arrange → Act → Assert** structure:

  ```php
  $order = Order::factory()->create(['status' => 'pending']);
  ProcessOrder::run($order);
  $this->assertEquals('paid', $order->fresh()->status);
  ```

* ✅ Use **Factories**, not manual setups.

* ✅ Keep **one behavior per test** (unless tightly related).

* ✅ Mock **sparingly** — use fakes for clarity.

* ❌ Don’t test **Laravel internals** — trust the framework.

---

## 🧰 Testing Tools

| Tool                     | Purpose                             | Notes                            |
| ------------------------ | ----------------------------------- | -------------------------------- |
| **PestPHP**              | Testing framework                   | Clean, readable syntax           |
| **Laravel Test Helpers** | Simulate HTTP, DB, queues           | `actingAs()`, `postJson()`, etc. |
| **Mockery**              | Mock dependencies                   | For edge integrations only       |
| **Laravel Fakes**        | Replace real services (Mail, Queue) | `Mail::fake()`, `Queue::fake()`  |
| **Pint**                 | Code style checker                  | Run `pint` before PRs            |
| **PHPStan**              | Static analysis                     | Catches bugs early               |

---

## ⚙️ CI Integration

Every pull request should automatically run:

* ✅ `composer test`
* ✅ `php artisan test --parallel`
* ✅ `vendor/bin/pint --test`
* ✅ `vendor/bin/phpstan analyse`

> 💡 If tests fail, fix the **code**, not the **test** — unless the rule changed.

---

## 🚨 Debugging Rule

* Use `logger()` or `ray()` locally.
* Never commit `dd()`, `dump()`, or `console.log()`.
* For shared debugging, create a **temporary branch**:

```
git checkout -b debug/payment-sync
git push origin debug/payment-sync
```

> 🧹 Temporary branches live short, teach much, and die young.

---

## 🧩 Golden Rule

> “A failing test is a gift — it’s your system telling you exactly where it hurts.”

---

## 👨‍💻 Author

**Shakil Alam**
Laravel Developer | [GitHub: itxshakil](https://github.com/itxshakil)

---
