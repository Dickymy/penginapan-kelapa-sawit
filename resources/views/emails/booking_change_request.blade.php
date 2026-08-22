<x-mail::message>
# Pengajuan Perubahan Booking Baru

Terdapat pengajuan perubahan baru untuk booking dengan kode **{{ $changeRequest->booking->booking_code }}**.

**Tamu:** {{ $changeRequest->user->name }}
**Tipe Perubahan:** {{ ucfirst(str_replace('_', ' ', $changeRequest->type)) }}
**Waktu Pengajuan:** {{ $changeRequest->created_at->format('d M Y, H:i') }}

**Detail Perubahan:**
<x-mail::panel>
### Data Lama
@if(isset($changeRequest->original_data['check_in']))
- **Check-in:** {{ \Carbon\Carbon::parse($changeRequest->original_data['check_in'])->format('d M Y') }}
- **Check-out:** {{ \Carbon\Carbon::parse($changeRequest->original_data['check_out'])->format('d M Y') }}
@endif
@if(isset($changeRequest->original_data['room_type_id']))
- **Tipe Kamar:** {{ \App\Models\RoomType::find($changeRequest->original_data['room_type_id'])->name ?? '-' }}
@endif
@if(isset($changeRequest->original_data['guest_count']))
- **Jumlah Tamu:** {{ $changeRequest->original_data['guest_count'] }} Orang
@endif

### Data Baru
@if(isset($changeRequest->requested_data['check_in']))
- **Check-in:** {{ \Carbon\Carbon::parse($changeRequest->requested_data['check_in'])->format('d M Y') }}
- **Check-out:** {{ \Carbon\Carbon::parse($changeRequest->requested_data['check_out'])->format('d M Y') }}
@endif
@if(isset($changeRequest->requested_data['room_type_id']))
- **Tipe Kamar:** {{ \App\Models\RoomType::find($changeRequest->requested_data['room_type_id'])->name ?? '-' }}
@endif
@if(isset($changeRequest->requested_data['guest_count']))
- **Jumlah Tamu:** {{ $changeRequest->requested_data['guest_count'] }} Orang
@endif
</x-mail::panel>

**Selisih Harga:** 
@if($changeRequest->price_difference > 0)
<span style="color: red;">+ Rp{{ number_format($changeRequest->price_difference, 0, ',', '.') }}</span> (Tamu kurang bayar)
@elseif($changeRequest->price_difference < 0)
<span style="color: blue;">- Rp{{ number_format(abs($changeRequest->price_difference), 0, ',', '.') }}</span> (Tamu lebih bayar / refund)
@else
Tidak ada perubahan harga.
@endif

Silakan login ke panel admin untuk memeriksa ketersediaan, menyetujui, atau menolak pengajuan ini.

<x-mail::button :url="route('admin.booking-changes.show', $changeRequest)">
Lihat Detail Pengajuan
</x-mail::button>

Terima kasih,<br>
Sistem {{ config('app.name') }}
</x-mail::message>
