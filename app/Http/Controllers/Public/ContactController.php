<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Mail\ContactAutoReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('public.contact');
    }

    public function store(StoreContactMessageRequest $request)
    {
        $message = ContactMessage::create($request->validated());

        Mail::to($message->email)->send(new ContactAutoReplyMail($message));

        return redirect()->back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera merespons Anda.');
    }
}
