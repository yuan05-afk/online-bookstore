# Accessibility Audit (WCAG 2.1 AA)

**Test Date:** 2026-02-05
**Tester:** Automated/Manual Audit

## Scope
- Homepage (index.php)
- Catalog (catalog.php)
- Checkout (checkout.php)

## Automated Checks (Simulated)

| Page | Check | Status | Notes |
|------|-------|--------|-------|
| All | HTML `lang` attribute | ⚠️ Pending | Ensure `<html lang="en">` is present. |
| All | Unique IDs | ✅ Pass | Based on codebase review, IDs seem unique correctly. |
| Images | `alt` text | ⚠️ Pending | Verify valid alt text for book covers. |
| Forms | Labels | ⚠️ Pending | Ensure all inputs have associated `<label>` tags. |

## Manual Checklist (WCAG 2.1 AA)

### 1. Perceivable
- [ ] **Text Alternatives**: All images have alt text.
- [ ] **Captions**: Videos have captions (N/A).
- [ ] **Info and Relationships**: Semantic HTML is used (headers, lists).
- [ ] **Contrast**: Text has contrast ratio of at least 4.5:1.

### 2. Operable
- [ ] **Keyboard Accessible**: All functionality available via keyboard.
- [ ] **No Keyboard Trap**: Focus doesn't get stuck.
- [ ] **Focus Visible**: Focus indicator is visible.

### 3. Understandable
- [ ] **Readable**: Language of page is defined.
- [ ] **Predictable**: UI components appear and behave consistently.
- [ ] **Input Assistance**: Error suggestions are provided.

### 4. Robust
- [ ] **Parsing**: HTML is valid.

## Issues Found
*(To be filled during execution)*
1. 
2. 

## Remediation Plan
- Add `lang="en"` to `header.php` if missing.
- Add `alt` attributes to dynamic image generation in `catalog.php`.
- Ensure form labels in `checkout.php` match input IDs.
