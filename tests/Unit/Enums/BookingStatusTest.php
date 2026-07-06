<?php

namespace Tests\Unit\Enums;

use App\Enums\BookingStatus;
use App\Exceptions\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;

class BookingStatusTest extends TestCase
{
    public function test_pending_can_transition_to_confirmed(): void
    {
        $this->assertTrue(
            BookingStatus::PendingPayment->canTransitionTo(BookingStatus::Confirmed)
        );
    }

    public function test_pending_can_transition_to_expired(): void
    {
        $this->assertTrue(
            BookingStatus::PendingPayment->canTransitionTo(BookingStatus::Expired)
        );
    }

    public function test_pending_can_transition_to_cancelled(): void
    {
        $this->assertTrue(
            BookingStatus::PendingPayment->canTransitionTo(BookingStatus::Cancelled)
        );
    }

    public function test_confirmed_can_transition_to_checked_in(): void
    {
        $this->assertTrue(
            BookingStatus::Confirmed->canTransitionTo(BookingStatus::CheckedIn)
        );
    }

    public function test_confirmed_can_transition_to_no_show(): void
    {
        $this->assertTrue(
            BookingStatus::Confirmed->canTransitionTo(BookingStatus::NoShow)
        );
    }

    public function test_checked_in_can_transition_to_checked_out(): void
    {
        $this->assertTrue(
            BookingStatus::CheckedIn->canTransitionTo(BookingStatus::CheckedOut)
        );
    }

    public function test_checked_out_can_transition_to_completed(): void
    {
        $this->assertTrue(
            BookingStatus::CheckedOut->canTransitionTo(BookingStatus::Completed)
        );
    }

    public function test_pending_cannot_transition_to_checked_in(): void
    {
        $this->assertFalse(
            BookingStatus::PendingPayment->canTransitionTo(BookingStatus::CheckedIn)
        );
    }

    public function test_confirmed_cannot_transition_to_completed(): void
    {
        $this->assertFalse(
            BookingStatus::Confirmed->canTransitionTo(BookingStatus::Completed)
        );
    }

    public function test_terminal_states_have_no_transitions(): void
    {
        $terminals = [
            BookingStatus::Completed,
            BookingStatus::Cancelled,
            BookingStatus::Expired,
            BookingStatus::NoShow,
        ];

        foreach ($terminals as $status) {
            $this->assertEmpty($status->allowedTransitions(), "{$status->value} should have no transitions");
            $this->assertTrue($status->isTerminal());
        }
    }

    public function test_transition_to_throws_on_invalid(): void
    {
        $this->expectException(InvalidStatusTransitionException::class);

        BookingStatus::Completed->transitionTo(BookingStatus::Confirmed);
    }

    public function test_all_statuses_have_labels(): void
    {
        foreach (BookingStatus::cases() as $status) {
            $this->assertNotEmpty($status->label());
        }
    }

    public function test_blocking_statuses(): void
    {
        $this->assertTrue(BookingStatus::PendingPayment->isBlocking());
        $this->assertTrue(BookingStatus::Confirmed->isBlocking());
        $this->assertTrue(BookingStatus::CheckedIn->isBlocking());
        $this->assertFalse(BookingStatus::Completed->isBlocking());
        $this->assertFalse(BookingStatus::Cancelled->isBlocking());
        $this->assertFalse(BookingStatus::Expired->isBlocking());
    }
}
