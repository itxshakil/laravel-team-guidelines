# 🧩 Pull Request Template

> _Before submitting, make sure your code aligns with our Laravel Team Guidelines and passes all checks._  
> Every PR should **teach something** — explain your reasoning, not just your result.

---

## 🧠 Summary

- **What changed:**  
  _Clearly describe the updates, new features, or fixes introduced._  
  (e.g. “Refactored order service for transaction safety.”)

- **Why it changed:**  
  _Explain the problem or motivation behind the change._  
  (e.g. “Previous logic didn’t handle failed payments properly.”)

---

## 🧪 Testing

- **How was this tested?**  
  _List manual or automated tests performed._  
  (e.g. “Tested via PHPUnit + local tinker commands for multiple users.”)

- **Test results / screenshots (if applicable):**  
  _Attach logs, console output, or before/after screenshots._

---

## 🔍 Review Checklist

> Ensure your PR meets our quality standards before requesting review.

- [ ] Code follows **Laravel best practices**
- [ ] No `env()` used outside config
- [ ] No `composer update` run on server
- [ ] No PHP in Blade templates
- [ ] No N+1 queries (checked via debugbar or telescope)
- [ ] Added/updated relevant validation and authorization logic
- [ ] Proper naming conventions and single quotes for literals
- [ ] Files and logs do **not contain sensitive data**
- [ ] Tests and documentation (if required) updated

---

## 🔄 Related Issues / References

_Link any related issues, discussions, or documentation updates._  
Example:
- Fixes #123
- Related to #456
- Docs: `/docs/architecture/transactions.md`

---

## 💬 Additional Notes

_Add context, warnings, migration notes, or TODOs for reviewers._  
Example: “Migration includes new index; please deploy during low-traffic hours.”

---

> 💡 **Tip:** Keep PRs focused and small.  
> A clear, well-explained PR saves reviewers time and helps everyone learn something new.

---

