# SPEC 06 — Loyalty & Promotion: Tasks

> **Status:** Ready for implementation  
> **Dependency:** SPEC 05 complete

---

## Task 1: Migrations (loyalty_transactions, allocations, promotions, promotion_usages)

- [ ] Create 4 migrations matching master schema
- [ ] Run migrations
- [ ] Commit

## Task 2: Models (LoyaltyTransaction, LoyaltyPointAllocation, Promotion, PromotionUsage)

- [ ] Create models with fillable, casts, relations
- [ ] Add hasMany relations to User and Booking
- [ ] Commit

## Task 3: LoyaltyPointService

- [ ] Implement full service (earn, redeem, reversal, expire, adjust, getBalance)
- [ ] FIFO lot allocation logic
- [ ] Idempotency keys throughout
- [ ] Update user.loyalty_balance_cache
- [ ] Commit

## Task 4: PromotionService

- [ ] Implement validation, discount calc, reserve/consume/release
- [ ] Lock + quota check
- [ ] Commit

## Task 5: PricingService + BookingService Integration

- [ ] Extend PricingService.calculateQuote to accept promo/points
- [ ] Extend BookingService to handle promo reserve and point debit during booking
- [ ] Handle release/reversal on booking cancel/expire
- [ ] Update checkout controller to accept promo_code/redeem_points
- [ ] Commit

## Task 6: Admin Controllers (Loyalty + Promotions)

- [ ] Create AdminLoyaltyController (index, show user ledger, adjust)
- [ ] Create AdminPromotionController (resource CRUD)
- [ ] Create views + routes
- [ ] Commit

## Task 7: Member Points Page + Expire Command

- [ ] Create MemberPointController (index — show balance, transactions)
- [ ] Create expire command `loyalty:expire-points`
- [ ] Register scheduler
- [ ] Commit

## Task 8: Tests + Final

- [ ] Write tests for earn, redeem, reversal, expire, promo validation
- [ ] Run full suite
- [ ] Build
- [ ] Commit
