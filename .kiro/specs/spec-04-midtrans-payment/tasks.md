# SPEC 04 — Midtrans Payment: Tasks

> **Status:** Ready for implementation  
> **Dependency:** SPEC 03 complete

---

## Task 1: Migrations — Payments & Webhook Events

- [ ] Create migration `create_payments_table`
- [ ] Create migration `create_payment_webhook_events_table`
- [ ] Run migrations
- [ ] Commit

**Depends on:** SPEC 03  
**Verification:** Tables created with indexes and FKs

---

## Task 2: Payment & WebhookEvent Models

- [ ] Create `app/Models/Payment.php` — casts, relations (belongsTo Booking)
- [ ] Create `app/Models/PaymentWebhookEvent.php` — casts, fillable
- [ ] Add `hasMany(Payment)` to Booking model
- [ ] Commit

**Depends on:** Task 1  
**Verification:** Models instantiate, relations work

---

## Task 3: Install Midtrans SDK & MidtransPaymentService

- [ ] Install `midtrans/midtrans-php` via composer
- [ ] Create `app/Services/MidtransPaymentService.php`
- [ ] Implement `createOrResumePayment(Booking)` — find or create attempt, call Snap API, save token
- [ ] Implement `verifySignature(array $payload)` — SHA-512 verification
- [ ] Implement `mapProviderStatus(array $payload)` — status mapping
- [ ] Implement `handleWebhook(array $payload)` — full flow with dedup, verify, lock, transition
- [ ] Implement `fetchStatus(string $providerOrderId)` — server-to-server GET
- [ ] Implement `reconcilePayment(Payment)` — fetch + process
- [ ] Handle late payment after booking expired (needs_attention)
- [ ] Commit

**Depends on:** Task 2  
**Verification:** Service methods callable (full test in Task 5)

---

## Task 4: Controllers, Views & Routes

- [ ] Create `app/Http/Controllers/Public/PaymentController.php` — pay, finish
- [ ] Create `app/Http/Controllers/Webhook/MidtransWebhookController.php` — handle
- [ ] Create view `resources/views/public/booking/pay.blade.php` — Snap.js integration
- [ ] Create view `resources/views/public/booking/finish.blade.php` — post-payment redirect
- [ ] Add routes: /booking/{code}/bayar, /booking/{code}/selesai, /webhook/midtrans
- [ ] Exclude webhook from CSRF middleware
- [ ] Update confirmation page: enable "Bayar Sekarang" button linking to pay route
- [ ] Commit

**Depends on:** Task 3  
**Verification:** Routes registered, pages render, webhook endpoint accessible

---

## Task 5: Reconciliation Command

- [ ] Create `app/Console/Commands/ReconcilePaymentsCommand.php`
- [ ] Query: payment pending, created > 10 min, last_status_checked_at < 5 min ago or null
- [ ] For each: call MidtransPaymentService::reconcilePayment
- [ ] Register in scheduler (every 5 minutes)
- [ ] Commit

**Depends on:** Task 3  
**Verification:** Command runs without error

---

## Task 6: Tests

- [ ] Create `tests/Feature/Payment/PaymentCreationTest.php` — create attempt, resume existing
- [ ] Create `tests/Feature/Payment/WebhookTest.php` — valid signature confirms booking, invalid rejected, amount mismatch, duplicate idempotent, late payment needs_attention
- [ ] Run full test suite
- [ ] Commit

**Depends on:** Task 4  
**Verification:** All tests pass

---

## Task 7: Final Verification

- [ ] Run `php artisan test`
- [ ] Run `npm run build`
- [ ] Run `php artisan route:list`
- [ ] Verify webhook endpoint has no CSRF
- [ ] Verify Server Key not in any committed file
- [ ] Final commit

**Depends on:** All  
**Verification:** All tests pass, build clean
