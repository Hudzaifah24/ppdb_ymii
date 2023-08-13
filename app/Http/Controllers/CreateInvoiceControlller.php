<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CreateInvoiceControlller extends Controller
{
    public function create() {
        $user = Auth::user();

        $data['user_id'] = $user->id;

        if ($user->Application->status != 'PAID') {
            $secret_key = 'Basic ' . config('xendit.auth_key');
            $type = 'application/json';
            $external_id = "invoice-". Str::random(10);
            $amount = 200000;
            $desc = "Biaya Pendaftaran";
            $email = $user->email;
            $currency = 'IDR';

            $data_request = Http::withHeaders([
                'Authorization' => $secret_key,
                'Content-Type' => $type
            ])->post('https://api.xendit.co/v2/invoices', [
                'external_id' => $external_id,
                'amount' => $amount,
                'description' => $desc,
                'payer_email' => $email,
                'currency' => $currency,
                'customer' => [
                    'given_names' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->no_tlp_ayah,
                ]
            ]);

            $response = $data_request->object();

            $application = Application::where('user_id', Auth::user()->id)->first();

            $application->update([
                'invoice_number' => $response->external_id,
                'payment_status' => $response->status,
                'payment_link' => $response->invoice_url,
            ]);
        }

        return redirect()->route('pay', $user->username);
    }
}
