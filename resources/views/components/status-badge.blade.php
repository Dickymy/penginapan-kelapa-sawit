{{-- Unified Status Badge Component --}}
{{-- Usage: <x-status-badge :status="$booking->status" /> --}}

@props(['status'])

@php
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;

// Determine label and color based on status type
if ($status instanceof BookingStatus) {
    $label = $status->label();
    $color = match($status) {
        BookingStatus::PendingPayment => 'yellow',
        BookingStatus::Confirmed => 'green',
        BookingStatus::CheckedIn => 'blue',
        BookingStatus::CheckedOut => 'indigo',
        BookingStatus::Completed => 'gray',
        BookingStatus::Cancelled => 'red',
        BookingStatus::Expired => 'red',
        BookingStatus::NoShow => 'red',
    };
} elseif ($status instanceof PaymentStatus) {
    $label = $status->label();
    $color = match($status) {
        PaymentStatus::Unpaid => 'gray',
        PaymentStatus::Pending => 'yellow',
        PaymentStatus::Paid => 'green',
        PaymentStatus::Failed => 'red',
        PaymentStatus::Expired => 'red',
        PaymentStatus::Refunded => 'purple',
        PaymentStatus::PartialRefund => 'purple',
    };
} elseif ($status instanceof RefundStatus) {
    $label = $status->label();
    $color = match($status) {
        RefundStatus::Requested => 'yellow',
        RefundStatus::Processing => 'blue',
        RefundStatus::Succeeded => 'green',
        RefundStatus::Failed => 'red',
        RefundStatus::Cancelled => 'gray',
    };
} else {
    // Fallback for string values
    $label = is_string($status) ? $status : (string) $status;
    $color = 'gray';
}

$classes = match($color) {
    'green' => 'bg-green-100 text-green-800',
    'red' => 'bg-red-100 text-red-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'blue' => 'bg-blue-100 text-blue-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
    'purple' => 'bg-purple-100 text-purple-800',
    default => 'bg-gray-100 text-gray-800',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $classes"]) }}>
    {{ $label }}
</span>
