<x-mail::message>
# Pengumuman Hasil Pengajuan Perubahan Booking

Halo **{{ $changeRequest->user->name }}**,

Pengajuan perubahan untuk booking Anda dengan kode **{{ $changeRequest->booking->booking_code }}** telah direview oleh admin.

@if($changeRequest->status === 'approved')
## <span style="color: green;">Status: DISETUJUI</span>

Pengajuan perubahan Anda telah **disetujui**.

@if($changeRequest->price_difference > 0)
### Tindakan Diperlukan: Kekurangan Pembayaran
Karena terdapat penambahan biaya sebesar **Rp{{ number_format($changeRequest->price_difference, 0, ',', '.') }}**, kami telah menerbitkan tagihan baru.
Perubahan Anda baru akan efektif sepenuhnya setelah tagihan ini dilunasi.

<x-mail::button :url="route('booking.my')">
Bayar Sekarang
</x-mail::button>
@elseif($changeRequest->price_difference < 0)
### Informasi Refund
Karena perubahan ini menyebabkan pengurangan biaya sebesar **Rp{{ number_format(abs($changeRequest->price_difference), 0, ',', '.') }}**, selisih dana akan dikembalikan (refund) ke rekening Anda. Silakan hubungi admin kami untuk informasi lebih lanjut.
@else
Data pemesanan Anda telah berhasil diperbarui di sistem kami.
@endif

@else
## <span style="color: red;">Status: DITOLAK</span>

Mohon maaf, pengajuan perubahan Anda **ditolak** oleh pihak penginapan.

@if($changeRequest->admin_notes)
**Catatan Admin:**
> {{ $changeRequest->admin_notes }}
@endif

Booking Anda tetap aktif dengan data jadwal dan kamar yang lama.
@endif

<x-mail::button :url="route('member.bookings.show', $changeRequest->booking)">
Lihat Detail Booking Anda
</x-mail::button>

Jika Anda memiliki pertanyaan, silakan hubungi kami via WhatsApp atau Email.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
