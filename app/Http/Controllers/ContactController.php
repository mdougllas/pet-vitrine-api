<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendContactMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|email',
            'message' => 'required|min:20'
        ]);

        $body = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message']
        ];

        Mail::to(config('mail.from.address'))
            ->send(new ContactMessage($body));
    }
}
