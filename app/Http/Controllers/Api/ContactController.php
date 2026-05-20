<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessage;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2500',
        ]);

        Contact::create($request->only(['name', 'email', 'subject', 'message']));

        Mail::to(config('mail.company_email', 'contact@vite-gourmand.fr'))
            ->send(new ContactMessage(
                senderEmail: $request->email,
                title:       $request->subject,
                description: $request->message,
            ));

        return response()->json(['message' => 'Message envoyé avec succès']);
    }
}