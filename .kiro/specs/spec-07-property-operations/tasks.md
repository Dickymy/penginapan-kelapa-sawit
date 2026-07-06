# SPEC 07 — Property Operations: Tasks

---

## Task 1: Migrations (refunds, audit_logs)

- [ ] Create `refunds` table migration
- [ ] Create `audit_logs` table migration
- [ ] Run migrations
- [ ] Commit

## Task 2: Models (Refund, AuditLog) + InvoiceService

- [ ] Create Refund model
- [ ] Create AuditLog model
- [ ] Create InvoiceService (eligibility check, generate invoice number, render PDF)
- [ ] Install barryvdh/laravel-dompdf
- [ ] Create invoice Blade template
- [ ] Commit

## Task 3: Complete + Award Integration

- [ ] Update Admin BookingController.complete to call LoyaltyPointService.awardForCompletedBooking
- [ ] Test: complete booking awards points, idempotent
- [ ] Commit

## Task 4: Cancellation Workflow Enhancement

- [ ] Update Admin BookingController.cancel to release promo + reverse points
- [ ] Test: cancel releases promo, reverses points
- [ ] Commit

## Task 5: Refund Controller + WhatsApp Helper

- [ ] Create Admin RefundController (create, store)
- [ ] Create WhatsApp helper (generate link from booking + settings)
- [ ] Add refund routes
- [ ] Commit

## Task 6: Invoice Controller + Audit Log Trait

- [ ] Create InvoiceController (download PDF — auth checks)
- [ ] Create AuditLog trait/helper for recording actions
- [ ] Wire audit logging into cancel, refund, complete, check-in, check-out
- [ ] Add routes
- [ ] Commit

## Task 7: Final Tests + Build

- [ ] Run full test suite
- [ ] Build frontend
- [ ] Commit
